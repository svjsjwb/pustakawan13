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
        'name' => 'Anak-Anak',
        'description' => 'Buku untuk anak-anak'
    ],
    [
        'name' => 'Remaja',
        'description' => 'Buku untuk remaja'
    ],
    [
        'name' => 'Dewasa',
        'description' => 'Buku untuk dewasa'
    ],
    [
        'name' => 'Pendidikan',
        'description' => 'Buku pendidikan'
    ],
    [
        'name' => 'Novel',
        'description' => 'Koleksi novel'
    ],
    [
        'name' => 'Teknologi',
        'description' => 'Buku teknologi'
    ],
    [
        'name' => 'Sejarah',
        'description' => 'Buku sejarah'
    ]
]);
    }
}