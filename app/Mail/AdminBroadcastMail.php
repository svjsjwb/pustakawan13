<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $messageBody;
    public ?string $recipientName;
    public ?string $actionUrl;
    public ?string $actionLabel;
    public ?string $senderName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subjectText,
        string $messageBody,
        ?string $recipientName = null,
        ?string $actionUrl = null,
        ?string $actionLabel = 'Lihat Detail',
        ?string $senderName = 'Admin Perpustakaan'
    ) {
        $this->subjectText = $subjectText;
        $this->messageBody = $messageBody;
        $this->recipientName = $recipientName;
        $this->actionUrl = $actionUrl;
        $this->actionLabel = $actionLabel;
        $this->senderName = $senderName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_broadcast',
            with: [
                'emailTitle'    => $this->subjectText,
                'subjectText'   => $this->subjectText,
                'messageBody'   => $this->messageBody,
                'recipientName' => $this->recipientName,
                'actionUrl'     => $this->actionUrl,
                'actionLabel'   => $this->actionLabel,
                'senderName'    => $this->senderName,
            ],
        );
    }
}
