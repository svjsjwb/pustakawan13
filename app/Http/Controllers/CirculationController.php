<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR PEMINJAMAN / SIRKULASI BUKU
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
         * =====================================================
         * DATA ANGGOTA
         * =====================================================
         */

        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();


        /*
         * =====================================================
         * DATA BUKU
         * =====================================================
         */

        $books = Book::orderBy('judul_buku')->get();


        /*
         * =====================================================
         * DATA RESERVASI
         * =====================================================
         */

        $reservations = Reservation::with([
            'member',
            'book',
            'bookCopy',
        ])
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->latest()
            ->get();


        /*
         * =====================================================
         * NAVIGASI BULAN
         * =====================================================
         */

        $month = (int) $request->get(
            'month',
            now()->month
        );

        $year = (int) $request->get(
            'year',
            now()->year
        );


        /*
         * =====================================================
         * VALIDASI BULAN
         * =====================================================
         */

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }


        /*
         * =====================================================
         * PERIODE AKTIF
         * =====================================================
         */

        $currentPeriod = Carbon::createFromDate(
            $year,
            $month,
            1
        )->locale('id');

        $monthLabel =
            $currentPeriod->translatedFormat('F Y');


        /*
         * =====================================================
         * BULAN SEBELUMNYA
         * =====================================================
         */

        $prevPeriod =
            $currentPeriod->copy()->subMonth();

        $prevMonth =
            $prevPeriod->month;

        $prevYear =
            $prevPeriod->year;


        /*
         * =====================================================
         * BULAN BERIKUTNYA
         * =====================================================
         */

        $nextPeriod =
            $currentPeriod->copy()->addMonth();

        $nextMonth =
            $nextPeriod->month;

        $nextYear =
            $nextPeriod->year;


        /*
         * =====================================================
         * CEK BULAN SAAT INI
         * =====================================================
         */

        $isCurrentMonth =
            (
                $month === now()->month
                &&
                $year === now()->year
            );


        /*
         * =====================================================
         * DATA PEMINJAMAN
         * =====================================================
         */

        $borrowings = Borrowing::with([
            'member',
            'details.book',
            'details.bookCopy.shelf.zone.floor',
        ])
            ->whereYear(
                'borrowed_at',
                $year
            )
            ->whereMonth(
                'borrowed_at',
                $month
            )
            ->latest('borrowed_at')
            ->get();


        /*
         * =====================================================
         * KIRIM KE VIEW
         * =====================================================
         */

        return view(
            'circulation.index',
            compact(
                'members',
                'books',
                'reservations',
                'borrowings',
                'month',
                'year',
                'monthLabel',
                'prevMonth',
                'prevYear',
                'nextMonth',
                'nextYear',
                'isCurrentMonth'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN TRANSAKSI PEMINJAMAN
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
                'exists:members,id',
            ],

            'book_id' => [
                'required',
                'exists:books,id',
            ],

            'borrowed_at' => [
                'required',
                'date',
            ],

            'due_at' => [
                'required',
                'date',
                'after_or_equal:borrowed_at',
            ],
        ]);


        /*
         * =====================================================
         * TRANSAKSI DATABASE
         * =====================================================
         */

        DB::transaction(function () use ($validated) {

            /*
             * KUNCI DATA BUKU
             */

            $book = Book::lockForUpdate()
                ->findOrFail(
                    $validated['book_id']
                );


            /*
             * =================================================
             * CARI BOOK COPY
             * =================================================
             */

            $bookCopy = BookCopy::where(
                'book_id',
                $book->id
            )
                ->where(
                    'status',
                    'available'
                )
                ->lockForUpdate()
                ->first();


            /*
             * =================================================
             * CEK STOK
             * =================================================
             */

            if (
                !$bookCopy
                ||
                $book->available_stock < 1
            ) {
                abort(
                    422,
                    'Buku sedang tidak tersedia.'
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

                'status' =>
                    'dipinjam',
            ]);


            /*
             * =================================================
             * DETAIL PEMINJAMAN
             * =================================================
             */

            BorrowingDetail::create([
                'borrowing_id' =>
                    $borrowing->id,

                'book_id' =>
                    $book->id,

                'book_copy_id' =>
                    $bookCopy->id,

                'quantity' =>
                    1,
            ]);


            /*
             * =================================================
             * UPDATE BOOK COPY
             * =================================================
             */

            $bookCopy->update([
                'status' =>
                    'borrowed',
            ]);


            /*
             * =================================================
             * KURANGI STOK
             * =================================================
             */

            $book->decrement('stok');
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        $borrowDate =
            Carbon::parse(
                $validated['borrowed_at']
            );


        return redirect()
            ->route(
                'circulation',
                [
                    'month' =>
                        $borrowDate->month,

                    'year' =>
                        $borrowDate->year,
                ]
            )
            ->with(
                'success',
                'Peminjaman berhasil diproses.'
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

        /*
         * =====================================================
         * CEK STATUS
         * =====================================================
         */

        if (
            $borrowing->status ===
            'dikembalikan'
        ) {

            return back()
                ->with(
                    'error',
                    'Peminjaman ini sudah dikembalikan.'
                );
        }


        /*
         * =====================================================
         * TRANSAKSI PENGEMBALIAN
         * =====================================================
         */

        DB::transaction(function () use (
            $borrowing
        ) {

            /*
             * LOAD DETAIL
             */

            $borrowing->load(
                'details'
            );


            foreach (
                $borrowing->details
                as $detail
            ) {

                /*
                 * =================================================
                 * JIKA PUNYA BOOK COPY
                 * =================================================
                 */

                if (
                    $detail->book_copy_id
                ) {

                    $bookCopy =
                        BookCopy::lockForUpdate()
                            ->find(
                                $detail->book_copy_id
                            );


                    /*
                     * KEMBALIKAN BOOK COPY
                     */

                    if ($bookCopy) {

                        $bookCopy->update([
                            'status' =>
                                'available',
                        ]);
                    }


                    /*
                     * TAMBAH STOK
                     */

                    $book =
                        Book::lockForUpdate()
                            ->findOrFail(
                                $detail->book_id
                            );


                    $book->increment(
                        'stok',
                        $detail->quantity
                    );

                } else {

                    /*
                     * =================================================
                     * DATA LAMA TANPA BOOK COPY
                     * =================================================
                     */

                    $book =
                        Book::lockForUpdate()
                            ->findOrFail(
                                $detail->book_id
                            );


                    $book->increment(
                        'stok',
                        $detail->quantity
                    );
                }
            }


            /*
             * =================================================
             * UPDATE STATUS
             * =================================================
             */

            $borrowing->update([
                'returned_at' =>
                    now()->toDateString(),

                'status' =>
                    'dikembalikan',
            ]);
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return back()
            ->with(
                'success',
                'Buku berhasil dikembalikan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PERPANJANG PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function extendLoan(
        Request $request,
        Borrowing $borrowing
    ) {

        /*
         * =====================================================
         * VALIDASI
         * =====================================================
         *
         * User dapat memilih 1 sampai 30 hari.
         */

        $validated = $request->validate([
            'extension_days' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],
        ], [

            'extension_days.required' =>
                'Jumlah hari perpanjangan wajib diisi.',

            'extension_days.integer' =>
                'Jumlah hari harus berupa angka.',

            'extension_days.min' =>
                'Minimal perpanjangan adalah 1 hari.',

            'extension_days.max' =>
                'Maksimal perpanjangan adalah 30 hari.',
        ]);


        /*
         * =====================================================
         * CEK STATUS
         * =====================================================
         *
         * Yang boleh diperpanjang:
         *
         * 1. dipinjam
         * 2. diperpanjang
         *
         * Dengan begitu buku yang sudah pernah
         * diperpanjang masih dapat diperpanjang lagi.
         */

        if (
            !in_array(
                $borrowing->status,
                [
                    'dipinjam',
                    'diperpanjang',
                ],
                true
            )
        ) {

            return back()
                ->with(
                    'error',
                    'Peminjaman yang sudah selesai tidak dapat diperpanjang.'
                );
        }


        /*
         * =====================================================
         * HITUNG TAMBAHAN HARI
         * =====================================================
         */

        $extensionDays =
            (int) $validated['extension_days'];


        /*
         * =====================================================
         * TANGGAL JATUH TEMPO BARU
         * =====================================================
         */

        $newDueDate =
            Carbon::parse(
                $borrowing->due_at
            )->addDays(
                $extensionDays
            );


        /*
         * =====================================================
         * UPDATE PEMINJAMAN
         * =====================================================
         */

        $borrowing->update([
            'due_at' =>
                $newDueDate->toDateString(),

            'status' =>
                'diperpanjang',
        ]);


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route(
                'circulation',
                [
                    'month' =>
                        Carbon::parse(
                            $borrowing->borrowed_at
                        )->month,

                    'year' =>
                        Carbon::parse(
                            $borrowing->borrowed_at
                        )->year,
                ]
            )
            ->with(
                'success',
                'Masa peminjaman berhasil diperpanjang ' .
                $extensionDays .
                ' hari.'
            );
    }
}