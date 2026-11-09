<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX IF NOT EXISTS books_cover_presence_created_idx ON books ((CASE WHEN NULLIF(TRIM(cover), '') IS NOT NULL THEN 0 ELSE 1 END), created_at DESC)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS books_cover_presence_created_idx");
        }
    }
};

