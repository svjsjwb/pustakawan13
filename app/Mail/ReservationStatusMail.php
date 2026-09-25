<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable
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
            subject: $this->status === 'disetujui'
                ? 'Reservasi Buku Disetujui'
                : 'Reservasi Buku Ditolak',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservation-status');
    }
}
