<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan book_copy_id ke borrowing_details jika belum ada
        if (!Schema::hasColumn('borrowing_details', 'book_copy_id')) {
            Schema::table('borrowing_details', function (Blueprint $table) {
                $table->foreignId('book_copy_id')
                    ->nullable()
                    ->after('book_id')
                    ->constrained('book_copies')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }

        // book_copy_id pada reservations sudah dibuat
        // langsung di migration create_reservations.
        // Jadi tidak perlu ditambahkan lagi di sini.
    }

    public function down(): void
    {
        if (Schema::hasColumn('borrowing_details', 'book_copy_id')) {
            Schema::table('borrowing_details', function (Blueprint $table) {
                $table->dropForeign(['book_copy_id']);
                $table->dropColumn('book_copy_id');
            });
        }
    }
};