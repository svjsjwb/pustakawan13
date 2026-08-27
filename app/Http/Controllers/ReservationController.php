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
    /*
    |--------------------------------------------------------------------------
    | DAFTAR RESERVASI
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
         * Tanggal yang dipilih untuk denah kursi.
         */
        $selectedDate = $request->get(
            'reservation_date',
            now()->format('Y-m-d')
        );


        /*
         * =====================================================
         * ANGGOTA AKTIF
         * =====================================================
         */

        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();


        /*
         * =====================================================
         * BUKU
         * =====================================================
         */

        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();


        /*
         * =====================================================
         * DAFTAR RESERVASI
         * =====================================================
         *
         * bookCopy ikut dimuat karena dibutuhkan
         * untuk mengetahui lokasi fisik buku.
         */

        $reservations = Reservation::with([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ])
            ->latest()
            ->get();


        /*
         * =====================================================
         * KURSI YANG SUDAH BOOKING
         * =====================================================
         *
         * Status menunggu dan disetujui dianggap
         * sudah melakukan booking.
         */

        $bookedSeats = Reservation::whereDate(
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
         * =====================================================
         * KIRIM KE VIEW
         * =====================================================
         */

        return view(
            'reservations.index',
            compact(
                'members',
                'books',
                'reservations',
                'bookedSeats',
                'selectedDate'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN RESERVASI
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
         * =====================================================
         * VALIDASI
         * =====================================================
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

            'reserved_at' => [
                'required',
                'date'
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:reserved_at'
            ],

            'seat_number' => [
                'nullable',
                'string',
                'regex:/^[ABC][1-8]$/'
            ],

        ]);


        /*
         * =====================================================
         * CEK KURSI
         * =====================================================
         */

        if (!empty($validated['seat_number'])) {

            $seatAlreadyBooked = Reservation::whereDate(
                'reserved_at',
                $validated['reserved_at']
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


            if ($seatAlreadyBooked) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Kursi ' .
                        $validated['seat_number'] .
                        ' sudah dipesan pada tanggal tersebut.'
                    );
            }
        }


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(function () use ($validated) {

            /*
             * Kunci buku terlebih dahulu.
             */

            $book = Book::lockForUpdate()
                ->findOrFail(
                    $validated['book_id']
                );


            /*
             * =================================================
             * CARI SATU EKSEMPLAR TERSEDIA
             * =================================================
             *
             * BookCopy menjadi sumber lokasi fisik buku.
             */

            $bookCopy = BookCopy::where(
                'book_id',
                $book->id
            )
                ->where(
                    'status',
                    'available'
                )
                ->lockForUpdate()
                ->first();


            /*
             * Tidak ada eksemplar tersedia.
             */

            if (!$bookCopy) {

                abort(
                    422,
                    'Tidak ada eksemplar buku yang tersedia.'
                );
            }


            /*
             * =================================================
             * CEK STOK LAMA
             * =================================================
             *
             * Tetap dipertahankan karena sistem lama
             * masih menggunakan available_stock.
             */

            if ($book->available_stock < 1) {

                abort(
                    422,
                    'Buku sedang tidak tersedia.'
                );
            }


            /*
             * =================================================
             * KUNCI BOOK COPY
             * =================================================
             */

            $bookCopy->update([
                'status' => 'reserved',
            ]);


            /*
             * =================================================
             * CEK KURSI LAGI
             * =================================================
             *
             * Dilakukan kembali di dalam transaction
             * untuk mencegah dua request memesan kursi
             * yang sama secara bersamaan.
             */

            if (!empty($validated['seat_number'])) {

                $seatAlreadyBooked = Reservation::whereDate(
                    'reserved_at',
                    $validated['reserved_at']
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


                if ($seatAlreadyBooked) {

                    abort(
                        422,
                        'Kursi ' .
                        $validated['seat_number'] .
                        ' baru saja dipesan oleh pengguna lain.'
                    );
                }
            }


            /*
             * =================================================
             * BUAT RESERVASI
             * =================================================
             */

            Reservation::create([

                'member_id' =>
                    $validated['member_id'],

                'book_id' =>
                    $book->id,

                'book_copy_id' =>
                    $bookCopy->id,

                'reserved_at' =>
                    $validated['reserved_at'],

                'expires_at' =>
                    $validated['expires_at'] ?? null,

                'seat_number' =>
                    $validated['seat_number'] ?? null,

                'status' =>
                    'menunggu',

            ]);


            /*
             * =================================================
             * KURANGI STOK
             * =================================================
             */

            $book->decrement(
                'available_stock'
            );
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Reservasi berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS RESERVASI
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Reservation $reservation
    ) {

        /*
         * =====================================================
         * VALIDASI STATUS
         * =====================================================
         */

        $validated = $request->validate([

            'status' => [
                'required',
                'in:menunggu,disetujui,ditolak,dibatalkan,selesai'
            ],

        ]);


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(function () use (
            $validated,
            $reservation
        ) {

            /*
             * Kunci reservation.
             */

            $reservation = Reservation::lockForUpdate()
                ->findOrFail(
                    $reservation->id
                );


            $oldStatus =
                $reservation->status;


            $newStatus =
                $validated['status'];


            /*
             * =================================================
             * TIDAK ADA PERUBAHAN
             * =================================================
             */

            if (
                $oldStatus ===
                $newStatus
            ) {

                return;
            }


            /*
             * =================================================
             * RESERVASI DITOLAK / DIBATALKAN
             * =================================================
             *
             * BookCopy yang sebelumnya reserved
             * dikembalikan menjadi available.
             */

            if (
                in_array(
                    $newStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
                &&
                !in_array(
                    $oldStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
            ) {

                /*
                 * Release BookCopy.
                 */

                if (
                    $reservation->book_copy_id
                ) {

                    $bookCopy =
                        BookCopy::lockForUpdate()
                            ->find(
                                $reservation->book_copy_id
                            );


                    if (
                        $bookCopy &&
                        $bookCopy->status ===
                        'reserved'
                    ) {

                        $bookCopy->update([
                            'status' =>
                                'available',
                        ]);
                    }
                }


                /*
                 * Kembalikan stok.
                 */

                $book =
                    Book::lockForUpdate()
                        ->findOrFail(
                            $reservation->book_id
                        );


                $book->increment(
                    'available_stock'
                );
            }


            /*
             * =================================================
             * RESERVASI DI-AKTIFKAN KEMBALI
             * =================================================
             *
             * Contoh:
             *
             * dibatalkan
             *      ↓
             * menunggu
             *
             * Cari BookCopy available baru.
             */

            if (
                in_array(
                    $oldStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
                &&
                !in_array(
                    $newStatus,
                    [
                        'ditolak',
                        'dibatalkan'
                    ]
                )
            ) {

                $book =
                    Book::lockForUpdate()
                        ->findOrFail(
                            $reservation->book_id
                        );


                /*
                 * Cari eksemplar tersedia.
                 */

                $bookCopy =
                    BookCopy::where(
                        'book_id',
                        $book->id
                    )
                    ->where(
                        'status',
                        'available'
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$bookCopy) {

                    abort(
                        422,
                        'Tidak ada eksemplar buku yang tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }


                /*
                 * Kunci BookCopy.
                 */

                $bookCopy->update([
                    'status' =>
                        'reserved',
                ]);


                /*
                 * Hubungkan reservation
                 * dengan BookCopy baru.
                 */

                $reservation->update([
                    'book_copy_id' =>
                        $bookCopy->id,
                ]);


                /*
                 * Pastikan stok tersedia.
                 */

                if (
                    $book->available_stock < 1
                ) {

                    abort(
                        422,
                        'Stok buku tidak tersedia untuk mengaktifkan kembali reservasi.'
                    );
                }


                /*
                 * Kurangi stok.
                 */

                $book->decrement(
                    'available_stock'
                );
            }


            /*
             * =================================================
             * UPDATE STATUS
             * =================================================
             */

            $reservation->update([
                'status' =>
                    $newStatus
            ]);
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Status reservasi berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BOOK LOCATOR
    |--------------------------------------------------------------------------
    */

    public function locator(
        Reservation $reservation
    ) {

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

        if (
            !$reservation->bookCopy
        ) {

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


        if (
            !$targetShelf
        ) {

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


        if (
            !$targetZone
        ) {

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


        if (
            !$targetFloor
        ) {

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
         */

        $bookCopies =
            $shelves
                ->flatMap(
                    function ($shelf)
                    use ($reservation) {

                        return $shelf->copies
                            ->map(
                                function ($copy)
                                use (
                                    $shelf,
                                    $reservation
                                ) {

                                    return [

                                        'id' =>
                                            $copy->id,

                                        'book_id' =>
                                            $copy->book_id,

                                        'title' =>
                                            $copy->book?->title
                                            ?? 'Buku',

                                        'barcode' =>
                                            $copy->barcode,

                                        'status' =>
                                            $copy->status,

                                        'shelf_id' =>
                                            $shelf->id,

                                        'shelf' =>
                                            $shelf->code,

                                        'section' =>
                                            (int)
                                            $copy->section,

                                        'row' =>
                                            (int)
                                            $copy->row,

                                        'column' =>
                                            (int)
                                            $copy->column,

                                        'is_target' =>
                                            $copy->id ===
                                            $reservation
                                                ->book_copy_id,

                                    ];
                                }
                            );
                    }
                )
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


    /*
    |--------------------------------------------------------------------------
    | HAPUS RESERVASI
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Reservation $reservation
    ) {

        DB::transaction(
            function () use (
                $reservation
            ) {

                $reservation =
                    Reservation::lockForUpdate()
                        ->findOrFail(
                            $reservation->id
                        );


                /*
                 * =================================================
                 * KEMBALIKAN BOOK COPY DAN STOK
                 * =================================================
                 *
                 * Reservation aktif masih memegang
                 * satu BookCopy.
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

                    if (
                        $reservation->book_copy_id
                    ) {

                        $bookCopy =
                            BookCopy::lockForUpdate()
                                ->find(
                                    $reservation
                                        ->book_copy_id
                                );


                        if (
                            $bookCopy &&
                            $bookCopy->status ===
                            'reserved'
                        ) {

                            $bookCopy->update([
                                'status' =>
                                    'available',
                            ]);
                        }
                    }


                    /*
                     * Kembalikan stok.
                     */

                    $book =
                        Book::lockForUpdate()
                            ->findOrFail(
                                $reservation->book_id
                            );


                    $book->increment(
                        'available_stock'
                    );
                }


                /*
                 * =================================================
                 * HAPUS RESERVASI
                 * =================================================
                 */

                $reservation->delete();
            }
        );


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Reservasi berhasil dihapus.'
            );
    }
}