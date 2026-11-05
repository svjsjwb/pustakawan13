<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('borrowing_id')
                ->nullable()
                ->constrained('borrowings')
                ->nullOnDelete();

            $table->foreignId('book_id')
                ->nullable()
                ->constrained('books')
                ->nullOnDelete();

            $table->foreignId('book_copy_id')
                ->nullable()
                ->constrained('book_copies')
                ->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            // Index
            $table->index(['borrowing_id', 'book_id']);
            $table->index('book_copy_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_details');
    }
};
