<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete();
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->nullOnDelete();
            $table->date('reserved_at');
            $table->date('expires_at')->nullable();
            $table->string('seat_number', 10)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'selesai'])->default('menunggu');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index(['reserved_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
