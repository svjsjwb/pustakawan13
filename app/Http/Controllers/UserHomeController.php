<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
                    'link'           => route('borrowings.index'),
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
                    'link'           => route('user.reservations.show', $lastReserve->id),
                ]);
            }

            $latestRequest = $requests->sortByDesc('date')->first();
        }

        // Buku populer
        $popularBooks = Book::with('category')
            ->where('stok', '>', 0)
            ->orderByDesc('stok')
            ->take(10)
            ->get();

        // Kategori
        $categoryOrder = ['Pendidikan', 'Anak-Anak', 'Remaja', 'Dewasa'];
        $categories = Category::whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, $categoryOrder, true))
            ->values();

        return view('user.home', compact(
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

    public function search(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));

        if ($keyword === '') {
            return response()->json(['books' => []]);
        }

        $books = Book::with('category')
            ->whereRaw('LOWER(title) LIKE ?', [mb_strtolower($keyword) . '%'])
            ->orderBy('title')
            ->take(8)
            ->get()
            ->map(fn ($book) => [
                'title' => $book->title,
                'author' => $book->author ?: 'Penulis tidak diketahui',
                'category' => $book->category?->name ?: 'Koleksi',
                'cover' => $book->cover ? asset('storage/' . $book->cover) : null,
                'url' => route('user.catalog', ['search' => $book->title]),
            ]);

        return response()->json(['books' => $books]);
    }
}