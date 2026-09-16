<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pengajuan Reservasi Buku Berhasil');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservation-submitted');
    }
}
