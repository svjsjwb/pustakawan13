<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryFloor;

class LibraryFloorSeeder extends Seeder
{
    public function run(): void
    {
        LibraryFloor::create([
            'name' => 'Lantai 1',
            'floor_number' => 1,
            'description' => 'Lantai koleksi utama',
        ]);

        LibraryFloor::create([
            'name' => 'Lantai 2',
            'floor_number' => 2,
            'description' => 'Lantai koleksi lanjutan',
        ]);
    }
}
