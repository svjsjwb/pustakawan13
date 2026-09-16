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
                'division'      => 'Editorial',
                'phone'         => '081234567801',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Budi Santoso',
                'division'      => 'IT & Digital',
                'phone'         => '081234567802',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Citra Lestari',
                'division'      => 'Pemasaran',
                'phone'         => '081234567803',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Dimas Pratama',
                'division'      => 'Produksi',
                'phone'         => '081234567804',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Eka Putri',
                'division'      => 'Keuangan',
                'phone'         => '081234567805',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Fajar Ramadhan',
                'division'      => 'SDM & Umum',
                'phone'         => '081234567806',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Gita Maharani',
                'division'      => 'Editorial',
                'phone'         => '081234567807',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Hendra Wijaya',
                'division'      => 'Logistik',
                'phone'         => '081234567808',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Intan Permata',
                'division'      => 'Desain Grafis',
                'phone'         => '081234567809',
                'address'       => 'Surakarta',
                'status'        => 'aktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Joko Setiawan',
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