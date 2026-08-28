<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Shelf;
use App\Models\LibraryZone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
         * =====================================================
         * TANGGAL DENAH KURSI
         * =====================================================
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

        $members = Member::where(
            'status',
            'aktif'
        )
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
         * QUERY RESERVASI
         * =====================================================
         *
         * BookCopy ikut dimuat karena diperlukan untuk
         * mengetahui lokasi fisik buku.
         */

        $query = Reservation::with([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);


        /*
         * =====================================================
         * TENTUKAN KOLOM BATAS WAKTU
         * =====================================================
         *
         * Prioritas:
         *
         * 1. due_at
         * 2. expires_at
         * 3. reserved_at
         */

        $dueColumn = Schema::hasColumn(
            'reservations',
            'due_at'
        )
            ? 'due_at'
            : (
                Schema::hasColumn(
                    'reservations',
                    'expires_at'
                )
                    ? 'expires_at'
                    : 'reserved_at'
            );


        /*
         * =====================================================
         * FILTER RENTANG TANGGAL
         * =====================================================
         */

        if (
            $request->filled('start_date')
            &&
            $request->filled('end_date')
        ) {

            $startDate = Carbon::parse(
                $request->start_date
            )->startOfDay();

            $endDate = Carbon::parse(
                $request->end_date
            )->endOfDay();

            $query->whereBetween(
                'reserved_at',
                [
                    $startDate,
                    $endDate,
                ]
            );

        } elseif (
            $request->filled('start_date')
        ) {

            $query->whereDate(
                'reserved_at',
                '>=',
                $request->start_date
            );

        } elseif (
            $request->filled('end_date')
        ) {

            $query->whereDate(
                'reserved_at',
                '<=',
                $request->end_date
            );

        } elseif (
            $request->filled('month')
            &&
            $request->filled('year')
        ) {

            /*
             * Filter bulan berdasarkan due date
             * / expires_at / reserved_at.
             */

            $query
                ->whereYear(
                    $dueColumn,
                    $request->year
                )
                ->whereMonth(
                    $dueColumn,
                    $request->month
                );

        } else {

            /*
             * Default:
             * tampilkan data dalam satu bulan terakhir.
             */

            $query->where(
                $dueColumn,
                '>=',
                now()->subMonth()->startOfDay()
            );
        }


        /*
         * =====================================================
         * AMBIL RESERVASI
         * =====================================================
         */

        $reservations = $query
            ->latest('reserved_at')
            ->get();


        /*
         * =====================================================
         * KURSI YANG SUDAH DIPESAN
         * =====================================================
         */

        $bookedSeats = Reservation::whereDate(
            'reserved_at',
            $selectedDate
        )
            ->whereIn(
                'status',
                [
                    'menunggu',
                    'disetujui',
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
                'exists:members,id',
            ],

            'book_id' => [
                'required',
                'exists:books,id',
            ],

            'reserved_at' => [
                'required',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:reserved_at',
            ],

            'seat_number' => [
                'nullable',
                'string',
                'regex:/^[ABC][1-8]$/',
            ],
        ]);


        /*
         * =====================================================
         * CEK KURSI SEBELUM TRANSACTION
         * =====================================================
         */

        if (
            !empty(
                $validated['seat_number']
            )
        ) {

            $seatAlreadyBooked =
                Reservation::whereDate(
                    'reserved_at',
                    $validated['reserved_at']
                )
                    ->where(
                        'seat_number',
                        $validated['seat_number']
                    )
                    ->whereIn(
                        'status',
                        [
                            'menunggu',
                            'disetujui',
                        ]
                    )
                    ->exists();


            if ($seatAlreadyBooked) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Kursi ' .
                        $validated['seat_number'] .
                        ' sudah dipesan oleh pengguna lain pada tanggal tersebut.'
                    );
            }
        }


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(
            function () use (
                $validated
            ) {

                /*
                 * =================================================
                 * KUNCI BUKU
                 * =================================================
                 */

                $book = Book::lockForUpdate()
                    ->findOrFail(
                        $validated['book_id']
                    );


                /*
                 * =================================================
                 * CEK STOK
                 * =================================================
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
                 * =================================================
                 * CARI BOOK COPY
                 * =================================================
                 *
                 * Reservation harus memegang satu eksemplar
                 * fisik yang tersedia.
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


                if (!$bookCopy) {

                    abort(
                        422,
                        'Tidak ada eksemplar buku yang tersedia.'
                    );
                }


                /*
                 * =================================================
                 * CEK KURSI LAGI DI DALAM TRANSACTION
                 * =================================================
                 *
                 * Untuk mengurangi kemungkinan dua request
                 * mengambil kursi yang sama.
                 */

                if (
                    !empty(
                        $validated['seat_number']
                    )
                ) {

                    $seatAlreadyBooked =
                        Reservation::whereDate(
                            'reserved_at',
                            $validated['reserved_at']
                        )
                            ->where(
                                'seat_number',
                                $validated['seat_number']
                            )
                            ->whereIn(
                                'status',
                                [
                                    'menunggu',
                                    'disetujui',
                                ]
                            )
                            ->lockForUpdate()
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
                 * RESERVATION DATA
                 * =================================================
                 */

                $reservationData = [
                    'member_id' =>
                        $validated['member_id'],

                    'book_id' =>
                        $validated['book_id'],

                    'book_copy_id' =>
                        $bookCopy->id,

                    'reserved_at' =>
                        $validated['reserved_at'],

                    'expires_at' =>
                        $validated['expires_at']
                        ?? null,

                    'seat_number' =>
                        $validated['seat_number']
                        ?? null,

                    'status' =>
                        'menunggu',
                ];


                /*
                 * =================================================
                 * KOMPATIBILITAS due_at
                 * =================================================
                 */

                if (
                    Schema::hasColumn(
                        'reservations',
                        'due_at'
                    )
                ) {

                    $reservationData['due_at'] =
                        $validated['expires_at']
                        ??
                        $validated['reserved_at'];
                }


                /*
                 * =================================================
                 * BUAT RESERVASI
                 * =================================================
                 */

                Reservation::create(
                    $reservationData
                );


                /*
                 * =================================================
                 * UBAH STATUS BOOK COPY
                 * =================================================
                 */

                $bookCopy->update([
                    'status' => 'reserved',
                ]);


                /*
                 * =================================================
                 * KURANGI STOK
                 * =================================================
                 */

                $book->decrement(
                    'available_stock'
                );
            }
        );


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route(
                'reservations.index'
            )
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
         * VALIDASI
         * =====================================================
         */

        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu,disetujui,ditolak,dibatalkan,selesai',
            ],
        ]);


        /*
         * =====================================================
         * TRANSACTION
         * =====================================================
         */

        DB::transaction(
            function () use (
                $validated,
                $reservation
            ) {

                /*
                 * Kunci reservation.
                 */

                $reservation =
                    Reservation::lockForUpdate()
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
                 * DITOLAK / DIBATALKAN
                 * =================================================
                 */

                if (
                    in_array(
                        $newStatus,
                        [
                            'ditolak',
                            'dibatalkan',
                        ]
                    )
                    &&
                    !in_array(
                        $oldStatus,
                        [
                            'ditolak',
                            'dibatalkan',
                        ]
                    )
                ) {

                    /*
                     * ---------------------------------------------
                     * RELEASE BOOK COPY
                     * ---------------------------------------------
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
                            $bookCopy
                            &&
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
                     * ---------------------------------------------
                     * KEMBALIKAN STOK
                     * ---------------------------------------------
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
                 * AKTIFKAN KEMBALI
                 * =================================================
                 *
                 * Contoh:
                 *
                 * dibatalkan → menunggu
                 * ditolak    → menunggu
                 */

                if (
                    in_array(
                        $oldStatus,
                        [
                            'ditolak',
                            'dibatalkan',
                        ]
                    )
                    &&
                    !in_array(
                        $newStatus,
                        [
                            'ditolak',
                            'dibatalkan',
                        ]
                    )
                ) {

                    /*
                     * ---------------------------------------------
                     * KUNCI BUKU
                     * ---------------------------------------------
                     */

                    $book =
                        Book::lockForUpdate()
                            ->findOrFail(
                                $reservation->book_id
                            );


                    /*
                     * ---------------------------------------------
                     * CEK STOK
                     * ---------------------------------------------
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
                     * ---------------------------------------------
                     * CARI BOOK COPY BARU
                     * ---------------------------------------------
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
                     * ---------------------------------------------
                     * RESERVE COPY
                     * ---------------------------------------------
                     */

                    $bookCopy->update([
                        'status' =>
                            'reserved',
                    ]);


                    /*
                     * ---------------------------------------------
                     * HUBUNGKAN RESERVATION
                     * ---------------------------------------------
                     */

                    $reservation->update([
                        'book_copy_id' =>
                            $bookCopy->id,
                    ]);


                    /*
                     * ---------------------------------------------
                     * KURANGI STOK
                     * ---------------------------------------------
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
                        $newStatus,
                ]);
            }
        );


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        return redirect()
            ->route(
                'reservations.index'
            )
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
         * =====================================================
         * LOAD DATA
         * =====================================================
         */

        $reservation->load([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);


        /*
         * =====================================================
         * CEK BOOK COPY
         * =====================================================
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
         * =====================================================
         * RAK TARGET
         * =====================================================
         */

        $targetShelf =
            $reservation
                ->bookCopy
                ->shelf;


        if (
            !$targetShelf
        ) {

            abort(
                404,
                'Rak buku belum ditentukan.'
            );
        }


        /*
         * =====================================================
         * ZONA TARGET
         * =====================================================
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
         * =====================================================
         * LANTAI TARGET
         * =====================================================
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
         * =====================================================
         * SEMUA ZONA DI LANTAI
         * =====================================================
         */

        $zoneIds =
            LibraryZone::where(
                'library_floor_id',
                $targetFloor->id
            )
                ->pluck('id');


        /*
         * =====================================================
         * SEMUA RAK DI LANTAI
         * =====================================================
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
         * =====================================================
         * DATA BOOK COPY UNTUK 3D LOCATOR
         * =====================================================
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
                                            ??
                                            'Buku',

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
         * =====================================================
         * KIRIM KE VIEW LOCATOR
         * =====================================================
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

                /*
                 * =================================================
                 * KUNCI RESERVATION
                 * =================================================
                 */

                $reservation =
                    Reservation::lockForUpdate()
                        ->findOrFail(
                            $reservation->id
                        );


                /*
                 * =================================================
                 * RELEASE COPY + STOK
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
                            'selesai',
                        ]
                    )
                ) {

                    /*
                     * ---------------------------------------------
                     * RELEASE BOOK COPY
                     * ---------------------------------------------
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
                            $bookCopy
                            &&
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
                     * ---------------------------------------------
                     * KEMBALIKAN STOK
                     * ---------------------------------------------
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
            ->route(
                'reservations.index'
            )
            ->with(
                'success',
                'Reservasi berhasil dihapus.'
            );
    }
}