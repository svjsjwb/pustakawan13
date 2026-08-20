<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Shelf;
use App\Models\LibraryZone;

class ReservationController extends Controller
{
    public function index()
    {
        // Anggota aktif
        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // Semua buku tetap ditampilkan.
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        // Reservasi terbaru
        //
        // bookCopy ikut dimuat karena nanti kita
        // membutuhkan lokasi eksemplar.
        $reservations = Reservation::with([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ])
            ->latest()
            ->get();

        return view('reservations.index', compact(
            'members',
            'books',
            'reservations'
        ));
    }


    /**
     * BUAT RESERVASI
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
             * Kunci buku terlebih dahulu.
             *
             * Tujuannya agar dua proses reservasi
             * tidak mengambil stok terakhir secara
             * bersamaan.
             */
            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);


            /*
             * Cari SATU eksemplar yang tersedia.
             *
             * BookCopy sekarang menjadi sumber lokasi
             * fisik buku.
             */
            $bookCopy = BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();


            /*
             * Tidak ada eksemplar yang tersedia.
             */
            if (!$bookCopy) {
                abort(
                    422,
                    'Tidak ada eksemplar buku yang tersedia.'
                );
            }


            /*
             * Pastikan available_stock lama
             * masih menunjukkan buku tersedia.
             *
             * Ini sementara dipertahankan karena
             * sistem lama masih menggunakan field ini.
             */
            if ($book->available_stock < 1) {
                abort(
                    422,
                    'Buku sedang tidak tersedia.'
                );
            }


            /*
             * Ubah status eksemplar menjadi RESERVED.
             */
            $bookCopy->update([
                'status' => 'reserved',
            ]);


            /*
             * Buat reservasi dan simpan eksemplar
             * yang benar-benar dikunci.
             */
            Reservation::create([
                'member_id' => $validated['member_id'],
                'book_id' => $book->id,
                'book_copy_id' => $bookCopy->id,
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $validated['expires_at'] ?? null,
                'status' => 'menunggu',
            ]);


            /*
             * Pertahankan sinkronisasi dengan
             * sistem stok lama.
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


    /**
     * UPDATE STATUS RESERVASI
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

            /*
             * Kunci reservation agar tidak diproses
             * oleh request lain secara bersamaan.
             */
            $reservation = Reservation::lockForUpdate()
                ->findOrFail($reservation->id);


            $oldStatus = $reservation->status;
            $newStatus = $validated['status'];


            /*
             * Tidak ada perubahan.
             */
            if ($oldStatus === $newStatus) {
                return;
            }


            /*
             * =====================================================
             * RESERVASI DITOLAK / DIBATALKAN
             * =====================================================
             *
             * Copy yang sebelumnya RESERVED
             * dikembalikan menjadi AVAILABLE.
             */
            if (
                in_array($newStatus, [
                    'ditolak',
                    'dibatalkan'
                ])
                &&
                !in_array($oldStatus, [
                    'ditolak',
                    'dibatalkan'
                ])
            ) {

                /*
                 * Kalau reservation baru memiliki
                 * book_copy_id, release copy tersebut.
                 */
                if ($reservation->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($reservation->book_copy_id);

                    if (
                        $bookCopy &&
                        $bookCopy->status === 'reserved'
                    ) {
                        $bookCopy->update([
                            'status' => 'available',
                        ]);
                    }
                }


                /*
                 * Kembalikan stok lama.
                 */
                $book = Book::lockForUpdate()
                    ->findOrFail($reservation->book_id);

                $book->increment('available_stock');
            }


            /*
             * =====================================================
             * RESERVASI DI-AKTIFKAN KEMBALI
             * =====================================================
             *
             * Contoh:
             *
             * dibatalkan
             *      ↓
             * menunggu
             *
             * Kita harus mencari copy AVAILABLE baru.
             */
            if (
                in_array($oldStatus, [
                    'ditolak',
                    'dibatalkan'
                ])
                &&
                !in_array($newStatus, [
                    'ditolak',
                    'dibatalkan'
                ])
            ) {

                $book = Book::lockForUpdate()
                    ->findOrFail($reservation->book_id);


                /*
                 * Cari eksemplar tersedia.
                 */
                $bookCopy = BookCopy::where(
                    'book_id',
                    $book->id
                )
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();


                if (!$bookCopy) {
                    abort(
                        422,
                        'Tidak ada eksemplar buku yang tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }


                /*
                 * Kunci copy.
                 */
                $bookCopy->update([
                    'status' => 'reserved',
                ]);


                /*
                 * Hubungkan reservation ke copy.
                 */
                $reservation->update([
                    'book_copy_id' => $bookCopy->id,
                ]);


                /*
                 * Kurangi stok lama.
                 */
                if ($book->available_stock < 1) {
                    abort(
                        422,
                        'Stok buku tidak tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }

                $book->decrement('available_stock');
            }


            /*
             * Update status reservation.
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

    public function locator(Reservation $reservation)
    {
        /*
     * =========================================================
     * LOAD DATA RESERVASI
     * =========================================================
     */

        $reservation->load([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);


        /*
     * =========================================================
     * CEK BOOK COPY
     * =========================================================
     */

        if (!$reservation->bookCopy) {

            abort(
                404,
                'Eksemplar buku tidak ditemukan.'
            );
        }


        /*
     * =========================================================
     * RAK TARGET
     * =========================================================
     */

        $targetShelf =
            $reservation->bookCopy->shelf;


        if (!$targetShelf) {

            abort(
                404,
                'Rak buku belum ditentukan.'
            );
        }


        /*
     * =========================================================
     * ZONA TARGET
     * =========================================================
     */

        $targetZone =
            $targetShelf->zone;


        if (!$targetZone) {

            abort(
                404,
                'Zona rak belum ditentukan.'
            );
        }


        /*
     * =========================================================
     * LANTAI TARGET
     * =========================================================
     */

        $targetFloor =
            $targetZone->floor;


        if (!$targetFloor) {

            abort(
                404,
                'Lantai rak belum ditentukan.'
            );
        }


        /*
     * =========================================================
     * SEMUA ZONA DI LANTAI YANG SAMA
     * =========================================================
     */

        $zoneIds =
            LibraryZone::where(
                'library_floor_id',
                $targetFloor->id
            )
            ->pluck('id');


        /*
     * =========================================================
     * SEMUA RAK DI LANTAI YANG SAMA
     * =========================================================
     *
     * Contoh lantai 1:
     *
     * A-01
     * A-02
     * B-01
     * B-02
     * C-01
     * C-02
     *
     * Setiap shelf membawa BookCopy-nya.
     */

        $shelves =
            Shelf::whereIn(
                'library_zone_id',
                $zoneIds
            )
            ->with([
                'copies.book',
                'zone.floor',
            ])
            ->orderBy('code')
            ->get();


        /*
     * =========================================================
     * DATA BOOK COPY UNTUK 3D
     * =========================================================
     *
     * Kita ubah Collection Laravel menjadi array
     * sederhana yang bisa dibaca JavaScript.
     */

        $bookCopies = $shelves
            ->flatMap(function ($shelf) use ($reservation) {

                return $shelf->copies->map(function ($copy) use (
                    $shelf,
                    $reservation
                ) {

                    return [

                        'id' => $copy->id,

                        'book_id' => $copy->book_id,

                        'title' =>
                        $copy->book?->title ?? 'Buku',

                        'barcode' =>
                        $copy->barcode,

                        'status' =>
                        $copy->status,

                        'shelf_id' =>
                        $shelf->id,

                        'shelf' =>
                        $shelf->code,

                        'section' =>
                        (int) $copy->section,

                        'row' =>
                        (int) $copy->row,

                        'column' =>
                        (int) $copy->column,

                        'is_target' =>
                        $copy->id ===
                            $reservation->book_copy_id,

                    ];
                });
            })
            ->values()
            ->toArray();


        /*
     * =========================================================
     * KIRIM KE VIEW
     * =========================================================
     */

        return view(
            'book-locator.show',
            [

                'reservation' =>
                $reservation,

                'targetShelf' =>
                $targetShelf,

                'shelves' =>
                $shelves,

                'bookCopies' =>
                $bookCopies,

            ]
        );
    }

    /**
     * HAPUS RESERVASI
     */
    public function destroy(Reservation $reservation)
    {
        DB::transaction(function () use ($reservation) {

            $reservation = Reservation::lockForUpdate()
                ->findOrFail($reservation->id);


            /*
             * Reservation yang masih aktif
             * masih memegang satu copy.
             */
            if (
                !in_array(
                    $reservation->status,
                    [
                        'ditolak',
                        'dibatalkan',
                        'selesai'
                    ]
                )
            ) {

                /*
                 * Release BookCopy.
                 */
                if ($reservation->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($reservation->book_copy_id);

                    if (
                        $bookCopy &&
                        $bookCopy->status === 'reserved'
                    ) {
                        $bookCopy->update([
                            'status' => 'available',
                        ]);
                    }
                }


                /*
                 * Kembalikan stok lama.
                 */
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
