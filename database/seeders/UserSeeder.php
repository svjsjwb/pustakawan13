<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed hanya akun admin perpustakaan.
     */
    public function run(): void
    {
        // ── ADMIN PERPUSTAKAAN ──────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Perpustakaan',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('perpusts'),
                'role'     => 'admin',
            ]
        );
    }
}
