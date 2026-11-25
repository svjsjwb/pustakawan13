<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
use App\Services\MemberStatusService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationCancellationService
{
    /**
     * Membatalkan otomatis reservasi yang berstatus MENUNGGU dan melewati batas waktu persetujuan admin (expires_at < hari ini).
     *
     * Alur yang benar:
     * - User mengajukan reservasi → status 'menunggu'.
     * - Admin memiliki batas waktu 3 hari (expires_at = created_at + 3 hari) untuk menyetujui atau menolak.
     * - Jika admin MENYETUJUI → data otomatis menjadi data peminjaman (Borrowing), reservasi selesai.
     * - Jika admin TIDAK menyetujui dalam 3 hari → sistem membatalkan otomatis dengan alasan kadaluarsa.
     * - Hanya reservasi berstatus 'menunggu' yang dibatalkan otomatis.
     * - Reservasi 'disetujui' / 'siap_diambil' TIDAK dibatalkan otomatis (sudah menjadi peminjaman).
     *
     * @return int Jumlah reservasi yang berhasil dibatalkan
     */
    public static function cancelExpiredReservations(): int
    {
        $today = Carbon::today()->toDateString();

        // Hanya batalkan reservasi 'menunggu' yang admin belum setujui melewati expires_at
        $expiredReservationIds = Reservation::query()
            ->where('status', 'menunggu')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', $today)
            ->pluck('id');

        if ($expiredReservationIds->isEmpty()) {
            return 0;
        }

        $cancelledCount = 0;

        foreach ($expiredReservationIds as $reservationId) {
            try {
                $cancelled = DB::transaction(function () use ($reservationId) {
                    $reservation = Reservation::whereKey($reservationId)
                        ->lockForUpdate()
                        ->first();

                    // Pastikan masih berstatus 'menunggu' (belum diproses admin)
                    if (!$reservation || $reservation->status !== 'menunggu') {
                        return null;
                    }

                    // 1. Lepaskan eksemplar buku (BookCopy) kembali menjadi available
                    if ($reservation->book_copy_id) {
                        $bookCopy = BookCopy::where('id', $reservation->book_copy_id)
                            ->lockForUpdate()
                            ->first();

                        if ($bookCopy && $bookCopy->status === 'reserved') {
                            $bookCopy->update(['status' => 'available']);
                        }
                    }

                    // 2. Kembalikan stok buku (+1) ke katalog
                    if ($reservation->book_id) {
                        $book = Book::where('id', $reservation->book_id)
                            ->lockForUpdate()
                            ->first();

                        if ($book) {
                            $book->increment('stok');
                        }
                    }

                    // 3. Update status reservasi menjadi dibatalkan karena admin tidak menyetujui dalam 3 hari
                    $reason = 'Reservasi dibatalkan otomatis oleh sistem karena admin tidak menyetujui dalam batas waktu 3 hari.';
                    $reservation->update([
                        'status' => 'dibatalkan',
                        'rejection_reason' => $reason,
                        'cancellation_requested_at' => null,
                    ]);

                    // 4. Sinkronisasi status keanggotaan
                    if ($reservation->member_id) {
                        $member = Member::find($reservation->member_id);
                        if ($member) {
                            app(MemberStatusService::class)->sync($member);
                        }
                    }

                    return $reservation;
                });

                if ($cancelled) {
                    $cancelled->loadMissing(['book', 'member', 'user']);

                    // Kirim notifikasi in-app dan email ke user & admin
                    NotificationService::reservationAutoCancelled($cancelled);

                    $cancelledCount++;
                }
            } catch (\Throwable $e) {
                Log::error("Gagal membatalkan otomatis reservasi ID {$reservationId}: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }

        return $cancelledCount;
    }
}
