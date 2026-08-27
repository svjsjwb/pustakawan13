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

            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            $table->date('reserved_at');

            $table->date('expires_at')->nullable();

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'dibatalkan',
                'selesai'
            ])->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
