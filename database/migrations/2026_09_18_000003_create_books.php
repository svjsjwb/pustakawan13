<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Bibliographic identity.
            $table->string('judul_buku');
            $table->string('penulis');
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('edition', 100)->nullable();
            $table->string('call_number', 100)->nullable();
            $table->string('ddc', 30)->nullable();
            $table->text('description')->nullable();
            $table->string('cover')->nullable();

            // Internal identifiers.
            $table->string('sku', 100)->unique();
            $table->string('no_iventaris', 50)->nullable();
            $table->string('kode_buku', 50)->nullable();

            // Catalog classification.
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->string('main_category')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('education_level')->nullable();

            // Legacy-compatible stock/status fields used by the application.
            $table->unsignedInteger('stok')->default(0);
            $table->enum('status', ['Tersedia', 'Dipinjam'])->default('Tersedia');
            $table->string('rak', 20)->nullable();

            $table->timestamps();

            $table->index('judul_buku');
            $table->index('penulis');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index(['main_category', 'sub_category']);
            $table->index('education_level');
            $table->index('no_iventaris');
            $table->index('kode_buku');
            $table->index('ddc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
