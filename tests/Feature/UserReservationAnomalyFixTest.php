<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserReservationAnomalyFixTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Category $category;
    protected Book $book;
    protected BookCopy $bookCopy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@pustakawan.test',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'name' => 'Budi Santoso',
            'email' => 'budi@pustakawan.test',
        ]);

        $this->category = Category::create([
            'name'  => 'Teknologi',
            'level' => 1,
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'judul_buku'  => 'Belajar Pemrograman Modern',
            'penulis'     => 'Jane Doe',
            'stok'        => 3,
        ]);

        $this->bookCopy = BookCopy::create([
            'book_id'     => $this->book->id,
            'copy_code'   => 'CP-TEST-001',
            'status'      => 'available',
        ]);
    }

    /**
     * Test 1: Regular user reservation records their own user_id and member link.
     */
    public function test_regular_user_reservation_records_own_user_id_and_member(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->post(route('user.reservations.store'), [
            'book_id'     => $this->book->id,
            'reserved_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('user.reservations'));

        // Check reservation is linked to regularUser
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->regularUser->id,
            'book_id' => $this->book->id,
            'status'  => 'menunggu',
        ]);

        // Check member is linked to regularUser, not admin
        $this->assertDatabaseHas('members', [
            'user_id' => $this->regularUser->id,
            'name'    => 'Budi Santoso',
            'email'   => 'budi@pustakawan.test',
        ]);

        $this->assertDatabaseMissing('members', [
            'user_id' => $this->adminUser->id,
        ]);
    }

    /**
     * Test 2: Admin cannot submit user reservation endpoint.
     */
    public function test_admin_cannot_submit_user_reservation(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson(route('user.reservations.store'), [
            'book_id'     => $this->book->id,
            'reserved_at' => now()->toDateString(),
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Hanya pengguna umum (role user) yang dapat membuat reservasi.',
        ]);

        // No reservation created
        $this->assertDatabaseMissing('reservations', [
            'book_id' => $this->book->id,
        ]);

        // No member record created for admin
        $this->assertDatabaseMissing('members', [
            'user_id' => $this->adminUser->id,
        ]);
    }

    /**
     * Test 3: Admin dashboard correctly displays genuine user name even when member_id is null.
     */
    public function test_admin_dashboard_displays_user_name_when_member_is_null(): void
    {
        // Create reservation with regular user and null member_id
        $reservation = Reservation::create([
            'user_id'     => $this->regularUser->id,
            'member_id'   => null,
            'book_id'     => $this->book->id,
            'reserved_at' => now(),
            'status'      => 'menunggu',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        // Should display the user's genuine name "Budi Santoso"
        $response->assertSee('Budi Santoso');
    }
}
