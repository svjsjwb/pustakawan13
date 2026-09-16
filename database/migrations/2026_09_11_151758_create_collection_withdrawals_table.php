<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_withdrawals', function (Blueprint $table) {

            $table->id();

            /*
             * Jenis penarikan:
             *
             * book = seluruh buku ditarik
             * copy = satu eksemplar ditarik
             */
            $table->enum('type', [
                'book',
                'copy',
            ]);

            /*
             * Buku yang terkait.
             *
             * Nullable supaya histori tetap aman
             * jika suatu saat data buku benar-benar dihapus.
             */
            $table->foreignId('book_id')
                ->nullable()
                ->constrained('books')
                ->nullOnDelete();

            /*
             * Eksemplar yang terkait.
             *
             * Nullable karena eksemplar akan dihapus
             * setelah histori penarikan disimpan.
             */
            $table->foreignId('book_copy_id')
                ->nullable()
                ->constrained('book_copies')
                ->nullOnDelete();

            /*
             * Snapshot data untuk kebutuhan laporan.
             *
             * Data ini tetap ada walaupun relasi
             * buku / eksemplar nantinya hilang.
             */
            $table->string('book_title');

            $table->string('barcode')
                ->nullable();

            /*
             * Jumlah eksemplar yang ditarik.
             *
             * Buku      = jumlah seluruh eksemplar
             * Eksemplar = 1
             */
            $table->unsignedInteger('quantity')
                ->default(1);

            /*
             * Alasan wajib dari pengguna.
             */
            $table->text('reason');

            /*
             * Waktu penarikan.
             */
            $table->timestamp('withdrawn_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_withdrawals');
    }
};