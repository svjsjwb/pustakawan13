<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->string('member_number')->unique();
            $table->string('name');

            $table->string('nis_nip')->nullable();

            $table->enum('gender', ['Laki-laki', 'Perempuan'])
                ->nullable();

            $table->string('class')->nullable();

            $table->text('address')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->date('registered_at')->nullable();

            $table->enum('status', ['Aktif', 'Tidak Aktif'])
                ->default('Aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};