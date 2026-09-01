<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Shelf;
use App\Models\BookCopy;

class BookCopySeeder extends Seeder
{
    public function run(): void
    {
        $shelves = Shelf::orderBy('id')->get();

        if ($shelves->isEmpty()) {
            throw new \RuntimeException('Data Shelf belum tersedia. Jalankan ShelfSeeder terlebih dahulu.');
        }

        // Buat daftar slot fisik rak
        $slots = [];
        foreach ($shelves as $shelf) {
            for ($row = 1; $row <= $shelf->row_count; $row++) {
                for ($column = 1; $column <= $shelf->column_count; $column++) {
                    $slots[] = [
                        'shelf_id' => $shelf->id,
                        'row'      => $row,
                        'column'   => $column,
                    ];
                }
            }
        }

        $totalSlots = count($slots);
        $books = Book::orderBy('id')->get();
        $totalCopiesNeeded = $books->sum('stok');

        if ($totalSlots < $totalCopiesNeeded) {
            throw new \RuntimeException(
                "Kapasitas slot rak ({$totalSlots}) tidak mencukupi untuk jumlah eksemplar ({$totalCopiesNeeded})."
            );
        }

        $slotIndex = 0;
        foreach ($books as $book) {
            for ($copyNumber = 1; $copyNumber <= $book->stok; $copyNumber++) {
                if (!isset($slots[$slotIndex])) {
                    break;
                }

                $slot = $slots[$slotIndex];
                $barcode = 'BC-' . str_pad($book->id, 5, '0', STR_PAD_LEFT) . '-' . str_pad($copyNumber, 3, '0', STR_PAD_LEFT);

                BookCopy::updateOrCreate(
                    [
                        'barcode' => $barcode,
                    ],
                    [
                        'book_id'  => $book->id,
                        'shelf_id' => $slot['shelf_id'],
                        'row'      => $slot['row'],
                        'column'   => $slot['column'],
                        'side'     => 'front',
                        'status'   => 'available',
                    ]
                );

                $slotIndex++;
            }
        }
    }
}

