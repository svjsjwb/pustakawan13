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
            'Buku Pendidikan' => ['SD', 'SMP', 'SMA'],
            'Anak' => ['Fiksi', 'Non Fiksi'],
            'Remaja' => ['Fiksi', 'Non Fiksi'],
            'Dewasa' => ['Fiksi', 'Non Fiksi'],
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
