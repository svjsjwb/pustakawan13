<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryZone;
use App\Models\Shelf;

class ShelfSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * =========================================================
         * LANTAI 1 — TSSO
         * =========================================================
         */

        $zoneTsso = LibraryZone::where('code', 'TSSO')
            ->whereHas('floor', function ($query) {
                $query->where('floor_number', 1);
            })
            ->firstOrFail();


        $tssoShelves = [
            [
                'code' => 'ANAK',
                'name' => 'TSSO-Anak',
                'position_y' => 0,
            ],
            [
                'code' => 'REMAJA',
                'name' => 'TSSO-Remaja',
                'position_y' => 5,
            ],
            [
                'code' => 'DEWASA',
                'name' => 'TSSO-Dewasa',
                'position_y' => 10,
            ],
        ];


        foreach ($tssoShelves as $shelf) {

            Shelf::updateOrCreate(
                [
                    'library_zone_id' => $zoneTsso->id,
                    'code' => $shelf['code'],
                ],
                [
                    'name' => $shelf['name'],

                    'row_count' => 3,

                    'column_count' => 30,

                    'width' => 4,

                    'height' => 2.5,

                    'depth' => 0.5,

                    'position_x' => 0,

                    'position_y' => $shelf['position_y'],
                ]
            );
        }


        /*
         * =========================================================
         * LANTAI 3 — TSPM
         * =========================================================
         */

        $zoneTspm = LibraryZone::where('code', 'TSPM')
            ->whereHas('floor', function ($query) {
                $query->where('floor_number', 3);
            })
            ->firstOrFail();


        $tspmShelves = [
            [
                'code' => '000',
                'name' => '000 – Karya Umum',
                'position_y' => 0,
            ],
            [
                'code' => '100',
                'name' => '100 – Filsafat',
                'position_y' => 5,
            ],
            [
                'code' => '200',
                'name' => '200 – Agama',
                'position_y' => 10,
            ],
            [
                'code' => '300',
                'name' => '300 – Ilmu-ilmu Sosial',
                'position_y' => 15,
            ],
            [
                'code' => '400',
                'name' => '400 – Bahasa',
                'position_y' => 20,
            ],
            [
                'code' => '500',
                'name' => '500 – Ilmu-ilmu Murni',
                'position_y' => 25,
            ],
            [
                'code' => '600',
                'name' => '600 – Ilmu-ilmu Terapan',
                'position_y' => 30,
            ],
            [
                'code' => '700',
                'name' => '700 – Kesenian dan Olahraga',
                'position_y' => 35,
            ],
            [
                'code' => '800',
                'name' => '800 – Kesusastraan',
                'position_y' => 40,
            ],
            [
                'code' => '900',
                'name' => '900 – Geografi dan Sejarah',
                'position_y' => 45,
            ],
            [
                'code' => 'Majalah',
                'name' => 'Majalah',
                'position_y' => 50,
            ],
        ];


        foreach ($tspmShelves as $shelf) {

            Shelf::updateOrCreate(
                [
                    'library_zone_id' => $zoneTspm->id,
                    'code' => $shelf['code'],
                ],
                [
                    'name' => $shelf['name'],

                    'row_count' => 3,

                    'column_count' => 30,

                    'width' => 4,

                    'height' => 2.5,

                    'depth' => 0.5,

                    'position_x' => 0,

                    'position_y' => $shelf['position_y'],
                ]
            );
        }
    }
}