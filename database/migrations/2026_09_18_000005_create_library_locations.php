<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_floors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('floor_number')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('library_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_floor_id')->constrained('library_floors')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['library_floor_id', 'code']);
        });

        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_zone_id')->constrained('library_zones')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name')->nullable();
            $table->unsignedInteger('row_count')->default(3);
            $table->unsignedInteger('column_count')->default(30);
            $table->decimal('position_x', 10, 2)->nullable();
            $table->decimal('position_y', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('depth', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['library_zone_id', 'code']);
        });

        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('racks');
        Schema::dropIfExists('shelves');
        Schema::dropIfExists('library_zones');
        Schema::dropIfExists('library_floors');
    }
};
