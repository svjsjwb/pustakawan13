<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('event_date');
            $table->string('location');
            $table->text('description');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('event_date');
        });

        Schema::create('collection_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['book', 'copy']);
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete();
            $table->string('book_title');
            $table->string('barcode')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->text('reason');
            $table->timestamp('withdrawn_at');
            $table->timestamps();
            $table->index(['type', 'withdrawn_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_withdrawals');
        Schema::dropIfExists('library_events');
    }
};
