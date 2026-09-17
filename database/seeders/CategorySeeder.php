<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['name' => 'Anak-Anak', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Remaja', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dewasa', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pendidikan', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Novel', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teknologi', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sejarah', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}