<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReservationController extends Controller
{
    public function index(Request $request)
    {
        $user         = Auth::user();
        $member       = $user->member;
        $reservations = collect();

        $statusCounts = [
            'semua'        => 0,
            'menunggu'     => 0,
            'disetujui'    => 0,
            'siap_diambil' => 0,
            'ditolak'      => 0,
            'dibatalkan'   => 0,
        ];

        if ($member) {
            $query = Reservation::with(['book.category', 'bookCopy'])
                ->where('member_id', $member->id);

            // Filter Pencarian
            $search = trim((string) $request->input('search', ''));

            if ($search !== '') {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('judul_buku', 'ilike', "{$search}%");
                });
            }

            // Filter Status
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            $reservations = $query
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();

            // Hitungan status untuk tab / badge via agregasi SQL cepat
            $counts = Reservation::where('member_id', $member->id)
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN status IN ('menunggu', 'disetujui', 'siap_diambil') THEN 1 END) as aktif,
                    COUNT(CASE WHEN status = 'menunggu' THEN 1 END) as menunggu,
                    COUNT(CASE WHEN status = 'disetujui' THEN 1 END) as disetujui,
                    COUNT(CASE WHEN status = 'siap_diambil' THEN 1 END) as siap_diambil,
                    COUNT(CASE WHEN status = 'ditolak' THEN 1 END) as ditolak,
                    COUNT(CASE WHEN status = 'dibatalkan' THEN 1 END) as dibatalkan
                ")
                ->first();

            $statusCounts['semua']        = (int) ($counts->total ?? 0);
            $statusCounts['aktif']        = (int) ($counts->aktif ?? 0);
            $statusCounts['menunggu']     = (int) ($counts->menunggu ?? 0);
            $statusCounts['disetujui']    = (int) ($counts->disetujui ?? 0);
            $statusCounts['siap_diambil'] = (int) ($counts->siap_diambil ?? 0);
            $statusCounts['ditolak']      = (int) ($counts->ditolak ?? 0);
            $statusCounts['dibatalkan']   = (int) ($counts->dibatalkan ?? 0);
        }

        return view(
            'user.reservations',
            compact('reservations', 'member', 'statusCounts')
        );
    }


    /**
     * Halaman Detail Reservasi Buku
     */
    public function show($id)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()
                ->route('user.reservations')
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        $reservation = Reservation::with([
            'book.category',
            'bookCopy.shelf.zone.floor'
        ])
            ->where('member_id', $member->id)
            ->findOrFail($id);

        return view(
            'user.reservation-detail',
            compact('reservation', 'member')
        );
    }


    /**
     * User mengajukan reservasi buku dari katalog
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id'     => 'required|exists:books,id',
            'reserved_at' => 'nullable|date',
            'seat_number' => 'nullable|string|max:10',
        ]);

        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'message' => 'Data anggota tidak ditemukan.',
            ], 404);
        }

        $book = Book::findOrFail($validated['book_id']);

        $alreadyReserved = Reservation::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', [
                'menunggu',
                'disetujui',
                'siap_diambil'
            ])
            ->exists();

        if ($alreadyReserved) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Buku ini sudah ada di daftar reservasi Anda.',
                    'already_reserved' => true,
                ], 409);
            }

            return back()->with('error', 'Buku ini sudah ada di daftar reservasi Anda.');
        }

        if ($book->available_stock < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Maaf, stok buku ini sedang habis sehingga tidak dapat direservasi.',
                ], 409);
            }

            return back()->with(
                'error',
                'Maaf, stok buku ini sedang habis sehingga tidak dapat direservasi.'
            );
        }

        $reservedAt = $validated['reserved_at'] ?? now()->toDateString();
        $createdReservation = null;

        DB::transaction(function () use (
            $member,
            $book,
            $reservedAt,
            $validated,
            $user,
            &$createdReservation
        ) {
            $lockedBook = Book::lockForUpdate()
                ->findOrFail($book->id);

            $duplicate = Reservation::where('member_id', $member->id)
                ->where('book_id', $lockedBook->id)
                ->whereIn('status', [
                    'menunggu',
                    'disetujui',
                    'siap_diambil'
                ])
                ->exists();

            if ($duplicate) {
                abort(
                    409,
                    'Buku ini sudah ada di daftar reservasi Anda.'
                );
            }

            $bookCopy = BookCopy::where('book_id', $lockedBook->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (!$bookCopy || $lockedBook->available_stock < 1) {
                abort(
                    409,
                    'Maaf, stok buku ini baru saja habis.'
                );
            }

            $bookCopy->update([
                'status' => 'reserved'
            ]);

            $lockedBook->decrement('stok');

            $createdReservation = Reservation::create([
                'user_id'      => $user->id,
                'member_id'    => $member->id,
                'book_id'      => $lockedBook->id,
                'book_copy_id' => $bookCopy?->id,
                'reserved_at'  => $reservedAt,
                'expires_at'   => now()
                    ->parse($reservedAt)
                    ->addDays(3)
                    ->toDateString(),
                'seat_number'  => $validated['seat_number'] ?? null,
                'status'       => 'menunggu',
            ]);

            // Kirim notifikasi ke Admin
            AppNotification::notifyAdmin(
                'reservation_request',
                'Reservasi Buku Baru',
                "{$user->name} mengajukan reservasi buku \"{$lockedBook->title}\".",
                [
                    'reservation_id' => $createdReservation->id,
                    'book_id'        => $lockedBook->id
                ]
            );
        });

        if ($createdReservation) {
            NotificationService::reservationSubmitted(
                $createdReservation,
                $user
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Reservasi buku berhasil diajukan.',
                'reservation_url' => route('reservations.index'),
            ]);
        }

        return redirect()
            ->route('user.reservations')
            ->with(
                'success',
                'Reservasi buku berhasil diajukan! Menunggu persetujuan Admin.'
            );
    }

    /**
     * User membatalkan reservasi buku
     */
    public function cancel(Request $request, $id)
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota tidak ditemukan.',
                ], 404);
            }

            return back()->with('error', 'Data anggota tidak ditemukan.');
        }

        $reservation = Reservation::where('member_id', $member->id)
            ->findOrFail($id);

        if (!in_array($reservation->status, ['menunggu', 'disetujui', 'siap_diambil'], true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi ini tidak dapat dibatalkan karena statusnya sudah ' . $reservation->display_status . '.',
                ], 422);
            }

            return back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($reservation, $user) {
            $lockedReservation = Reservation::lockForUpdate()->findOrFail($reservation->id);

            // Lepaskan BookCopy yang di-reserve jika ada
            if ($lockedReservation->book_copy_id) {
                $bookCopy = BookCopy::lockForUpdate()->find($lockedReservation->book_copy_id);
                if ($bookCopy && $bookCopy->status === 'reserved') {
                    $bookCopy->update([
                        'status' => 'available',
                    ]);
                }
            }

            // Kembalikan stok buku
            $book = Book::lockForUpdate()->find($lockedReservation->book_id);
            if ($book) {
                $book->increment('stok');
            }

            // Update status reservasi
            $lockedReservation->update([
                'status'           => 'dibatalkan',
                'rejection_reason' => 'Dibatalkan oleh Pengguna',
            ]);

            // Sinkronisasi status member jika aktif
            if ($lockedReservation->member) {
                app(\App\Services\MemberStatusService::class)->sync($lockedReservation->member);
            }

            // Notifikasi ke Admin
            AppNotification::notifyAdmin(
                'reservation_cancelled',
                'Reservasi Dibatalkan',
                "{$user->name} membatalkan reservasi buku \"{$book?->title}\".",
                [
                    'reservation_id' => $lockedReservation->id,
                    'book_id'        => $lockedReservation->book_id,
                ]
            );
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibatalkan.',
                'status'  => 'dibatalkan',
            ]);
        }

        return redirect()
            ->route('user.reservations.show', $reservation->id)
            ->with('success', 'Reservasi berhasil dibatalkan.');
    }

    /**
     * Endpoint polling status reservasi user untuk pembaruan realtime
     */
    public function statusFeed()
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'success' => true,
                'counts' => [
                    'aktif' => 0,
                    'menunggu' => 0,
                    'siap_diambil' => 0
                ],
                'reservations' => []
            ]);
        }

        $allRes = Reservation::where(
            'member_id',
            $member->id
        )->get();

        $counts = [
            'aktif' => $allRes
                ->whereIn('status', [
                    'menunggu',
                    'disetujui',
                    'siap_diambil'
                ])
                ->count(),

            'menunggu' => $allRes
                ->where('status', 'menunggu')
                ->count(),

            'disetujui' => $allRes
                ->where('status', 'disetujui')
                ->count(),

            'siap_diambil' => $allRes
                ->where('status', 'siap_diambil')
                ->count(),

            'ditolak' => $allRes
                ->where('status', 'ditolak')
                ->count(),
        ];

        $reservations = $allRes->map(function ($r) {
            return [
                'id' => $r->id,
                'status' => strtolower($r->status),
                'rejection_reason' => $r->rejection_reason,
                'updated_at' => $r->updated_at
                    ? $r->updated_at->toISOString()
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'counts' => $counts,
            'reservations' => $reservations,
        ]);
    }
}
