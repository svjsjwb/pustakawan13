<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
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
        | Termasuk peminjaman yang sudah terlambat.
        |
        */

        $hasActiveBorrowing = Borrowing::where('member_id', $member->id)
            ->whereNull('returned_at')
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | RESERVASI AKTIF
        |--------------------------------------------------------------------------
        |
        | Reservasi hanya aktif jika:
        | - status bukan ditolak
        | - status bukan dibatalkan
        | - status bukan selesai
        | - expires_at belum lewat
        |
        */

        $hasActiveReservation = Reservation::where('member_id', $member->id)
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS MEMBER
        |--------------------------------------------------------------------------
        */

        $member->update([
            'status' => ($hasActiveBorrowing || $hasActiveReservation)
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
        | Filter berdasarkan bulan/tahun jika ada parameter,
        | jika tidak, tampilkan 1 bulan terakhir.
        |
        */

        $oneMonthAgo = now()->subMonth()->startOfDay();

        $query = Borrowing::with([
            'member',
            'detail.book',
            'detail.bookCopy'
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
            ->where('status', 'dipinjam')
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


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {
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
            'seat_number' => [
                'required',
                'string',
                'regex:/^[ABC][1-8]$/',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            // Kunci data buku agar stok tidak berubah bersamaan.
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);

            // Kunci satu eksemplar fisik yang tersedia.
            $bookCopy = BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$bookCopy || $book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            // Periksa kursi yang sudah digunakan.
            $seatAlreadyUsed = Borrowing::whereDate(
                'borrowed_at',
                $validated['borrowed_at']
            )
                ->where('seat_number', $validated['seat_number'])
                ->where('status', 'dipinjam')
                ->exists();

            $seatReserved = Reservation::whereDate(
                'reserved_at',
                $validated['borrowed_at']
            )
                ->where('seat_number', $validated['seat_number'])
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->exists();

            if ($seatAlreadyUsed || $seatReserved) {
                abort(
                    422,
                    'Kursi ' . $validated['seat_number'] .
                        ' sudah digunakan atau dipesan pada tanggal tersebut.'
                );
            }

            // Tandai eksemplar sebagai sedang dipinjam.
            $bookCopy->update([
                'status' => 'borrowed',
            ]);

            // Buat transaksi peminjaman.
            $borrowing = Borrowing::create([
                'member_id'   => $validated['member_id'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at'      => $validated['due_at'],
                'seat_number' => $validated['seat_number'],
                'status'      => 'dipinjam',
            ]);

            // Hubungkan transaksi dengan satu eksemplar fisik.
            $borrowing->detail()->create([
                'book_id'      => $book->id,
                'book_copy_id' => $bookCopy->id,
                'quantity'     => 1,
            ]);

            // Kurangi stok buku.
            $book->decrement('stok');

            // Sinkronisasi status member.
            $this->syncMemberStatus(
                Member::findOrFail($validated['member_id'])
            );
        });

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman buku berhasil dibuat.');
    }


    /*
    |--------------------------------------------------------------------------
    | PENGEMBALIAN BUKU
    |--------------------------------------------------------------------------
    */


    public function returnBook(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {

            // Ambil ulang dan kunci transaksi peminjaman.
            $borrowing = Borrowing::lockForUpdate()
                ->findOrFail($borrowing->id);

            if ($borrowing->status === 'dikembalikan') {
                abort(422, 'Buku sudah dikembalikan.');
            }

            // Ambil detail peminjaman beserta eksemplar fisiknya.
            $detail = $borrowing->detail()
                ->lockForUpdate()
                ->first();

            if (!$detail) {
                abort(422, 'Detail peminjaman tidak ditemukan.');
            }

            if (!$detail->book_copy_id) {
                abort(422, 'Eksemplar fisik buku tidak terhubung.');
            }

            // Kunci eksemplar fisik.
            $bookCopy = BookCopy::lockForUpdate()
                ->findOrFail($detail->book_copy_id);

            if ($bookCopy->status !== 'borrowed') {
                abort(422, 'Status eksemplar tidak sesuai dengan transaksi peminjaman.');
            }

            // Kembalikan status eksemplar sesuai kondisinya.
            $bookCopy->update([
                'status' => 'available',
            ]);

            // Sinkronkan status berdasarkan kondisi fisik buku.
            $bookCopy->syncStatus();

            // Tambahkan stok kembali.
            $book = Book::lockForUpdate()
                ->findOrFail($detail->book_id);

            $book->increment('stok', $detail->quantity ?? 1);

            // Tandai transaksi sudah dikembalikan.
            $borrowing->update([
                'status'      => 'dikembalikan',
                'returned_at' => now()->toDateString(),
            ]);

            // Sinkronisasi status member.
            $this->syncMemberStatus(
                Member::findOrFail($borrowing->member_id)
            );
        });

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }



    /*
    |--------------------------------------------------------------------------
    | HAPUS PEMINJAMAN
    |--------------------------------------------------------------------------
    */


    public function destroy(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {

            // Kunci transaksi peminjaman.
            $borrowing = Borrowing::lockForUpdate()
                ->findOrFail($borrowing->id);

            // Cegah penghapusan peminjaman yang terhubung reservasi.
            $hasReservation = Reservation::where(
                'borrowing_id',
                $borrowing->id
            )->lockForUpdate()->exists();

            if ($hasReservation) {
                abort(
                    422,
                    'Peminjaman yang berasal dari reservasi tidak dapat dihapus.'
                );
            }

            // Ambil satu detail peminjaman.
            $detail = $borrowing->detail()
                ->lockForUpdate()
                ->first();

            if (!$detail) {
                abort(422, 'Detail peminjaman tidak ditemukan.');
            }

            // Jika masih dipinjam, pulihkan eksemplar dan stok.
            if ($borrowing->status === 'dipinjam') {

                if (!$detail->book_copy_id) {
                    abort(422, 'Eksemplar fisik buku tidak terhubung.');
                }

                $bookCopy = BookCopy::lockForUpdate()
                    ->findOrFail($detail->book_copy_id);

                if ($bookCopy->status !== 'borrowed') {
                    abort(
                        422,
                        'Status eksemplar tidak sesuai dengan transaksi peminjaman.'
                    );
                }

                $bookCopy->update([
                    'status' => 'available',
                ]);

                $bookCopy->syncStatus();

                $book = Book::lockForUpdate()
                    ->findOrFail($detail->book_id);

                $book->increment('stok', $detail->quantity ?? 1);
            }

            $memberId = $borrowing->member_id;

            // Hapus detail dan transaksi.
            $detail->delete();
            $borrowing->delete();

            // Sinkronisasi status member.
            $this->syncMemberStatus(
                Member::findOrFail($memberId)
            );
        });

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
