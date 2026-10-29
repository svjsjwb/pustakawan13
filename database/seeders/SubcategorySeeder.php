<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Buku Pelajaran' => ['TK', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA/SMAK/MAK', 'Referensi'],
            'Buku Umum' => ['Agama', 'Majalah', 'Peta', 'Referensi', 'Buku Anak', 'Buku Remaja', 'Buku Dewasa', 'Ilmu Sosial', 'Ilmu Murni/Sains',  'Bahasa/Sastra', 'Ilmu Terapan'],
        ];

        foreach ($data as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            foreach ($subcategories as $name) {
                Subcategory::updateOrCreate([
                    'category_id' => $category->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
