<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    /**
     * Sinkronisasi status member berdasarkan aktivitas peminjaman dan reservasi saat ini.
     * (Fitur dari Pandu)
     */
    private function syncMemberStatus(Member $member): void
    {
        $hasActiveBorrowing = Borrowing::where('member_id', $member->id)
            ->whereNull('returned_at')
            ->exists();

        $hasActiveReservation = Reservation::where('member_id', $member->id)
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->exists();

        $member->update([
            'status' => ($hasActiveBorrowing || $hasActiveReservation)
                ? 'aktif'
                : 'nonaktif',
        ]);
    }

    /**
     * DAFTAR PEMINJAMAN / SIRKULASI BUKU
     */
    public function index(Request $request)
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

        /*
        |--------------------------------------------------------------------------
        | NAVIGASI BULAN (dari Pandu — fitur baru)
        |--------------------------------------------------------------------------
        */

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $currentPeriod = Carbon::createFromDate($year, $month, 1)->locale('id');
        $monthLabel    = $currentPeriod->translatedFormat('F Y');

        $prevPeriod = $currentPeriod->copy()->subMonth();
        $prevMonth  = $prevPeriod->month;
        $prevYear   = $prevPeriod->year;

        $nextPeriod = $currentPeriod->copy()->addMonth();
        $nextMonth  = $nextPeriod->month;
        $nextYear   = $nextPeriod->year;

        $isCurrentMonth = $month === now()->month && $year === now()->year;

        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        $borrowings = Borrowing::with([
            'member',
            'details.book',
            'details.bookCopy.shelf.zone.floor',
        ])
            ->whereYear('borrowed_at', $year)
            ->whereMonth('borrowed_at', $month)
            ->latest()
            ->get();

        return view('circulation.index', compact(
            'members',
            'books',
            'reservations',
            'borrowings',
            'monthLabel',
            'month',
            'year',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'isCurrentMonth'
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


        $createdBorrowing = null;

        DB::transaction(function () use ($validated, &$createdBorrowing) {

            /*
             * Kunci buku selama proses peminjaman.
             */
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);


            /*
             * Cari SATU eksemplar fisik yang tersedia.
             */
            $bookCopy = BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();


            /*
             * Cek BookCopy DAN available_stock
             * karena sistem lama masih menggunakan available_stock.
             */
            if (!$bookCopy || $book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }


            /*
             * Buat transaksi peminjaman.
             */
            $createdBorrowing = Borrowing::create([
                'member_id'  => $validated['member_id'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at'     => $validated['due_at'],
                'status'     => 'dipinjam',
            ]);


            /*
             * Simpan BookCopy yang benar-benar dipinjam.
             */
            BorrowingDetail::create([
                'borrowing_id' => $createdBorrowing->id,
                'book_id'      => $book->id,
                'book_copy_id' => $bookCopy->id,
                'quantity'     => 1,
            ]);


            /*
             * Ubah status fisik buku.
             */
            $bookCopy->update(['status' => 'borrowed']);


            /*
             * Pertahankan sistem stok lama.
             */
            $book->decrement('stok');


            /*
             * Sinkronisasi status member.
             */
            $this->syncMemberStatus(
                Member::findOrFail($validated['member_id'])
            );
        });

        if ($createdBorrowing) {
            NotificationService::borrowingApproved($createdBorrowing);
        }

        return redirect()
            ->route('circulation')
            ->with('success', 'Peminjaman berhasil diproses.');
    }


    /**
     * PENGEMBALIAN BUKU
     */
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        DB::transaction(function () use ($borrowing) {

            /*
             * Ambil detail peminjaman.
             */
            $borrowing->load('details');

            foreach ($borrowing->details as $detail) {

                /*
                 * Kalau book_copy_id tersedia,
                 * kembalikan copy fisik tersebut.
                 */
                if ($detail->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($detail->book_copy_id);

                    if ($bookCopy) {
                        $bookCopy->update(['status' => 'available']);
                    }
                } else {

                    /*
                     * Data lama belum memiliki book_copy_id.
                     * Tetap gunakan sistem stok lama.
                     */
                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);

                    $book->increment('stok', $detail->quantity);
                }

                /*
                 * Untuk peminjaman baru,
                 * stok lama juga harus dikembalikan.
                 */
                if ($detail->book_copy_id) {
                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);

                    $book->increment('stok', $detail->quantity);
                }
            }

            /*
             * Update status peminjaman.
             */
            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'status'      => 'dikembalikan',
            ]);

            /*
             * Sinkronisasi status member.
             */
            $this->syncMemberStatus(
                Member::findOrFail($borrowing->member_id)
            );
        });

        return redirect()
            ->route('circulation')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    public function extend(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Peminjaman sudah selesai.');
        }

        $request->validate([
            'due_at' => [
                'required',
                'date',
                'after:today'
            ]
        ]);

        $borrowing->update(['due_at' => $request->due_at]);

        return redirect()
            ->route('circulation')
            ->with('success', 'Tanggal pengembalian berhasil diperpanjang.');
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
            'due_at'                 => $newDue,
            'extension_status'       => 'disetujui',
            'extension_admin_notes'  => 'Disetujui oleh Admin',
        ]);

        NotificationService::extensionApproved($borrowing);

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
            'extension_status'      => 'ditolak',
            'extension_admin_notes' => $notes,
        ]);

        NotificationService::extensionRejected($borrowing, null, $notes);

        return back()->with('success', 'Perpanjangan peminjaman telah ditolak.');
    }
}
