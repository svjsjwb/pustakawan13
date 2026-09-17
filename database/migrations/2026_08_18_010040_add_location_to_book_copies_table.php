<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {

            /*
             * Rak fisik.
             */
            $table->foreignId('shelf_id')
                ->nullable()
                ->after('book_id')
                ->constrained('shelves')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            /*
             * Section rak.
             *
             * 1 = A-01
             * 2 = A-02
             */
            $table->unsignedTinyInteger('section')
                ->nullable()
                ->after('shelf_id');


            /*
             * Muka rak.
             *
             * front = depan
             * back  = belakang
             */
            $table->enum('side', [
                'front',
                'back',
            ])
                ->default('front')
                ->after('section');


            /*
             * Baris.
             *
             * 1 = atas
             * 2 = tengah
             * 3 = bawah
             */
            $table->unsignedTinyInteger('row')
                ->nullable()
                ->after('side');


            /*
             * Kolom.
             *
             * 1 - 30
             */
            $table->unsignedTinyInteger('column')
                ->nullable()
                ->after('row');
        });
    }

    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {

            $table->dropForeign([
                'shelf_id',
            ]);

            $table->dropColumn([
                'shelf_id',
                'section',
                'side',
                'row',
                'column',
            ]);
        });
    }
};
