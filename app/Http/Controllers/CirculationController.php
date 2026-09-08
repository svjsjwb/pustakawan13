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

    public function extend(
        Request $request,
        Borrowing $borrowing
    ) {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with(
                'error',
                'Peminjaman sudah selesai.'
            );
        }

        $request->validate([
            'due_at' => [
                'required',
                'date',
                'after:today'
            ]
        ]);

        $borrowing->update([
            'due_at' => $request->due_at
        ]);

        return redirect()
            ->route('circulation')
            ->with(
                'success',
                'Tanggal pengembalian berhasil diperpanjang.'
            );
    }

    /**
     * Setujui Permintaan Perpanjangan
     */
    public function approveExtension(Borrowing $borrowing)
    {
        if ($borrowing->extension_status !== 'menunggu') {
            return back()->with('error', 'Tidak ada permintaan perpanjangan yang menunggu persetujuan.');
        }

        $newDue = $borrowing->extension_requested_due_at;
        if (!$newDue) {
            $newDue = $borrowing->due_at->addDays(7);
        }

        $borrowing->update([
            'due_at' => $newDue,
            'extension_status' => 'disetujui',
            'extension_admin_notes' => 'Disetujui oleh Admin',
        ]);

        $borrowing->load('details.book', 'member');
        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        $user = \App\Models\User::where('email', $borrowing->member?->email)->first();
        if ($user) {
            \App\Models\AppNotification::notifyUser(
                $user->id,
                'extension_approved',
                'Perpanjangan Peminjaman Disetujui',
                "Perpanjangan untuk buku \"{$bookTitle}\" disetujui. Batas pengembalian baru: " . $borrowing->due_at->format('d M Y'),
                ['borrowing_id' => $borrowing->id]
            );
        }

        return back()->with('success', 'Perpanjangan peminjaman berhasil disetujui.');
    }

    /**
     * Tolak Permintaan Perpanjangan
     */
    public function rejectExtension(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->extension_status !== 'menunggu') {
            return back()->with('error', 'Tidak ada permintaan perpanjangan yang menunggu.');
        }

        $notes = $request->input('admin_notes', 'Ditolak oleh Admin');

        $borrowing->update([
            'extension_status' => 'ditolak',
            'extension_admin_notes' => $notes,
        ]);

        $borrowing->load('details.book', 'member');
        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        $user = \App\Models\User::where('email', $borrowing->member?->email)->first();
        if ($user) {
            \App\Models\AppNotification::notifyUser(
                $user->id,
                'extension_rejected',
                'Perpanjangan Peminjaman Ditolak',
                "Permintaan perpanjangan buku \"{$bookTitle}\" ditolak. Catatan: {$notes}",
                ['borrowing_id' => $borrowing->id]
            );
        }

        return back()->with('success', 'Perpanjangan peminjaman telah ditolak.');
    }
}

