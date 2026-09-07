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
                'name' => 'Ahmad Fauzan',
                'division' => 'Editorial',
                'phone' => '081234567801',
                'status' => 'aktif',
            ],
            [
                'name' => 'Budi Santoso',
                'division' => 'IT & Digital',
                'phone' => '081234567802',
                'status' => 'aktif',
            ],
            [
                'name' => 'Citra Lestari',
                'division' => 'Pemasaran',
                'phone' => '081234567803',
                'status' => 'aktif',
            ],
            [
                'name' => 'Dimas Pratama',
                'division' => 'Produksi',
                'phone' => '081234567804',
                'status' => 'aktif',
            ],
            [
                'name' => 'Eka Putri',
                'division' => 'Keuangan',
                'phone' => '081234567805',
                'status' => 'aktif',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'division' => 'SDM & Umum',
                'phone' => '081234567806',
                'status' => 'aktif',
            ],
            [
                'name' => 'Gita Maharani',
                'division' => 'Editorial',
                'phone' => '081234567807',
                'status' => 'aktif',
            ],
            [
                'name' => 'Hendra Wijaya',
                'division' => 'Logistik',
                'phone' => '081234567808',
                'status' => 'aktif',
            ],
            [
                'name' => 'Intan Permata',
                'division' => 'Desain Grafis',
                'phone' => '081234567809',
                'status' => 'aktif',
            ],
            [
                'name' => 'Joko Setiawan',
                'division' => 'Percetakan',
                'phone' => '081234567810',
                'status' => 'aktif',
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
