<?php

namespace App\Mail;

use App\Models\LibraryEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LibraryEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LibraryEvent $event) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pemberitahuan Acara Perpustakaan: ' . $this->event->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.library-event');
    }
}
