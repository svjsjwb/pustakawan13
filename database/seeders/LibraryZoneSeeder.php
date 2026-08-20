<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryFloor;
use App\Models\LibraryZone;

class LibraryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $floor1 = LibraryFloor::where(
            'floor_number',
            1
        )->firstOrFail();

        $floor2 = LibraryFloor::where(
            'floor_number',
            2
        )->firstOrFail();


        LibraryZone::create([
            'library_floor_id' => $floor1->id,
            'code' => 'A',
            'name' => 'Koleksi Arsitektur',
            'description' => 'Koleksi buku arsitektur dan desain.',
        ]);

        LibraryZone::create([
            'library_floor_id' => $floor1->id,
            'code' => 'B',
            'name' => 'Koleksi Teknologi',
            'description' => 'Koleksi teknologi dan komputer.',
        ]);

        LibraryZone::create([
            'library_floor_id' => $floor1->id,
            'code' => 'C',
            'name' => 'Koleksi Umum',
            'description' => 'Koleksi umum dan referensi.',
        ]);
    }
}