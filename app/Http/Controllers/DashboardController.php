<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // STATISTIK
        // =========================

        $totalBooks = Book::count();

        $borrowedBooks = Borrowing::where(
            'status',
            'dipinjam'
        )->count();

        $activeMembers = Member::where(
            'status',
            'aktif'
        )->count();

        $lateBorrowings = Borrowing::where(
            'status',
            'dipinjam'
        )
            ->whereDate(
                'due_at',
                '<',
                now()->startOfDay()
            )
            ->count();


        // =========================
        // 5 RESERVASI TERBARU
        // =========================

        $reservations = Reservation::with([
            'member',
            'book'
        ])
            ->latest()
            ->take(5)
            ->get();


        // =========================
        // STATUS UNTUK DASHBOARD
        // =========================

        foreach ($reservations as $reservation) {

            /*
            |--------------------------------------------------------------------------
            | DEFAULT
            |--------------------------------------------------------------------------
            */

            $reservation->display_status = $reservation->status;


            /*
            |--------------------------------------------------------------------------
            | MENUNGGU
            |--------------------------------------------------------------------------
            */

            if ($reservation->status === 'menunggu') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | DITOLAK
            |--------------------------------------------------------------------------
            */

            if ($reservation->status === 'ditolak') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | DIBATALKAN
            |--------------------------------------------------------------------------
            */

            if ($reservation->status === 'dibatalkan') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SELESAI
            |--------------------------------------------------------------------------
            */

            if ($reservation->status === 'selesai') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | DISETUJUI
            |--------------------------------------------------------------------------
            */

            if ($reservation->status !== 'disetujui') {
                continue;
            }


            // Cari transaksi peminjaman
            $borrowing = Borrowing::where(
                'member_id',
                $reservation->member_id
            )
                ->whereHas('details', function ($query) use ($reservation) {

                    $query->where(
                        'book_id',
                        $reservation->book_id
                    );
                })
                ->latest()
                ->first();


            /*
            |--------------------------------------------------------------------------
            | SUDAH ADA PEMINJAMAN
            |--------------------------------------------------------------------------
            */

            if ($borrowing) {

                // -------------------------
                // SUDAH DIKEMBALIKAN
                // -------------------------

                if ($borrowing->status === 'dikembalikan') {

                    $reservation->display_status = 'selesai';

                    continue;
                }


                // -------------------------
                // MASIH DIPINJAM
                // -------------------------

                if ($borrowing->status === 'dipinjam') {

                    $dueDate = Carbon::parse(
                        $borrowing->due_at
                    )->startOfDay();

                    $today = now()->startOfDay();


                    // Terlambat
                    if ($today->gt($dueDate)) {

                        $daysLate = $dueDate->diffInDays($today);

                        $reservation->display_status =
                            'terlambat ' . $daysLate . ' hari';
                    } else {

                        $reservation->display_status =
                            'dipinjam';
                    }

                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | BELUM DIPINJAM
            |--------------------------------------------------------------------------
            */

            if ($reservation->expires_at) {

                $expiresDate = Carbon::parse(
                    $reservation->expires_at
                )->startOfDay();

                $today = now()->startOfDay();


                // Masa reservasi sudah lewat
                if ($today->gt($expiresDate)) {

                    $daysLate = $expiresDate->diffInDays($today);

                    $reservation->display_status =
                        'terlambat ' . $daysLate . ' hari';

                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | MASIH DISETUJUI
            |--------------------------------------------------------------------------
            */

            $reservation->display_status = 'disetujui';
        }


        // =========================
        // KIRIM KE VIEW
        // =========================

        return view('dashboard.index', compact(
            'totalBooks',
            'borrowedBooks',
            'activeMembers',
            'lateBorrowings',
            'reservations'
        ));
    }
}
