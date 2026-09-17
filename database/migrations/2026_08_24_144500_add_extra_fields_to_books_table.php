<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('no_iventaris', 50)->nullable()->after('status');
            $table->string('kode_buku', 50)->nullable()->after('no_iventaris');
            $table->string('ddc', 30)->nullable()->after('kode_buku');
            $table->string('rak', 20)->nullable()->after('ddc');
            $table->string('edition', 50)->nullable()->after('rak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['no_iventaris', 'kode_buku', 'ddc', 'rak', 'edition']);
        });
    }
};
