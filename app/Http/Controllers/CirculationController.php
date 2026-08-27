<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CirculationController extends Controller
{
    public function index()
    {
        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        /*
         * Semua buku tetap ditampilkan,
         * termasuk yang stok tersedia = 0.
         */
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

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


        $borrowings = Borrowing::with([
            'member',
            'details.book',
            'details.bookCopy.shelf.zone.floor',
        ])
            ->latest()
            ->get();

        return view('circulation.index', compact(
            'members',
            'books',
            'reservations',
            'borrowings'
        ));
    }


    /**
     * PEMINJAMAN BUKU
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

            'borrowed_at' => [
                'required',
                'date'
            ],

            'due_at' => [
                'required',
                'date',
                'after_or_equal:borrowed_at'
            ],
        ]);


        DB::transaction(function () use ($validated) {

            /*
             * Kunci buku selama proses peminjaman.
             */
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);


            /*
             * Cari SATU eksemplar fisik yang tersedia.
             *
             * Copy dengan status:
             * - reserved
             * - borrowed
             * - lost
             * - damaged
             * - maintenance
             *
             * tidak boleh dipilih.
             */
            $bookCopy = BookCopy::where(
                'book_id',
                $book->id
            )
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();


            /*
             * Pengaman backend.
             *
             * Kita cek BookCopy DAN available_stock
             * karena sistem lama masih menggunakan
             * available_stock.
             */
            if (!$bookCopy || $book->available_stock < 1) {
                abort(
                    422,
                    'Buku sedang tidak tersedia.'
                );
            }


            /*
             * Buat transaksi peminjaman.
             */
            $borrowing = Borrowing::create([
                'member_id' => $validated['member_id'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'],
                'status' => 'dipinjam',
            ]);


            /*
             * Simpan BookCopy yang benar-benar dipinjam.
             */
            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'book_id' => $book->id,
                'book_copy_id' => $bookCopy->id,
                'quantity' => 1,
            ]);


            /*
             * Ubah status fisik buku.
             */
            $bookCopy->update([
                'status' => 'borrowed',
            ]);


            /*
             * Pertahankan sistem stok lama.
             */
            $book->decrement('available_stock');
        });


        return redirect()
            ->route('circulation')
            ->with(
                'success',
                'Peminjaman berhasil diproses.'
            );
    }


    /**
     * PENGEMBALIAN BUKU
     */
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with(
                'error',
                'Peminjaman ini sudah dikembalikan.'
            );
        }


        DB::transaction(function () use ($borrowing) {

            /*
             * Ambil detail peminjaman.
             */
            $borrowing->load('details');


            foreach ($borrowing->details as $detail) {

                /*
                 * =================================================
                 * PEMINJAMAN BARU
                 * =================================================
                 *
                 * Kalau book_copy_id tersedia,
                 * kembalikan copy fisik tersebut.
                 */
                if ($detail->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($detail->book_copy_id);


                    if ($bookCopy) {

                        $bookCopy->update([
                            'status' => 'available',
                        ]);
                    }
                } else {

                    /*
                     * =================================================
                     * DATA PEMINJAMAN LAMA
                     * =================================================
                     *
                     * Data lama belum memiliki book_copy_id.
                     *
                     * Jadi tetap gunakan sistem stok lama
                     * supaya data historis tidak rusak.
                     */
                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);


                    $book->increment(
                        'available_stock',
                        $detail->quantity
                    );
                }


                /*
                 * Untuk peminjaman baru,
                 * stok lama juga harus dikembalikan.
                 */
                if ($detail->book_copy_id) {

                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);


                    $book->increment(
                        'available_stock',
                        $detail->quantity
                    );
                }
            }


            /*
             * Update status peminjaman.
             */
            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'status' => 'dikembalikan',
            ]);
        });


        return redirect()
            ->route('circulation')
            ->with(
                'success',
                'Buku berhasil dikembalikan.'
            );
    }
}
