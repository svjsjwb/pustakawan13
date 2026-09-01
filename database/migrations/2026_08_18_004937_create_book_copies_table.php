<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();

            /*
             * Buku induk.
             *
             * Contoh:
             * books.id = 10
             * berarti semua copy di bawahnya
             * adalah eksemplar dari buku yang sama.
             */
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
             * Barcode unik untuk setiap eksemplar fisik.
             *
             * Nullable karena pada tahap awal
             * buku lama mungkin belum memiliki barcode.
             */
            $table->string('barcode')->nullable()->unique();

            /*
             * Status fisik eksemplar.
             */
            $table->enum('status', [
                'available',
                'reserved',
                'borrowed',
                'lost',
                'damaged',
                'maintenance',
            ])->default('available');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
