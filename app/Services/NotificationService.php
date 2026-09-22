<?php

namespace App\Services;

use App\Mail\AdminActivityAlertMail;
use App\Mail\BorrowingApprovedMail;
use App\Mail\BorrowingRejectedMail;
use App\Mail\BorrowingReturnedMail;
use App\Mail\BorrowingSubmittedMail;
use App\Mail\ExtensionStatusMail;
use App\Mail\FirstLoginMail;
use App\Mail\ReservationStatusMail;
use App\Mail\ReservationSubmittedMail;
use App\Mail\WelcomeMail;
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
     * Dispatch email alert to all active administrators.
     */
    public static function notifyAdmins($mailable): void
    {
        $adminEmails = User::where('role', 'admin')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->toArray();

        $configuredAdmin = config('mail.admin_email', env('ADMIN_NOTIFICATION_EMAIL'));
        if (!empty($configuredAdmin) && !in_array($configuredAdmin, $adminEmails)) {
            $adminEmails[] = $configuredAdmin;
        }

        if (empty($adminEmails)) {
            $fromAddress = config('mail.from.address');
            if ($fromAddress && !str_contains($fromAddress, 'example.com')) {
                $adminEmails[] = $fromAddress;
            }
        }

        foreach (array_unique($adminEmails) as $email) {
            self::queueMailSafely($email, $mailable);
        }
    }

    /**
     * User baru berhasil mendaftar akun.
     */
    public static function userRegistered(User $user): void
    {
        // 1. Email selamat datang ke User baru
        if ($user->email) {
            self::queueMailSafely($user->email, new WelcomeMail($user));
        }

        // 2. In-app notification ke Admin
        AppNotification::notifyAdmin(
            'user_registered',
            'Pendaftaran Pengguna Baru',
            "Pengguna baru {$user->name} ({$user->email}) telah mendaftar.",
            ['user_id' => $user->id]
        );

        // 3. Email alert ke Admin
        self::notifyAdmins(new AdminActivityAlertMail(
            eventTitle: 'Pendaftaran Pengguna Baru',
            messageText: "Pengguna baru telah mendaftar di sistem perpustakaan.",
            details: [
                'Nama Pengguna' => $user->name,
                'Email'         => $user->email,
                'Role'          => $user->role,
                'Tanggal Daftar'=> now()->translatedFormat('d M Y H:i') . ' WIB',
            ],
            actionUrl: url('/members'),
            actionLabel: 'Kelola Anggota'
        ));
    }

    /**
     * User melakukan login pertama kali.
     */
    public static function firstLogin(User $user, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        // 1. In-app notification ke User
        AppNotification::notifyUser(
            $user->id,
            'first_login',
            'Selamat Datang di Perpustakaan',
            'Ini adalah login pertama Anda. Nikmati kemudahan akses koleksi buku dan layanan kami.',
            ['user_id' => $user->id]
        );

        // 2. Email notification ke User
        if ($user->email && $user->notificationsAllowed('email')) {
            self::queueMailSafely($user->email, new FirstLoginMail($user, $ipAddress, $userAgent));
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

        // 2. Email notification ke User
        if ($user->email && $user->notificationsAllowed('borrowing')) {
            self::queueMailSafely($user->email, new BorrowingSubmittedMail($borrowing, $user));
        }

        // 3. Email alert ke Admin
        self::notifyAdmins(new AdminActivityAlertMail(
            eventTitle: 'Pengajuan Peminjaman Buku Baru',
            messageText: "Pengguna {$user->name} telah mengajukan peminjaman buku.",
            details: [
                'Peminjam'    => $user->name,
                'Email'       => $user->email,
                'Judul Buku'  => $bookTitle,
                'Batas Waktu' => $borrowing->due_at ? $borrowing->due_at->format('d M Y') : '-',
            ],
            actionUrl: url('/circulation'),
            actionLabel: 'Buka Sirkulasi'
        ));
    }

    /**
     * Pengembalian buku oleh user / dikonfirmasi admin.
     */
    public static function borrowingReturned(Borrowing $borrowing, ?User $user = null): void
    {
        $borrowing->loadMissing(['details.book', 'member']);
        $user = self::resolveUser($borrowing, $user);

        $bookTitle = $borrowing->details->first()?->book?->title ?? $borrowing->book?->title ?? 'Buku Perpustakaan';
        $userName  = $user?->name ?? $borrowing->member?->name ?? 'Pengguna';

        // 1. In-app & Email ke User jika ada
        if ($user) {
            AppNotification::notifyUser(
                $user->id,
                'borrowing_returned',
                'Buku Berhasil Dikembalikan',
                "Buku \"{$bookTitle}\" telah berhasil dikembalikan ke perpustakaan. Terima kasih!",
                ['borrowing_id' => $borrowing->id]
            );

            if ($user->email && $user->notificationsAllowed('borrowing')) {
                self::queueMailSafely($user->email, new BorrowingReturnedMail($borrowing, $user));
            }
        } elseif ($borrowing->member?->email) {
            self::queueMailSafely($borrowing->member->email, new BorrowingReturnedMail($borrowing, null));
        }

        // 2. In-app notification ke Admin
        AppNotification::notifyAdmin(
            'borrowing_returned',
            'Buku Telah Dikembalikan',
            "Buku \"{$bookTitle}\" telah dikembalikan oleh {$userName}.",
            ['borrowing_id' => $borrowing->id]
        );

        // 3. Email alert ke Admin
        self::notifyAdmins(new AdminActivityAlertMail(
            eventTitle: 'Pengembalian Buku Perpustakaan',
            messageText: "Peminjaman buku telah selesai dan buku telah dikembalikan.",
            details: [
                'Peminjam'       => $userName,
                'Judul Buku'     => $bookTitle,
                'Tanggal Pinjam' => $borrowing->borrowed_at ? \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d M Y') : '-',
                'Tanggal Kembali'=> now()->translatedFormat('d M Y'),
            ],
            actionUrl: url('/circulation'),
            actionLabel: 'Buka Sirkulasi'
        ));
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

        // 2. Email notification ke User
        if ($user->email && $user->notificationsAllowed('reservation')) {
            self::queueMailSafely($user->email, new ReservationSubmittedMail($reservation, $user));
        }

        // 3. Email alert ke Admin
        self::notifyAdmins(new AdminActivityAlertMail(
            eventTitle: 'Pengajuan Reservasi Buku Baru',
            messageText: "Pengguna {$user->name} telah mengajukan reservasi untuk buku \"{$bookTitle}\".",
            details: [
                'Pemohon'     => $user->name,
                'Email'       => $user->email,
                'Judul Buku'  => $bookTitle,
                'Tanggal'     => $reservation->reserved_at ?? now()->toDateString(),
                'Nomor Kursi' => $reservation->seat_number ?? '-',
            ],
            actionUrl: url('/reservations'),
            actionLabel: 'Buka Reservasi'
        ));
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
