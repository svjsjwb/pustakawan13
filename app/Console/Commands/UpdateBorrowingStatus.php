<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateBorrowingStatus extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'borrowings:update-status';

    /**
     * Deskripsi command.
     */
    protected $description = 'Memperbarui status peminjaman dan status member berdasarkan tanggal jatuh tempo';

    /**
     * Jalankan command.
     */
    public function handle(): int
    {
        $today = now()->toDateString();

        DB::transaction(function () use ($today) {

            /*
        |--------------------------------------------------------------------------
        | 1. Tandai peminjaman yang sudah melewati jatuh tempo
        |--------------------------------------------------------------------------
        */

            Borrowing::whereNull('returned_at')
                ->whereIn('status', [
                    'dipinjam',
                    'diperpanjang',
                ])
                ->whereDate('due_at', '<', $today)
                ->update([
                    'status' => 'terlambat',
                ]);

            /*
        |--------------------------------------------------------------------------
        | 2. Sinkronkan status seluruh member
        |--------------------------------------------------------------------------
        */

            $members = Member::all();

            foreach ($members as $member) {

                /*
            |--------------------------------------------------------------------------
            | PEMINJAMAN AKTIF
            |--------------------------------------------------------------------------
            |
            | Selama returned_at masih NULL,
            | peminjaman dianggap masih aktif.
            |
            | Termasuk peminjaman yang sudah terlambat.
            |
            */

                $hasActiveBorrowing = Borrowing::where(
                    'member_id',
                    $member->id
                )
                    ->whereNull('returned_at')
                    ->exists();

                /*
            |--------------------------------------------------------------------------
            | RESERVASI AKTIF
            |--------------------------------------------------------------------------
            */

                $hasActiveReservation = \App\Models\Reservation::where(
                    'member_id',
                    $member->id
                )
                    ->whereNotIn('status', [
                        'ditolak',
                        'dibatalkan',
                        'selesai',
                    ])
                    ->exists();

                /*
            |--------------------------------------------------------------------------
            | STATUS MEMBER
            |--------------------------------------------------------------------------
            */

                Member::where(
                    'id',
                    $member->id
                )->update([
                    'status' => (
                        $hasActiveBorrowing ||
                        $hasActiveReservation
                    )
                        ? 'aktif'
                        : 'nonaktif',
                ]);
            }
        });

        $this->info(
            'Status peminjaman dan status member berhasil diperbarui.'
        );

        return self::SUCCESS;
    }
}
