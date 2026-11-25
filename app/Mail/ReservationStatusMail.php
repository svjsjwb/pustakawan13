<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public string $status,
    ) {
        $this->status = strtolower($status);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->status) {
                'disetujui' => 'Reservasi Buku Disetujui',
                'dibatalkan' => 'Reservasi Buku Dibatalkan Otomatis',
                default => 'Reservasi Buku Ditolak',
            },
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservation-status');
    }
}
