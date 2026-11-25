<?php

namespace App\Console\Commands;

use App\Services\ReservationCancellationService;
use Illuminate\Console\Command;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan otomatis reservasi yang tidak diambil dalam batas waktu 3 hari dan mengembalikan stok buku';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan reservasi kadaluarsa...');

        $cancelledCount = ReservationCancellationService::cancelExpiredReservations();

        if ($cancelledCount > 0) {
            $this->info("Berhasil membatalkan {$cancelledCount} reservasi kadaluarsa dan mengembalikan stok buku.");
        } else {
            $this->info('Tidak ada reservasi kadaluarsa yang perlu dibatalkan.');
        }

        return Command::SUCCESS;
    }
}
