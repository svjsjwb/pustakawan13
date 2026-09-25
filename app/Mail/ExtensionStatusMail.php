<?php

namespace App\Mail;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExtensionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $status  disetujui|ditolak|mandiri
     */
    public function __construct(
        public Borrowing $borrowing,
        public User $user,
        public string $status = 'disetujui',
        public string $notes = '',
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'ditolak' => 'Perpanjangan Peminjaman Ditolak',
            default   => 'Perpanjangan Peminjaman Berhasil',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.extension-status');
    }
}
