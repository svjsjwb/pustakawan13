<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryFloor;
use App\Models\LibraryZone;

class LibraryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $floor1 = LibraryFloor::where('floor_number', 1)->firstOrFail();
        $floor3 = LibraryFloor::where('floor_number', 3)->firstOrFail();


        /*
         * =========================================================
         * LANTAI 1 — TSSO
         * =========================================================
         */

        LibraryZone::updateOrCreate(
            [
                'library_floor_id' => $floor1->id,
                'code' => 'TSSO',
            ],
            [
                'name' => 'TSSO',
                'description' => 'Koleksi TSSO untuk anak, remaja, dan dewasa.',
            ]
        );


        /*
         * =========================================================
         * LANTAI 3 — TSPM
         * =========================================================
         */

        LibraryZone::updateOrCreate(
            [
                'library_floor_id' => $floor3->id,
                'code' => 'TSPM',
            ],
            [
                'name' => 'TSPM',
                'description' => 'Koleksi TSPM berdasarkan klasifikasi DDC.',
            ]
        );
    }
}