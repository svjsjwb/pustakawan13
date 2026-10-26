<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();

                // Relasi ke user
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                // Identitas anggota
                $table->string('member_number')->unique();
                $table->string('name');

                $table->string('division')->nullable();
                $table->string('nis_nip')->nullable();

                $table->enum('gender', [
                    'Laki-laki',
                    'Perempuan'
                ])->nullable();

                $table->string('class')->nullable();
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->date('registered_at')->nullable();

                // Pertahankan enum yang digunakan aplikasi
                $table->enum('status', [
                    'aktif',
                    'nonaktif'
                ])->default('nonaktif');

                $table->timestamps();

                // Index
                $table->index('user_id');
                $table->index('email');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
