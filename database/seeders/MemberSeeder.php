<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        Member::insert([
            [
                'name' => 'Ahmad Fauzan',
                'email' => 'ahmad@example.com',
                'phone' => '081234567801',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567802',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra@example.com',
                'phone' => '081234567803',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Dimas Pratama',
                'email' => 'dimas@example.com',
                'phone' => '081234567804',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Eka Putri',
                'email' => 'eka@example.com',
                'phone' => '081234567805',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar@example.com',
                'phone' => '081234567806',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Gita Maharani',
                'email' => 'gita@example.com',
                'phone' => '081234567807',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra@example.com',
                'phone' => '081234567808',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Intan Permata',
                'email' => 'intan@example.com',
                'phone' => '081234567809',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
            [
                'name' => 'Joko Setiawan',
                'email' => 'joko@example.com',
                'phone' => '081234567810',
                'address' => 'Surakarta',
                'status' => 'aktif',
            ],
        ]);
    }
}
