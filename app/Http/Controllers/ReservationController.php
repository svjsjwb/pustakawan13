<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Shelf;
use App\Models\LibraryZone;

class ReservationController extends Controller
{
    /**
     * Sinkronisasi status anggota berdasarkan aktivitas aktif.
     *
     * Aktif jika memiliki:
     * - peminjaman yang belum dikembalikan, atau
     * - reservasi yang masih berlaku.
     */
    private function syncMemberStatus(Member $member): void
    {
        $hasActiveBorrowing = Borrowing::where('member_id', $member->id)
            ->whereNull('returned_at')
            ->exists();

        $hasActiveReservation = Reservation::where('member_id', $member->id)
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now()->toDateString());
            })
            ->exists();

        $member->update([
            'status' => ($hasActiveBorrowing || $hasActiveReservation)
                ? 'aktif'
                : 'nonaktif',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR RESERVASI
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
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
         *
         * Gunakan kolom schema baru: judul_buku.
         */
        $books = Book::orderBy('judul_buku')->get();

        /*
         * =====================================================
         * QUERY RESERVASI + FILTER ADMIN
         * =====================================================
         */
        $query = Reservation::with([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);

        // Tentukan kolom batas waktu yang tersedia pada schema.
        $dueColumn = Schema::hasColumn('reservations', 'due_at')
            ? 'due_at'
            : (
                Schema::hasColumn('reservations', 'expires_at')
                    ? 'expires_at'
                    : 'reserved_at'
            );

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [
                    $endDate->copy()->startOfDay(),
                    $startDate->copy()->endOfDay(),
                ];
            }

            $query->whereBetween('reserved_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('reserved_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('reserved_at', '<=', $request->end_date);
        } elseif ($request->filled('year') && $request->filled('month')) {
            $query->whereYear($dueColumn, $request->year)
                ->whereMonth($dueColumn, $request->month);
        } else {
            $query->where(
                'reserved_at',
                '>=',
                now()->subMonth()->startOfDay()
            );
        }

        $reservations = $query
            ->latest('reserved_at')
            ->get();

        /*
         * =====================================================
         * KURSI YANG SUDAH BOOKING
         * =====================================================
         */
        $bookedSeats = Reservation::whereDate(
            'reserved_at',
            $selectedDate
        )
            ->whereIn('status', [
                'menunggu',
                'disetujui',
            ])
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();

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

            $reservation = Reservation::create([

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

            $this->syncMemberStatus(
                Member::findOrFail($validated['member_id'])
            );


            /*
             * =================================================
             * KURANGI STOK
             * =================================================
             */

            $book->decrement(
                'stok'
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
            $reservation,
            $request
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
                    'stok'
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
             *      Γåô
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
                    'stok'
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
                'rejection_reason' =>
                    $newStatus === 'ditolak' ? ($request->input('rejection_reason') ?? 'Ditolak oleh Admin') : $reservation->rejection_reason,
            ]);

            // Kirim notifikasi dan email ke user serta proses pemindahan ke tabel peminjaman buku
            if ($newStatus === 'disetujui') {
                // Pastikan atau cari BookCopy untuk peminjaman
                $bookCopy = null;
                if ($reservation->book_copy_id) {
                    $bookCopy = BookCopy::lockForUpdate()->find($reservation->book_copy_id);
                }
                if (!$bookCopy) {
                    $bookCopy = BookCopy::where('book_id', $reservation->book_id)
                        ->whereIn('status', ['available', 'reserved'])
                        ->lockForUpdate()
                        ->first();
                    if ($bookCopy) {
                        $reservation->update(['book_copy_id' => $bookCopy->id]);
                    }
                }
                if ($bookCopy) {
                    $bookCopy->update(['status' => 'borrowed']);
                }

                // Cari atau sinkronkan Member jika belum terhubung
                $member = null;
                if ($reservation->member_id) {
                    $member = Member::find($reservation->member_id);
                }
                if (!$member && $reservation->user_id) {
                    $user = $reservation->user;
                    if ($user) {
                        $member = Member::firstOrCreate(
                            ['email' => $user->email],
                            [
                                'name'    => $user->name,
                                'user_id' => $user->id,
                                'phone'   => '-',
                                'address' => '-',
                                'status'  => 'aktif',
                            ]
                        );
                        $reservation->update(['member_id' => $member->id]);
                    }
                }
                $memberId = $member?->id ?? $reservation->member_id;
                $userId   = $reservation->user_id ?? $member?->user_id;

                // Default durasi peminjaman 14 hari
                $borrowedAt = now()->toDateString();
                $dueAt      = now()->addDays(14)->toDateString();

                // Pindahkan data ke tabel peminjaman buku (borrowings & borrowing_details)
                $borrowing = Borrowing::where('reservation_id', $reservation->id)->first();
                if (!$borrowing) {
                    $borrowing = Borrowing::create([
                        'user_id'        => $userId,
                        'member_id'      => $memberId,
                        'reservation_id' => $reservation->id,
                        'book_id'        => $reservation->book_id,
                        'seat_number'    => $reservation->seat_number,
                        'borrowed_at'    => $borrowedAt,
                        'due_at'         => $dueAt,
                        'status'         => 'dipinjam',
                    ]);

                    BorrowingDetail::create([
                        'borrowing_id' => $borrowing->id,
                        'book_id'      => $reservation->book_id,
                        'book_copy_id' => $bookCopy?->id,
                        'quantity'     => 1,
                    ]);
                } else {
                    $borrowing->update([
                        'user_id'     => $userId,
                        'member_id'   => $memberId,
                        'book_id'     => $reservation->book_id,
                        'seat_number' => $reservation->seat_number,
                        'borrowed_at' => $borrowedAt,
                        'due_at'      => $dueAt,
                        'status'      => 'dipinjam',
                    ]);
                }

                $reservation->update([
                    'borrowing_id' => $borrowing->id,
                ]);

                NotificationService::reservationApproved($reservation);
                NotificationService::borrowingApproved($borrowing);
            } elseif (in_array($newStatus, ['ditolak', 'dibatalkan'])) {
                $existingBorrowing = Borrowing::where('reservation_id', $reservation->id)->first();
                if ($existingBorrowing && $existingBorrowing->status === 'dipinjam') {
                    $existingBorrowing->update(['status' => 'ditolak']);
                }

                $reason = $request->input('rejection_reason', 'Ditolak oleh Admin');
                NotificationService::reservationRejected($reservation, null, $reason);
            }

            // Sinkronisasi status member
            if ($reservation->member) {
                $this->syncMemberStatus($reservation->member);
            }
        });


        /*
         * =====================================================
         * REDIRECT
         * =====================================================
         */

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status reservasi berhasil diperbarui.',
                'status'  => $reservation->status,
            ]);
        }

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'Status reservasi berhasil diperbarui.'
            );
    }


    /**
     * Endpoint polling status reservasi untuk Admin (realtime update)
     */
    public function statusFeed()
    {
        $reservations = Reservation::with(['member', 'book'])
            ->latest()
            ->take(50)
            ->get();

        $data = $reservations->map(function ($r) {
            return [
                'id'          => $r->id,
                'member_name' => $r->member?->name ?? 'Anggota',
                'book_title'  => $r->book?->title ?? '-',
                'status'      => strtolower($r->status),
                'reserved_at' => $r->reserved_at ? $r->reserved_at->format('d/m/Y') : '-',
                'expires_at'  => $r->expires_at ? $r->expires_at->format('d/m/Y') : '-',
                'updated_at'  => $r->updated_at ? $r->updated_at->toISOString() : null,
            ];
        });

        return response()->json([
            'success'      => true,
            'total'        => Reservation::count(),
            'reservations' => $data,
        ]);
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
                        'stok'
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
