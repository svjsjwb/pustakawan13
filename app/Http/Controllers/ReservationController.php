<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReservationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR RESERVASI / TRANSAKSI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
         * Tanggal yang dipilih untuk denah kursi.
         */
        $selectedDate = $request->get(
            'reservation_date',
            now()->format('Y-m-d')
        );

        /*
         * =====================================================
         * ANGGOTA AKTIF & BUKU
         * =====================================================
         */

        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        /*
         * =====================================================
         * DAFTAR RESERVASI / TRANSAKSI DENGAN FILTER TANGGAL
         * =====================================================
         *
         * 1. Filter Rentang Tanggal Kalender (Date Range Picker):
         *    - Pengguna bebas memilih rentang tanggal fleksibel (start_date s/d end_date).
         *    - Menggunakan whereBetween pada query database.
         *
         * 2. Aturan Batas 1 Bulan (Default jika tanpa filter khusus):
         *    - Data disembunyikan jika > 1 bulan berdasarkan jatuh tempo (due_at/expires_at).
         *    - Transaksi lintas bulan tetap masuk ke periode bulan berikutnya.
         *    - Data lama TIDAK dihapus dari database.
         */

        $query = Reservation::with(['member', 'book']);

        // Tentukan kolom jatuh tempo / batas waktu (due_at / expires_at / fallback reserved_at)
        $dueColumn = Schema::hasColumn('reservations', 'due_at')
            ? 'due_at'
            : (Schema::hasColumn('reservations', 'expires_at') ? 'expires_at' : 'reserved_at');

        // 1. Prioritas: Filter Rentang Tanggal Kalender Bebas (start_date & end_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('reserved_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('reserved_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('reserved_at', '<=', $request->end_date);
        } elseif ($request->filled('month') && $request->filled('year')) {
            // 2. Filter berdasarkan Bulan & Tahun
            $query
                ->whereYear($dueColumn, $request->year)
                ->whereMonth($dueColumn, $request->month);
        } else {
            // 3. Default: Batas 1 bulan dari due date
            $query->where($dueColumn, '>=', now()->subMonth()->startOfDay());
        }

        $reservations = $query->latest('reserved_at')->get();

        /*
         * =====================================================
         * KURSI YANG SUDAH BOOKING
         * =====================================================
         */

        $bookedSeats = Reservation::whereDate(
            'reserved_at',
            $selectedDate
        )
            ->whereIn('status', [
                'menunggu',
                'disetujui'
            ])
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();

        /*
         * =====================================================
         * KIRIM KE VIEW
         * =====================================================
         */

        return view('reservations.index', compact(
            'members',
            'books',
            'reservations',
            'bookedSeats',
            'selectedDate'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN RESERVASI
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'exists:members,id'
            ],
            'book_id' => [
                'required',
                'exists:books,id'
            ],
            'reserved_at' => [
                'required',
                'date'
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:reserved_at'
            ],
            'seat_number' => [
                'nullable',
                'string',
                'regex:/^[ABC][1-8]$/'
            ],
        ]);

        if (!empty($validated['seat_number'])) {
            $seatAlreadyBooked = Reservation::whereDate(
                'reserved_at',
                $validated['reserved_at']
            )
                ->where(
                    'seat_number',
                    $validated['seat_number']
                )
                ->whereIn('status', [
                    'menunggu',
                    'disetujui'
                ])
                ->exists();

            if ($seatAlreadyBooked) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Kursi ' .
                        $validated['seat_number'] .
                        ' sudah dipesan oleh pengguna lain pada tanggal tersebut.'
                    );
            }
        }

        DB::transaction(function () use ($validated) {
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);

            if ($book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            if (!empty($validated['seat_number'])) {
                $seatAlreadyBooked = Reservation::whereDate(
                    'reserved_at',
                    $validated['reserved_at']
                )
                    ->where(
                        'seat_number',
                        $validated['seat_number']
                    )
                    ->whereIn('status', [
                        'menunggu',
                        'disetujui'
                    ])
                    ->exists();

                if ($seatAlreadyBooked) {
                    abort(
                        422,
                        'Kursi ' .
                        $validated['seat_number'] .
                        ' baru saja dipesan oleh pengguna lain.'
                    );
                }
            }

            $reservationData = [
                'member_id'   => $validated['member_id'],
                'book_id'     => $validated['book_id'],
                'reserved_at' => $validated['reserved_at'],
                'expires_at'  => $validated['expires_at'] ?? null,
                'seat_number' => $validated['seat_number'] ?? null,
                'status'      => 'menunggu',
            ];

            if (Schema::hasColumn('reservations', 'due_at')) {
                $reservationData['due_at'] = $validated['expires_at'] ?? $validated['reserved_at'];
            }

            Reservation::create($reservationData);

            $book->decrement('available_stock');
        });

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservasi berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS RESERVASI
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Reservation $reservation
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu,disetujui,ditolak,dibatalkan,selesai'
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $reservation
        ) {
            $oldStatus = $reservation->status;
            $newStatus = $validated['status'];

            if (
                in_array($newStatus, ['ditolak', 'dibatalkan']) &&
                !in_array($oldStatus, ['ditolak', 'dibatalkan'])
            ) {
                $book = Book::lockForUpdate()->findOrFail($reservation->book_id);
                $book->increment('available_stock');
            }

            if (
                in_array($oldStatus, ['ditolak', 'dibatalkan']) &&
                !in_array($newStatus, ['ditolak', 'dibatalkan'])
            ) {
                $book = Book::lockForUpdate()->findOrFail($reservation->book_id);

                if ($book->available_stock < 1) {
                    abort(422, 'Stok buku tidak tersedia untuk mengaktifkan kembali reservasi.');
                }

                $book->decrement('available_stock');
            }

            $reservation->update([
                'status' => $newStatus
            ]);
        });

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Status reservasi berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS RESERVASI
    |--------------------------------------------------------------------------
    */

    public function destroy(Reservation $reservation)
    {
        DB::transaction(function () use ($reservation) {
            if (!in_array($reservation->status, ['ditolak', 'dibatalkan', 'selesai'])) {
                $book = Book::lockForUpdate()->findOrFail($reservation->book_id);
                $book->increment('available_stock');
            }

            $reservation->delete();
        });

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservasi berhasil dihapus.');
    }
}
