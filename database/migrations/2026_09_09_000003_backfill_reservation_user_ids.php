<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE reservations r
            INNER JOIN members m ON m.id = r.member_id
            SET r.user_id = m.user_id
            WHERE r.user_id IS NULL AND m.user_id IS NOT NULL
        SQL);
    }

    public function down(): void
    {
        // Existing user_id values must not be removed during rollback.
    }
};
