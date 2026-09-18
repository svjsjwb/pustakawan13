<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class BookCopySeeder extends Seeder
{
    public function run(): void
    {
        $shelves = Shelf::orderBy('id')->get();

        if ($shelves->isEmpty()) {
            throw new \RuntimeException('Data shelf belum tersedia. Jalankan ShelfSeeder terlebih dahulu.');
        }

        $slots = [];
        foreach ($shelves as $shelf) {
            for ($row = 1; $row <= $shelf->row_count; $row++) {
                for ($column = 1; $column <= $shelf->column_count; $column++) {
                    $slots[] = [
                        'shelf_id' => $shelf->id,
                        'row' => $row,
                        'column' => $column,
                    ];
                }
            }
        }

        $books = Book::orderBy('id')->get();
        $totalCopiesNeeded = $books->sum('stok');

        if (count($slots) < $totalCopiesNeeded) {
            throw new \RuntimeException(
                "Kapasitas slot rak (" . count($slots) . ") tidak mencukupi untuk jumlah eksemplar ({$totalCopiesNeeded})."
            );
        }

        $slotIndex = 0;

        foreach ($books as $book) {
            for ($copyNumber = 1; $copyNumber <= $book->stok; $copyNumber++) {
                $slot = $slots[$slotIndex++];
                $barcode = sprintf(
                    'BC-%05d-%03d',
                    $book->id,
                    $copyNumber
                );

                BookCopy::updateOrCreate(
                    ['barcode' => $barcode],
                    [
                        'book_id' => $book->id,
                        'shelf_id' => $slot['shelf_id'],
                        'section' => 1,
                        'side' => 'front',
                        'row' => $slot['row'],
                        'column' => $slot['column'],
                        'status' => 'available',
                        'condition' => 'baik',
                    ]
                );
            }
        }
    }
}
