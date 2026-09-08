<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
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
        $admin = \App\Models\User::where('email', 'admin@perpus.com')->first();
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
        $user = \App\Models\User::where('email', 'user@perpus.com')->first();
        $response = $this->actingAs($user)->get('/user/catalog');

        $response->assertStatus(200);
        $response->assertSee('Katalog');
        $response->assertSee('ucat-pagination-nav');
        $response->assertSee('Previous');
        $response->assertSee('Next');
        $response->assertSee('ucat-page-btn');
    }
}

