<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi status enum dan penambahan kolom pada borrowings
        Schema::table('borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('borrowings', 'extension_status')) {
                $table->enum('extension_status', ['tidak_ada', 'menunggu', 'disetujui', 'ditolak'])
                      ->default('tidak_ada')
                      ->after('rejection_reason');
            }
            if (!Schema::hasColumn('borrowings', 'extension_requested_due_at')) {
                $table->date('extension_requested_due_at')->nullable()->after('extension_status');
            }
            if (!Schema::hasColumn('borrowings', 'extension_reason')) {
                $table->text('extension_reason')->nullable()->after('extension_requested_due_at');
            }
            if (!Schema::hasColumn('borrowings', 'extension_admin_notes')) {
                $table->text('extension_admin_notes')->nullable()->after('extension_reason');
            }
        });

        // Mengubah status enum untuk mendukung 'menunggu' dan 'ditolak'
        DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('menunggu', 'dipinjam', 'ditolak', 'dikembalikan', 'terlambat') NOT NULL DEFAULT 'menunggu'");

        // 2. Modifikasi reservations untuk rejection_reason
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'extension_status',
                'extension_requested_due_at',
                'extension_reason',
                'extension_admin_notes',
            ]);
        });

        DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('dipinjam', 'dikembalikan', 'terlambat') NOT NULL DEFAULT 'dipinjam'");

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
