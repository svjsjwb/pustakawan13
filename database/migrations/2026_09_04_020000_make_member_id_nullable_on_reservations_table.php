<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE reservations MODIFY member_id BIGINT UNSIGNED NULL;');
        } else {
            Schema::table('reservations', function (Blueprint $table) {
                $table->unsignedBigInteger('member_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE reservations MODIFY member_id BIGINT UNSIGNED NOT NULL;');
        } else {
            Schema::table('reservations', function (Blueprint $table) {
                $table->unsignedBigInteger('member_id')->nullable(false)->change();
            });
        }
    }
};
