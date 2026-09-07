<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowingDueWarningNotification extends Notification
{
    use Queueable;

    public Borrowing $borrowing;

    /**
     * Create a new notification instance.
     */
    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
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
        $firstDetail = $this->borrowing->details->first();
        $bookTitle = $firstDetail?->book?->judul_buku ?: 'Buku Perpustakaan';
        $dueFormatted = Carbon::parse($this->borrowing->due_at)->translatedFormat('d M Y');

        return [
            'title'        => 'Peringatan Jatuh Tempo (H-1)',
            'message'      => "Peminjaman buku \"{$bookTitle}\" akan jatuh tempo besok ({$dueFormatted}). Harap segera kembalikan atau lakukan perpanjangan.",
            'type'         => 'borrowing_due_soon',
            'color'        => 'amber',
            'url'          => route('user.history', ['tab' => 'borrowings']),
            'reference_id' => $this->borrowing->id,
        ];
    }
}
