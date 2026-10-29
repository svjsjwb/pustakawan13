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
             * Rak tempat eksemplar disimpan.
             */
            $table->foreignId('shelf_id')
                ->nullable()
                ->constrained('shelves')
                ->nullOnDelete();

            /*
             * Posisi fisik eksemplar di dalam rak.
             */
            $table->unsignedInteger('section')
                ->nullable();

            $table->string('side', 20)
                ->nullable();

            $table->unsignedInteger('row')
                ->nullable();

            $table->unsignedInteger('column')
                ->nullable();

            /*
             * Nomor eksemplar.
             *
             * Contoh:
             * Buku A -> c.1, c.2, c.3
             * Buku B -> c.1, c.2, c.3
             */
            $table->string('copy', 20);

            /*
             * Status operasional eksemplar.
             */
            $table->enum('status', [
                'available',
                'reserved',
                'borrowed',
                'maintenance',
                'lost',
            ])->default('available');

            /*
             * Kondisi fisik eksemplar.
             */
            $table->enum('condition', [
                'baik',
                'rusak',
                'hilang',
            ])->default('baik');

            /*
             * Copy hanya unik dalam buku yang sama.
             *
             * Buku A -> c.1
             * Buku B -> c.1
             * diperbolehkan.
             */
            $table->unique([
                'book_id',
                'copy',
            ]);

            /*
             * Index untuk pencarian posisi copy.
             */
            $table->index([
                'shelf_id',
                'section',
                'side',
                'row',
                'column',
            ]);

            /*
             * Index untuk pencarian copy berdasarkan
             * buku dan status.
             */
            $table->index([
                'book_id',
                'status',
            ]);

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
