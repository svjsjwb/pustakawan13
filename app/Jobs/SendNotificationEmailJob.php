<?php

namespace App\Jobs;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan maksimal jika terjadi transient failure.
     */
    public int $tries = 3;

    /**
     * Jeda waktu antar percobaan ulang (dalam detik).
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 180];

    /**
     * Batas waktu eksekusi job (detik).
     */
    public int $timeout = 60;

    public string $email;
    public Mailable $mailable;
    public string $notificationType;
    public ?int $userId;
    public ?string $subject;
    public ?int $emailLogId = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $email,
        Mailable $mailable,
        string $notificationType = 'general',
        ?int $userId = null,
        ?string $subject = null
    ) {
        $this->email = trim($email);
        $this->mailable = $mailable;
        $this->notificationType = $notificationType;
        $this->userId = $userId;
        $this->subject = $subject ?? (property_exists($mailable, 'subject') ? $mailable->subject : null);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $provider = config('mail.default', 'smtp');

        // 1. Validasi keabsahan email & filter dummy email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL) || str_ends_with(strtolower($this->email), '@example.com') || str_ends_with(strtolower($this->email), '@test.local')) {
            EmailLog::create([
                'user_id'           => $this->userId,
                'email'             => $this->email,
                'notification_type' => $this->notificationType,
                'subject'           => $this->subject,
                'status'            => 'skipped',
                'provider'          => $provider,
                'error_message'     => 'Email dilewati: alamat email dummy atau format RFC tidak valid.',
            ]);

            Log::info("Email notification skipped for [{$this->email}]: dummy or invalid address.");
            return;
        }

        // 2. Buat atau ambil record EmailLog
        $emailLog = EmailLog::create([
            'user_id'           => $this->userId,
            'email'             => $this->email,
            'notification_type' => $this->notificationType,
            'subject'           => $this->subject,
            'status'            => 'queued',
            'provider'          => $provider,
        ]);

        $this->emailLogId = $emailLog->id;

        // 3. Kirim email melalui mailer Laravel aktif
        try {
            Mail::to($this->email)->send($this->mailable);

            $emailLog->update([
                'status'  => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Email successfully sent to [{$this->email}] via [{$provider}] for [{$this->notificationType}].");
        } catch (\Throwable $e) {
            $emailLog->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Failed to send email to [{$this->email}]: " . $e->getMessage());

            // Re-throw agar retry/backoff queue Laravel berjalan sesuai $tries
            throw $e;
        }
    }

    /**
     * Handle job failure setelah semua retry habis.
     */
    public function failed(?\Throwable $exception): void
    {
        if ($this->emailLogId) {
            EmailLog::where('id', $this->emailLogId)->update([
                'status'        => 'failed',
                'error_message' => $exception ? $exception->getMessage() : 'Exhausted maximum retry attempts.',
            ]);
        }
    }
}
