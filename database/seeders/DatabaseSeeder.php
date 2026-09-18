<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
            LibraryFloorSeeder::class,
            LibraryZoneSeeder::class,
            ShelfSeeder::class,
            RackSeeder::class,
            BookSeeder::class,
            BookCopySeeder::class,
            MemberSeeder::class,
        ]);
    }
}
