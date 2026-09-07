<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom user_id ke tabel members jika belum ada
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id');
            }
        });

        // 2. Sinkronisasi data user eksisting (role = 'user') ke tabel members
        $existingUsers = DB::table('users')
            ->where('role', 'user')
            ->get();

        foreach ($existingUsers as $u) {
            $memberExists = DB::table('members')
                ->where('user_id', $u->id)
                ->orWhere('email', $u->email)
                ->exists();

            if (!$memberExists) {
                DB::table('members')->insert([
                    'user_id'    => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'phone'      => $u->phone ?: '-',
                    'division'   => 'Anggota',
                    'status'     => 'Aktif',
                    'created_at' => $u->created_at ?: now(),
                    'updated_at' => $u->created_at ?: now(),
                ]);
            } else {
                // Pastikan user_id terisi jika sebelumnya hanya cocok berdasarkan email
                DB::table('members')
                    ->whereNull('user_id')
                    ->where('email', $u->email)
                    ->update(['user_id' => $u->id]);
            }
        }

        // 3. Buat Trigger Database MySQL agar user baru otomatis tercatat di tabel members
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared("DROP TRIGGER IF EXISTS trg_users_sync_to_members;");

            DB::unprepared("
                CREATE TRIGGER trg_users_sync_to_members
                AFTER INSERT ON users
                FOR EACH ROW
                BEGIN
                    IF NEW.role = 'user' THEN
                        INSERT INTO members (user_id, name, email, phone, division, status, created_at, updated_at)
                        VALUES (
                            NEW.id,
                            NEW.name,
                            NEW.email,
                            COALESCE(NEW.phone, '-'),
                            'Anggota',
                            'aktif',
                            COALESCE(NEW.created_at, NOW()),
                            COALESCE(NEW.created_at, NOW())
                        );
                    END IF;
                END;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared("DROP TRIGGER IF EXISTS trg_users_sync_to_members;");
        }

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
