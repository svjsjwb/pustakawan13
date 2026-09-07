<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            'Anak' => [
                'Fiksi',
                'Non-Fiksi',
            ],

            'Remaja' => [
                'Fiksi',
                'Non-Fiksi',
            ],

            'Dewasa' => [
                'Fiksi',
                'Non-Fiksi',
            ],

            'Buku Pendidikan' => [
                'SD/MI',
                'SMP/MTs',
                'SMA/MA/SMK',
            ],

        ];


        foreach ($categories as $categoryName => $subcategories) {

            $category = Category::updateOrCreate(
                [
                    'name' => $categoryName,
                ],
                [
                    'description' => 'Kategori buku ' . $categoryName,
                ]
            );


            foreach ($subcategories as $subcategoryName) {

                Subcategory::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $subcategoryName,
                    ]
                );

            }
        }
    }
}