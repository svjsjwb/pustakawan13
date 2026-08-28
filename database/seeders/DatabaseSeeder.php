<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            CategorySeeder::class,

            BookSeeder::class,

            MemberSeeder::class,

            LibraryFloorSeeder::class,

            LibraryZoneSeeder::class,

            ShelfSeeder::class,

            BookCopySeeder::class,

            AdminSeeder::class,

        ]);
    }
}
