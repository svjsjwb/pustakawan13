<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LibraryZone;
use App\Models\Shelf;

class ShelfSeeder extends Seeder
{
    public function run(): void
    {
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

        $this->createShelves($zoneA, 'A', 2, 0, 0);
        $this->createShelves($zoneB, 'B', 2, 0, 5);
        $this->createShelves($zoneC, 'C', 2, 0, 10);
    }

    private function createShelves(
        LibraryZone $zone,
        string $prefix,
        int $count,
        float $startX,
        float $startY
    ): void {
        for ($i = 1; $i <= $count; $i++) {
            $code = $prefix . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

            Shelf::updateOrCreate(
                ['code' => $code],
                [
                    'library_zone_id' => $zone->id,
                    'name'            => 'Rak ' . $zone->name . ' ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'row_count'       => 3,
                    'column_count'    => 30,
                    'width'           => 4,
                    'height'          => 2.5,
                    'depth'           => 0.5,
                    'position_x'      => $startX + (($i - 1) * 5),
                    'position_y'      => $startY,
                ]
            );
        }
    }
}
