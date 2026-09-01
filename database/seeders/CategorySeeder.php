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
                'Novel',
                'Cerpen',
                'Komik',
                'Dongeng',
            ],

            'Remaja' => [
                'Novel',
                'Cerpen',
                'Komik',
                'Agama',
            ],

            'Dewasa' => [
                'Novel',
                'Cerpen',
                'Agama',
                'Sejarah',
                'Teknologi',
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