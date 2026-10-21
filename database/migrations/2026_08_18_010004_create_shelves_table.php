<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('library_zone_id')
                ->constrained('library_zones')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
             * Kode unik rak dalam satu zona.
             *
             * Contoh:
             * A-01
             * A-02
             * A-03
             */
            $table->string('code');

            $table->string('name')->nullable();

            /*
             * Jumlah baris dan kolom fisik pada rak.
             */
            $table->unsignedInteger('row_count')->default(5);
            $table->unsignedInteger('column_count')->default(10);

            /*
             * Posisi rak pada denah 2D.
             *
             * Nanti bisa dipakai oleh Book Locator.
             */
            $table->decimal('position_x', 10, 2)->nullable();
            $table->decimal('position_y', 10, 2)->nullable();

            /*
             * Ukuran rak.
             *
             * Nanti berguna untuk visualisasi 3D.
             */
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('depth', 10, 2)->nullable();

            $table->timestamps();

            $table->unique([
                'library_zone_id',
                'code'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelves');
    }
};
