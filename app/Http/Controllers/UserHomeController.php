<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class UserHomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = Member::where('email', $user?->email)->first();

        // ── 4 WIDGET DASHBOARD USER ──────────────────────────────
        $activeLoansCount    = 0;
        $activeReservesCount = 0;
        $historyCount        = 0;
        $latestRequest       = null;

        if ($member) {
            $activeLoansCount = Borrowing::where('member_id', $member->id)
                ->where('status', 'dipinjam')
                ->count();

            $activeReservesCount = Reservation::where('member_id', $member->id)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->count();

            $historyCount = Borrowing::where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->count();

            // Kumpulkan permohonan terakhir (Peminjaman, Reservasi, atau Usulan Buku)
            $requests = collect();

            $lastBorrow = Borrowing::with('details.book')
                ->where('member_id', $member->id)
                ->latest()
                ->first();
            if ($lastBorrow) {
                $bookTitle = $lastBorrow->details->first()?->book?->title ?? 'Buku';
                $requests->push([
                    'type'           => $lastBorrow->extension_status === 'menunggu' ? 'Perpanjangan Peminjaman' : 'Peminjaman Buku',
                    'title'          => $bookTitle,
                    'status'         => $lastBorrow->extension_status === 'menunggu' ? 'Menunggu Persetujuan' : $lastBorrow->display_status,
                    'status_raw'     => $lastBorrow->extension_status === 'menunggu' ? 'menunggu' : $lastBorrow->status,
                    'date'           => $lastBorrow->created_at,
                    'notes'          => $lastBorrow->rejection_reason ?? ($lastBorrow->extension_reason ?? null),
                    'link'           => route('user.loans'),
                ]);
            }

            $lastReserve = Reservation::with('book')
                ->where('member_id', $member->id)
                ->latest()
                ->first();
            if ($lastReserve) {
                $requests->push([
                    'type'           => 'Reservasi Buku',
                    'title'          => $lastReserve->book?->title ?? 'Buku',
                    'status'         => $lastReserve->display_status,
                    'status_raw'     => $lastReserve->status,
                    'date'           => $lastReserve->created_at,
                    'notes'          => $lastReserve->rejection_reason,
                    'link'           => route('user.reservations'),
                ]);
            }

            $latestRequest = $requests->sortByDesc('date')->first();
        }

        // Buku terbaru
        $latestBooks = Book::with('category')
            ->latest()
            ->take(8)
            ->get();

        // Buku populer
        $popularBooks = Book::with('category')
            ->where('available_stock', '>', 0)
            ->orderByDesc('available_stock')
            ->take(8)
            ->get();

        // Kategori
        $categories = Category::orderBy('name')
            ->take(8)
            ->get();

        return view('user.home', compact(
            'latestBooks',
            'popularBooks',
            'categories',
            'user',
            'member',
            'activeLoansCount',
            'activeReservesCount',
            'historyCount',
            'latestRequest'
        ));
    }
}