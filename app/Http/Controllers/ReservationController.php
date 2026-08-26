<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR RESERVASI
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
         * ANGGOTA AKTIF
         * =====================================================
         */

        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        /*
         * =====================================================
         * BUKU
         * =====================================================
         */

        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        /*
         * =====================================================
         * DAFTAR RESERVASI
         * =====================================================
         *
         * Ambil member dan buku sekaligus.
         */

        $reservations = Reservation::with([
            'member',
            'book'
        ])
            ->latest()
            ->get();

        /*
         * =====================================================
         * KURSI YANG SUDAH BOOKING
         * =====================================================
         *
         * Status menunggu dan disetujui dianggap sudah booking.
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
        /*
         * =====================================================
         * VALIDASI
         * =====================================================
         */

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


        /*
         * =====================================================
         * CEK KURSI
         * =====================================================
         *
         * Hanya cek kursi jika tempat duduk dipilih
         */

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
                        ' sudah dipesan pada tanggal tersebut.'
                    );
            }

        }


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(function () use ($validated) {

            /*
             * Kunci data buku.
             */

            $book = Book::lockForUpdate()
                ->findOrFail(
                    $validated['book_id']
                );


            /*
             * =================================================
             * CEK STOK
             * =================================================
             */

            if ($book->available_stock < 1) {

                abort(
                    422,
                    'Buku sedang tidak tersedia.'
                );
            }


            /*
             * =================================================
             * CEK KURSI LAGI
             * =================================================
             *
             * Hanya cek kursi jika tempat duduk dipilih
             */

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


            /*
             * =================================================
             * BUAT RESERVASI
             * =================================================
             */

            Reservation::create([

                'member_id' => $validated['member_id'],

                'book_id' => $validated['book_id'],

                'reserved_at' => $validated['reserved_at'],

                'expires_at' =>
                    $validated['expires_at'] ?? null,

                'seat_number' =>
                    $validated['seat_number'] ?? null,

                'status' => 'menunggu',

            ]);


            /*
             * =================================================
             * KURANGI STOK
             * =================================================
             */

            $book->decrement(
                'available_stock'
            );
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Reservasi berhasil dibuat.'
            );
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

        /*
         * =====================================================
         * VALIDASI STATUS
         * =====================================================
         */

        $validated = $request->validate([

            'status' => [
                'required',
                'in:menunggu,disetujui,ditolak,dibatalkan,selesai'
            ],

        ]);


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(function () use (
            $validated,
            $reservation
        ) {

            $oldStatus = $reservation->status;

            $newStatus = $validated['status'];


            /*
             * =================================================
             * KEMBALIKAN STOK
             * =================================================
             *
             * Jika status aktif berubah menjadi:
             *
             * ditolak
             * dibatalkan
             */

            if (
                in_array(
                    $newStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
                &&
                !in_array(
                    $oldStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $reservation->book_id
                    );

                $book->increment(
                    'available_stock'
                );
            }


            /*
             * =================================================
             * AMBIL STOK KEMBALI
             * =================================================
             *
             * Jika reservasi sebelumnya ditolak/dibatalkan
             * kemudian diaktifkan lagi.
             */

            if (
                in_array(
                    $oldStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
                &&
                !in_array(
                    $newStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $reservation->book_id
                    );


                if ($book->available_stock < 1) {

                    abort(
                        422,
                        'Stok buku tidak tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }


                $book->decrement(
                    'available_stock'
                );
            }


            /*
             * =================================================
             * UPDATE STATUS
             * =================================================
             */

            $reservation->update([
                'status' => $newStatus
            ]);
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Status reservasi berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS RESERVASI
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Reservation $reservation
    ) {

        DB::transaction(function () use (
            $reservation
        ) {

            /*
             * =================================================
             * KEMBALIKAN STOK
             * =================================================
             */

            if (
                !in_array(
                    $reservation->status,
                    [
                        'ditolak',
                        'dibatalkan',
                        'selesai'
                    ]
                )
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $reservation->book_id
                    );

                $book->increment(
                    'available_stock'
                );
            }


            /*
             * =================================================
             * HAPUS
             * =================================================
             */

            $reservation->delete();
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Reservasi berhasil dihapus.'
            );
    }
}