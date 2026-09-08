<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\LibraryZone;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Shelf;
use App\Models\User;
use App\Notifications\ReservationStatusNotification;
use App\Services\RealtimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReservationController extends Controller
{
    /*
     * |--------------------------------------------------------------------------
     * | DAFTAR RESERVASI
     * |--------------------------------------------------------------------------
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

        $books = Book::orderBy('judul_buku')->get();

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
            'user',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);

        /*
         * =====================================================
         * TENTUKAN KOLOM TANGGAL
         * =====================================================
         *
         * Gunakan 'reserved_at' sebagai basis agar semua reservasi
         * (termasuk reservasi online pengguna yang expires_at = null)
         * otomatis masuk dan tampil di tabel admin.
         */

        $dateColumn = 'reserved_at';

        /*
         * =====================================================
         * FILTER RENTANG TANGGAL
         * =====================================================
         */

        if (
            $request->filled('start_date') &&
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
            $request->filled('month') &&
            $request->filled('year')
        ) {
            /*
             * Filter bulan berdasarkan reserved_at.
             */

            $query
                ->whereYear(
                    $dateColumn,
                    $request->year
                )
                ->whereMonth(
                    $dateColumn,
                    $request->month
                );
        } else {
            /*
             * Default:
             * tampilkan data dalam 1 bulan terakhir,
             * DAN selalu sertakan reservasi yang masih 'menunggu' atau 'disetujui'
             * agar permintaan reservasi baru dari user langsung terlihat.
             */

            $query->where(function ($q) use ($dateColumn) {
                $q->where(
                    $dateColumn,
                    '>=',
                    now()->subMonth()->startOfDay()
                )->orWhereIn('status', ['menunggu', 'disetujui']);
            });
        }

        /*
         * =====================================================
         * AMBIL RESERVASI
         * =====================================================
         */

        $reservations = $query
            ->latest('reserved_at')
            ->latest('id')
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
     * |--------------------------------------------------------------------------
     * | SIMPAN RESERVASI
     * |--------------------------------------------------------------------------
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
                        'Kursi '
                            . $validated['seat_number']
                            . ' sudah dipesan oleh pengguna lain pada tanggal tersebut.'
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
                            'Kursi '
                                . $validated['seat_number']
                                . ' baru saja dipesan oleh pengguna lain.'
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
                            ?? $validated['reserved_at'];
                }

                /*
                 * =================================================
                 * BUAT RESERVASI
                 * =================================================
                 */

                $createdReservation = Reservation::create(
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

                $book->decrement('stok');
            }
        );

        // Broadcast real-time event untuk Admin
        if (isset($createdReservation) && $createdReservation) {
            $createdReservation->load(['member', 'book']);
            RealtimeService::publish('reservation.created', [
                'id'                    => $createdReservation->id,
                'user_id'               => $createdReservation->user_id,
                'member_id'             => $createdReservation->member_id,
                'member_name'           => $createdReservation->member?->name ?? 'Anggota',
                'is_online_user'        => false,
                'book_id'               => $createdReservation->book_id,
                'book_title'            => $createdReservation->book?->title ?? $createdReservation->book?->judul_buku ?? '-',
                'reserved_at'           => $createdReservation->reserved_at ? $createdReservation->reserved_at->toDateString() : now()->toDateString(),
                'reserved_at_formatted' => $createdReservation->reserved_at ? $createdReservation->reserved_at->format('d/m/Y') : now()->format('d/m/Y'),
                'expires_at'            => $createdReservation->expires_at ? $createdReservation->expires_at->format('d/m/Y') : null,
                'status'                => 'menunggu',
                'status_label'          => 'Menunggu',
                'seat_number'           => $createdReservation->seat_number,
            ]);
        }

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
     * |--------------------------------------------------------------------------
     * | UPDATE STATUS RESERVASI
     * |--------------------------------------------------------------------------
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

        $newStatus = $validated['status'];

        /*
         * =====================================================
         * AC-4: PENCEGAHAN STOK MINUS
         * Validasi stok buku SEBELUM transaksi dijalankan.
         * Jika stok = 0, batalkan proses persetujuan.
         * =====================================================
         */

        if ($newStatus === 'disetujui') {
            $book = Book::find($reservation->book_id);

            if (!$book || $book->stok < 1) {
                return redirect()
                    ->route('reservations.index')
                    ->with(
                        'error',
                        'Stok buku "' . ($book->judul_buku ?? $book->title ?? 'Tidak Diketahui') . '" habis (0). Persetujuan reservasi dibatalkan.'
                    );
            }
        }

        /*
         * =====================================================
         * AC-1: SIMPAN USER_ID PEMESAN ASLI
         * Tangkap user_id sebelum mutasi apapun agar tidak
         * pernah ter-override oleh ID Admin yang sedang login.
         * =====================================================
         */

        $originalUserId = $reservation->user_id;

        /*
         * =====================================================
         * AC-3: DATABASE TRANSACTION
         * Seluruh operasi dibungkus DB::transaction agar jika
         * insert ke pinjam_buku gagal, update status reservasi
         * otomatis di-rollback.
         * =====================================================
         */

        $createdBorrowing = null;
        $borrowedAt = now();
        $dueAt = now()->addDays(14);

        try {
            DB::transaction(
                function () use (
                    $validated,
                    $reservation,
                    $originalUserId,
                    &$createdBorrowing,
                    $borrowedAt,
                    $dueAt
                ) {
                    $newStatus = $validated['status'];

                    /*
                     * Kunci reservation.
                     */

                    $reservation =
                        Reservation::lockForUpdate()
                            ->findOrFail(
                                $reservation->id
                            );

                    /*
                     * AC-1: Pastikan user_id tetap milik pemesan asli
                     */
                    $reservation->user_id = $originalUserId;

                    $oldStatus =
                        $reservation->status;

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
                        ) &&
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
                         * ---------------------------------------------
                         * KEMBALIKAN STOK
                         * ---------------------------------------------
                         */

                        $book =
                            Book::lockForUpdate()
                                ->findOrFail(
                                    $reservation->book_id
                                );

                        $book->increment('stok');
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
                        ) &&
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
                            $book->stok < 1
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
                         * (AC-1: Selalu sertakan user_id asli)
                         * ---------------------------------------------
                         */

                        $reservation->book_copy_id = $bookCopy->id;
                        $reservation->user_id = $originalUserId;
                        $reservation->save();

                        /*
                         * ---------------------------------------------
                         * KURANGI STOK
                         * ---------------------------------------------
                         */

                        $book->decrement('stok');
                    }

                    /*
                     * =================================================
                     * AC-2: OTOMASI PEMINJAMAN SAAT DISETUJUI
                     * Saat status berubah ke 'disetujui', otomatis
                     * insert baris baru ke tabel pinjam_buku (borrowings).
                     * =================================================
                     */

                    if ($newStatus === 'disetujui') {
                        // Pastikan member terhubung (jika reservasi dibuat user online)
                        if (!$reservation->member_id && $reservation->user_id) {
                            $user = $reservation->user;
                            if ($user) {
                                $member = Member::where('user_id', $user->id)
                                    ->orWhere('email', $user->email)
                                    ->orWhere('name', $user->name)
                                    ->first();
                                if (!$member) {
                                    $member = Member::create([
                                        'user_id'  => $user->id,
                                        'name'     => $user->name,
                                        'email'    => $user->email,
                                        'phone'    => $user->phone ?: '-',
                                        'division' => 'Anggota',
                                        'status'   => 'Aktif',
                                    ]);
                                }
                                $reservation->member_id = $member->id;
                                $reservation->user_id = $originalUserId; // AC-1
                                $reservation->save();
                            }
                        }

                        // Pastikan book copy ada dan statusnya berubah menjadi 'borrowed'
                        if ($reservation->book_copy_id) {
                            $bookCopy = BookCopy::lockForUpdate()->find($reservation->book_copy_id);
                            if ($bookCopy) {
                                $bookCopy->update(['status' => 'borrowed']);
                            }
                        } else {
                            $bookCopy = BookCopy::where('book_id', $reservation->book_id)
                                ->whereIn('status', ['available', 'tersedia'])
                                ->lockForUpdate()
                                ->first();
                            if ($bookCopy) {
                                $bookCopy->update(['status' => 'borrowed']);
                                $reservation->book_copy_id = $bookCopy->id;
                                $reservation->user_id = $originalUserId; // AC-1
                                $reservation->save();
                            }
                        }

                        /*
                         * -------------------------------------------------
                         * AC-2: Pemetaan Data (reservasi_buku → pinjam_buku)
                         *
                         * reservation_id ← reservasi_buku.id
                         * user_id        ← reservasi_buku.user_id (pemesan asli)
                         * book_id        ← reservasi_buku.buku_id
                         * borrowed_at    ← Tanggal saat ini
                         * due_at         ← Tanggal saat ini + 14 hari
                         * status         ← 'dipinjam'
                         * -------------------------------------------------
                         */
                        if (!$reservation->borrowing_id && $reservation->member_id) {
                            $createdBorrowing = Borrowing::create([
                                'member_id'      => $reservation->member_id,
                                'user_id'        => $originalUserId,        // AC-1 & AC-2: ID pemesan asli
                                'reservation_id' => $reservation->id,       // AC-2: id_reservasi
                                'book_id'        => $reservation->book_id,  // AC-2: buku_id
                                'borrowed_at'    => $borrowedAt->toDateString(),  // AC-2: tgl_pinjam
                                'due_at'         => $dueAt->toDateString(),       // AC-2: tgl_harus_kembali
                                'status'         => 'dipinjam',             // AC-2: status_pinjam
                                'seat_number'    => $reservation->seat_number,
                            ]);

                            BorrowingDetail::create([
                                'borrowing_id' => $createdBorrowing->id,
                                'book_id'      => $reservation->book_id,
                                'book_copy_id' => $reservation->book_copy_id,
                                'quantity'     => 1,
                            ]);

                            $reservation->borrowing_id = $createdBorrowing->id;
                        } elseif ($reservation->borrowing_id) {
                            $createdBorrowing = Borrowing::find($reservation->borrowing_id);
                        }
                    }

                    /*
                     * =================================================
                     * UPDATE STATUS
                     * AC-1: Selalu pastikan user_id = pemesan asli
                     * =================================================
                     */

                    $reservation->status = $newStatus;
                    $reservation->user_id = $originalUserId; // AC-1: Jaminan akhir
                    $reservation->save();

                    /*
                     * =================================================
                     * KIRIM NOTIFIKASI KE USER (JIKA ALLOW NOTIFICATION)
                     * =================================================
                     */
                    if (in_array($newStatus, ['disetujui', 'ditolak']) && $oldStatus !== $newStatus) {
                        $targetUser = $reservation->user;
                        if (!$targetUser && $reservation->member) {
                            $targetUser = User::where('id', $reservation->member->user_id)
                                ->orWhere('email', $reservation->member->email)
                                ->first();
                        }

                        if ($targetUser && $targetUser->is_notification_enabled) {
                            $targetUser->notify(new ReservationStatusNotification($reservation, $newStatus));
                        }
                    }

                    /*
                     * =================================================
                     * BROADCAST REAL-TIME VIA SSE
                     * =================================================
                     */
                    $reservation->load(['member', 'book']);
                    $bookTitle = $reservation->book?->title ?? $reservation->book?->judul_buku ?? 'Buku';
                    $memberName = $reservation->member?->name ?? $reservation->user?->name ?? 'Anggota';

                    if ($newStatus === 'disetujui') {
                        RealtimeService::publish('reservation.approved', [
                            'id'           => $reservation->id,
                            'user_id'      => $originalUserId,
                            'status'       => 'disetujui',
                            'status_label' => 'Dipinjam/Siap Diambil',
                            'book_title'   => $bookTitle,
                            'member_name'  => $memberName,
                            'borrowed_at'  => $borrowedAt->format('d/m/Y'),
                            'due_at'       => $dueAt->format('d/m/Y'),
                        ]);

                        if ($createdBorrowing) {
                            RealtimeService::publish('borrowing.created', [
                                'id'                    => $createdBorrowing->id,
                                'member_id'             => $createdBorrowing->member_id,
                                'member_name'           => $memberName,
                                'book_id'               => $reservation->book_id,
                                'book_title'            => $bookTitle,
                                'borrowed_at'           => $createdBorrowing->borrowed_at->toDateString(),
                                'borrowed_at_formatted' => $createdBorrowing->borrowed_at->format('d/m/Y'),
                                'due_at'                => $createdBorrowing->due_at->toDateString(),
                                'due_at_formatted'      => $createdBorrowing->due_at->format('d/m/Y'),
                                'status'                => 'dipinjam',
                                'display_status'        => 'Sedang Dipinjam',
                                'year'                  => (int) $createdBorrowing->borrowed_at->year,
                                'month'                 => (int) $createdBorrowing->borrowed_at->month,
                            ]);
                        }
                    } else {
                        RealtimeService::publish('reservation.updated', [
                            'id'           => $reservation->id,
                            'user_id'      => $originalUserId,
                            'status'       => $newStatus,
                            'status_label' => ucfirst($newStatus),
                        ]);
                    }
                }
            );
        } catch (\Exception $e) {
            /*
             * AC-3: Jika terjadi exception di dalam transaksi
             * (misalnya insert ke pinjam_buku gagal), maka
             * seluruh perubahan otomatis di-rollback oleh
             * DB::transaction.
             */
            return redirect()
                ->route('reservations.index')
                ->with(
                    'error',
                    'Gagal memproses reservasi: ' . $e->getMessage()
                );
        }

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
     * |--------------------------------------------------------------------------
     * | BOOK LOCATOR
     * |--------------------------------------------------------------------------
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
                    function ($shelf) use ($reservation) {
                        return $shelf
                            ->copies
                            ->map(
                                function ($copy) use (
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
                                            (int) $copy->section,
                                        'row' =>
                                            (int) $copy->row,
                                        'column' =>
                                            (int) $copy->column,
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
     * |--------------------------------------------------------------------------
     * | HAPUS RESERVASI
     * |--------------------------------------------------------------------------
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
                     * ---------------------------------------------
                     * KEMBALIKAN STOK
                     * ---------------------------------------------
                     */

                    $book =
                        Book::lockForUpdate()
                            ->findOrFail(
                                $reservation->book_id
                            );

                    $book->increment('stok');
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
