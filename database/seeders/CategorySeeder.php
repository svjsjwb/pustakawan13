<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

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