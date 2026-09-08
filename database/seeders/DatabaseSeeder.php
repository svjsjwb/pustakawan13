<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            UserSeeder::class,

            CategorySeeder::class,

            BookSeeder::class,

            MemberSeeder::class,

            LibraryFloorSeeder::class,

            LibraryZoneSeeder::class,

            ShelfSeeder::class,

            BookCopySeeder::class,

        ]);
    }
}
