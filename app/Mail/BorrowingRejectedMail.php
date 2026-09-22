<?php

namespace App\Mail;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowingRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Borrowing $borrowing,
        public User $user,
        public string $reason = 'Ditolak oleh Admin',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Peminjaman Buku Ditolak');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.borrowing-rejected');
    }
}
