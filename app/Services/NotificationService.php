<?php

namespace App\Services;

use App\Mail\BorrowingApprovedMail;
use App\Mail\BorrowingRejectedMail;
use App\Mail\BorrowingSubmittedMail;
use App\Mail\ExtensionStatusMail;
use App\Mail\ReservationStatusMail;
use App\Mail\ReservationSubmittedMail;
use App\Models\AppNotification;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Helper to resolve User from Member or direct User relation.
     */
    protected static function resolveUser($source, ?User $explicitUser = null): ?User
    {
        if ($explicitUser) {
            return $explicitUser;
        }

        if (isset($source->user) && $source->user instanceof User) {
            return $source->user;
        }

        if (!empty($source->user_id)) {
            $user = User::find($source->user_id);
            if ($user) {
                return $user;
            }
        }

        $email = $source->member?->email ?? null;
        if ($email) {
            return User::where('email', $email)->first();
        }

        return null;
    }

    /**
     * Dispatch an email safely using queue so mail failure doesn't break the transaction.
     */
    protected static function queueMailSafely(string $email, $mailable): void
    {
        try {
            Mail::to($email)->queue($mailable);
        } catch (\Throwable $e) {
            Log::warning("Failed to queue email to {$email}: " . $e->getMessage());
        }
    }

    /**
     * Peminjaman diajukan oleh user.
     */
    public static function borrowingSubmitted(Borrowing $borrowing, ?User $user = null): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_submitted',
            'Pengajuan Peminjaman Berhasil',
            "Pengajuan peminjaman untuk buku \"{$bookTitle}\" telah dikirim dan menunggu konfirmasi.",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('borrowing')) {
            self::queueMailSafely($user->email, new BorrowingSubmittedMail($borrowing, $user));
        }
    }

    /**
     * Peminjaman disetujui oleh admin/petugas.
     */
    public static function borrowingApproved(Borrowing $borrowing, ?User $user = null): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';
        $dueAt = $borrowing->due_at ? $borrowing->due_at->format('d M Y') : '-';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_approved',
            'Peminjaman Disetujui',
            "Peminjaman buku \"{$bookTitle}\" telah disetujui. Batas pengembalian: {$dueAt}.",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('borrowing')) {
            self::queueMailSafely($user->email, new BorrowingApprovedMail($borrowing, $user));
        }
    }

    /**
     * Peminjaman ditolak oleh admin.
     */
    public static function borrowingRejected(Borrowing $borrowing, ?User $user = null, string $reason = 'Ditolak oleh Admin'): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_rejected',
            'Peminjaman Ditolak',
            "Peminjaman buku \"{$bookTitle}\" ditolak. Alasan: {$reason}",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('borrowing')) {
            self::queueMailSafely($user->email, new BorrowingRejectedMail($borrowing, $user, $reason));
        }
    }

    /**
     * Reservasi diajukan oleh user.
     */
    public static function reservationSubmitted(Reservation $reservation, ?User $user = null): void
    {
        $reservation->loadMissing(['book', 'member']);
        $user = self::resolveUser($reservation, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $reservation->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'reservation_submitted',
            'Reservasi Berhasil Diajukan',
            "Pengajuan reservasi buku \"{$bookTitle}\" berhasil diajukan dan sedang menunggu persetujuan.",
            ['reservation_id' => $reservation->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('reservation')) {
            self::queueMailSafely($user->email, new ReservationSubmittedMail($reservation, $user));
        }
    }

    /**
     * Reservasi disetujui oleh admin.
     */
    public static function reservationApproved(Reservation $reservation, ?User $user = null): void
    {
        $reservation->loadMissing(['book', 'member']);
        $user = self::resolveUser($reservation, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $reservation->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'reservation_approved',
            'Reservasi Disetujui',
            "Reservasi Anda untuk buku \"{$bookTitle}\" telah disetujui.",
            ['reservation_id' => $reservation->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('reservation')) {
            self::queueMailSafely($user->email, new ReservationStatusMail($reservation, 'disetujui'));
        }
    }

    /**
     * Reservasi ditolak oleh admin.
     */
    public static function reservationRejected(Reservation $reservation, ?User $user = null, string $reason = 'Ditolak oleh Admin'): void
    {
        $reservation->loadMissing(['book', 'member']);
        $user = self::resolveUser($reservation, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $reservation->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'reservation_rejected',
            'Reservasi Ditolak',
            "Reservasi Anda untuk buku \"{$bookTitle}\" ditolak. Alasan: {$reason}",
            ['reservation_id' => $reservation->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('reservation')) {
            self::queueMailSafely($user->email, new ReservationStatusMail($reservation, 'ditolak'));
        }
    }

    /**
     * Perpanjangan disetujui admin.
     */
    public static function extensionApproved(Borrowing $borrowing, ?User $user = null): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';
        $dueAt = $borrowing->due_at ? $borrowing->due_at->format('d M Y') : '-';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_extension_approved',
            'Perpanjangan Peminjaman Disetujui',
            "Permintaan perpanjangan untuk buku \"{$bookTitle}\" telah disetujui. Batas baru: {$dueAt}.",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('extension')) {
            self::queueMailSafely($user->email, new ExtensionStatusMail($borrowing, $user, 'disetujui'));
        }
    }

    /**
     * Perpanjangan ditolak admin.
     */
    public static function extensionRejected(Borrowing $borrowing, ?User $user = null, string $notes = 'Ditolak oleh Admin'): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_extension_rejected',
            'Perpanjangan Peminjaman Ditolak',
            "Permintaan perpanjangan untuk buku \"{$bookTitle}\" ditolak. Catatan: {$notes}",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('extension')) {
            self::queueMailSafely($user->email, new ExtensionStatusMail($borrowing, $user, 'ditolak', $notes));
        }
    }

    /**
     * Perpanjangan mandiri oleh user (langsung aktif).
     */
    public static function extensionSelfApproved(Borrowing $borrowing, ?User $user = null): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);
        if (!$user) {
            return;
        }

        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';
        $dueAt = $borrowing->due_at ? $borrowing->due_at->format('d M Y') : '-';

        // 1. In-app notification
        AppNotification::notifyUser(
            $user->id,
            'borrowing_extended',
            'Perpanjangan Berhasil',
            "Peminjaman buku \"{$bookTitle}\" berhasil diperpanjang hingga {$dueAt}.",
            ['borrowing_id' => $borrowing->id]
        );

        // 2. Email notification
        if ($user->email && $user->notificationsAllowed('extension')) {
            self::queueMailSafely($user->email, new ExtensionStatusMail($borrowing, $user, 'mandiri'));
        }
    }
}
