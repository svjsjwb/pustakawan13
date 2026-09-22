<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Console\Command;

class ResetBookData extends Command
{
    protected $signature = 'library:clean-test-books';
    protected $description = 'Bersihkan buku uji coba dan tampilkan jumlah total buku';

    public function handle(): int
    {
        $testBooks = Book::where('judul_buku', 'Test Notification Book')->get();
        foreach ($testBooks as $book) {
            BookCopy::where('book_id', $book->id)->delete();
            $book->delete();
        }

        $totalBooks = Book::count();
        $totalCopies = BookCopy::count();

        $this->info("Buku uji coba berhasil dibersihkan.");
        $this->info("Total Koleksi Buku Asli: {$totalBooks} judul.");
        $this->info("Total Eksemplar (BookCopy): {$totalCopies} eksemplar.");

        return self::SUCCESS;
    }
}
