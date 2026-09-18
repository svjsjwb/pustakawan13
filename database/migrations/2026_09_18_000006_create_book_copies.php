<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('shelf_id')->nullable()->constrained('shelves')->nullOnDelete();
            $table->unsignedTinyInteger('section')->nullable();
            $table->enum('side', ['front', 'back'])->default('front');
            $table->unsignedTinyInteger('row')->nullable();
            $table->unsignedTinyInteger('column')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->enum('status', ['available', 'reserved', 'borrowed', 'lost', 'damaged', 'maintenance'])->default('available');
            $table->enum('condition', ['baik', 'rusak'])->default('baik');
            $table->timestamps();

            $table->index(['shelf_id', 'section', 'side', 'row', 'column']);
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
