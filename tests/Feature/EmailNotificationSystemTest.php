<?php

namespace Tests\Feature;

use App\Mail\AdminActivityAlertMail;
use App\Mail\BorrowingApprovedMail;
use App\Mail\BorrowingDueReminderMail;
use App\Mail\BorrowingRejectedMail;
use App\Mail\BorrowingReturnedMail;
use App\Mail\BorrowingSubmittedMail;
use App\Mail\ExtensionStatusMail;
use App\Mail\FirstLoginMail;
use App\Mail\ReservationStatusMail;
use App\Mail\ReservationSubmittedMail;
use App\Mail\WelcomeMail;
use App\Models\AppNotification;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationSystemTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected User $user;
    protected Member $member;
    protected Book $book;
    protected BookCopy $copy;
    protected Category $category;

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

        $this->category = Category::firstOrCreate(
            ['name' => 'Test Notification Category'],
            ['level' => 1]
        );

        $this->book = Book::first() ?? Book::create([
            'judul_buku' => 'Test Notification Book',
            'penulis' => 'Test Author',
            'category_id' => $this->category->id,
            'stok' => 5,
        ]);

        $this->copy = BookCopy::where('book_id', $this->book->id)->first() ?? BookCopy::create([
            'book_id' => $this->book->id,
            'barcode' => 'TESTBC' . uniqid(),
            'status' => 'available',
            'condition' => 'baik',
        ]);
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

    public function test_user_borrowing_is_automatically_approved_after_validation(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('user.loans.store'), [
            'book_id' => $this->book->id,
        ]);

        $response->assertRedirect(route('borrowings.index'));
        $borrowing = Borrowing::where('user_id', $this->user->id)->latest('id')->first();

        $this->assertNotNull($borrowing);
        $this->assertSame('dipinjam', $borrowing->status);
        $this->assertSame($this->book->stok - 1, $this->book->fresh()->stok);
        Mail::assertQueued(BorrowingApprovedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });
        Mail::assertNotQueued(BorrowingSubmittedMail::class);
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_approved',
        ]);
    }

    public function test_sixth_active_book_is_rejected_without_creating_borrowing_or_reducing_stock(): void
    {
        $this->actingAs($this->user);

        for ($index = 0; $index < 5; $index++) {
            $book = Book::create([
                'judul_buku' => 'Active Limit Book ' . $index,
                'penulis' => 'Test Author',
                'category_id' => $this->category->id,
                'stok' => 1,
            ]);
            $copy = BookCopy::create([
                'book_id' => $book->id,
                'barcode' => 'LIMIT' . uniqid(),
                'status' => 'borrowed',
                'condition' => 'baik',
            ]);
            $borrowing = Borrowing::create([
                'member_id' => $this->member->id,
                'user_id' => $this->user->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(14),
                'status' => 'dipinjam',
            ]);
            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'book_id' => $book->id,
                'book_copy_id' => $copy->id,
                'quantity' => 1,
            ]);
        }

        $stockBefore = $this->book->fresh()->stok;
        $borrowingCountBefore = Borrowing::where('user_id', $this->user->id)->count();

        $response = $this->from('/user/catalog')->post(route('user.loans.store'), [
            'book_id' => $this->book->id,
        ]);

        $response->assertRedirect('/user/catalog');
        $response->assertSessionHas('error', 'Maksimal 5 buku. Silakan kembalikan salah satu buku yang sedang dipinjam sebelum melakukan peminjaman baru.');
        $this->assertSame($borrowingCountBefore, Borrowing::where('user_id', $this->user->id)->count());
        $this->assertSame($stockBefore, $this->book->fresh()->stok);
        Mail::assertNotQueued(BorrowingApprovedMail::class);
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

    public function test_user_registration_queues_welcome_mail_and_admin_alert(): void
    {
        $uniqueEmail = 'newmember_' . uniqid() . '@example.com';
        $newUser = User::create([
            'name' => 'New Member',
            'email' => $uniqueEmail,
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        NotificationService::userRegistered($newUser);

        Mail::assertQueued(WelcomeMail::class, function ($mail) use ($uniqueEmail) {
            return $mail->hasTo($uniqueEmail);
        });

        Mail::assertQueued(AdminActivityAlertMail::class);
    }

    public function test_first_login_queues_first_login_mail(): void
    {
        NotificationService::firstLogin($this->user, '127.0.0.1', 'PHPUnit');

        Mail::assertQueued(FirstLoginMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'first_login',
        ]);
    }

    public function test_borrowing_returned_queues_returned_mail_and_admin_alert(): void
    {
        $borrowing = Borrowing::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'borrowed_at' => now()->subDays(5),
            'due_at' => now()->addDays(9),
            'status' => 'dikembalikan',
            'returned_at' => now()->toDateString(),
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->copy->id,
            'quantity' => 1,
        ]);

        NotificationService::borrowingReturned($borrowing, $this->user);

        Mail::assertQueued(BorrowingReturnedMail::class, function ($mail) {
            return $mail->hasTo('testuser@example.com');
        });

        Mail::assertQueued(AdminActivityAlertMail::class);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->user->id,
            'type' => 'borrowing_returned',
        ]);
    }
}

