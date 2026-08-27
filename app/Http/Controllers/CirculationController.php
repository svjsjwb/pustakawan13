<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // Semua buku tetap ditampilkan, termasuk yang stok tersedia = 0
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        /*
         * =====================================================
         * 1. NAVIGASI BULAN OTOMATIS (DEFAULT: BULAN INI)
         * =====================================================
         * Saat halaman pertama kali dibuka, otomatis mengambil bulan & tahun
         * dari tanggal sistem saat ini (now()->month & now()->year).
         */

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        // Pengaman input bulan dan tahun
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        // Buat objek Carbon untuk periode aktif (Bahasa Indonesia)
        $currentPeriod = Carbon::createFromDate($year, $month, 1)->locale('id');
        $monthLabel    = $currentPeriod->translatedFormat('F Y'); // Contoh: "Agustus 2026"

        // Hitung parameter bulan sebelumnya (Previous Month <)
        $prevPeriod = $currentPeriod->copy()->subMonth();
        $prevMonth  = $prevPeriod->month;
        $prevYear   = $prevPeriod->year;

        // Hitung parameter bulan berikutnya (Next Month >)
        $nextPeriod = $currentPeriod->copy()->addMonth();
        $nextMonth  = $nextPeriod->month;
        $nextYear   = $nextPeriod->year;

        $isCurrentMonth = ($month === now()->month && $year === now()->year);

        /*
         * =====================================================
         * 2. QUERY & PENGARSIPAN BULANAN (whereMonth & whereYear)
         * =====================================================
         * - Menampilkan data peminjaman khusus untuk bulan & tahun yang dipilih.
         * - Data peminjaman dari bulan-bulan lain TIDAK dihapus dari database,
         *   melainkan terarsip rapi dan dapat dibuka kembali kapan saja.
         */

        $borrowings = Borrowing::with([
            'member',
            'details.book'
        ])
            ->whereYear('borrowed_at', $year)
            ->whereMonth('borrowed_at', $month)
            ->latest('borrowed_at')
            ->get();

        return view('circulation.index', compact(
            'members',
            'books',
            'borrowings',
            'month',
            'year',
            'monthLabel',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'isCurrentMonth'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN TRANSAKSI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'   => ['required', 'exists:members,id'],
            'book_id'     => ['required', 'exists:books,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at'      => ['required', 'date', 'after_or_equal:borrowed_at'],
        ]);

        DB::transaction(function () use ($validated) {
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);

            if ($book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            $borrowing = Borrowing::create([
                'member_id'   => $validated['member_id'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at'      => $validated['due_at'],
                'status'      => 'dipinjam',
            ]);

            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'book_id'      => $book->id,
                'quantity'     => 1,
            ]);

            $book->decrement('available_stock');
        });

        // Redirect ke sirkulasi bulan peminjaman yang bersangkutan
        $borrowDate = Carbon::parse($validated['borrowed_at']);

        return redirect()
            ->route('circulation', [
                'month' => $borrowDate->month,
                'year'  => $borrowDate->year
            ])
            ->with('success', 'Peminjaman berhasil diproses.');
    }

    /*
    |--------------------------------------------------------------------------
    | PENGEMBALIAN BUKU
    |--------------------------------------------------------------------------
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
            $borrowing->load('details');

            foreach ($borrowing->details as $detail) {
                $book = Book::lockForUpdate()
                    ->findOrFail($detail->book_id);

                $book->increment(
                    'available_stock',
                    $detail->quantity
                );
            }

            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'status'      => 'dikembalikan',
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Buku berhasil dikembalikan.');
    }
}
