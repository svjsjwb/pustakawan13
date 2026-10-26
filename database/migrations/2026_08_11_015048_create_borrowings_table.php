<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->nullable()
                ->constrained('books')
                ->nullOnDelete();

            // Data peminjaman
            $table->date('borrowed_at');
            $table->date('due_at');
            $table->date('returned_at')->nullable();

            // Nomor tempat duduk
            $table->string('seat_number', 10)->nullable();

            // Status peminjaman
            $table->enum('status', [
                'menunggu',
                'dipinjam',
                'diperpanjang',
                'ditolak',
                'dikembalikan',
                'terlambat',
            ])->default('menunggu');

            // Penolakan
            $table->text('rejection_reason')->nullable();

            // Perpanjangan
            $table->enum('extension_status', [
                'tidak_ada',
                'menunggu',
                'disetujui',
                'ditolak',
            ])->default('tidak_ada');

            $table->date('extension_requested_due_at')->nullable();
            $table->text('extension_reason')->nullable();
            $table->text('extension_admin_notes')->nullable();
            $table->json('extension_history')->nullable();

            // Reminder
            $table->boolean('is_reminder_sent')->default(false);

            $table->timestamps();

            // Index
            $table->index(['member_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['borrowed_at', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};