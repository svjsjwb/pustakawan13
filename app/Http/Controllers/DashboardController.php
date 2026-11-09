<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Reservation;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
         * ========================================================
         * STATISTIK CARD (TOTAL, REALTIME - CACHE 60s)
         * ========================================================
         */

        $stats = Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'totalBooks'     => Book::count(),
                'borrowedBooks'  => Borrowing::where('status', 'dipinjam')->count(),
                'activeMembers'  => Member::where('status', 'aktif')->count(),
                'lateBorrowings' => Borrowing::where('status', 'dipinjam')
                    ->whereDate('due_at', '<', today())
                    ->count(),
            ];
        });

        $totalBooks     = $stats['totalBooks'];
        $borrowedBooks  = $stats['borrowedBooks'];
        $activeMembers  = $stats['activeMembers'];
        $lateBorrowings = $stats['lateBorrowings'];


        /*
         * ========================================================
         * PERIODE OTOMATIS & GRAFIK
         * ========================================================
         */

        $startDate = now()->startOfMonth()->startOfDay();
        $endDate   = now()->endOfMonth()->endOfDay();

        $chart7Days = $this->generateBorrowChart(
            $startDate,
            $endDate
        );

        $max7 = !empty($chart7Days)
            ? max(array_column($chart7Days, 'count'))
            : 0;

        $max7 = $max7 > 0 ? $max7 : 5;


        /*
         * ========================================================
         * RESERVASI TERBARU
         * ========================================================
         */

        $reservations = Reservation::with([
            'user:id,name',
            'member:id,name',
            'book:id,judul_buku'
        ])
            ->latest()
            ->take(20)
            ->get();


        // tabel buku populer (dioptimasi: 1 query agregasi SQL + cache 5 menit)
        $popularBooks = Cache::remember('admin_dashboard_popular_books_v2', 300, function () {
            return BorrowingDetail::query()
                ->select('book_id', DB::raw('SUM(COALESCE(quantity, 1)) as total_borrowed'))
                ->groupBy('book_id')
                ->orderByDesc('total_borrowed')
                ->take(5)
                ->with('book:id,judul_buku')
                ->get()
                ->map(function ($detail) {
                    $total = (int) $detail->total_borrowed;
                    return [
                        'book_id'           => $detail->book_id,
                        'title'             => $detail->book?->title ?? '-',
                        'borrowing_total'   => $total,
                        'reservation_total' => 0,
                        'total'             => $total,
                    ];
                })
                ->all();
        });

        if (!is_array($popularBooks)) {
            $popularBooks = [];
        }

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
         * 5. Aktivitas manual
         */

        $activities = collect();


        /*
         * ========================================================
         * 1. ANGGOTA BARU
         * ========================================================
         */

        $members = Member::select(['id', 'name', 'created_at'])
            ->latest()
            ->take(4)
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

        $books = Book::select(['id', 'judul_buku', 'created_at'])
            ->latest()
            ->take(4)
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

        foreach (
            $reservations->take(4)
            as $reservation
        ) {

            $activities->push([
                'type' =>
                'reservation',

                'title' =>
                'Reservasi baru',

                'description' =>
                $reservation->member?->name
                    ?? '-',

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

        $borrowings = Borrowing::select(['id', 'member_id', 'created_at'])
            ->with('member:id,name')
            ->latest()
            ->take(4)
            ->get();


        foreach ($borrowings as $borrowing) {

            $activities->push([
                'type' =>
                'borrowing',

                'title' =>
                'Peminjaman baru',

                'description' =>
                $borrowing->member?->name
                    ?? '-',

                'created_at' =>
                $borrowing->created_at,

                'icon' =>
                '+',
            ]);
        }


        /*
         * ========================================================
         * 5. AKTIVITAS MANUAL
         * ========================================================
         */

        $manualActivities = Activity::latest()->take(4)->get();


        foreach (
            $manualActivities
            as $manualActivity
        ) {

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
         * SORT AKTIVITAS
         * ========================================================
         */

        $activities = $activities
            ->sortBy([
                [
                    'pinned_at',
                    'desc'
                ],
                [
                    'created_at',
                    'desc'
                ],
            ])
            ->take(4)
            ->values();


        /*
         * ========================================================
         * SEMUA AKTIVITAS MANUAL
         * ========================================================
         */

        $allManualActivities = $manualActivities;


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
                'popularBooks',
                'activities',
                'allManualActivities'
            )
        );
    }


    /*
     * ========================================================
     * GRAFIK PEMINJAMAN
     * ========================================================
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

        $borrowings = Borrowing::select(['created_at'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->get();

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
                $weekEnd->gt(
                    $endDate
                )
            ) {

                $weekEnd =
                    $endDate->copy();
            }


            $labels[] =
                'M' . $weekNumber;


            $counts[] =
                $borrowings->filter(function ($b) use ($weekStart, $weekEnd) {
                    return $b->created_at && $b->created_at->between($weekStart, $weekEnd);
                })->count();


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
            $labels
            as $index => $label
        ) {

            $count =
                $counts[$index] ?? 0;


            $height =
                $max > 0
                ? max(
                    8,
                    round(
                        ($count / $max)
                            * 100
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
