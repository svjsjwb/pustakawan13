<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Authentication
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();

            // Role & profile
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('phone')->nullable();
            $table->string('theme')->nullable();
            $table->string('layout_density')->nullable();

            // Notification preferences
            $table->boolean('allow_notifications')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('reservation_notifications')->default(true);
            $table->boolean('borrowing_notifications')->default(true);
            $table->boolean('extension_notifications')->default(true);
            $table->boolean('return_reminder_notifications')->default(true);
            $table->boolean('late_return_notifications')->default(true);
            $table->boolean('fine_notifications')->default(true);

            // User preferences
            $table->json('favorite_categories')->nullable();
            $table->json('favorite_genres')->nullable();
            $table->string('last_theme_used')->nullable();
            $table->timestamp('last_login_at')->nullable();

            // Google authentication
            $table->string('google_id')->nullable()->unique();

            $table->timestamps();

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
