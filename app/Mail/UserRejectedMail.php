<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public ?string $reason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Informasi Status Pendaftaran - ' . config('app.name', 'Perpustakaan Tiga Serangkai'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.user-rejected');
    }
}
