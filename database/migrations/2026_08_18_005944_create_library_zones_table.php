<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_zones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('library_floor_id')
                ->constrained('library_floors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('code');
            $table->string('name');

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique([
                'library_floor_id',
                'code'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_zones');
    }
};
