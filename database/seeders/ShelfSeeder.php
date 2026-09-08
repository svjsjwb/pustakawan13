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
         * AMBIL ZONA
         * =========================================================
         *
         * Semua zona A, B, C berada di LANTAI 1.
         */

        $zoneA = LibraryZone::where('code', 'A')
            ->whereHas('floor', function ($query) {
                $query->where('floor_number', 1);
            })
            ->firstOrFail();


        $zoneB = LibraryZone::where('code', 'B')
            ->whereHas('floor', function ($query) {
                $query->where('floor_number', 1);
            })
            ->firstOrFail();


        $zoneC = LibraryZone::where('code', 'C')
            ->whereHas('floor', function ($query) {
                $query->where('floor_number', 1);
            })
            ->firstOrFail();


        /*
         * =========================================================
         * LANTAI 1 - ZONA A
         * =========================================================
         *
         * A-01
         * A-02
         */

        $this->createShelves(
            $zoneA,
            'A',
            2,
            0,
            0
        );


        /*
         * =========================================================
         * LANTAI 1 - ZONA B
         * =========================================================
         *
         * B-01
         * B-02
         */

        $this->createShelves(
            $zoneB,
            'B',
            2,
            0,
            5
        );


        /*
         * =========================================================
         * LANTAI 1 - ZONA C
         * =========================================================
         *
         * C-01
         * C-02
         */

        $this->createShelves(
            $zoneC,
            'C',
            2,
            0,
            10
        );
    }


    /*
     * =============================================================
     * CREATE SHELVES
     * =============================================================
     */

    private function createShelves(
        LibraryZone $zone,
        string $prefix,
        int $count,
        float $startX,
        float $startY
    ): void {

        for ($i = 1; $i <= $count; $i++) {

            Shelf::create([

                /*
                 * Zona
                 */

                'library_zone_id' => $zone->id,


                /*
                 * Kode
                 *
                 * A-01
                 * A-02
                 */

                'code' =>
                $prefix . '-' .
                    str_pad(
                        $i,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ),


                /*
                 * Nama
                 */

                'name' =>
                'Rak ' .
                    $zone->name .
                    ' ' .
                    str_pad(
                        $i,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ),


                /*
                 * =================================================
                 * DIMENSI SLOT
                 * =================================================
                 *
                 * 3 baris
                 * 20 kolom
                 *
                 * = 60 slot per shelf
                 */

                'row_count' => 3,

                'column_count' => 30,


                /*
                 * =================================================
                 * UKURAN FISIK
                 * =================================================
                 */

                'width' => 4,

                'height' => 2.5,

                'depth' => 0.5,


                /*
                 * =================================================
                 * POSISI
                 * =================================================
                 *
                 * A → y 0
                 * B → y 5
                 * C → y 10
                 *
                 * Jadi tidak saling menumpuk.
                 */

                'position_x' =>
                $startX + (($i - 1) * 5),

                'position_y' =>
                $startY,
            ]);
        }
    }
}
