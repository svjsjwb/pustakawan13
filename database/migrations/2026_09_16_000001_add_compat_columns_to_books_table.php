<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'judul_buku')) {
                $table->string('judul_buku')->nullable()->after('title');
            }
            if (!Schema::hasColumn('books', 'penulis')) {
                $table->string('penulis')->nullable()->after('author');
            }
            if (!Schema::hasColumn('books', 'stok')) {
                $table->integer('stok')->default(0)->after('available_stock');
            }
            if (!Schema::hasColumn('books', 'status')) {
                $table->string('status', 50)->default('Tersedia')->after('stok');
            }
        });

        // Sync initial data from existing columns
        try {
            DB::statement("UPDATE books SET 
                judul_buku = COALESCE(judul_buku, title),
                penulis = COALESCE(penulis, author),
                stok = COALESCE(stok, available_stock, stock, 0),
                status = CASE WHEN COALESCE(available_stock, stock, 0) > 0 THEN 'Tersedia' ELSE 'Dipinjam' END
            ");
        } catch (\Throwable $e) {
            // Ignore if error during initial sync
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['judul_buku', 'penulis', 'stok', 'status'] as $col) {
                if (Schema::hasColumn('books', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
