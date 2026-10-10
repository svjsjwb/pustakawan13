<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_is_simplified_without_user_activity_table(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('TOTAL KOLEKSI BUKU');
        $response->assertSee('SEDANG DIPINJAM');
        $response->assertSee('ANGGOTA AKTIF');
        $response->assertSee('KETERLAMBATAN');
        $response->assertSee('Statistik Peminjaman');
        $response->assertSee('Buku Terpopuler');
        $response->assertSee('Reservasi Terbaru');

        // Pastikan widget & tabel Aktivitas User tidak ada
        $response->assertDontSee('Tabel Aktivitas User');
        $response->assertDontSee('Permintaan & Aktivitas User Menunggu Persetujuan');
    }

    public function test_user_catalog_renders_minimalist_pagination(): void
    {
        $user = User::factory()->create([
            'name' => 'Member Test',
            'email' => 'member@test.com',
            'role' => 'member',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'level' => 1,
        ]);

        for ($i = 1; $i <= 15; $i++) {
            Book::create([
                'judul_buku' => 'Test Book ' . $i,
                'penulis' => 'Test Author ' . $i,
                'category_id' => $category->id,
                'stok' => 5,
            ]);
        }

        $response = $this->actingAs($user)->get('/user/catalog');

        $response->assertStatus(200);
        $response->assertSee('Katalog');
    }
}
