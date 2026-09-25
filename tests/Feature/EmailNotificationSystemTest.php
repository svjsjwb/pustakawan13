<?php

namespace Tests\Feature;

use App\Mail\BorrowingApprovedMail;
use App\Mail\BorrowingDueReminderMail;
use App\Mail\BorrowingRejectedMail;
use App\Mail\BorrowingSubmittedMail;
use App\Mail\ExtensionStatusMail;
use App\Mail\ReservationStatusMail;
use App\Mail\ReservationSubmittedMail;
use App\Models\AppNotification;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationSystemTest extends TestCase
{
    protected User $user;
    protected Member $member;
    protected Book $book;
    protected BookCopy $copy;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->user = User::firstOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        $this->member = Member::firstOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Test User',
                'phone' => '08123456789',
                'status' => 'aktif',
            ]
        );

        $category = \App\Models\Category::firstOrCreate(['name' => 'Pendidikan'], ['level' => 1]);

        $this->book = Book::firstOrCreate(
            ['judul_buku' => 'Test Notification Book'],
            [
                'penulis' => 'Test Author',
                'category_id' => $category->id,
                'stok' => 5,
            ]
        );

        $this->copy = BookCopy::firstOrCreate(
            ['book_id' => $this->book->id, 'barcode' => 'TESTBC001'],
            [
                'status' => 'available',
                'condition' => 'good',
            ]
        );
    }

    public function test_borrowing_submitted_triggers_in_app_and_queued_email(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'dipinjam',
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        NotificationService::borrowingSubmitted($borrowing, $this->user);

        Mail::assertQueued(BorrowingSubmittedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_submitted',
        ]);
    }

    public function test_borrowing_approved_triggers_in_app_and_queued_email(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'dipinjam',
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        NotificationService::borrowingApproved($borrowing);

        Mail::assertQueued(BorrowingApprovedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_approved',
        ]);
    }

    public function test_borrowing_rejected_triggers_in_app_and_queued_email(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'ditolak',
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        NotificationService::borrowingRejected($borrowing, null, 'Stok tidak mencukupi');

        Mail::assertQueued(BorrowingRejectedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_rejected',
        ]);
    }

    public function test_reservation_submitted_triggers_in_app_and_queued_email(): void
    {
        $reservation = Reservation::create([
            'member_id' => $this->member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'reserved_at' => now(),
            'expires_at' => now()->addDays(3),
            'status' => 'menunggu',
        ]);

        NotificationService::reservationSubmitted($reservation, $this->user);

        Mail::assertQueued(ReservationSubmittedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'reservation_submitted',
        ]);
    }

    public function test_reservation_approved_triggers_in_app_and_queued_email(): void
    {
        $reservation = Reservation::create([
            'member_id' => $this->member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'reserved_at' => now(),
            'expires_at' => now()->addDays(3),
            'status' => 'disetujui',
        ]);

        NotificationService::reservationApproved($reservation);

        Mail::assertQueued(ReservationStatusMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'reservation_approved',
        ]);
    }

    public function test_reservation_rejected_triggers_in_app_and_queued_email(): void
    {
        $reservation = Reservation::create([
            'member_id' => $this->member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'reserved_at' => now(),
            'expires_at' => now()->addDays(3),
            'status' => 'ditolak',
        ]);

        NotificationService::reservationRejected($reservation, null, 'Buku sedang diperbaiki');

        Mail::assertQueued(ReservationStatusMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'reservation_rejected',
        ]);
    }

    public function test_extension_approved_triggers_in_app_and_queued_email(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(21),
            'status' => 'dipinjam',
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        NotificationService::extensionApproved($borrowing);

        Mail::assertQueued(ExtensionStatusMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_extension_approved',
        ]);
    }

    public function test_h1_reminder_command_dispatches_in_app_and_queued_mail(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now()->subDays(13),
            'due_at' => now()->addDay()->startOfDay(),
            'status' => 'dipinjam',
            'is_reminder_sent' => false,
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        $this->artisan('library:send-due-reminders')
            ->assertSuccessful();

        $this->assertTrue($borrowing->fresh()->is_reminder_sent);

        Mail::assertQueued(BorrowingDueReminderMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'return_reminder',
        ]);
    }
}
