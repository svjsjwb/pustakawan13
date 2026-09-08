<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed akun admin dan user untuk aplikasi perpustakaan.
     */
    public function run(): void
    {
        // ── ADMIN ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@perpus.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@perpus.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // ── USER ───────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'user@perpus.com'],
            [
                'name'     => 'Pengguna',
                'email'    => 'user@perpus.com',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        \App\Models\Member::updateOrCreate(
            ['email' => 'user@perpus.com'],
            [
                'name'    => 'Pengguna Perpustakaan',
                'phone'   => '081234567890',
                'address' => 'Surakarta',
                'status'  => 'aktif',
            ]
        );
    }
}
