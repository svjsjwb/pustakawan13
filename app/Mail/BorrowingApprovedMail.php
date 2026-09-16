<?php

namespace App\Mail;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Borrowing $borrowing,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Peminjaman Buku Disetujui');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.borrowing-approved');
    }
}
