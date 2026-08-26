<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * ============================================================
     * HALAMAN DASHBOARD
     * ============================================================
     *
     * Grafik "Statistik Peminjaman" pada dashboard ini sekarang
     * MENGIKUTI PERIODE OTOMATIS yang sama dengan periode default
     * halaman Laporan (ReportController::getPeriod), yaitu:
     *
     *      bulan berjalan (tanggal 1 - akhir bulan)
     *
     * Tidak ada lagi pilihan manual "7 Hari Terakhir" / "1 Bulan
     * Terakhir" seperti sebelumnya.
     */
    public function index(Request $request)
    {
        /*
         * ========================================================
         * STATISTIK CARD (TOTAL, REALTIME - TIDAK DIBATASI PERIODE)
         * ========================================================
         */

        $totalBooks =
            Book::count();

        $borrowedBooks =
            Borrowing::where(
                'status',
                'dipinjam'
            )->count();

        $activeMembers =
            Member::where(
                'status',
                'aktif'
            )->count();

        $lateBorrowings =
            Borrowing::where(
                'status',
                'dipinjam'
            )
            ->where(
                'due_at',
                '<',
                now()
            )
            ->count();


        /*
         * ========================================================
         * PERIODE OTOMATIS (SAMA DENGAN DEFAULT HALAMAN LAPORAN)
         * ========================================================
         *
         * ReportController::getPeriod() default-nya adalah
         * rangeType = 'month' dengan selectedDate = hari ini,
         * sehingga rentangnya selalu bulan berjalan.
         */

        $startDate =
            now()->startOfMonth()->startOfDay();

        $endDate =
            now()->endOfMonth()->endOfDay();


        /*
         * ========================================================
         * GRAFIK STATISTIK PEMINJAMAN (BULAN BERJALAN)
         * ========================================================
         */

        $chart7Days =
            $this->generateBorrowChart(
                $startDate,
                $endDate
            );

        $max7 =
            !empty($chart7Days)
                ? max(array_column($chart7Days, 'count'))
                : 0;

        $max7 =
            $max7 > 0
                ? $max7
                : 5;


        /*
         * ========================================================
         * RESERVASI (UNTUK PANEL BUKU TERPOPULER, AKTIVITAS
         * TERBARU, DAN TABEL RESERVASI TERBARU)
         * ========================================================
         */

        $reservations =
            Reservation::with(['member', 'book'])
                ->latest()
                ->take(20)
                ->get();


        /*
         * ========================================================
         * KIRIM DATA KE VIEW
         * ========================================================
         */

        return view(
            'dashboard.index',
            compact(
                'totalBooks',
                'borrowedBooks',
                'activeMembers',
                'lateBorrowings',

                'chart7Days',
                'max7',

                'reservations'
            )
        );
    }


    /**
     * ============================================================
     * GENERATE GRAFIK PEMINJAMAN PER MINGGU (BULAN BERJALAN)
     * ============================================================
     *
     * Logika pembagian minggu ini SAMA dengan
     * ReportController::generateDateChart() untuk periode 'month',
     * supaya angka pada dashboard selalu konsisten dengan Laporan.
     *
     * Return: array of ['label' => .., 'count' => .., 'height' => ..]
     */
    private function generateBorrowChart(
        Carbon $startDate,
        Carbon $endDate
    ) {
        $labels = [];

        $counts = [];

        $cursor =
            $startDate->copy();

        $weekNumber = 1;


        while (
            $cursor->lte($endDate)
        ) {

            $weekStart =
                $cursor->copy()
                    ->startOfDay();

            $weekEnd =
                $cursor->copy()
                    ->addDays(6)
                    ->endOfDay();


            if (
                $weekEnd->gt($endDate)
            ) {
                $weekEnd =
                    $endDate->copy();
            }


            $labels[] =
                'M' . $weekNumber;

            $counts[] =
                Borrowing::whereBetween(
                    'created_at',
                    [
                        $weekStart,
                        $weekEnd
                    ]
                )->count();


            $cursor =
                $weekEnd->copy()
                    ->addSecond();

            $weekNumber++;
        }


        $max =
            !empty($counts)
                ? max($counts)
                : 0;


        $chart = [];

        foreach ($labels as $index => $label) {

            $count =
                $counts[$index] ?? 0;

            $height =
                $max > 0
                    ? max(8, round(($count / $max) * 100))
                    : 8;

            $chart[] = [
                'label' => $label,
                'count' => $count,
                'height' => $height,
            ];
        }


        return $chart;
    }
}