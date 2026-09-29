<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Illuminate\Http\Request;

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
        |--------------------------------------------------------------------------
        | TANGGAL YANG DIPILIH
        |--------------------------------------------------------------------------
        */

        $selectedDate = $request->get(
            'borrowed_at',
            now()->format('Y-m-d')
        );


        /*
        |--------------------------------------------------------------------------
        | ANGGOTA
        |--------------------------------------------------------------------------
        */

        $members = Member::orderBy('name')->get();


        /*
        |--------------------------------------------------------------------------
        | BUKU
        |--------------------------------------------------------------------------
        */

        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(judul_buku, ' ', -1) AS UNSIGNED)"
        )->get();


        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        |
        | Filter berdasarkan bulan/tahun jika ada parameter,
        | jika tidak, tampilkan 1 bulan terakhir.
        |
        */

        $oneMonthAgo = now()->subMonth()->startOfDay();

        $query = Borrowing::with([
            'member',
            'details.book'
        ])->select([
            'id',
            'member_id',
            'borrowed_at',
            'due_at',
            'returned_at',
            'status',
            'seat_number',
            'created_at',
            'updated_at',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER BERDASARKAN DUE_AT (dari Pandu — fitur baru)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('month') && $request->filled('year')) {
            $query
                ->whereYear('due_at', $request->year)
                ->whereMonth('due_at', $request->month);
        } else {
            $query->where('due_at', '>=', $oneMonthAgo);
        }

        $borrowings = $query->latest('borrowed_at')->get();


        /*
        |--------------------------------------------------------------------------
        | KURSI YANG SUDAH DIGUNAKAN
        |--------------------------------------------------------------------------
        */

        $borrowedSeats = Borrowing::whereDate('borrowed_at', $selectedDate)
            ->whereIn('status', ['dipinjam', 'diperpanjang', 'terlambat'])
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | KURSI DARI RESERVASI YANG MASIH AKTIF
        |--------------------------------------------------------------------------
        */

        $reservedSeats = Reservation::whereDate('reserved_at', $selectedDate)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | GABUNGKAN KURSI
        |--------------------------------------------------------------------------
        */

        $bookedSeats = array_values(
            array_unique(
                array_merge($borrowedSeats, $reservedSeats)
            )
        );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
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

}
