<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserReservationAnomalyFixTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $regularUser;
    protected Member $member;
    protected Category $category;
    protected Book $book;
    protected BookCopy $bookCopy;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@pustakawan.test',
        ]);

        // Regular Member User
        $this->regularUser = User::factory()->create([
            'role' => 'member',
            'name' => 'Budi Santoso',
            'email' => 'budi@pustakawan.test',
        ]);

        // Member record untuk regular user
        $this->member = Member::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->regularUser->name,
            'email' => $this->regularUser->email,
            'phone' => '08123456789',
            'division' => 'Anggota',
            'status' => 'Aktif',
        ]);

        // Category
        $this->category = Category::create([
            'name' => 'Teknologi',
            'level' => 1,
        ]);

        // Book
        $this->book = Book::create([
            'category_id' => $this->category->id,
            'judul_buku' => 'Belajar Pemrograman Modern',
            'penulis' => 'Jane Doe',
            'stok' => 3,
        ]);

        // Book Copy
        $this->bookCopy = BookCopy::create([
            'book_id' => $this->book->id,
            'copy_code' => 'CP-TEST-001',
            'status' => 'available',
        ]);
    }

    /**
     * Test 1:
     * Regular user reservation records their own user_id and member link.
     */
    public function test_regular_user_reservation_records_own_user_id_and_member(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->post(route('user.reservations.store'), [
            'book_id' => $this->book->id,
            'reserved_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('user.reservations'));

        // Reservation harus terhubung ke regular user
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->regularUser->id,
            'member_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => 'menunggu',
        ]);

        // Member harus terhubung ke user yang benar
        $this->assertDatabaseHas('members', [
            'user_id' => $this->regularUser->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@pustakawan.test',
        ]);

        // Admin tidak boleh otomatis dibuat sebagai member
        $this->assertDatabaseMissing('members', [
            'user_id' => $this->adminUser->id,
        ]);
    }

    /**
     * Test 2:
     * Admin cannot submit user reservation endpoint.
     */
    public function test_admin_cannot_submit_user_reservation(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson(route('user.reservations.store'), [
            'book_id' => $this->book->id,
            'reserved_at' => now()->toDateString(),
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Data anggota tidak ditemukan.',
        ]);

        // Tidak ada reservation yang dibuat
        $this->assertDatabaseMissing('reservations', [
            'book_id' => $this->book->id,
        ]);

        // Admin tidak boleh dibuat sebagai member
        $this->assertDatabaseMissing('members', [
            'user_id' => $this->adminUser->id,
        ]);
    }

    public function test_cancellation_of_approved_reservation_waits_for_admin_approval(): void
    {
        $member = Member::where('user_id', $this->regularUser->id)->first() ?? Member::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->regularUser->name,
            'email' => $this->regularUser->email,
            'phone' => '08123456789',
            'division' => 'Anggota',
            'status' => 'aktif',
        ]);

        $this->book->update(['stok' => 2]);
        $this->bookCopy->update(['status' => 'borrowed']);
        $reservation = Reservation::create([
            'user_id' => $this->regularUser->id,
            'member_id' => $member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->bookCopy->id,
            'reserved_at' => now(),
            'status' => 'disetujui',
        ]);

        $this
            ->actingAs($this->regularUser)
            ->post(route('user.reservations.cancel', $reservation->id))
            ->assertRedirect(route('user.reservations.show', $reservation->id))
            ->assertSessionHas('cancellation_request_pending', true);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'disetujui',
        ]);
        $this->assertNotNull($reservation->fresh()->cancellation_requested_at);
        $this->assertDatabaseHas('book_copies', [
            'id' => $this->bookCopy->id,
            'status' => 'borrowed',
        ]);
        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'stok' => 2,
        ]);

        $this
            ->get(route('user.reservations.show', $reservation->id))
            ->assertOk()
            ->assertSee('window.showToast', false)
            ->assertSee('Menunggu Persetujuan Pembatalan')
            ->assertSee('Menunggu Persetujuan Admin')
            ->assertDontSee('Dibatalkan oleh Anggota');

        $this
            ->get(route('user.reservations'))
            ->assertOk()
            ->assertSee('Menunggu Persetujuan Pembatalan');

        $this
            ->actingAs($this->adminUser)
            ->patch(route('reservations.resolveCancellation', $reservation->id), [
                'decision' => 'approve',
            ])
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'dibatalkan',
            'cancellation_requested_at' => null,
        ]);
        $this->assertDatabaseHas('book_copies', [
            'id' => $this->bookCopy->id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'stok' => 3,
        ]);
        $approvalNotice = AppNotification::query()
            ->where('user_id', $this->regularUser->id)
            ->where('type', 'reservation_cancelled')
            ->firstOrFail();
        $this->assertSame('approved', $approvalNotice->data['decision']);

        $this
            ->actingAs($this->regularUser)
            ->get(route('user.reservations.show', $reservation->id))
            ->assertOk()
            ->assertSee('Dibatalkan oleh Anggota')
            ->assertDontSee('Menunggu Persetujuan Pembatalan');
    }

    public function test_admin_can_reject_cancellation_request_without_changing_approved_status(): void
    {
        $member = Member::where('user_id', $this->regularUser->id)->first() ?? Member::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->regularUser->name,
            'email' => $this->regularUser->email,
            'phone' => '08123456789',
            'division' => 'Anggota',
            'status' => 'aktif',
        ]);

        $this->book->update(['stok' => 2]);
        $this->bookCopy->update(['status' => 'borrowed']);

        $reservation = Reservation::create([
            'user_id' => $this->regularUser->id,
            'member_id' => $member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->bookCopy->id,
            'reserved_at' => now(),
            'status' => 'disetujui',
        ]);

        $this
            ->actingAs($this->regularUser)
            ->post(route('user.reservations.cancel', $reservation->id))
            ->assertRedirect(route('user.reservations.show', $reservation->id))
            ->assertSessionHas('cancellation_request_pending', true);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'disetujui',
        ]);
        $this->assertNotNull($reservation->fresh()->cancellation_requested_at);

        $this
            ->actingAs($this->adminUser)
            ->patch(route('reservations.resolveCancellation', $reservation->id), [
                'decision' => 'reject',
            ])
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'disetujui',
            'cancellation_requested_at' => null,
        ]);
        $this->assertDatabaseHas('book_copies', [
            'id' => $this->bookCopy->id,
            'status' => 'borrowed',
        ]);
        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'stok' => 2,
        ]);

        $rejectionNotice = AppNotification::query()
            ->where('user_id', $this->regularUser->id)
            ->where('type', 'reservation_cancellation_rejected')
            ->firstOrFail();
        $this->assertSame('rejected', $rejectionNotice->data['decision']);
        $this->assertSame($reservation->id, $rejectionNotice->data['reservation_id']);

        $this
            ->actingAs($this->regularUser)
            ->get(route('user.reservations.show', $reservation->id))
            ->assertOk()
            ->assertSee('Disetujui')
            ->assertSee('Permintaan Pembatalan Ditolak oleh Admin')
            ->assertSee('Status reservasi tetap Disetujui dan proses pengambilan buku dapat dilanjutkan.')
            ->assertSeeInOrder([
                'Tracking Status Reservasi',
                'Informasi Reservasi',
                'Permintaan Pembatalan Ditolak oleh Admin',
            ])
            ->assertSee('&#10003; Persetujuan Reservasi', false)
            ->assertSee('rsv-tracking-step is-done', false)
            ->assertSee('rsv-tracking-step is-active', false)
            ->assertSee('Menunggu Pengambilan')
            ->assertSee('Batalkan Reservasi')
            ->assertDontSee('Menunggu Persetujuan Pembatalan');

        $this
            ->getJson(route('user.reservations.statusFeed'))
            ->assertOk()
            ->assertJsonPath('reservations.0.status', 'disetujui');
    }

    public function test_admin_cannot_change_main_status_while_cancellation_request_is_pending(): void
    {
        $member = Member::where('user_id', $this->regularUser->id)->first() ?? Member::create([
            'user_id' => $this->regularUser->id,
            'name' => $this->regularUser->name,
            'email' => $this->regularUser->email,
            'phone' => '08123456789',
            'division' => 'Anggota',
            'status' => 'aktif',
        ]);

        $reservation = Reservation::create([
            'user_id' => $this->regularUser->id,
            'member_id' => $member->id,
            'book_id' => $this->book->id,
            'book_copy_id' => $this->bookCopy->id,
            'reserved_at' => now(),
            'status' => 'disetujui',
            'cancellation_requested_at' => now(),
        ]);

        $this
            ->actingAs($this->adminUser)
            ->patch(route('reservations.updateStatus', $reservation->id), [
                'status' => 'dibatalkan',
            ])
            ->assertStatus(409);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'disetujui',
        ]);
        $this->assertNotNull($reservation->fresh()->cancellation_requested_at);
    }

    /**
     * Test 3:
     * Admin dashboard correctly displays genuine user name
     * even when member_id is null.
     */
    public function test_admin_dashboard_displays_user_name_when_member_is_null(): void
    {
        // Reservation dengan user_id valid tetapi member_id null
        Reservation::create([
            'user_id' => $this->regularUser->id,
            'member_id' => null,
            'book_id' => $this->book->id,
            'reserved_at' => now(),
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        // Dashboard harus menampilkan nama asli user
        $response->assertSee('Budi Santoso');
    }
}
