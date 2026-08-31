<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Novel',
                'description' => 'Koleksi Novel'
            ],
            [
                'name' => 'Teknologi',
                'description' => 'Buku Teknologi'
            ],
            [
                'name' => 'Sejarah',
                'description' => 'Buku Sejarah'
            ],
            [
                'name' => 'Pendidikan',
                'description' => 'Buku Pendidikan'
            ]
        ]);
    }
}
