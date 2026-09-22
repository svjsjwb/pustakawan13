<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Buku Pendidikan', 'level' => 1],
            ['name' => 'Anak', 'level' => 1],
            ['name' => 'Remaja', 'level' => 1],
            ['name' => 'Dewasa', 'level' => 1],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'parent_id' => null,
                    'level' => $category['level'],
                ]
            );
        }
    }
}
