<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [];

        for ($i = 1; $i <= 50; $i++) {

            $categoryId = $i % 2 === 0 ? 2 : 1;

            $books[] = [
                'category_id' => $categoryId,
                'title' => 'Buku Dummy ' . $i,
                'author' => 'Penulis Dummy ' . $i,
                'publisher' => 'Penerbit Dummy',
                'publication_year' => 2020 + ($i % 6),
                'isbn' => '978000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'call_number' => '000 DUM ' . $i,
                'stock' => 5,
                'available_stock' => 5,
                'description' => 'Deskripsi buku dummy nomor ' . $i,
                'cover' => null,
            ];
        }

        Book::insert($books);
    }
}
