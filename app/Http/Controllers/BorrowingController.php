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
        |--------------------------------------------------------------------------
        | TANGGAL YANG DIPILIH
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk melihat kursi yang sudah digunakan.
        |
        */

        $selectedDate = $request->get(
            'borrowed_at',
            now()->format('Y-m-d')
        );


        /*
        |--------------------------------------------------------------------------
        | ANGGOTA AKTIF
        |--------------------------------------------------------------------------
        */

        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BUKU
        |--------------------------------------------------------------------------
        */

        $books = Book::orderBy('judul_buku')->get();


        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        |
        | PENTING:
        |
        | Data transaksi yang ditampilkan dibatasi berdasarkan
        | TANGGAL PENGEMBALIAN (due_at).
        |
        | Transaksi yang due_at-nya lebih dari 1 bulan yang lalu
        | tidak ditampilkan pada tabel utama.
        |
        | DATA TIDAK DIHAPUS DARI DATABASE.
        |
        | Contoh:
        |
        | Jika sekarang 26 Agustus 2026:
        |
        | due_at >= 26 Juli 2026
        |     -> ditampilkan
        |
        | due_at < 26 Juli 2026
        |     -> tidak ditampilkan
        |
        | borrowed_at TIDAK digunakan sebagai batas filter.
        |
        */

        $oneMonthAgo = now()
            ->subMonth()
            ->startOfDay();

        $query = Borrowing::with([
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
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER 1 BULAN BERDASARKAN DUE_AT
        |--------------------------------------------------------------------------
        |
        | Transaksi lintas bulan (borrowed_at di akhir bulan dan due_at di bulan berikutnya)
        | tetap tampil karena filter mengacu pada due_at, bukan borrowed_at.
        |
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
        |
        | Kursi dianggap terpakai jika:
        |
        | 1. Ada peminjaman aktif
        | 2. Ada reservasi yang disetujui
        |
        | BAGIAN INI TETAP MENGGUNAKAN borrowed_at
        | karena berhubungan dengan tanggal penggunaan kursi.
        |
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
        |--------------------------------------------------------------------------
        | KURSI DARI RESERVASI YANG MASIH AKTIF
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | GABUNGKAN KURSI PEMINJAMAN DAN RESERVASI
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | KIRIM KE VIEW
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


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | CEK KURSI
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | CEK KURSI DARI RESERVASI
        |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | JIKA KURSI SUDAH DIGUNAKAN
        |--------------------------------------------------------------------------
        */

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
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | KUNCI BUKU
            |--------------------------------------------------------------------------
            */

            $book = Book::lockForUpdate()
                ->findOrFail(
                    $validated['book_id']
                );


            /*
            |--------------------------------------------------------------------------
            | CEK STOK
            |--------------------------------------------------------------------------
            */

            if ($book->available_stock < 1) {

                abort(
                    422,
                    'Buku sedang tidak tersedia.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CEK KURSI LAGI
            |--------------------------------------------------------------------------
            |
            | Dilakukan di dalam transaction
            | untuk mengurangi kemungkinan
            | dua pengguna memilih kursi yang sama.
            |
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
            |--------------------------------------------------------------------------
            | BUAT PEMINJAMAN
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | DETAIL BUKU
            |--------------------------------------------------------------------------
            |
            | Sesuaikan dengan struktur tabel borrowing_details
            | milik project.
            |
            */

            $borrowing->details()->create([

                'book_id' =>
                    $validated['book_id'],

                'quantity' => 1,

            ]);


            /*
            |--------------------------------------------------------------------------
            | KURANGI STOK
            |--------------------------------------------------------------------------
            */

            $book->decrement(
                'available_stock'
            );
        });


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | JANGAN PROSES ULANG
            |--------------------------------------------------------------------------
            |
            | Peminjaman yang sudah dikembalikan
            | tidak boleh diproses lagi.
            |
            */

            if ($borrowing->status === 'dikembalikan') {

                abort(
                    422,
                    'Buku sudah dikembalikan.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | AMBIL SEMUA DETAIL BUKU
            |--------------------------------------------------------------------------
            */

            $details = $borrowing->details;


            foreach ($details as $detail) {

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $detail->book_id
                    );


                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK
                |--------------------------------------------------------------------------
                */

                $book->increment(
                    'available_stock',
                    $detail->quantity ?? 1
                );
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            $borrowing->update([
                'status' => 'dikembalikan'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | JIKA MASIH DIPINJAM
            |--------------------------------------------------------------------------
            |
            | Kembalikan stok terlebih dahulu.
            |
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
            |--------------------------------------------------------------------------
            | HAPUS DETAIL
            |--------------------------------------------------------------------------
            */

            $borrowing->details()->delete();


            /*
            |--------------------------------------------------------------------------
            | HAPUS PEMINJAMAN
            |--------------------------------------------------------------------------
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