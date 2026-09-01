<?php

namespace Database\Seeders;

use App\Models\Category;
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
         * | SUB KATEGORI LEVEL 2
         * |--------------------------------------------------------------------------
         */

        Category::updateOrCreate(
            [
                'name' => 'Cerita Anak',
                'parent_id' => $anak->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Komik Anak',
                'parent_id' => $anak->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Pendidikan Anak',
                'parent_id' => $anak->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Novel Remaja',
                'parent_id' => $remaja->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Komik Remaja',
                'parent_id' => $remaja->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Pengembangan Diri',
                'parent_id' => $remaja->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Novel Dewasa',
                'parent_id' => $dewasa->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Bisnis',
                'parent_id' => $dewasa->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Teknologi',
                'parent_id' => $dewasa->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'SD',
                'parent_id' => $pendidikan->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'SMP',
                'parent_id' => $pendidikan->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'SMA',
                'parent_id' => $pendidikan->id,
            ],
            [
                'level' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'name' => 'Perguruan Tinggi',
                'parent_id' => $pendidikan->id,
            ],
            [
                'level' => 2,
            ]
        );
    }
}
