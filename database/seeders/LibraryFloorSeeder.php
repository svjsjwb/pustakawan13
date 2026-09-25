<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryFloor;

class LibraryFloorSeeder extends Seeder
{
    public function run(): void
    {
        LibraryFloor::updateOrCreate(
            ['floor_number' => 1],
            [
                'name' => 'Lantai 1',
                'description' => 'Lantai koleksi utama',
            ]
        );

        LibraryFloor::updateOrCreate(
            ['floor_number' => 2],
            [
                'name' => 'Lantai 2',
                'description' => 'Lantai koleksi lanjutan',
            ]
        );
    }
}
