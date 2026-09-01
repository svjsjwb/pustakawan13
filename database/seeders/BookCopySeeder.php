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
        /*
         * =========================================================
         * AMBIL SEMUA SHELF
         * =========================================================
         */

        $shelves = Shelf::orderBy('id')->get();

        if ($shelves->isEmpty()) {
            throw new \RuntimeException(
                'Shelf belum tersedia.'
            );
        }


        /*
         * =========================================================
         * BUAT SEMUA SLOT FISIK
         * =========================================================
         *
         * Setiap Shelf memiliki:
         *
         * 3 baris
         * 20 kolom
         *
         * Jadi:
         *
         * 1 Shelf = 60 slot
         *
         * Contoh:
         *
         * A-01
         * ├── Row 1 : Column 1 - 20
         * ├── Row 2 : Column 1 - 20
         * └── Row 3 : Column 1 - 20
         *
         * A-02 memiliki slotnya sendiri.
         *
         * A-01 + A-02 nantinya divisualisasikan
         * sebagai satu rak fisik dengan sekat tengah.
         */

        $slots = [];

        foreach ($shelves as $shelf) {

            for (
                $row = 1;
                $row <= $shelf->row_count;
                $row++
            ) {

                for (
                    $column = 1;
                    $column <= $shelf->column_count;
                    $column++
                ) {

                    $slots[] = [
                        'shelf_id' => $shelf->id,
                        'row' => $row,
                        'column' => $column,
                    ];
                }
            }
        }


        /*
         * =========================================================
         * HITUNG KAPASITAS TOTAL RAK
         * =========================================================
         */

        $totalSlots = count($slots);


        /*
         * =========================================================
         * AMBIL SEMUA BUKU
         * =========================================================
         *
         * Jumlah BookCopy mengikuti nilai stock
         * masing-masing buku.
         *
         * Contoh:
         *
         * Buku A → stock 1
         * Buku B → stock 5
         * Buku C → stock 10
         *
         * Maka BookCopy dibuat sebanyak:
         *
         * 1 + 5 + 10 = 16 copy
         */

        $books = Book::orderBy('id')->get();


        /*
         * =========================================================
         * HITUNG TOTAL EKSEMPLAR
         * =========================================================
         */

        $totalCopies = $books->sum('stock');


        /*
         * =========================================================
         * CEK KAPASITAS
         * =========================================================
         */

        if ($totalSlots < $totalCopies) {

            throw new \RuntimeException(
                'Jumlah slot rak tidak mencukupi untuk seluruh BookCopy. ' .
                    'Kapasitas slot: ' . $totalSlots .
                    ', kebutuhan eksemplar: ' . $totalCopies . '.'
            );
        }


        /*
         * =========================================================
         * DISTRIBUSI BOOK COPY
         * =========================================================
         *
         * Slot diisi berurutan berdasarkan Shelf.
         *
         * Contoh:
         *
         * A-01
         * Row 1 Column 1
         * Row 1 Column 2
         * ...
         * Row 1 Column 20
         * Row 2 Column 1
         * ...
         * Row 3 Column 20
         *
         * Kemudian:
         *
         * A-02
         * Row 1 Column 1
         * ...
         *
         * Jika A-02 penuh, lanjut B-01.
         */

        $slotIndex = 0;


        foreach ($books as $book) {

            /*
             * =====================================================
             * JUMLAH COPY MENGIKUTI STOCK
             * =====================================================
             */

            for (
                $copyNumber = 1;
                $copyNumber <= $book->stock;
                $copyNumber++
            ) {

                /*
                 * =================================================
                 * CEK SLOT
                 * =================================================
                 */

                if (!isset($slots[$slotIndex])) {

                    throw new \RuntimeException(
                        'Tidak ada slot tersedia untuk buku ID ' .
                            $book->id .
                            ' copy ke-' .
                            $copyNumber .
                            '.'
                    );
                }


                /*
                 * =================================================
                 * AMBIL SLOT
                 * =================================================
                 */

                $slot = $slots[$slotIndex];


                /*
                 * =================================================
                 * BUAT BARCODE
                 * =================================================
                 *
                 * Contoh:
                 *
                 * BK-00001-001
                 * BK-00001-002
                 * BK-00001-003
                 */

                $barcode =
                    'BK-' .
                    str_pad(
                        $book->id,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ) .
                    '-' .
                    str_pad(
                        $copyNumber,
                        3,
                        '0',
                        STR_PAD_LEFT
                    );


                /*
                 * =================================================
                 * BUAT BOOK COPY
                 * =================================================
                 */

                BookCopy::create([

                    'book_id' =>
                    $book->id,

                    'shelf_id' =>
                    $slot['shelf_id'],

                    'row' =>
                    $slot['row'],

                    'column' =>
                    $slot['column'],

                    'barcode' =>
                    $barcode,

                    'status' =>
                    'available',
                ]);


                /*
                 * =================================================
                 * PINDAH KE SLOT BERIKUTNYA
                 * =================================================
                 */

                $slotIndex++;
            }
        }
    }
}
