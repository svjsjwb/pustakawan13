
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE borrowings
            DROP CONSTRAINT IF EXISTS borrowings_status_check
        ');

        DB::statement("
            ALTER TABLE borrowings
            ADD CONSTRAINT borrowings_status_check
            CHECK (status IN (
                'menunggu',
                'dipinjam',
                'diperpanjang',
                'ditolak',
                'dikembalikan',
                'terlambat',
                'dibatalkan'
            ))
        ");
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE borrowings
            DROP CONSTRAINT IF EXISTS borrowings_status_check
        ');

        DB::statement("
            ALTER TABLE borrowings
            ADD CONSTRAINT borrowings_status_check
            CHECK (status IN (
                'menunggu',
                'dipinjam',
                'diperpanjang',
                'ditolak',
                'dikembalikan',
                'terlambat'
            ))
        ");
    }
};
