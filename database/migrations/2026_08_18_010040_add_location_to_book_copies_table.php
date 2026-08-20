<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->foreignId('shelf_id')
                ->nullable()
                ->after('book_id')
                ->constrained('shelves')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->unsignedInteger('row')
                ->nullable()
                ->after('shelf_id');

            $table->unsignedInteger('column')
                ->nullable()
                ->after('row');
        });
    }

    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->dropForeign(['shelf_id']);
            $table->dropColumn([
                'shelf_id',
                'row',
                'column',
            ]);
        });
    }
};
