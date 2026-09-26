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
                'division'      => 'CEO',
                'phone'         => '081234567801',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Budi Santoso',
                'email'         => 'budi@example.com',
                'division'      => 'COO',
                'phone'         => '081234567802',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Citra Lestari',
                'email'         => 'citra@example.com',
                'division'      => 'CFO',
                'phone'         => '081234567803',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Dimas Pratama',
                'email'         => 'dimas@example.com',
                'division'      => 'Finance Director',
                'phone'         => '081234567804',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Eka Putri',
                'email'         => 'eka@example.com',
                'division'      => 'HROD Director',
                'phone'         => '081234567805',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Fajar Ramadhan',
                'email'         => 'fajar@example.com',
                'division'      => 'PDC',
                'phone'         => '081234567806',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Gita Maharani',
                'email'         => 'gita@example.com',
                'division'      => 'CEO',
                'phone'         => '081234567807',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Hendra Wijaya',
                'email'         => 'hendra@example.com',
                'division'      => 'COO',
                'phone'         => '081234567808',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Intan Permata',
                'email'         => 'intan@example.com',
                'division'      => 'CFO',
                'phone'         => '081234567809',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
                'registered_at' => now(),
            ],
            [
                'name'          => 'Joko Setiawan',
                'email'         => 'joko@example.com',
                'division'      => 'Finance Director',
                'phone'         => '081234567810',
                'address'       => 'Surakarta',
                'status'        => 'nonaktif',
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