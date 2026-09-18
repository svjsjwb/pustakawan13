<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\BookProposal;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
         * ========================================================
         * STATISTIK CARD (TOTAL, REALTIME)
         * ========================================================
         */

        $totalBooks = Book::count();

        $borrowedBooks = Borrowing::where('status', 'dipinjam')->count();

        $activeMembers = Member::where('status', 'aktif')->count();

        $lateBorrowings = Borrowing::where('status', 'dipinjam')
            ->where('due_at', '<', now())
            ->count();


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
            'member',
            'book'
        ])
            ->latest()
            ->take(20)
            ->get();


        /*
         * ========================================================
         * BUKU TERPOPULER
         * ========================================================
         *
         * Popularitas dihitung dari dua aktivitas:
         *
         * 1. PEMINJAMAN LANGSUNG
         *    +1
         *
         * 2. RESERVASI YANG DISETUJUI
         *    +1
         *
         * Tidak dihitung:
         *
         * - Reservasi menunggu
         * - Reservasi ditolak
         * - Reservasi dibatalkan
         *
         * Peminjaman yang berasal dari reservasi
         * tidak dihitung lagi karena reservasinya
         * sudah memberikan +1.
         *
         * Tujuannya mencegah satu transaksi dihitung 2x.
         * ========================================================
         */


        /*
         * ========================================================
         * 1. PEMINJAMAN LANGSUNG
         * ========================================================
         *
         * reservation_id NULL =
         * peminjaman tidak berasal dari reservasi.
         */

        $borrowingCounts = collect();

        $allBorrowings = Borrowing::with('details')
            ->whereNull('reservation_id')
            ->get();


        foreach ($allBorrowings as $borrowing) {

            foreach ($borrowing->details as $detail) {

                $bookId = $detail->book_id;

                $quantity = $detail->quantity ?? 1;

                $borrowingCounts[$bookId] =
                    ($borrowingCounts[$bookId] ?? 0)
                    + $quantity;
            }
        }


        /*
         * ========================================================
         * 2. RESERVASI YANG DISETUJUI
         * ========================================================
         *
         * Hanya status "disetujui" yang memberikan +1.
         */

        $reservationCounts = Reservation::whereIn('status', [
            'disetujui',
            'selesai',
        ])
            ->selectRaw('book_id, COUNT(*) as total')
            ->groupBy('book_id')
            ->pluck('total', 'book_id');


        /*
         * ========================================================
         * GABUNGKAN ID BUKU
         * ========================================================
         */

        $popularBookIds = $borrowingCounts
            ->keys()
            ->merge(
                $reservationCounts->keys()
            )
            ->unique()
            ->values();


        /*
         * ========================================================
         * BENTUK DATA BUKU POPULER
         * ========================================================
         */

        $popularBooks = Book::whereIn(
            'id',
            $popularBookIds
        )
            ->get()
            ->map(
                function ($book) use (
                    $borrowingCounts,
                    $reservationCounts
                ) {

                    $borrowingTotal =
                        (int) (
                            $borrowingCounts[$book->id] ?? 0
                        );


                    $reservationTotal =
                        (int) (
                            $reservationCounts[$book->id] ?? 0
                        );


                    return [
                        'book_id' =>
                        $book->id,

                        'title' =>
                        $book->title ?? '-',

                        'borrowing_total' =>
                        $borrowingTotal,

                        'reservation_total' =>
                        $reservationTotal,

                        'total' =>
                        $borrowingTotal
                            + $reservationTotal,
                    ];
                }
            )
            ->sort(
                function ($a, $b) {

                    /*
                     * TOTAL TERBESAR
                     */

                    if (
                        $a['total'] !==
                        $b['total']
                    ) {

                        return
                            $b['total']
                            <=>
                            $a['total'];
                    }


                    /*
                     * JIKA TOTAL SAMA,
                     * PEMINJAMAN JADI PEMBEDA
                     */

                    if (
                        $a['borrowing_total'] !==
                        $b['borrowing_total']
                    ) {

                        return
                            $b['borrowing_total']
                            <=>
                            $a['borrowing_total'];
                    }


                    /*
                     * JIKA MASIH SAMA,
                     * RESERVASI JADI PEMBEDA
                     */

                    return
                        $b['reservation_total']
                        <=>
                        $a['reservation_total'];
                }
            )
            ->take(5)
            ->values();


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

        $members = Member::latest()
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

        $books = Book::latest()
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

        foreach (
            $reservations->take(20)
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

        $borrowings = Borrowing::with([
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

        $manualActivities =
            \Illuminate\Support\Facades\Schema::hasTable(
                'activities'
            )
            ? Activity::latest()->get()
            : collect();


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

        $allManualActivities =
            \Illuminate\Support\Facades\Schema::hasTable(
                'activities'
            )
            ? Activity::latest()->get()
            : collect();


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
                Borrowing::whereBetween(
                    'created_at',
                    [
                        $weekStart,
                        $weekEnd
                    ]
                )
                ->whereIn(
                    'status',
                    [
                        'dipinjam',
                        'dikembalikan'
                    ]
                )
                ->count();


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
