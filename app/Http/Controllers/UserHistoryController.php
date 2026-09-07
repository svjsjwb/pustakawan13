<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHistoryController extends Controller
{
    /**
     * Tampilkan halaman riwayat transaksi user (Peminjaman & Reservasi)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('tab', 'borrowings');
        $search = $request->get('search');
        $statusFilter = $request->get('status');

        // Cari ID member user yang tersinkronisasi
        $memberId = Member::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->value('id');

        // ─── 1. QUERY RIWAYAT PEMINJAMAN ────────────────────────────────
        $borrowingsQuery = Borrowing::with(['details.book', 'member'])
            ->where(function ($q) use ($user, $memberId) {
                if ($memberId) {
                    $q->where('member_id', $memberId);
                } else {
                    $q->whereRaw('1 = 0'); // Belum ada riwayat jika member belum terhubung
                }
            });

        if ($search) {
            $borrowingsQuery->whereHas('details.book', function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && $activeTab === 'borrowings') {
            if ($statusFilter === 'selesai') {
                $borrowingsQuery->where('status', 'dikembalikan');
            } elseif ($statusFilter === 'terlambat') {
                $borrowingsQuery->where(function ($q) {
                    $q->where('status', 'terlambat')
                      ->orWhere(function ($sub) {
                          $sub->whereIn('status', ['dipinjam', 'diperpanjang'])
                              ->whereDate('due_at', '<', now()->toDateString());
                      });
                });
            } elseif ($statusFilter === 'dipinjam') {
                $borrowingsQuery->whereIn('status', ['dipinjam', 'diperpanjang'])
                    ->whereDate('due_at', '>=', now()->toDateString());
            }
        }

        $borrowings = $borrowingsQuery
            ->orderBy('borrowed_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(8, ['*'], 'borrowings_page')
            ->withQueryString();

        // ─── 2. QUERY RIWAYAT RESERVASI ─────────────────────────────────
        $reservationsQuery = Reservation::with('book')
            ->where(function ($q) use ($user, $memberId) {
                $q->where('user_id', $user->id);
                if ($memberId) {
                    $q->orWhere('member_id', $memberId);
                }
            });

        if ($search && $activeTab === 'reservations') {
            $reservationsQuery->whereHas('book', function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && $activeTab === 'reservations') {
            if ($statusFilter === 'pending') {
                $reservationsQuery->where('status', 'menunggu');
            } elseif ($statusFilter === 'disetujui') {
                $reservationsQuery->where('status', 'disetujui');
            } elseif ($statusFilter === 'dibatalkan') {
                $reservationsQuery->whereIn('status', ['dibatalkan', 'ditolak']);
            } elseif ($statusFilter === 'selesai') {
                $reservationsQuery->where('status', 'selesai');
            }
        }

        $reservations = $reservationsQuery
            ->orderBy('reserved_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(8, ['*'], 'reservations_page')
            ->withQueryString();

        // Hitung total untuk badge tab
        $totalBorrowings = Borrowing::where('member_id', $memberId)->count();
        $totalReservations = Reservation::where('user_id', $user->id)
            ->when($memberId, fn($q) => $q->orWhere('member_id', $memberId))
            ->count();

        return view('user.history', compact(
            'borrowings',
            'reservations',
            'activeTab',
            'search',
            'statusFilter',
            'totalBorrowings',
            'totalReservations'
        ));
    }
}
