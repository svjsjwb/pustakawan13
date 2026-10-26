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

            // Relasi user
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Relasi anggota
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();

            // Relasi buku
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            // Copy buku
            $table->unsignedBigInteger('book_copy_id')->nullable();

            // Peminjaman
            $table->unsignedBigInteger('borrowing_id')->nullable();

            // Waktu reservasi
            $table->date('reserved_at');
            $table->date('expires_at')->nullable();

            // Nomor tempat duduk
            $table->string('seat_number', 10)->nullable();

            // Status
            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'dibatalkan',
                'selesai'
            ])->default('menunggu');

            // Alasan penolakan
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
