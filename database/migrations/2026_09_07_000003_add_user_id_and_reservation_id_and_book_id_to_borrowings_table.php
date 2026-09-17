<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowings', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('member_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('borrowings', 'reservation_id')) {
                $table->foreignId('reservation_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('reservations')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('borrowings', 'book_id')) {
                $table->foreignId('book_id')
                    ->nullable()
                    ->after('reservation_id')
                    ->constrained('books')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'book_id')) {
                $table->dropForeign(['book_id']);
                $table->dropColumn('book_id');
            }
            if (Schema::hasColumn('borrowings', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
                $table->dropColumn('reservation_id');
            }
            if (Schema::hasColumn('borrowings', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
