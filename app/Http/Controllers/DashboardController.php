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
         * PERIODE OTOMATIS & GRAFIK (MEMPERTAHANKAN SISTEM ASLI)
         * ========================================================
         */
        $startDate = now()->startOfMonth()->startOfDay();
        $endDate   = now()->endOfMonth()->endOfDay();

        $chart7Days = $this->generateBorrowChart($startDate, $endDate);

        $max7 = !empty($chart7Days)
            ? max(array_column($chart7Days, 'count'))
            : 0;

        $max7 = $max7 > 0 ? $max7 : 5;

        $reservations = Reservation::with(['member', 'book'])
            ->latest()
            ->take(20)
            ->get();


        /*
         * ========================================================
         * BUKU TERPOPULER (dari Pandu — fitur baru)
         * ========================================================
         *
         * Popularitas = jumlah peminjaman + jumlah reservasi valid.
         * Reservasi ditolak/dibatalkan tidak dihitung.
         */

        $borrowingCounts = collect();

        $allBorrowings = Borrowing::with('details')->get();

        foreach ($allBorrowings as $borrowing) {
            foreach ($borrowing->details as $detail) {
                $bookId   = $detail->book_id;
                $quantity = $detail->quantity ?? 1;

                $borrowingCounts[$bookId] =
                    ($borrowingCounts[$bookId] ?? 0) + $quantity;
            }
        }

        $reservationCounts = Reservation::whereNotIn('status', [
            'ditolak',
            'dibatalkan',
        ])
            ->selectRaw('book_id, COUNT(*) as total')
            ->groupBy('book_id')
            ->pluck('total', 'book_id');

        $popularBookIds = $borrowingCounts
            ->keys()
            ->merge($reservationCounts->keys())
            ->unique()
            ->values();

        $popularBooks = Book::whereIn('id', $popularBookIds)
            ->get()
            ->map(function ($book) use ($borrowingCounts, $reservationCounts) {
                $borrowingTotal   = (int) ($borrowingCounts[$book->id] ?? 0);
                $reservationTotal = (int) ($reservationCounts[$book->id] ?? 0);

                return [
                    'book_id'           => $book->id,
                    'title'             => $book->title ?? '-',
                    'borrowing_total'   => $borrowingTotal,
                    'reservation_total' => $reservationTotal,
                    'total'             => $borrowingTotal + $reservationTotal,
                ];
            })
            ->sort(function ($a, $b) {
                if ($a['total'] !== $b['total']) {
                    return $b['total'] <=> $a['total'];
                }
                if ($a['borrowing_total'] !== $b['borrowing_total']) {
                    return $b['borrowing_total'] <=> $a['borrowing_total'];
                }
                return $b['reservation_total'] <=> $a['reservation_total'];
            })
            ->take(5)
            ->values();


        /*
         * ========================================================
         * AKTIVITAS TERBARU
         * ========================================================
         *
         * Sumber aktivitas:
         *  1. Anggota baru
         *  2. Koleksi buku baru
         *  3. Peminjaman baru
         *  4. Reservasi baru
         *  5. Aktivitas manual (announcement dari Pandu)
         *
         * Digabung, diurutkan created_at desc, diambil 4 terbaru.
         */

        $activities = collect();

        // 1. Anggota baru
        $members = Member::latest()->take(20)->get();
        foreach ($members as $member) {
            $activities->push([
                'type'        => 'member',
                'title'       => 'Anggota baru',
                'description' => $member->name ?? '-',
                'created_at'  => $member->created_at,
                'icon'        => '+',
            ]);
        }

        // 2. Buku baru
        $books = Book::latest()->take(20)->get();
        foreach ($books as $book) {
            $activities->push([
                'type'        => 'book',
                'title'       => 'Koleksi buku baru',
                'description' => $book->title ?? '-',
                'created_at'  => $book->created_at,
                'icon'        => '+',
            ]);
        }

        // 3. Reservasi baru
        foreach ($reservations->take(20) as $reservation) {
            $activities->push([
                'type'        => 'reservation',
                'title'       => 'Reservasi baru',
                'description' => $reservation->member?->name ?? '-',
                'created_at'  => $reservation->created_at,
                'icon'        => '+',
            ]);
        }

        // 4. Peminjaman baru
        $borrowings = Borrowing::with(['member', 'details.book'])
            ->latest()
            ->take(20)
            ->get();

        foreach ($borrowings as $borrowing) {
            $activities->push([
                'type'        => 'borrowing',
                'title'       => 'Peminjaman baru',
                'description' => $borrowing->member?->name ?? '-',
                'created_at'  => $borrowing->created_at,
                'icon'        => '+',
            ]);
        }

        // 5. Aktivitas manual (announcement — dari Pandu)
        $manualActivities = Activity::latest()->get();

        foreach ($manualActivities as $manualActivity) {
            $activities->push([
                'id'          => $manualActivity->id,
                'type'        => 'manual',
                'title'       => $manualActivity->title ?? '-',
                'description' => $manualActivity->description ?? '-',
                'created_at'  => $manualActivity->created_at,
                'pinned_at'   => $manualActivity->pinned_at,
                'icon'        => '📢',
            ]);
        }

        $activities = $activities
            ->sortBy([
                ['pinned_at', 'desc'],
                ['created_at', 'desc'],
            ])
            ->take(4)
            ->values();


        /*
         * ========================================================
         * SEMUA AKTIVITAS MANUAL (UNTUK FORM EDIT — dari Pandu)
         * ========================================================
         */

        $allManualActivities = Activity::latest()->get();


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

    private function generateBorrowChart(Carbon $startDate, Carbon $endDate)
    {
        $labels = [];
        $counts = [];
        $cursor = $startDate->copy();
        $weekNumber = 1;

        while ($cursor->lte($endDate)) {
            $weekStart = $cursor->copy()->startOfDay();
            $weekEnd   = $cursor->copy()->addDays(6)->endOfDay();

            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }

            $labels[] = 'M' . $weekNumber;

            $counts[] = Borrowing::whereBetween('created_at', [$weekStart, $weekEnd])
                ->whereIn('status', ['dipinjam', 'dikembalikan'])
                ->count();

            $cursor = $weekEnd->copy()->addSecond();
            $weekNumber++;
        }

        $max   = !empty($counts) ? max($counts) : 0;
        $chart = [];

        foreach ($labels as $index => $label) {
            $count  = $counts[$index] ?? 0;
            $height = $max > 0 ? max(8, round(($count / $max) * 100)) : 8;

            $chart[] = [
                'label'  => $label,
                'count'  => $count,
                'height' => $height,
            ];
        }

        return $chart;
    }
}
