<?php

namespace App\Console\Commands;

use App\Mail\BorrowingDueReminderMail;
use App\Models\AppNotification;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBorrowingDueReminders extends Command
{
    protected $signature = 'library:send-due-reminders';
    protected $description = 'Kirim notifikasi in-app dan email pengingat H-1 pengembalian buku';

    public function handle(): int
    {
        $sent = 0;

        Borrowing::with(['user', 'member', 'details.book'])
            ->whereDate('due_at', now()->addDay()->toDateString())
            ->whereIn('status', ['dipinjam', 'diperpanjang'])
            ->where('is_reminder_sent', false)
            ->get()
            ->each(function (Borrowing $borrowing) use (&$sent): void {
                $email = $borrowing->user?->email ?: $borrowing->member?->email;
                if (!$email) {
                    return;
                }

                $user = $borrowing->user ?: User::where('email', $email)->first();
                $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';
                $dueDate = $borrowing->due_at ? $borrowing->due_at->format('d M Y') : 'besok';

                // 1. In-app notification
                if ($user) {
                    AppNotification::notifyUser(
                        $user->id,
                        'return_reminder',
                        'Pengingat Pengembalian Buku H-1',
                        "Buku \"{$bookTitle}\" jatuh tempo besok ({$dueDate}). Mohon kembalikan tepat waktu.",
                        ['borrowing_id' => $borrowing->id]
                    );
                }

                // 2. Email notification via Queue
                if (!$user || $user->notificationsAllowed('return_reminder')) {
                    try {
                        Mail::to($email)->queue(new BorrowingDueReminderMail($borrowing));
                    } catch (\Throwable $e) {
                        Log::warning("Failed to queue H-1 reminder email to {$email}: " . $e->getMessage());
                    }
                }

                $borrowing->forceFill(['is_reminder_sent' => true])->save();
                $sent++;
            });

        $this->info("{$sent} pengingat jatuh tempo diproses (in-app & queue email).");

        return self::SUCCESS;
    }
}
