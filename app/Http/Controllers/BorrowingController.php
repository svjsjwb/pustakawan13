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
    private function syncMemberStatus(Member $member): void
    {
        $hasActiveBorrowing = Borrowing::where(
            'member_id',
            $member->id
        )
            ->whereNull('returned_at')
            ->exists();

        $hasActiveReservation = Reservation::where(
            'member_id',
            $member->id
        )
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->exists();

        $member->update([
            'status' => (
                $hasActiveBorrowing ||
                $hasActiveReservation
            )
                ? 'aktif'
                : 'nonaktif',
        ]);
    }

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
        |
        | Semua member tetap bisa dipilih untuk meminjam.
        | Status TidakAktif bukan berarti member dilarang meminjam.
        |
        */

        $members = Member::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | BUKU
        |--------------------------------------------------------------------------
        */

        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        |
        | Data transaksi yang ditampilkan dibatasi berdasarkan due_at.
        | Transaksi yang due_at-nya lebih dari 1 bulan yang lalu
        | tidak ditampilkan pada tabel utama.
        |
        | Data tetap tersimpan di database.
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
        | FILTER BERDASARKAN DUE_AT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('month') && $request->filled('year')) {

            $query
                ->whereYear('due_at', $request->year)
                ->whereMonth('due_at', $request->month);
        } else {

            $query->where('due_at', '>=', $oneMonthAgo);
        }

        $borrowings = $query
            ->latest('borrowed_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | KURSI YANG SUDAH DIGUNAKAN
        |--------------------------------------------------------------------------
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
        | GABUNGKAN KURSI
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
            | CEK KURSI LAGI DI DALAM TRANSACTION
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
            | MEMBER MENJADI AKTIF
            |--------------------------------------------------------------------------
            |
            | Begitu member memiliki peminjaman baru,
            | status member menjadi Aktif.
            |
            */

            $this->syncMemberStatus(
                Member::findOrFail(
                    $validated['member_id']
                )
            );

            /*
            |--------------------------------------------------------------------------
            | DETAIL BUKU
            |--------------------------------------------------------------------------
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
            | UPDATE PEMINJAMAN
            |--------------------------------------------------------------------------
            |
            | returned_at juga diisi supaya sistem dapat mengetahui
            | bahwa peminjaman sudah benar-benar selesai.
            |
            */

            $borrowing->update([
                'status' => 'dikembalikan',
                'returned_at' => now()->toDateString(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | CEK PEMINJAMAN AKTIF MEMBER
            |--------------------------------------------------------------------------
            |
            | Member tetap Aktif jika masih mempunyai peminjaman lain
            | yang belum dikembalikan dan belum melewati jatuh tempo.
            |
            */

            $this->syncMemberStatus(
                Member::findOrFail(
                    $borrowing->member_id
                )
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
            | SIMPAN MEMBER ID
            |--------------------------------------------------------------------------
            */

            $memberId = $borrowing->member_id;

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

            /*
            |--------------------------------------------------------------------------
            | CEK ULANG STATUS MEMBER
            |--------------------------------------------------------------------------
            */

            $this->syncMemberStatus(
                Member::findOrFail($memberId)
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
                'Data peminjaman berhasil dihapus.'
            );
    }
}
