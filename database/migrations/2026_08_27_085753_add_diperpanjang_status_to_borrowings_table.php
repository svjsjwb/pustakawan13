<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE borrowings
                MODIFY status
                ENUM(
                    'dipinjam',
                    'diperpanjang',
                    'dikembalikan',
                    'terlambat'
                )
                NOT NULL
                DEFAULT 'dipinjam'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE borrowings
                MODIFY status
                ENUM(
                    'dipinjam',
                    'dikembalikan',
                    'terlambat'
                )
                NOT NULL
                DEFAULT 'dipinjam'
            ");
        }
    }
};