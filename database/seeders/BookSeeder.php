<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL KATEGORI + SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        $categories = Category::with('subcategories')
            ->whereIn('name', [
                'Anak',
                'Remaja',
                'Dewasa',
                'Buku Pendidikan',
            ])
            ->get()
            ->keyBy('name');


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN SEMUA KATEGORI ADA
        |--------------------------------------------------------------------------
        */

        $categoryNames = [
            'Anak',
            'Remaja',
            'Dewasa',
            'Buku Pendidikan',
        ];

        foreach ($categoryNames as $categoryName) {

            if (! $categories->has($categoryName)) {
                throw new \Exception(
                    "Kategori '{$categoryName}' tidak ditemukan."
                );
            }

            $category = $categories->get($categoryName);

            if ($category->subcategories->isEmpty()) {
                throw new \Exception(
                    "Kategori '{$categoryName}' belum memiliki subkategori."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BUAT 50 BUKU DUMMY
        |--------------------------------------------------------------------------
        |
        | Pembagian:
        |
        | 1  → Anak
        | 2  → Remaja
        | 3  → Dewasa
        | 0  → Buku Pendidikan
        |
        */

        for ($i = 1; $i <= 50; $i++) {

            $category = match ($i % 4) {

                1 => $categories->get('Anak'),

                2 => $categories->get('Remaja'),

                3 => $categories->get('Dewasa'),

                0 => $categories->get('Buku Pendidikan'),
            };


            /*
            |--------------------------------------------------------------------------
            | PILIH SUBKATEGORI
            |--------------------------------------------------------------------------
            |
            | Subkategori dipilih bergantian berdasarkan subkategori
            | yang memang dimiliki oleh kategori tersebut.
            |
            */

            $subcategories = $category
                ->subcategories
                ->values();

            $subcategory = $subcategories[($i - 1) % $subcategories->count()];


            /*
            |--------------------------------------------------------------------------
            | SIMPAN BUKU
            |--------------------------------------------------------------------------
            */

            Book::create([

                'category_id' =>
                $category->id,

                'subcategory_id' =>
                $subcategory->id,

                'title' =>
                'Buku Dummy ' . $i,

                'sku' =>
                'BK-2026-' .
                    str_pad(
                        $i,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ),

                'author' =>
                'Penulis Dummy ' . $i,

                'publisher' =>
                'Penerbit Dummy',

                'publication_year' =>
                2020 + ($i % 6),

                'isbn' =>
                '978000000' .
                    str_pad(
                        $i,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),

                'call_number' =>
                '000 DUM ' . $i,

                'stock' => 5,

                'available_stock' => 5,

                'description' =>
                'Deskripsi buku dummy nomor ' . $i,

                'cover' => null,
            ]);
        }
    }
}
