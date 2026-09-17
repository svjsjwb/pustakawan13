<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('books')
            ->orderBy('id')
            ->each(function ($book) {

                // Jangan membuat copy lagi jika buku ini
                // sudah memiliki book_copies.
                $existingCopies = DB::table('book_copies')
                    ->where('book_id', $book->id)
                    ->count();

                if ($existingCopies > 0) {
                    return;
                }

                /*
                 * Pastikan nilai stok tidak negatif.
                 */
                $stock = max(0, (int) $book->stock);

                $availableStock = max(
                    0,
                    min(
                        (int) $book->available_stock,
                        $stock
                    )
                );

                /*
                 * Jumlah buku yang sedang tidak tersedia.
                 *
                 * Untuk data lama, kita belum tahu copy mana
                 * yang benar-benar sedang dipinjam.
                 *
                 * Jadi sementara kita representasikan
                 * sisanya sebagai borrowed.
                 */
                $borrowedStock = $stock - $availableStock;

                /*
                 * Buat copy yang AVAILABLE.
                 */
                for ($i = 1; $i <= $availableStock; $i++) {
                    DB::table('book_copies')->insert([
                        'book_id' => $book->id,

                        'barcode' => sprintf(
                            'BK-%05d-%03d',
                            $book->id,
                            $i
                        ),

                        'status' => 'available',

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                /*
                 * Buat copy yang sementara dianggap BORROWED.
                 */
                for (
                    $i = $availableStock + 1;
                    $i <= $stock;
                    $i++
                ) {
                    DB::table('book_copies')->insert([
                        'book_id' => $book->id,

                        'barcode' => sprintf(
                            'BK-%05d-%03d',
                            $book->id,
                            $i
                        ),

                        'status' => 'borrowed',

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * Hapus copy hasil migrasi berdasarkan pola
         * barcode yang dibuat di migration ini.
         */
        DB::table('book_copies')
            ->where('barcode', 'like', 'BK-%')
            ->delete();
    }
};
