<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Buku Pelajaran', 'level' => 1],
            ['name' => 'Buku Umum', 'level' => 1],
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
