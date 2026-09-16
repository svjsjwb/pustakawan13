<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan 7 kolom preferensi notifikasi ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_notifications')->default(true)->after('role');
            $table->boolean('email_notifications')->default(true)->after('allow_notifications');
            $table->boolean('reservation_notifications')->default(true)->after('email_notifications');
            $table->boolean('borrowing_notifications')->default(true)->after('reservation_notifications');
            $table->boolean('extension_notifications')->default(true)->after('borrowing_notifications');
            $table->boolean('return_reminder_notifications')->default(true)->after('extension_notifications');
            $table->boolean('fine_notifications')->default(true)->after('return_reminder_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'allow_notifications',
                'email_notifications',
                'reservation_notifications',
                'borrowing_notifications',
                'extension_notifications',
                'return_reminder_notifications',
                'fine_notifications',
            ]);
        });
    }
};
