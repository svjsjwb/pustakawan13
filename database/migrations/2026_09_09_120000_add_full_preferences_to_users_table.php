<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'theme')) {
                $table->string('theme', 20)->default('light')->after('role');
            }
            if (!Schema::hasColumn('users', 'layout_density')) {
                $table->string('layout_density', 20)->default('normal')->after('theme');
            }
            if (!Schema::hasColumn('users', 'late_return_notifications')) {
                $table->boolean('late_return_notifications')->default(true)->after('return_reminder_notifications');
            }
            if (!Schema::hasColumn('users', 'favorite_categories')) {
                $table->json('favorite_categories')->nullable()->after('late_return_notifications');
            }
            if (!Schema::hasColumn('users', 'favorite_genres')) {
                $table->json('favorite_genres')->nullable()->after('favorite_categories');
            }
            if (!Schema::hasColumn('users', 'last_theme_used')) {
                $table->string('last_theme_used', 20)->default('light')->after('favorite_genres');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('last_theme_used');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'theme',
                'layout_density',
                'late_return_notifications',
                'favorite_categories',
                'favorite_genres',
                'last_theme_used',
                'last_login_at',
            ]);
        });
    }
};
