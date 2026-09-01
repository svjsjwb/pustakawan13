<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database aplikasi.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
            LibraryFloorSeeder::class,
            LibraryZoneSeeder::class,
            ShelfSeeder::class,
            RackSeeder::class,
            BookSeeder::class,
            BookCopySeeder::class,
            MemberSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
