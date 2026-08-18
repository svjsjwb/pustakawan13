<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
         * Tanggal yang dipilih untuk melihat
         * kursi yang sudah digunakan.
         */
        $selectedDate = $request->get(
            'borrowed_at',
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
         * DATA PEMINJAMAN
         * =====================================================
         */

       $borrowings = Borrowing::with([
    'member',
    'details.book'
])
    ->select([
        'id',
        'member_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'seat_number',
        'created_at',
        'updated_at',
    ])
    ->latest()
    ->get();


        /*
         * =====================================================
         * KURSI YANG SUDAH DIGUNAKAN
         * =====================================================
         *
         * Kursi dianggap terpakai jika:
         *
         * 1. Ada peminjaman aktif
         * 2. Ada reservasi yang disetujui
         *
         */

        $borrowedSeats = Borrowing::whereDate(
            'borrowed_at',
            $selectedDate
        )
            ->where(
                'status',
                'dipinjam'
            )
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();


        /*
         * Kursi dari reservasi yang masih aktif
         */

        $reservedSeats = Reservation::whereDate(
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
         * Gabungkan kursi peminjaman
         * dan kursi reservasi.
         */

        $bookedSeats = array_values(
            array_unique(
                array_merge(
                    $borrowedSeats,
                    $reservedSeats
                )
            )
        );


        /*
         * =====================================================
         * KIRIM KE VIEW
         * =====================================================
         */

        return view(
            'borrowings.index',
            compact(
                'members',
                'books',
                'borrowings',
                'bookedSeats',
                'selectedDate'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
         * =====================================================
         * VALIDASI
         * =====================================================
         */

        dd($request->all());

         $validated = $request->validate([
        'member_id' => [
            'required',
            'exists:members,id'
        ],

        'book_id' => [
            'required',
            'exists:books,id'
        ],

        'borrowed_at' => [
            'required',
            'date'
        ],

        'due_at' => [
            'required',
            'date',
            'after_or_equal:borrowed_at'
        ],

        'seat_number' => [
            'required',
            'string',
            'regex:/^[ABC][1-8]$/'
        ],
    ]);


        /*
         * =====================================================
         * CEK KURSI
         * =====================================================
         */

        $seatAlreadyUsed = Borrowing::whereDate(
            'borrowed_at',
            $validated['borrowed_at']
        )
            ->where(
                'seat_number',
                $validated['seat_number']
            )
            ->where(
                'status',
                'dipinjam'
            )
            ->exists();


        /*
         * Cek juga kursi dari reservasi
         */

        $seatReserved = Reservation::whereDate(
            'reserved_at',
            $validated['borrowed_at']
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


        if ($seatAlreadyUsed || $seatReserved) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kursi ' .
                    $validated['seat_number'] .
                    ' sudah digunakan atau dipesan pada tanggal tersebut.'
                );
        }


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(function () use ($validated) {

            /*
             * =================================================
             * KUNCI BUKU
             * =================================================
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
             * Dilakukan di dalam transaction
             * untuk mengurangi kemungkinan
             * dua pengguna memilih kursi yang sama.
             */

            $seatAlreadyUsed = Borrowing::whereDate(
                'borrowed_at',
                $validated['borrowed_at']
            )
                ->where(
                    'seat_number',
                    $validated['seat_number']
                )
                ->where(
                    'status',
                    'dipinjam'
                )
                ->exists();


            $seatReserved = Reservation::whereDate(
                'reserved_at',
                $validated['borrowed_at']
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


            if (
                $seatAlreadyUsed ||
                $seatReserved
            ) {

                abort(
                    422,
                    'Kursi ' .
                    $validated['seat_number'] .
                    ' baru saja digunakan atau dipesan.'
                );
            }


            /*
             * =================================================
             * BUAT PEMINJAMAN
             * =================================================
             */

            $borrowing = Borrowing::create([

                'member_id' =>
                    $validated['member_id'],

                'borrowed_at' =>
                    $validated['borrowed_at'],

                'due_at' =>
                    $validated['due_at'],

                'seat_number' =>
                    $validated['seat_number'],

                'status' =>
                    'dipinjam',

            ]);


            /*
             * =================================================
             * DETAIL BUKU
             * =================================================
             *
             * Sesuaikan dengan struktur tabel borrowing_details
             * milik project kamu.
             */

            $borrowing->details()->create([

                'book_id' =>
                    $validated['book_id'],

                'quantity' => 1,

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
            ->route('borrowings.index')
            ->with(
                'success',
                'Peminjaman buku berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PENGEMBALIAN BUKU
    |--------------------------------------------------------------------------
    */

    public function returnBook(
        Borrowing $borrowing
    ) {

        DB::transaction(function () use (
            $borrowing
        ) {

            /*
             * Jangan proses ulang
             * peminjaman yang sudah dikembalikan.
             */

            if ($borrowing->status === 'dikembalikan') {

                abort(
                    422,
                    'Buku sudah dikembalikan.'
                );
            }


            /*
             * Ambil semua detail buku
             */

            $details = $borrowing->details;


            foreach ($details as $detail) {

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $detail->book_id
                    );


                /*
                 * Kembalikan stok
                 */

                $book->increment(
                    'available_stock',
                    $detail->quantity ?? 1
                );
            }


            /*
             * Update status
             */

            $borrowing->update([
                'status' => 'dikembalikan'
            ]);
        });


        /*
         * Redirect
         */

        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                'Buku berhasil dikembalikan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Borrowing $borrowing
    ) {

        DB::transaction(function () use (
            $borrowing
        ) {

            /*
             * Jika masih dipinjam,
             * kembalikan stok terlebih dahulu.
             */

            if ($borrowing->status === 'dipinjam') {

                foreach (
                    $borrowing->details
                    as $detail
                ) {

                    $book = Book::lockForUpdate()
                        ->findOrFail(
                            $detail->book_id
                        );


                    $book->increment(
                        'available_stock',
                        $detail->quantity ?? 1
                    );
                }
            }


            /*
             * Hapus detail
             */

            $borrowing->details()->delete();


            /*
             * Hapus peminjaman
             */

            $borrowing->delete();
        });


        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                'Data peminjaman berhasil dihapus.'
            );
    }
}