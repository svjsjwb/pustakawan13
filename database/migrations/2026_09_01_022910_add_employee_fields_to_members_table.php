<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {

            if (!Schema::hasColumn('members', 'member_number')) {
                $table->string('member_number')->nullable()->unique();
            }

            if (!Schema::hasColumn('members', 'nip')) {
                $table->string('nip')->nullable()->unique();
            }

            if (!Schema::hasColumn('members', 'position')) {
                $table->string('position')->nullable();
            }

            if (!Schema::hasColumn('members', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('members', 'status')) {
                $table->string('status')->default('Aktif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {

            if (Schema::hasColumn('members', 'member_number')) {
                $table->dropUnique(['member_number']);
                $table->dropColumn('member_number');
            }

            if (Schema::hasColumn('members', 'nip')) {
                $table->dropUnique(['nip']);
                $table->dropColumn('nip');
            }

            if (Schema::hasColumn('members', 'position')) {
                $table->dropColumn('position');
            }

            if (Schema::hasColumn('members', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('members', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};