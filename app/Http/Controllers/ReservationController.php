<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\LibraryZone;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Shelf;
use App\Services\MemberStatusService;
use App\Services\NotificationService;
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
         * View reservations/index tidak me-render seluruh daftar $books.
         * Gunakan koleksi kosong untuk mencegah overhead memori 15.000+ buku.
         */
        $books = collect();

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

        $reservationRequests = Reservation::with(['member', 'book'])
            ->where(function ($query) {
                $query->where('status', 'menunggu')
                    ->orWhereNotNull('cancellation_requested_at');
            })
            ->orderByRaw('COALESCE(cancellation_requested_at, created_at) DESC')
            ->orderByDesc('id')
            ->get();

        // Kolom batas waktu reservasi resmi pada skema adalah expires_at.
        $dueColumn = 'expires_at';

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
            $query
                ->whereYear($dueColumn, $request->year)
                ->whereMonth($dueColumn, $request->month);
        } else {
            $query->where(
                'reserved_at',
                '>=',
                now()->subMonth()->startOfDay()
            );
        }

        $reservations = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
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
                'reservationRequests',
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
                        'Kursi '
                            . $validated['seat_number']
                            . ' sudah dipesan pada tanggal tersebut.'
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
                        'Kursi '
                            . $validated['seat_number']
                            . ' baru saja dipesan oleh pengguna lain.'
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

            app(MemberStatusService::class)->sync(
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
     * |--------------------------------------------------------------------------
     * | UPDATE STATUS RESERVASI
     * |--------------------------------------------------------------------------
     */


    public function updateStatus(Request $request, Reservation $reservation)
    {
        // Admin hanya boleh menyetujui atau menolak reservasi menunggu.
        $validated = $request->validate([
            'status' => [
                'required',
                'in:disetujui,ditolak,dibatalkan',
            ],
            'rejection_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $newStatus = $validated['status'];

        // Proses perubahan status dalam satu transaksi.
        $updatedData = DB::transaction(function () use (
            $reservation,
            $newStatus,
            $validated
        ) {
            // Kunci data reservasi agar tidak diproses bersamaan.
            $lockedReservation = Reservation::whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Permintaan pembatalan harus diselesaikan melalui
            // endpoint khusus, bukan aksi perubahan status biasa.
            if ($lockedReservation->cancellation_requested_at) {
                abort(
                    409,
                    'Selesaikan permintaan pembatalan melalui aksi Setujui atau Tolak Pembatalan.'
                );
            }

            // Admin tidak boleh membatalkan reservasi melalui aksi biasa.
            if ($newStatus === 'dibatalkan') {
                abort(
                    422,
                    'Pembatalan reservasi harus melalui proses permintaan pembatalan.'
                );
            }

            // Reservasi hanya boleh diproses jika masih menunggu.
            if ($lockedReservation->status !== 'menunggu') {
                abort(
                    422,
                    'Hanya reservasi dengan status menunggu yang dapat diproses.'
                );
            }

            // Reservasi wajib memiliki eksemplar yang sudah dialokasikan.
            if (!$lockedReservation->book_copy_id) {
                abort(
                    422,
                    'Reservasi tidak memiliki eksemplar buku.'
                );
            }

            // Kunci eksemplar yang memang terhubung dengan reservasi.
            $bookCopy = BookCopy::where('id', $lockedReservation->book_copy_id)
                ->where('book_id', $lockedReservation->book_id)
                ->lockForUpdate()
                ->first();

            if (!$bookCopy || $bookCopy->status !== 'reserved') {
                abort(
                    422,
                    'Eksemplar buku yang dialokasikan tidak tersedia atau statusnya tidak valid.'
                );
            }

            // Cari anggota yang terhubung dengan reservasi.
            $member = null;

            if ($lockedReservation->member_id) {
                $member = Member::find($lockedReservation->member_id);
            }

            // Jika member_id belum tersedia, coba cari melalui user.
            if (!$member && $lockedReservation->user_id) {
                $user = $lockedReservation->user;

                if ($user) {
                    $member = $user->member;
                }
            }

            $memberId = $member?->id ?? $lockedReservation->member_id;
            $userId = $lockedReservation->user_id ?? $member?->user_id;

            // Pastikan anggota tersedia sebelum membuat peminjaman.
            if (!$memberId) {
                abort(
                    422,
                    'Data anggota tidak ditemukan untuk reservasi ini.'
                );
            }

            // Pastikan reservasi belum pernah menghasilkan peminjaman.
            // Jangan menggunakan ulang transaksi peminjaman lama.
            if ($lockedReservation->borrowing_id) {
                abort(
                    409,
                    'Reservasi ini sudah terhubung dengan transaksi peminjaman.'
                );
            }

            // Jika reservasi ditolak.
            if ($newStatus === 'ditolak') {
                // Lepaskan eksemplar yang sebelumnya berstatus reserved.
                $bookCopy->update([
                    'status' => 'available',
                ]);

                // Kembalikan stok karena reservasi tidak dilanjutkan.
                Book::whereKey($lockedReservation->book_id)
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->increment('stok');

                $rejectionReason = $validated['rejection_reason']
                    ?? 'Ditolak oleh Admin';

                $lockedReservation->update([
                    'status' => 'ditolak',
                    'rejection_reason' => $rejectionReason,
                    'member_id' => $memberId,
                ]);

                // Sinkronisasi status anggota.
                app(MemberStatusService::class)->sync(
                    Member::findOrFail($memberId)
                );

                return [
                    'reservation_id' => $lockedReservation->id,
                    'borrowing_id' => null,
                    'member_id' => $memberId,
                    'status' => 'ditolak',
                    'rejection_reason' => $rejectionReason,
                ];
            }

            // Jika reservasi disetujui.
            // Eksemplar yang dialokasikan berubah menjadi borrowed.
            $bookCopy->update([
                'status' => 'borrowed',
            ]);

            // Tanggal peminjaman dan batas pengembalian.
            $borrowedAt = now()->toDateString();
            $dueAt = now()->addDays(14)->toDateString();

            // Buat satu transaksi peminjaman baru.
            $borrowing = Borrowing::create([
                'user_id' => $userId,
                'member_id' => $memberId,
                'book_id' => $lockedReservation->book_id,
                'seat_number' => $lockedReservation->seat_number,
                'borrowed_at' => $borrowedAt,
                'due_at' => $dueAt,
                'status' => 'dipinjam',
            ]);

            // Hubungkan satu detail dengan eksemplar yang benar.
            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'book_id' => $lockedReservation->book_id,
                'book_copy_id' => $bookCopy->id,
                'quantity' => 1,
            ]);

            // Update reservasi dan hubungkan dengan peminjaman.
            $lockedReservation->update([
                'status' => 'disetujui',
                'member_id' => $memberId,
                'borrowing_id' => $borrowing->id,
            ]);

            // Sinkronisasi status anggota.
            app(MemberStatusService::class)->sync(
                Member::findOrFail($memberId)
            );

            return [
                'reservation_id' => $lockedReservation->id,
                'borrowing_id' => $borrowing->id,
                'member_id' => $memberId,
                'status' => 'disetujui',
                'rejection_reason' => null,
            ];
        });

        // Kirim notifikasi setelah transaksi berhasil.
        $updatedReservation = Reservation::with([
            'book',
            'member',
            'user',
        ])->findOrFail($updatedData['reservation_id']);

        if ($updatedData['status'] === 'disetujui') {
            $borrowing = Borrowing::findOrFail(
                $updatedData['borrowing_id']
            );

            NotificationService::reservationApproved(
                $updatedReservation
            );

            NotificationService::borrowingApproved(
                $borrowing
            );

            $successMessage = 'Reservasi berhasil disetujui dan peminjaman berhasil dibuat.';
        } else {
            NotificationService::reservationRejected(
                $updatedReservation,
                null,
                $updatedData['rejection_reason']
            );

            $successMessage = 'Reservasi berhasil ditolak.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'status' => $updatedData['status'],
            ]);
        }

        return redirect()
            ->route('reservations.index')
            ->with('success', $successMessage);
    }


    public function resolveCancellationRequest(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
        ]);

        $approved = $validated['decision'] === 'approve';

        DB::transaction(function () use ($reservation, $approved) {
            $lockedReservation = Reservation::lockForUpdate()
                ->findOrFail($reservation->id);

            if (!$lockedReservation->cancellation_requested_at) {
                abort(409, 'Tidak ada permintaan pembatalan yang menunggu.');
            }

            if (!in_array($lockedReservation->status, ['disetujui', 'siap_diambil'], true)) {
                abort(422, 'Status reservasi tidak valid untuk keputusan pembatalan.');
            }

            // Jika admin menolak, reservasi tetap disetujui.
            // Penanda pengajuan pertama TIDAK boleh dihapus.
            if (!$approved) {
                $lockedReservation->update([
                    'cancellation_requested_at' => null,
                ]);

                return;
            }

            // Kunci dan periksa eksemplar buku.
            if (!$lockedReservation->book_copy_id) {
                abort(409, 'Eksemplar buku pada reservasi tidak ditemukan.');
            }

            $bookCopy = BookCopy::lockForUpdate()
                ->findOrFail($lockedReservation->book_copy_id);

            if (!in_array($bookCopy->status, ['reserved', 'borrowed'], true)) {
                abort(409, 'Status eksemplar buku tidak valid untuk pembatalan.');
            }

            // Jika ada peminjaman aktif, ubah menjadi dibatalkan.
            if ($lockedReservation->borrowing_id) {
                $borrowing = Borrowing::lockForUpdate()
                    ->findOrFail($lockedReservation->borrowing_id);

                if ($borrowing->status !== 'dipinjam') {
                    abort(409, 'Peminjaman tidak lagi berstatus aktif.');
                }

                $borrowing->update([
                    'status' => 'dibatalkan',
                ]);
            }

            // Kembalikan eksemplar ke persediaan.
            $bookCopy->update([
                'status' => 'available',
            ]);

            // Tambahkan kembali stok buku satu kali.
            Book::lockForUpdate()
                ->findOrFail($lockedReservation->book_id)
                ->increment('stok');

            // Batalkan reservasi.
            // cancellation_request_used_at tetap dipertahankan.
            $lockedReservation->update([
                'status' => 'dibatalkan',
                'cancellation_requested_at' => null,
            ]);

            if ($lockedReservation->member) {
                app(MemberStatusService::class)->sync($lockedReservation->member);
            }
        });

        $reservation->refresh()->loadMissing(['book', 'member']);

        $userId = $reservation->user_id ?? $reservation->member?->user_id;

        if ($userId) {
            $bookTitle = $reservation->book?->title ?? 'Buku';

            AppNotification::notifyUser(
                $userId,
                $approved ? 'reservation_cancelled' : 'reservation_cancellation_rejected',
                $approved ? 'Pembatalan Reservasi Disetujui' : 'Pembatalan Reservasi Ditolak',
                $approved
                    ? "Admin menyetujui pembatalan reservasi buku \"{$bookTitle}\"."
                    : "Admin menolak permintaan pembatalan buku \"{$bookTitle}\". Status reservasi tetap Disetujui dan proses pengambilan buku dapat dilanjutkan.",
                [
                    'reservation_id' => $reservation->id,
                    'decision' => $approved ? 'approved' : 'rejected',
                ]
            );
        }

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                $approved
                    ? 'Permintaan pembatalan reservasi disetujui.'
                    : 'Permintaan pembatalan reservasi ditolak.'
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
                'id' => $r->id,
                'member_name' => $r->member?->name ?? 'Anggota',
                'book_title' => $r->book?->title ?? '-',
                'status' => strtolower($r->status),
                'reserved_at' => $r->reserved_at ? $r->reserved_at->format('d/m/Y') : '-',
                'expires_at' => $r->expires_at ? $r->expires_at->format('d/m/Y') : '-',
                'updated_at' => $r->updated_at ? $r->updated_at->toISOString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => Reservation::count(),
            'reservations' => $data,
        ]);
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
