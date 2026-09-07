<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
         * |--------------------------------------------------------------------------
         * | KATEGORI UTAMA
         * |--------------------------------------------------------------------------
         */

        $anak = Category::updateOrCreate(
            [
                'name' => 'Anak',
                'parent_id' => null,
            ],
            [
                'level' => 1,
            ]
        );

        $remaja = Category::updateOrCreate(
            [
                'name' => 'Remaja',
                'parent_id' => null,
            ],
            [
                'level' => 1,
            ]
        );

        $dewasa = Category::updateOrCreate(
            [
                'name' => 'Dewasa',
                'parent_id' => null,
            ],
            [
                'level' => 1,
            ]
        );

        $pendidikan = Category::updateOrCreate(
            [
                'name' => 'Pendidikan',
                'parent_id' => null,
            ],
            [
                'level' => 1,
            ]
        );


        /*
         * |--------------------------------------------------------------------------
         * | SUB KATEGORI LEVEL 2 (via tabel subcategories)
         * |--------------------------------------------------------------------------
         */

        $subcategoryData = [
            $anak->id => [
                'Cerita Anak',
                'Komik Anak',
                'Pendidikan Anak',
                'Dongeng',
                'Novel',
                'Cerpen',
            ],
            $remaja->id => [
                'Novel Remaja',
                'Komik Remaja',
                'Pengembangan Diri',
                'Agama & Moral',
                'Cerpen',
            ],
            $dewasa->id => [
                'Novel Dewasa',
                'Bisnis & Ekonomi',
                'Teknologi & Sains',
                'Sejarah & Budaya',
                'Agama',
                'Cerpen',
            ],
            $pendidikan->id => [
                'Tingkat SD/MI',
                'Tingkat SMP/MTs',
                'Tingkat SMA/SMK',
                'Perguruan Tinggi',
            ],
        ];

        foreach ($subcategoryData as $categoryId => $subcategories) {
            foreach ($subcategories as $subName) {
                Subcategory::updateOrCreate(
                    [
                        'category_id' => $categoryId,
                        'name'        => $subName,
                    ]
                );
            }
        }
    }
}
