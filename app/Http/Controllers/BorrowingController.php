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
    /**
     * Sinkronisasi status member berdasarkan aktivitas SAAT INI.
     *
     * Member AKTIF jika:
     * - memiliki peminjaman yang belum dikembalikan
     * ATAU
     * - memiliki reservasi yang masih berlaku
     */
    private function syncMemberStatus(Member $member): void
    {
        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN AKTIF
        |--------------------------------------------------------------------------
        |
        | Selama returned_at masih NULL,
        | peminjaman dianggap masih aktif.
        |
        | Termasuk peminjaman yang sudah terlambat.
        |
        */

        $hasActiveBorrowing = Borrowing::where(
            'member_id',
            $member->id
        )
            ->whereNull('returned_at')
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | RESERVASI AKTIF
        |--------------------------------------------------------------------------
        |
        | Reservasi hanya aktif jika:
        |
        | - status bukan ditolak
        | - status bukan dibatalkan
        | - status bukan selesai
        | - expires_at belum lewat
        |
        */

        $hasActiveReservation = Reservation::where(
            'member_id',
            $member->id
        )
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->whereNotNull('expires_at')
            ->whereDate(
                'expires_at',
                '>=',
                now()->toDateString()
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS MEMBER
        |--------------------------------------------------------------------------
        */

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
        */

        $members = Member::orderBy(
            'name'
        )->get();


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
        */

        $oneMonthAgo =
            now()
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

        if (
            $request->filled('month') &&
            $request->filled('year')
        ) {

            $query
                ->whereYear(
                    'due_at',
                    $request->year
                )
                ->whereMonth(
                    'due_at',
                    $request->month
                );

        } else {

            $query->where(
                'due_at',
                '>=',
                $oneMonthAgo
            );
        }


        $borrowings =
            $query
            ->latest('borrowed_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | KURSI YANG SUDAH DIGUNAKAN
        |--------------------------------------------------------------------------
        */

        $borrowedSeats =
            Borrowing::whereDate(
                'borrowed_at',
                $selectedDate
            )
            ->where(
                'status',
                'dipinjam'
            )
            ->whereNotNull(
                'seat_number'
            )
            ->pluck(
                'seat_number'
            )
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | KURSI DARI RESERVASI YANG MASIH AKTIF
        |--------------------------------------------------------------------------
        */

        $reservedSeats =
            Reservation::whereDate(
                'reserved_at',
                $selectedDate
            )
            ->whereIn(
                'status',
                [
                    'menunggu',
                    'disetujui'
                ]
            )
            ->whereNotNull(
                'seat_number'
            )
            ->pluck(
                'seat_number'
            )
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | GABUNGKAN KURSI
        |--------------------------------------------------------------------------
        */

        $bookedSeats =
            array_values(
                array_unique(
                    array_merge(
                        $borrowedSeats,
                        $reservedSeats
                    )
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


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([

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

        $seatAlreadyUsed =
            Borrowing::whereDate(
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
        | CEK KURSI RESERVASI
        |--------------------------------------------------------------------------
        */

        $seatReserved =
            Reservation::whereDate(
                'reserved_at',
                $validated['borrowed_at']
            )
            ->where(
                'seat_number',
                $validated['seat_number']
            )
            ->whereIn(
                'status',
                [
                    'menunggu',
                    'disetujui'
                ]
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | JIKA KURSI TERPAKAI
        |--------------------------------------------------------------------------
        */

        if (
            $seatAlreadyUsed ||
            $seatReserved
        ) {

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

        DB::transaction(
            function () use (
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | KUNCI BUKU
                |--------------------------------------------------------------------------
                */

                $book =
                    Book::lockForUpdate()
                    ->findOrFail(
                        $validated['book_id']
                    );


                /*
                |--------------------------------------------------------------------------
                | CEK STOK
                |--------------------------------------------------------------------------
                */

                if (
                    $book->available_stock < 1
                ) {

                    abort(
                        422,
                        'Buku sedang tidak tersedia.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CEK KURSI LAGI
                |--------------------------------------------------------------------------
                */

                $seatAlreadyUsed =
                    Borrowing::whereDate(
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


                $seatReserved =
                    Reservation::whereDate(
                        'reserved_at',
                        $validated['borrowed_at']
                    )
                    ->where(
                        'seat_number',
                        $validated['seat_number']
                    )
                    ->whereIn(
                        'status',
                        [
                            'menunggu',
                            'disetujui'
                        ]
                    )
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

                $borrowing =
                    Borrowing::create([

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

                $borrowing
                    ->details()
                    ->create([

                        'book_id' =>
                            $validated['book_id'],

                        'quantity' =>
                            1,

                    ]);


                /*
                |--------------------------------------------------------------------------
                | KURANGI STOK
                |--------------------------------------------------------------------------
                */

                $book->decrement(
                    'available_stock'
                );
            }
        );


        return redirect()
            ->route(
                'borrowings.index'
            )
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

        DB::transaction(
            function () use (
                $borrowing
            ) {

                /*
                |--------------------------------------------------------------------------
                | JANGAN PROSES ULANG
                |--------------------------------------------------------------------------
                */

                if (
                    $borrowing->status ===
                    'dikembalikan'
                ) {

                    abort(
                        422,
                        'Buku sudah dikembalikan.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK
                |--------------------------------------------------------------------------
                */

                $details =
                    $borrowing->details;


                foreach (
                    $details as $detail
                ) {

                    $book =
                        Book::lockForUpdate()
                        ->findOrFail(
                            $detail->book_id
                        );


                    $book->increment(
                        'available_stock',
                        $detail->quantity ?? 1
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE PEMINJAMAN
                |--------------------------------------------------------------------------
                */

                $borrowing->update([

                    'status' =>
                        'dikembalikan',

                    'returned_at' =>
                        now()->toDateString(),

                ]);


                /*
                |--------------------------------------------------------------------------
                | SINKRONISASI MEMBER
                |--------------------------------------------------------------------------
                */

                $this->syncMemberStatus(
                    Member::findOrFail(
                        $borrowing->member_id
                    )
                );
            }
        );


        return redirect()
            ->route(
                'borrowings.index'
            )
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

        DB::transaction(
            function () use (
                $borrowing
            ) {

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK JIKA MASIH DIPINJAM
                |--------------------------------------------------------------------------
                */

                if (
                    $borrowing->status ===
                    'dipinjam'
                ) {

                    foreach (
                        $borrowing->details
                        as $detail
                    ) {

                        $book =
                            Book::lockForUpdate()
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
                | MEMBER ID
                |--------------------------------------------------------------------------
                */

                $memberId =
                    $borrowing->member_id;


                /*
                |--------------------------------------------------------------------------
                | HAPUS DETAIL
                |--------------------------------------------------------------------------
                */

                $borrowing
                    ->details()
                    ->delete();


                /*
                |--------------------------------------------------------------------------
                | HAPUS PEMINJAMAN
                |--------------------------------------------------------------------------
                */

                $borrowing->delete();


                /*
                |--------------------------------------------------------------------------
                | SINKRONISASI MEMBER
                |--------------------------------------------------------------------------
                */

                $this->syncMemberStatus(
                    Member::findOrFail(
                        $memberId
                    )
                );
            }
        );


        return redirect()
            ->route(
                'borrowings.index'
            )
            ->with(
                'success',
                'Data peminjaman berhasil dihapus.'
            );
    }
}