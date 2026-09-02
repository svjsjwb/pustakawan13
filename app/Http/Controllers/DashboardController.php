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
     */
    public function index(Request $request)
    {
        /*
         * ========================================================
         * STATISTIK CARD
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
         * PERIODE OTOMATIS
         * ========================================================
         *
         * Mengikuti periode default halaman laporan:
         * bulan berjalan.
         */

        $startDate =
            now()
                ->startOfMonth()
                ->startOfDay();

        $endDate =
            now()
                ->endOfMonth()
                ->endOfDay();


        /*
         * ========================================================
         * GRAFIK STATISTIK PEMINJAMAN
         * ========================================================
         */

        $chart7Days =
            $this->generateBorrowChart(
                $startDate,
                $endDate
            );

        $max7 =
            !empty($chart7Days)
                ? max(
                    array_column(
                        $chart7Days,
                        'count'
                    )
                )
                : 0;

        $max7 =
            $max7 > 0
                ? $max7
                : 5;


        /*
         * ========================================================
         * RESERVASI
         * ========================================================
         *
         * Tetap digunakan untuk:
         * - Buku Terpopuler
         * - Reservasi Terbaru
         */

        $reservations =
            Reservation::with([
                'member',
                'book'
            ])
            ->latest()
            ->take(20)
            ->get();


        /*
         * ========================================================
         * AKTIVITAS TERBARU
         * ========================================================
         *
         * Sumber aktivitas:
         *
         * 1. Anggota baru
         * 2. Koleksi buku baru
         * 3. Peminjaman baru
         * 4. Reservasi baru
         *
         * Semua digabung.
         *
         * Kemudian diurutkan berdasarkan created_at
         * dari yang paling baru ke paling lama.
         *
         * Hanya 4 aktivitas terbaru yang ditampilkan.
         */

        $activities = collect();


        /*
         * ========================================================
         * 1. ANGGOTA BARU
         * ========================================================
         */

        $members =
            Member::latest()
                ->take(20)
                ->get();

        foreach ($members as $member) {

            $activities->push([

                'type' =>
                    'member',

                'title' =>
                    'Anggota baru',

                'description' =>
                    $member->name ?? '-',

                'created_at' =>
                    $member->created_at,

                'icon' =>
                    '+',
            ]);
        }


        /*
         * ========================================================
         * 2. BUKU BARU
         * ========================================================
         */

        $books =
            Book::latest()
                ->take(20)
                ->get();

        foreach ($books as $book) {

            $activities->push([

                'type' =>
                    'book',

                'title' =>
                    'Koleksi buku baru',

                'description' =>
                    $book->title ?? '-',

                'created_at' =>
                    $book->created_at,

                'icon' =>
                    '+',
            ]);
        }


        /*
         * ========================================================
         * 3. RESERVASI BARU
         * ========================================================
         */

        foreach ($reservations as $reservation) {

            $activities->push([

                'type' =>
                    'reservation',

                'title' =>
                    'Reservasi baru',

                'description' =>
                    $reservation->member?->name ?? '-',

                'created_at' =>
                    $reservation->created_at,

                'icon' =>
                    '+',
            ]);
        }


        /*
         * ========================================================
         * 4. PEMINJAMAN BARU
         * ========================================================
         */

        $borrowings =
            Borrowing::with([
                'member',
                'details.book'
            ])
            ->latest()
            ->take(20)
            ->get();

        foreach ($borrowings as $borrowing) {

            $activities->push([

                'type' =>
                    'borrowing',

                'title' =>
                    'Peminjaman baru',

                'description' =>
                    $borrowing->member?->name ?? '-',

                'created_at' =>
                    $borrowing->created_at,

                'icon' =>
                    '+',
            ]);
        }


        /*
         * ========================================================
         * URUTKAN AKTIVITAS
         * ========================================================
         *
         * Yang paling baru selalu di atas.
         */

        $activities =
            $activities
                ->sortByDesc(
                    'created_at'
                )
                ->take(4)
                ->values();


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

                'reservations',
                'activities'
            )
        );
    }


    /**
     * ============================================================
     * GENERATE GRAFIK PEMINJAMAN PER MINGGU
     * ============================================================
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
                $cursor
                    ->copy()
                    ->startOfDay();

            $weekEnd =
                $cursor
                    ->copy()
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
                $weekEnd
                    ->copy()
                    ->addSecond();

            $weekNumber++;
        }


        $max =
            !empty($counts)
                ? max($counts)
                : 0;


        $chart = [];

        foreach (
            $labels as $index => $label
        ) {

            $count =
                $counts[$index] ?? 0;


            $height =
                $max > 0
                    ? max(
                        8,
                        round(
                            ($count / $max) * 100
                        )
                    )
                    : 8;


            $chart[] = [

                'label' =>
                    $label,

                'count' =>
                    $count,

                'height' =>
                    $height,
            ];
        }


        return $chart;
    }
}