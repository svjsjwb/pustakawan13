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
            throw new \RuntimeException(
                'Data shelf belum tersedia. Jalankan ShelfSeeder terlebih dahulu.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Buat daftar seluruh slot rak
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | 2. Ambil maksimal 200 judul yang memiliki eksemplar
        |--------------------------------------------------------------------------
        |
        | Tetap menyimpan seluruh judul buku di database.
        | Hanya 200 judul yang akan mempunyai BookCopy.
        |
        */

        $booksWithCopies = Book::orderBy('id')
            ->take(170)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. Setiap judul memiliki maksimal 3 eksemplar
        |--------------------------------------------------------------------------
        */

        $copiesPerBook = 3;

        $totalCopiesNeeded = $booksWithCopies->count() * $copiesPerBook;

        /*
        |--------------------------------------------------------------------------
        | 4. Cek kapasitas rak
        |--------------------------------------------------------------------------
        */

        if (count($slots) < $totalCopiesNeeded) {
            throw new \RuntimeException(
                'Kapasitas slot rak (' . count($slots) .
                ') tidak mencukupi untuk jumlah eksemplar (' .
                $totalCopiesNeeded . ').'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Cari slot yang sudah digunakan
        |--------------------------------------------------------------------------
        |
        | Supaya ketika seeder dijalankan ulang, slot lama tidak ditimpa.
        |
        */

        $usedSlots = BookCopy::query()
            ->get(['shelf_id', 'row', 'column'])
            ->map(function ($copy) {
                return $copy->shelf_id . '-' . $copy->row . '-' . $copy->column;
            })
            ->flip();

        /*
        |--------------------------------------------------------------------------
        | 6. Buat BookCopy untuk 200 judul
        |--------------------------------------------------------------------------
        */

        foreach ($booksWithCopies as $book) {

            for ($copyNumber = 1; $copyNumber <= $copiesPerBook; $copyNumber++) {

                $barcode = sprintf(
                    'BC-%05d-%03d',
                    $book->id,
                    $copyNumber
                );

                /*
                |--------------------------------------------------------------------------
                | Kalau barcode sudah ada, jangan buat ulang.
                |--------------------------------------------------------------------------
                */

                $existingCopy = BookCopy::where('barcode', $barcode)->first();

                if ($existingCopy) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Cari slot kosong
                |--------------------------------------------------------------------------
                */

                $slot = null;

                foreach ($slots as $candidateSlot) {
                    $slotKey = $candidateSlot['shelf_id'] .
                        '-' .
                        $candidateSlot['row'] .
                        '-' .
                        $candidateSlot['column'];

                    if (!$usedSlots->has($slotKey)) {
                        $slot = $candidateSlot;
                        break;
                    }
                }

                if (!$slot) {
                    throw new \RuntimeException(
                        'Tidak ada slot rak kosong untuk BookCopy.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Tandai slot sebagai terpakai
                |--------------------------------------------------------------------------
                */

                $slotKey = $slot['shelf_id'] .
                    '-' .
                    $slot['row'] .
                    '-' .
                    $slot['column'];

                $usedSlots->put($slotKey, true);

                /*
                |--------------------------------------------------------------------------
                | Buat BookCopy
                |--------------------------------------------------------------------------
                */

                BookCopy::create([
                    'book_id' => $book->id,
                    'barcode' => $barcode,
                    'shelf_id' => $slot['shelf_id'],
                    'section' => 1,
                    'side' => 'front',
                    'row' => $slot['row'],
                    'column' => $slot['column'],
                    'status' => 'available',
                    'condition' => 'baik',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Sinkronkan stok seluruh buku
        |--------------------------------------------------------------------------
        |
        | Stok = jumlah BookCopy yang statusnya available.
        |
        */

        Book::query()
            ->withCount([
                'copies as available_copies_count' => function ($query) {
                    $query->where('status', 'available');
                },
            ])
            ->each(function ($book) {
                $availableStock = $book->available_copies_count;

                $book->update([
                    'stok' => $availableStock,
                ]);
            });

        /*
        |--------------------------------------------------------------------------
        | 8. Informasi hasil seeder
        |--------------------------------------------------------------------------
        */

        $totalCopies = BookCopy::count();

        $this->command->info(
            "BookCopySeeder selesai. Total BookCopy saat ini: {$totalCopies}."
        );

        $this->command->info(
            "Target: maksimal 200 judul dengan 2 eksemplar per judul."
        );
    }
}