<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationStatusNotification extends Notification
{
    use Queueable;

    public Reservation $reservation;
    public string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, string $status)
    {
        $this->reservation = $reservation;
        $this->status = strtolower($status);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $bookTitle = $this->reservation->book?->judul_buku ?: 'Buku';
        $isApproved = $this->status === 'disetujui';

        return [
            'title'        => $isApproved ? 'Reservasi Disetujui' : 'Reservasi Ditolak',
            'message'      => $isApproved
                ? "Pengajuan reservasi buku \"{$bookTitle}\" telah disetujui oleh admin."
                : "Pengajuan reservasi buku \"{$bookTitle}\" ditolak oleh admin.",
            'type'         => $isApproved ? 'reservation_approved' : 'reservation_rejected',
            'color'        => $isApproved ? 'green' : 'red',
            'url'          => route('user.reservations'),
            'reference_id' => $this->reservation->id,
        ];
    }
}
