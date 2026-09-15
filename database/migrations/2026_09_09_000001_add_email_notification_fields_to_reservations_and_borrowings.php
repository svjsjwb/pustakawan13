<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->boolean('is_reminder_sent')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn('is_reminder_sent');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
