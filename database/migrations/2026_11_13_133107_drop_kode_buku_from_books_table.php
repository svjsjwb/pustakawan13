<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('books', 'kode_buku')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropIndex('books_kode_buku_index');
                $table->dropColumn('kode_buku');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('books', 'kode_buku')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('kode_buku', 50)->nullable();
                $table->index('kode_buku');
            });
        }
    }
};