<?php

namespace App\Console\Commands;

use App\Mail\BorrowingDueReminderMail;
use App\Models\Borrowing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBorrowingDueReminders extends Command
{
    protected $signature = 'library:send-due-reminders';
    protected $description = 'Kirim email pengingat H-1 pengembalian buku';

    public function handle(): int
    {
        $sent = 0;

        Borrowing::with(['user', 'member', 'book', 'details.book'])
            ->whereDate('due_at', now()->addDay()->toDateString())
            ->whereIn('status', ['dipinjam', 'diperpanjang'])
            ->where('is_reminder_sent', false)
            ->get()
            ->each(function (Borrowing $borrowing) use (&$sent): void {
                $email = $borrowing->user?->email ?: $borrowing->member?->email;

                if (!$email) {
                    return;
                }

                Mail::to($email)->send(new BorrowingDueReminderMail($borrowing));
                $borrowing->forceFill(['is_reminder_sent' => true])->save();
                $sent++;
            });

        $this->info("{$sent} pengingat jatuh tempo dikirim.");

        return self::SUCCESS;
    }
}
