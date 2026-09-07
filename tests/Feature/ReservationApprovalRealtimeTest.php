<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserFavorite;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReservationApprovalRealtimeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Category $category;
    protected Book $book;
    protected BookCopy $bookCopy;
    protected Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@pustakawan.test',
        ]);

        // Regular Member User
        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'name' => 'Budi Santoso',
            'email' => 'budi@pustakawan.test',
        ]);

        $this->member = Member::create([
            'user_id'  => $this->regularUser->id,
            'name'     => $this->regularUser->name,
            'email'    => $this->regularUser->email,
            'phone'    => '08123456789',
            'division' => 'Anggota',
            'status'   => 'Aktif',
        ]);

        $this->category = Category::create([
            'name'  => 'Teknologi',
            'level' => 1,
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'judul_buku'  => 'Belajar Laravel Real-time',
            'penulis'     => 'John Doe',
            'stok'        => 5,
        ]);

        $this->bookCopy = BookCopy::create([
            'book_id'     => $this->book->id,
            'copy_code'   => 'CP-001',
            'status'      => 'available',
        ]);
    }

    /**
     * AC-1: Data reservasi baru yang dikirim user mem-publish event realtime untuk admin.
     * AC-2: Data reservasi tidak akan masuk ke Tabel Data Pinjam sebelum admin memberikan persetujuan.
     */
    public function test_user_reservation_emits_realtime_event_and_does_not_create_borrowing_until_approved(): void
    {
        $this->actingAs($this->regularUser);

        // User membuat reservasi
        $response = $this->post(route('user.reservations.store'), [
            'book_id'     => $this->book->id,
            'reserved_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('user.reservations'));
        $response->assertSessionHas('reservation_success');

        // Pastikan reservasi terbentuk dengan status menunggu
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->regularUser->id,
            'book_id' => $this->book->id,
            'status'  => 'menunggu',
        ]);

        // AC-2: Data reservasi TIDAK MASUK ke Tabel Data Pinjam (Borrowing)
        $this->assertEquals(0, Borrowing::count());

        // AC-1: Event realtime reservation.created tercatat
        $this->assertDatabaseHas('realtime_events', [
            'event' => 'reservation.created',
        ]);

        // Cek endpoint poll
        $pollResponse = $this->getJson(route('events.poll', ['last_id' => 0]));
        $pollResponse->assertOk();
    }

    /**
     * AC-3: Setelah admin menyetujui reservasi, data otomatis masuk ke Tabel Data Pinjam
     * dengan kalkulasi Batas Kembali = Tanggal Disetujui + 14 Hari.
     */
    public function test_admin_approval_creates_borrowing_with_14_days_due_date_and_emits_realtime(): void
    {
        // Buat reservasi berstatus menunggu
        $reservation = Reservation::create([
            'user_id'      => $this->regularUser->id,
            'member_id'    => $this->member->id,
            'book_id'      => $this->book->id,
            'book_copy_id' => $this->bookCopy->id,
            'reserved_at'  => now()->toDateString(),
            'status'       => 'menunggu',
        ]);

        $this->actingAs($this->adminUser);

        // Simulasi waktu persetujuan
        $fixedNow = Carbon::create(2026, 9, 7, 10, 30, 0);
        Carbon::setTestNow($fixedNow);

        $response = $this->patch(route('reservations.updateStatus', $reservation->id), [
            'status' => 'disetujui',
        ]);

        $response->assertRedirect(route('reservations.index'));
        $response->assertSessionHas('success');

        // Reservasi sekarang berstatus disetujui dan terhubung dengan borrowing_id
        $reservation->refresh();
        $this->assertEquals('disetujui', $reservation->status);
        $this->assertNotNull($reservation->borrowing_id);

        // AC-3: Data otomatis masuk ke Tabel Data Pinjam (Borrowing)
        $this->assertEquals(1, Borrowing::count());
        $borrowing = Borrowing::first();

        // Tanggal Pinjam = T_approve (2026-09-07)
        $this->assertEquals('2026-09-07', $borrowing->borrowed_at->toDateString());

        // Masa Tenggat = T_approve + 14 hari (2026-09-21)
        $this->assertEquals('2026-09-21', $borrowing->due_at->toDateString());
        $this->assertEquals('dipinjam', $borrowing->status);

        // Detail peminjaman terbentuk
        $this->assertDatabaseHas('borrowing_details', [
            'borrowing_id' => $borrowing->id,
            'book_id'      => $this->book->id,
        ]);

        // Status eksemplar buku berubah menjadi borrowed
        $this->bookCopy->refresh();
        $this->assertEquals('borrowed', $this->bookCopy->status);

        // Realtime events tercatat untuk user & admin
        $this->assertDatabaseHas('realtime_events', [
            'event' => 'reservation.approved',
        ]);
        $this->assertDatabaseHas('realtime_events', [
            'event' => 'borrowing.created',
        ]);

        Carbon::setTestNow(); // reset time
    }

    /**
     * AC-4: Pada halaman Favorit Saya, setiap item buku dipastikan hanya menampilkan
     * fungsi/tombol Lihat di Katalog Saya dan Reservasi, dengan isi informasi yang identik dengan Beranda.
     */
    public function test_favorites_page_shows_book_info_and_strictly_two_actions(): void
    {
        // Tambahkan buku ke favorit user
        UserFavorite::create([
            'user_id' => $this->regularUser->id,
            'book_id' => $this->book->id,
        ]);

        $this->actingAs($this->regularUser);

        $response = $this->get(route('favorites.index'));
        $response->assertOk();

        // Memuat informasi buku identik Beranda
        $response->assertSee($this->book->title);
        $response->assertSee($this->book->author);
        $response->assertSee($this->category->name);
        $response->assertSee('Tersedia');

        // Memuat TEPAT dua fungsi/tombol aksi
        $response->assertSee('Lihat di Katalog Saya');
        $response->assertSee('Reservasi');

        // Memastikan tombol hapus/sampah cepat tidak ditampilkan lagi pada kartu
        $response->assertDontSee('favorite-remove-btn');
        $response->assertDontSee('Hapus dari Favorit');
    }
}
