<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'          => 'Ahmad Fauzan',
                'email'         => 'ahmad@example.com',
                'division'      => 'Editorial',
                'phone'         => '081234567801',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Budi Santoso',
                'email'         => 'budi@example.com',
                'division'      => 'IT & Digital',
                'phone'         => '081234567802',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Citra Lestari',
                'email'         => 'citra@example.com',
                'division'      => 'Pemasaran',
                'phone'         => '081234567803',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Dimas Pratama',
                'email'         => 'dimas@example.com',
                'division'      => 'Produksi',
                'phone'         => '081234567804',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Eka Putri',
                'email'         => 'eka@example.com',
                'division'      => 'Keuangan',
                'phone'         => '081234567805',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Fajar Ramadhan',
                'email'         => 'fajar@example.com',
                'division'      => 'SDM & Umum',
                'phone'         => '081234567806',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Gita Maharani',
                'email'         => 'gita@example.com',
                'division'      => 'Editorial',
                'phone'         => '081234567807',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Hendra Wijaya',
                'email'         => 'hendra@example.com',
                'division'      => 'Logistik',
                'phone'         => '081234567808',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Intan Permata',
                'email'         => 'intan@example.com',
                'division'      => 'Desain Grafis',
                'phone'         => '081234567809',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Joko Setiawan',
                'email'         => 'joko@example.com',
                'division'      => 'Percetakan',
                'phone'         => '081234567810',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
        ];

        foreach ($members as $data) {
            Member::updateOrCreate(
                ['phone' => $data['phone']],
                $data
            );
        }
    }
}