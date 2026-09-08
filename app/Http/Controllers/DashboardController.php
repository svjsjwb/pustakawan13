<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Activity;
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
         * 5. AKTIVITAS MANUAL (ANNOUNCEMENT)
         * ========================================================
         *
         * Aktivitas yang dibuat admin melalui tombol
         * "+ Tambah Aktivitas". Hanya aktivitas inilah yang
         * muncul sebagai popup announcement di halaman /home.
         */

        $manualActivities =
            Activity::latest()
                ->get();

        foreach ($manualActivities as $manualActivity) {

            $activities->push([

                'id' =>
                    $manualActivity->id,

                'type' =>
                    'manual',

                'title' =>
                    $manualActivity->title ?? '-',

                'description' =>
                    $manualActivity->description ?? '-',

                'created_at' =>
                    $manualActivity->created_at,

                'pinned_at' =>
                    $manualActivity->pinned_at,

                'icon' =>
                    '📢',
            ]);
        }


        /*
         * ========================================================
         * URUTKAN AKTIVITAS
         * ========================================================
         *
         * Yang paling baru selalu di atas.
         *
         * Termasuk aktivitas manual yang baru dibuat.
         */

        $activities =
            $activities
                ->sortBy([
                    ['pinned_at', 'desc'],
                    ['created_at', 'desc'],
                ])
                ->take(4)
                ->values();


        /*
         * ========================================================
         * SEMUA AKTIVITAS MANUAL (UNTUK FORM EDIT)
         * ========================================================
         *
         * Dikirim ke view agar panel dashboard dapat membuat
         * form "Edit Aktivitas" tanpa query tambahan.
         */

        $allManualActivities =
            Activity::latest()
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

                'reservations',
                'activities',
                'allManualActivities'
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