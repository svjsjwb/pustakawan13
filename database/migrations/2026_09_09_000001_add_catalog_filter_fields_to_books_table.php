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
        Schema::table('books', function (Blueprint $table) {
            $table->string('main_category')->nullable()->after('category_id')->index();
            $table->string('sub_category')->nullable()->after('main_category')->index();
            $table->string('education_level')->nullable()->after('sub_category')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['main_category']);
            $table->dropIndex(['sub_category']);
            $table->dropIndex(['education_level']);
            $table->dropColumn(['main_category', 'sub_category', 'education_level']);
        });
    }
};
