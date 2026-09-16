<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\BookProposal;
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

        $max = !empty($counts) ? max($counts) : 0;
        $chart = [];

        foreach ($labels as $index => $label) {
            $count = $counts[$index] ?? 0;
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