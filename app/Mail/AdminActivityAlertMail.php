<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminActivityAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param string $eventTitle   Judul aktivitas (misal: "Registrasi Pengguna Baru", "Peminjaman Buku Baru")
     * @param string $messageText  Deskripsi detail aktivitas
     * @param array  $details      Key-value data pendukung (misal: ['Nama' => 'John', 'Buku' => 'Laskar Pelangi'])
     * @param string|null $actionUrl  Link ke halaman admin terkait
     * @param string|null $actionLabel Label tombol aksi
     */
    public function __construct(
        public string $eventTitle,
        public string $messageText,
        public array $details = [],
        public ?string $actionUrl = null,
        public ?string $actionLabel = 'Lihat di Dashboard Admin',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[Admin Alert] ' . $this->eventTitle . ' - ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-alert');
    }
}
