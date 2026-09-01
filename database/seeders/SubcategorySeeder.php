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
            'Anak' => [
                'Cerita Anak',
                'Komik Anak',
                'Pendidikan Anak',
                'Dongeng',
            ],
            'Remaja' => [
                'Novel Remaja',
                'Komik Remaja',
                'Pengembangan Diri',
                'Agama & Moral',
            ],
            'Dewasa' => [
                'Novel Dewasa',
                'Bisnis & Ekonomi',
                'Teknologi & Sains',
                'Sejarah & Budaya',
            ],
            'Pendidikan' => [
                'Tingkat SD/MI',
                'Tingkat SMP/MTs',
                'Tingkat SMA/SMK',
                'Perguruan Tinggi',
            ],
        ];

        foreach ($data as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->first();

            if ($category) {
                foreach ($subcategories as $subName) {
                    Subcategory::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'name' => $subName,
                        ]
                    );
                }
            }
        }
    }
}
