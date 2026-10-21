<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {

            if (Schema::hasColumn('members', 'member_number')) {
                try {
                    $table->dropUnique('members_member_number_unique');
                } catch (\Throwable $e) {}
                $table->dropColumn('member_number');
            }

            if (Schema::hasColumn('members', 'nip')) {
                try {
                    $table->dropUnique('members_nip_unique');
                } catch (\Throwable $e) {}
                $table->dropColumn('nip');
            }

            if (Schema::hasColumn('members', 'position')) {
                $table->dropColumn('position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('member_number')->nullable();
            $table->string('nip')->nullable();
            $table->string('position')->nullable();
        });
    }
};