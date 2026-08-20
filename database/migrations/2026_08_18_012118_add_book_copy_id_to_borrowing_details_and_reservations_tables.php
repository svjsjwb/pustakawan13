<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->foreignId('book_copy_id')
                ->nullable()
                ->after('book_id')
                ->constrained('book_copies')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('book_copy_id')
                ->nullable()
                ->after('book_id')
                ->constrained('book_copies')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropForeign(['book_copy_id']);
            $table->dropColumn('book_copy_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['book_copy_id']);
            $table->dropColumn('book_copy_id');
        });
    }
};
