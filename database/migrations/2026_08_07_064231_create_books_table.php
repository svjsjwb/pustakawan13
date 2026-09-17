<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('judul_buku');
            $table->string('penulis');

            $table
                ->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->unsignedInteger('stok')->default(0);

            $table->enum('status', [
                'Tersedia',
                'Dipinjam'
            ])->default('Tersedia');

            $table->timestamps();

            $table->index('judul_buku');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
