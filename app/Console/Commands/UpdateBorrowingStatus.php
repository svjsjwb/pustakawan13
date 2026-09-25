<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
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
    protected $description =
        'Memperbarui status peminjaman dan status member berdasarkan aktivitas saat ini';


    /**
     * Jalankan command.
     */
    public function handle(): int
    {
        $today =
            now()->toDateString();


        DB::transaction(
            function () use (
                $today
            ) {

                /*
                |--------------------------------------------------------------------------
                | 1. TANDAI PEMINJAMAN YANG TERLAMBAT
                |--------------------------------------------------------------------------
                |
                | Peminjaman yang:
                |
                | - belum dikembalikan
                | - statusnya dipinjam / diperpanjang
                | - due_at sudah lewat
                |
                | akan menjadi terlambat.
                |
                */

                Borrowing::whereNull(
                    'returned_at'
                )
                    ->whereIn(
                        'status',
                        [
                            'dipinjam',
                            'diperpanjang',
                        ]
                    )
                    ->whereDate(
                        'due_at',
                        '<',
                        $today
                    )
                    ->update([
                        'status' =>
                            'terlambat',
                    ]);


                /*
                |--------------------------------------------------------------------------
                | 2. SINKRONISASI STATUS SELURUH MEMBER
                |--------------------------------------------------------------------------
                */

                $members =
                    Member::all();


                foreach (
                    $members as $member
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | PEMINJAMAN AKTIF
                    |--------------------------------------------------------------------------
                    |
                    | returned_at NULL berarti belum dikembalikan.
                    |
                    | Tidak peduli apakah statusnya:
                    |
                    | dipinjam
                    | diperpanjang
                    | terlambat
                    |
                    | Semuanya tetap membuat member AKTIF.
                    |
                    */

                    $hasActiveBorrowing =
                        Borrowing::where(
                            'member_id',
                            $member->id
                        )
                        ->whereNull(
                            'returned_at'
                        )
                        ->exists();


                    /*
                    |--------------------------------------------------------------------------
                    | RESERVASI AKTIF
                    |--------------------------------------------------------------------------
                    |
                    | Reservasi hanya membuat member AKTIF jika:
                    |
                    | - status bukan ditolak
                    | - status bukan dibatalkan
                    | - status bukan selesai
                    | - expires_at belum lewat
                    |
                    */

                    $hasActiveReservation =
                        Reservation::where(
                            'member_id',
                            $member->id
                        )
                        ->whereNotIn(
                            'status',
                            [
                                'ditolak',
                                'dibatalkan',
                                'selesai',
                            ]
                        )
                        ->whereNotNull(
                            'expires_at'
                        )
                        ->whereDate(
                            'expires_at',
                            '>=',
                            $today
                        )
                        ->exists();


                    /*
                    |--------------------------------------------------------------------------
                    | TENTUKAN STATUS MEMBER
                    |--------------------------------------------------------------------------
                    */

                    $newStatus =
                        (
                            $hasActiveBorrowing ||
                            $hasActiveReservation
                        )
                            ? 'aktif'
                            : 'nonaktif';


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE HANYA JIKA BERUBAH
                    |--------------------------------------------------------------------------
                    |
                    | Mengurangi query UPDATE yang tidak perlu.
                    |
                    */

                    if (
                        $member->status !==
                        $newStatus
                    ) {

                        $member->update([
                            'status' =>
                                $newStatus,
                        ]);
                    }
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SELESAI
        |--------------------------------------------------------------------------
        */

        $this->info(
            'Status peminjaman dan status member berhasil diperbarui.'
        );


        return self::SUCCESS;
    }
}