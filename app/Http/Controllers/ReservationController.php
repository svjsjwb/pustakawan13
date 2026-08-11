<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        // Anggota aktif
        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // Semua buku tetap ditampilkan.
        // Urutan angka dibuat natural:
        // Dummy 1, 2, 3 ... 9, 10, 11 ...
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        // Reservasi terbaru
        $reservations = Reservation::with([
            'member',
            'book'
        ])
            ->latest()
            ->get();

        return view('reservations.index', compact(
            'members',
            'books',
            'reservations'
        ));
    }


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

            'reserved_at' => [
                'required',
                'date'
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:reserved_at'
            ],
        ]);


        DB::transaction(function () use ($validated) {

            /*
             * Kunci baris buku selama proses reservasi.
             * Ini mencegah dua orang melakukan reservasi
             * pada stok terakhir secara bersamaan.
             */
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);


            /*
             * Cek stok di backend.
             * Jangan cuma mengandalkan option disabled di HTML.
             */
            if ($book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }


            /*
             * Buat reservasi.
             */
            Reservation::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $validated['expires_at'] ?? null,
                'status' => 'menunggu',
            ]);


            /*
             * Reservasi dianggap mengambil 1 slot stok.
             *
             * Contoh:
             * Stok tersedia 5
             * Reservasi dibuat
             * Stok tersedia menjadi 4
             */
            $book->decrement('available_stock');
        });


        return redirect()
            ->route('reservations')
            ->with(
                'success',
                'Reservasi berhasil dibuat.'
            );
    }


    /*
     * SETUJUI / TOLAK / BATALKAN / SELESAI
     */
    public function updateStatus(
        Request $request,
        Reservation $reservation
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu,disetujui,ditolak,dibatalkan,selesai'
            ],
        ]);


        DB::transaction(function () use (
            $validated,
            $reservation
        ) {

            $oldStatus = $reservation->status;
            $newStatus = $validated['status'];


            /*
             * Kalau status berubah menjadi DITOLAK
             * atau DIBATALKAN,
             * stok yang sebelumnya dikunci oleh reservasi
             * harus dikembalikan.
             */
            if (
                in_array($newStatus, ['ditolak', 'dibatalkan'])
                &&
                !in_array($oldStatus, ['ditolak', 'dibatalkan'])
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail($reservation->book_id);

                $book->increment('available_stock');
            }


            /*
             * Kalau reservasi yang sebelumnya ditolak /
             * dibatalkan diaktifkan kembali,
             * ambil stok lagi.
             */
            if (
                in_array($oldStatus, ['ditolak', 'dibatalkan'])
                &&
                !in_array($newStatus, ['ditolak', 'dibatalkan'])
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail($reservation->book_id);

                if ($book->available_stock < 1) {
                    abort(
                        422,
                        'Stok buku tidak tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }

                $book->decrement('available_stock');
            }


            /*
             * Update status reservasi.
             */
            $reservation->update([
                'status' => $newStatus
            ]);
        });


        return redirect()
            ->route('reservations')
            ->with(
                'success',
                'Status reservasi berhasil diperbarui.'
            );
    }


    /*
     * HAPUS RESERVASI
     */
    public function destroy(Reservation $reservation)
    {
        DB::transaction(function () use ($reservation) {

            /*
             * Kalau reservasi masih memegang stok,
             * kembalikan stok sebelum dihapus.
             */
            if (
                !in_array(
                    $reservation->status,
                    ['ditolak', 'dibatalkan', 'selesai']
                )
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail($reservation->book_id);

                $book->increment('available_stock');
            }


            $reservation->delete();
        });


        return redirect()
            ->route('reservations')
            ->with(
                'success',
                'Reservasi berhasil dihapus.'
            );
    }
}
