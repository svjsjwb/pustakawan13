<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReservationController extends Controller
{
    public function index(Request $request)
    {
        $user         = Auth::user();
        $member       = Member::where('email', $user->email)->first();
        $reservations = collect();
        $statusCounts = [
            'semua'        => 0,
            'menunggu'     => 0,
            'disetujui'    => 0,
            'siap_diambil' => 0,
            'ditolak'      => 0,
        ];

        if ($member) {
            $query = Reservation::with(['book.category', 'bookCopy'])
                ->where('member_id', $member->id);

            // Filter Pencarian
            if ($search = $request->input('search')) {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
                });
            }

            // Filter Status
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            $reservations = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

            // Hitungan status untuk tab / badge
            $allRes = Reservation::where('member_id', $member->id)->get();
            $statusCounts['semua']        = $allRes->count();
            $statusCounts['aktif']        = $allRes->whereIn('status', ['menunggu', 'disetujui', 'siap_diambil'])->count();
            $statusCounts['menunggu']     = $allRes->where('status', 'menunggu')->count();
            $statusCounts['disetujui']    = $allRes->where('status', 'disetujui')->count();
            $statusCounts['siap_diambil'] = $allRes->where('status', 'siap_diambil')->count();
            $statusCounts['ditolak']      = $allRes->where('status', 'ditolak')->count();
        }

        return view('user.reservations', compact('reservations', 'member', 'statusCounts'));
    }

    /**
     * Halaman Detail Reservasi Buku
     */
    public function show($id)
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        if (!$member) {
            return redirect()->route('user.reservations')->with('error', 'Data anggota tidak ditemukan.');
        }

        $reservation = Reservation::with(['book.category', 'bookCopy'])
            ->where('member_id', $member->id)
            ->findOrFail($id);

        return view('user.reservation-detail', compact('reservation', 'member'));
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

        $user = Auth::user();
        $member = Member::firstOrCreate(
            ['email' => $user->email],
            [
                'name'    => $user->name,
                'phone'   => '-',
                'address' => '-',
                'status'  => 'aktif',
            ]
        );

        $book = Book::findOrFail($validated['book_id']);

        $alreadyReserved = Reservation::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['menunggu', 'disetujui', 'siap_diambil'])
            ->exists();

        if ($alreadyReserved) {
            return response()->json([
                'message' => 'Buku ini sudah ada di daftar reservasi Anda.',
                'already_reserved' => true,
            ], 409);
        }

        if ($book->available_stock < 1) {
            return back()->with('error', 'Maaf, stok buku ini sedang habis sehingga tidak dapat direservasi.');
        }

        $reservedAt = $validated['reserved_at'] ?? now()->toDateString();

        DB::transaction(function () use ($member, $book, $reservedAt, $validated, $user) {
            $lockedBook = Book::lockForUpdate()->findOrFail($book->id);

            $duplicate = Reservation::where('member_id', $member->id)
                ->where('book_id', $lockedBook->id)
                ->whereIn('status', ['menunggu', 'disetujui', 'siap_diambil'])
                ->exists();

            if ($duplicate) {
                abort(409, 'Buku ini sudah ada di daftar reservasi Anda.');
            }

            $bookCopy = BookCopy::where('book_id', $lockedBook->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if ($bookCopy) {
                $bookCopy->update(['status' => 'reserved']);
            }

            $lockedBook->decrement('available_stock');

            $reservation = Reservation::create([
                'member_id'    => $member->id,
                'book_id'      => $lockedBook->id,
                'book_copy_id' => $bookCopy?->id,
                'reserved_at'  => $reservedAt,
                'expires_at'   => now()->parse($reservedAt)->addDays(3)->toDateString(),
                'seat_number'  => $validated['seat_number'] ?? null,
                'status'       => 'menunggu',
            ]);

            // Kirim notifikasi ke Admin
            AppNotification::notifyAdmin(
                'reservation_request',
                'Reservasi Buku Baru',
                "{$user->name} mengajukan reservasi buku \"{$lockedBook->title}\".",
                ['reservation_id' => $reservation->id, 'book_id' => $lockedBook->id]
            );
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Reservasi buku berhasil diajukan.',
                'reservation_url' => route('reservations.index'),
            ]);
        }

        return redirect()->route('user.reservations')->with('success', 'Reservasi buku berhasil diajukan! Menunggu persetujuan Admin.');
    }
}
