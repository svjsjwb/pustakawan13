<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $novel = Category::where(
            'name',
            'Novel'
        )->firstOrFail();

        $teknologi = Category::where(
            'name',
            'Teknologi'
        )->firstOrFail();

        $sejarah = Category::where(
            'name',
            'Sejarah'
        )->firstOrFail();

        $pendidikan = Category::where(
            'name',
            'Pendidikan'
        )->firstOrFail();


        for ($i = 1; $i <= 50; $i++) {

            $category = match ($i % 4) {

                1 => $novel,

                2 => $teknologi,

                3 => $sejarah,

                0 => $pendidikan,
            };


            Book::create([

                'category_id' => $category->id,

                'title' =>
                'Buku Dummy ' . $i,

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
