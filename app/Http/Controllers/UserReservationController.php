<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReservationController extends Controller
{
    // ─── Konstanta Aturan Bisnis ─────────────────────

    const MAX_ACTIVE_RESERVATIONS = 3;

    // ─── Halaman Daftar Reservasi ────────────────────

    /**
     * Tampilkan halaman "Reservasi Saya".
     */
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role !== 'user') {
            return redirect()->route('dashboard');
        }

        $reservations = Reservation::with('book')
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'selesai', 'dibatalkan', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        // Ambil daftar buku yang stoknya tersedia untuk modal buat reservasi
        $availableBooks = Book::where('stok', '>', 0)
            ->orderBy('judul_buku')
            ->get();

        return view('user.reservations', compact('reservations', 'availableBooks'));
    }

    // ─── Buat Reservasi Baru ─────────────────────────

    /**
     * Simpan reservasi baru oleh pengguna yang login.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->role !== 'user') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengguna umum (role user) yang dapat membuat reservasi.',
                ], 403);
            }
            return back()->with('reservation_error', 'Akun Administrator tidak dapat membuat reservasi buku online untuk anggota.');
        }

        $request->validate([
            'book_id'     => 'required|exists:books,id',
            'reserved_at' => 'nullable|date',
            'seat_number' => 'nullable|string|max:10',
        ]);

        $bookId = (int) $request->book_id;
        $reservedAt = $request->filled('reserved_at') ? $request->reserved_at : now()->toDateString();
        $seatNumber = $request->filled('seat_number') ? $request->seat_number : null;

        // BR-1: Maks 3 reservasi aktif
        $activeCount = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->count();

        if ($activeCount >= self::MAX_ACTIVE_RESERVATIONS) {
            return back()->with('reservation_error',
                'Kamu sudah memiliki ' . self::MAX_ACTIVE_RESERVATIONS . ' reservasi aktif. Selesaikan atau batalkan reservasi yang ada sebelum menambah yang baru.');
        }

        // BR-4: Tidak boleh reservasi buku yang sudah direservasi (aktif)
        $alreadyReserved = Reservation::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($alreadyReserved) {
            return back()->with('reservation_error',
                'Kamu sudah memiliki reservasi aktif untuk buku ini.');
        }

        // Hubungkan ke data member: prioritaskan user_id
        $member = Member::where('user_id', $user->id)->first();

        if (!$member && $user->email) {
            $member = Member::where('email', $user->email)->first();
            if ($member && !$member->user_id) {
                $member->update(['user_id' => $user->id]);
            }
        }

        if (!$member) {
            $member = Member::create([
                'user_id'  => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone ?: '-',
                'division' => 'Anggota',
                'status'   => 'Aktif',
            ]);
        }

        // Cari eksemplar buku (BookCopy) yang tersedia jika ada
        $bookCopy = BookCopy::where('book_id', $bookId)
            ->whereIn('status', ['available', 'tersedia'])
            ->first();

        // Simpan reservasi dengan status 'menunggu' (Pending)
        $newReservation = null;
        DB::transaction(function () use ($user, $bookId, $member, $bookCopy, $reservedAt, $seatNumber, &$newReservation) {
            $newReservation = Reservation::create([
                'user_id'      => $user->id,
                'member_id'    => $member?->id,
                'book_id'      => $bookId,
                'book_copy_id' => $bookCopy?->id,
                'reserved_at'  => $reservedAt,
                'seat_number'  => $seatNumber,
                'expires_at'   => null,
                'status'       => 'menunggu',
            ]);
        });

        // Broadcast real-time event untuk Admin
        if ($newReservation) {
            $book = Book::find($bookId);
            \App\Services\RealtimeService::publish('reservation.created', [
                'id'             => $newReservation->id,
                'user_id'        => $user->id,
                'member_id'      => $member?->id,
                'member_name'    => $member?->name ?? $user->name,
                'is_online_user' => true,
                'book_id'        => $bookId,
                'book_title'     => $book->title ?? $book->judul_buku ?? '-',
                'reserved_at'    => $reservedAt,
                'reserved_at_formatted' => \Carbon\Carbon::parse($reservedAt)->format('d/m/Y'),
                'expires_at'     => null,
                'status'         => 'menunggu',
                'status_label'   => 'Menunggu',
                'seat_number'    => $seatNumber,
            ]);
        }

        // Redirect ke halaman Reservasi Saya dengan notif sukses
        return redirect()->route('user.reservations')
            ->with('reservation_success', 'Reservasi berhasil dibuat dengan status Pending! Menunggu persetujuan admin.');
    }

    // ─── Batalkan Reservasi ─────────────────────────

    /**
     * Batalkan reservasi milik pengguna (hanya jika status = menunggu).
     */
    public function cancel(Reservation $reservation)
    {
        // Otorisasi: pastikan reservasi milik user yang login
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak punya akses ke reservasi ini.');
        }

        // BR-3: Hanya bisa batalkan saat menunggu
        if (!$reservation->isCancellable()) {
            return back()->with('reservation_error',
                'Reservasi ini sudah tidak dapat dibatalkan karena statusnya "' . $reservation->statusLabel() . '".');
        }

        $reservation->update(['status' => 'dibatalkan']);

        \App\Services\RealtimeService::publish('reservation.updated', [
            'id'           => $reservation->id,
            'status'       => 'dibatalkan',
            'status_label' => 'Dibatalkan',
        ]);

        return back()->with('reservation_success', 'Reservasi berhasil dibatalkan.');
    }
}
