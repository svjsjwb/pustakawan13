<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rack;
use Illuminate\Support\Facades\DB;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        // Hindari duplikat saat re-seed
        DB::table('racks')->truncate();

        Rack::insert([
            [
                'code'        => 'A1',
                'name'        => 'Rak A1 – Novel & Fiksi',
                'description' => 'Koleksi buku novel, cerpen, dan fiksi umum',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'A2',
                'name'        => 'Rak A2 – Teknologi & Sains',
                'description' => 'Koleksi buku komputer, sains, dan teknologi terapan',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'B1',
                'name'        => 'Rak B1 – Sejarah & Budaya',
                'description' => 'Koleksi buku sejarah, geografi, dan budaya nusantara',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'B2',
                'name'        => 'Rak B2 – Pendidikan & Referensi',
                'description' => 'Koleksi buku pelajaran, ensiklopedia, dan kamus',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'C1',
                'name'        => 'Rak C1 – Agama & Filsafat',
                'description' => 'Koleksi buku agama, moral, dan filsafat',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'C2',
                'name'        => 'Rak C2 – Kesehatan & Olahraga',
                'description' => 'Koleksi buku kedokteran, farmasi, gizi, dan kebugaran',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'D1',
                'name'        => 'Rak D1 – Ekonomi & Bisnis',
                'description' => 'Koleksi buku manajemen, akuntansi, dan wirausaha',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'D2',
                'name'        => 'Rak D2 – Hukum & Sosial',
                'description' => 'Koleksi buku hukum, sosiologi, dan ilmu politik',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
