<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('book_copies', 'section')) {
            Schema::table('book_copies', function (Blueprint $table) {
                $table->unsignedTinyInteger('section')
                    ->default(1)
                    ->after('shelf_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('book_copies', 'section')) {
            Schema::table('book_copies', function (Blueprint $table) {
                $table->dropColumn('section');
            });
        }
    }
};