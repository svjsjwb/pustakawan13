<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\Reservation;
use App\Services\NotificationService;
use App\Services\ReservationCancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReservationController extends Controller
{
    public function index(Request $request)
    {
        // Jalankan auto-cancel on-demand jika ada reservasi melewati batas waktu 3 hari
        ReservationCancellationService::cancelExpiredReservations();

        $user = Auth::user();
        $member = $user->member;
        $reservations = collect();

        $statusCounts = [
            'semua' => 0,
            'menunggu' => 0,
            'disetujui' => 0,
            'siap_diambil' => 0,
            'ditolak' => 0,
            'dibatalkan' => 0,
        ];

        if ($member) {
            $query = Reservation::with(['book.category', 'bookCopy'])
                ->where('member_id', $member->id);

            // Filter Pencarian
            $search = trim((string) $request->input('search', ''));

            if ($search !== '') {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('judul_buku', 'like', "{$search}%");
                });
            }

            // Filter Status
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            $reservations = $query
                ->orderByDesc('created_at')
                ->get();

            // Hitungan status untuk tab / badge
            $allRes = Reservation::where('member_id', $member->id)->get();

            $statusCounts['semua'] = $allRes->count();

            $statusCounts['aktif'] = $allRes
                ->whereIn('status', [
                    'menunggu',
                    'disetujui',
                    'siap_diambil'
                ])
                ->count();

            $statusCounts['menunggu'] = $allRes
                ->where('status', 'menunggu')
                ->count();

            $statusCounts['disetujui'] = $allRes
                ->where('status', 'disetujui')
                ->count();

            $statusCounts['siap_diambil'] = $allRes
                ->where('status', 'siap_diambil')
                ->count();

            $statusCounts['ditolak'] = $allRes
                ->where('status', 'ditolak')
                ->count();

            $statusCounts['dibatalkan'] = $allRes
                ->where('status', 'dibatalkan')
                ->count();
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
            'bookCopy'
        ])
            ->where('member_id', $member->id)
            ->findOrFail($id);

        $cancellationRejectionNotification = AppNotification::query()
            ->where('user_id', $user->id)
            ->where('role', 'user')
            ->where('type', 'reservation_cancellation_rejected')
            ->whereJsonContains('data->reservation_id', (int) $reservation->id)
            ->latest()
            ->first();

        return view(
            'user.reservation-detail',
            compact('reservation', 'member', 'cancellationRejectionNotification')
        );
    }

    /**
     * Cancel a reservation owned by the authenticated member.
     */

    public function cancel($id)
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()
                ->route('user.reservations')
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        $cancellationState = DB::transaction(function () use ($member, $id) {
            $reservation = Reservation::where('member_id', $member->id)
                ->lockForUpdate()
                ->findOrFail($id);

            if ($reservation->status === 'dibatalkan') {
                return 'cancelled';
            }

            if (!in_array($reservation->status, ['menunggu', 'disetujui', 'siap_diambil'], true)) {
                abort(422, 'Reservasi ini tidak dapat dibatalkan.');
            }

            // Jika permintaan masih menunggu keputusan admin.
            if ($reservation->cancellation_requested_at) {
                return 'pending';
            }

            // Pembatalan reservasi yang sudah disetujui hanya boleh diajukan sekali.
            if (in_array($reservation->status, ['disetujui', 'siap_diambil'], true)) {
                if ($reservation->cancellation_request_used_at) {
                    return 'already_used';
                }

                $reservation->update([
                    'cancellation_requested_at' => now(),
                    'cancellation_request_used_at' => now(),
                ]);

                return 'pending_requested';
            }

            // Reservasi berstatus menunggu dapat dibatalkan langsung oleh pengguna.
            if ($reservation->book_copy_id) {
                $bookCopy = BookCopy::lockForUpdate()->find($reservation->book_copy_id);

                if ($bookCopy && $bookCopy->status === 'reserved') {
                    $bookCopy->update(['status' => 'available']);
                }
            }

            $book = Book::lockForUpdate()->findOrFail($reservation->book_id);
            $book->increment('stok');

            $reservation->update([
                'status' => 'dibatalkan',
            ]);

            return 'cancelled';
        });

        if ($cancellationState === 'pending_requested') {
            $reservation = Reservation::with('book')->findOrFail($id);

            AppNotification::notifyAdmin(
                'reservation_cancellation_request',
                'Permintaan Pembatalan Reservasi',
                Auth::user()->name . ' meminta pembatalan reservasi buku "' . ($reservation->book?->title ?? 'Buku') . '".',
                ['reservation_id' => $reservation->id]
            );
        }

        if ($cancellationState === 'already_used') {
            return redirect()
                ->route('user.reservations.show', $id)
                ->with('error', 'Anda sudah pernah mengajukan pembatalan untuk reservasi ini. Pengajuan ulang tidak diperbolehkan.');
        }

        return redirect()
            ->route('user.reservations.show', $id)
            ->with(
                'cancellation_request_pending',
                in_array($cancellationState, ['pending', 'pending_requested'], true)
            )
            ->with('reservation_cancelled', $cancellationState === 'cancelled');
    }


    /**
     * User mengajukan reservasi buku dari katalog
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reserved_at' => 'nullable|date|after_or_equal:today',
            'seat_number' => 'nullable|string|max:10',
        ], [
            'reserved_at.after_or_equal' => 'Tanggal reservasi tidak boleh sebelum hari ini.',
            'reserved_at.date' => 'Format tanggal reservasi tidak valid.',
        ]);

        $user = Auth::user();
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
            return response()->json([
                'message' => 'Buku ini sudah ada di daftar reservasi Anda.',
                'already_reserved' => true,
            ], 409);
        }

        if ($book->available_stock < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Maaf, stok buku ini sedang habis sehingga tidak dapat direservasi.',
                ], 422);
            }
            return back()->with(
                'error',
                'Maaf, stok buku ini sedang habis sehingga tidak dapat direservasi.'
            );
        }

        $reservedAt = !empty($validated['reserved_at'])
            ? \Carbon\Carbon::parse($validated['reserved_at'])->toDateString()
            : now()->toDateString();
        // expires_at = 3 hari dari SAAT RESERVASI DIBUAT (batas admin untuk menyetujui)
        // Bukan 3 hari dari reserved_at, karena jika admin menyetujui => langsung menjadi peminjaman
        $expiresAt = now()->addDays(3)->toDateString();
        $createdReservation = null;

        DB::transaction(function () use (
            $member,
            $book,
            $reservedAt,
            $expiresAt,
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
                'user_id' => $user->id,
                'member_id' => $member->id,
                'book_id' => $lockedBook->id,
                'book_copy_id' => $bookCopy?->id,
                'reserved_at' => $reservedAt,
                'expires_at' => $expiresAt,
                'seat_number' => $validated['seat_number'] ?? null,
                'status' => 'menunggu',
            ]);

            // Kirim notifikasi ke Admin
            AppNotification::notifyAdmin(
                'reservation_request',
                'Reservasi Buku Baru',
                "{$user->name} mengajukan reservasi buku \"{$lockedBook->title}\".",
                [
                    'reservation_id' => $createdReservation->id,
                    'book_id' => $lockedBook->id
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
                'message' => 'Reservasi buku berhasil diajukan. Admin akan menyetujui dalam 3 hari.',
                'reservation_url' => route('user.reservations'),
                'reserved_at' => \Carbon\Carbon::parse($createdReservation->reserved_at)->translatedFormat('d M Y'),
                'expires_at' => \Carbon\Carbon::parse($createdReservation->expires_at)->translatedFormat('d M Y'),
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
     * Endpoint polling status reservasi user untuk pembaruan realtime
     */
    public function statusFeed()
    {
        // Jalankan auto-cancel on-demand jika ada reservasi melewati batas waktu 3 hari
        ReservationCancellationService::cancelExpiredReservations();

        $user = Auth::user();
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
                'status_label' => $r->display_status,
                'cancellation_requested_at' => $r->cancellation_requested_at?->toISOString(),
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
