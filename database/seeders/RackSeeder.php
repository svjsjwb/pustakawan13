<?php

namespace Database\Seeders;

use App\Models\Rack;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        $racks = [
            ['A1', 'Rak A1 – Koleksi Anak'],
            ['A2', 'Rak A2 – Koleksi Remaja'],
            ['B1', 'Rak B1 – Referensi Umum'],
            ['B2', 'Rak B2 – Pendidikan'],
            ['C1', 'Rak C1 – Koleksi Dewasa'],
            ['C2', 'Rak C2 – Koleksi Umum'],
        ];

        foreach ($racks as [$code, $name]) {
            Rack::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => "Lokasi koleksi {$name}.",
                ]
            );
        }
    }
}
