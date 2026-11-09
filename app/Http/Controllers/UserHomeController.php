<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Book;
use App\Models\Category;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    public function index(?Request $request = null)
    {
        $user   = Auth::user();
        $member = $user?->member;

        // ── 4 WIDGET DASHBOARD USER ──────────────────────────────
        $activeLoansCount    = 0;
        $activeReservesCount = 0;
        $historyCount        = 0;
        $latestRequest       = null;

        if ($member) {
            $borrowingStats = Borrowing::where('member_id', $member->id)
                ->selectRaw("
                    COUNT(CASE WHEN status IN ('dipinjam', 'diperpanjang', 'terlambat') THEN 1 END) as active_loans,
                    COUNT(CASE WHEN status = 'dikembalikan' THEN 1 END) as history_count
                ")
                ->first();

            $activeLoansCount = (int) ($borrowingStats->active_loans ?? 0);
            $historyCount     = (int) ($borrowingStats->history_count ?? 0);

            $activeReservesCount = Reservation::where('member_id', $member->id)
                ->whereIn('status', ['menunggu', 'disetujui'])
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
                    'type'       => $lastBorrow->extension_status === 'menunggu'
                        ? 'Perpanjangan Peminjaman'
                        : 'Peminjaman Buku',

                    'title'      => $bookTitle,

                    'status'     => $lastBorrow->extension_status === 'menunggu'
                        ? 'Menunggu Persetujuan'
                        : $lastBorrow->display_status,

                    'status_raw' => $lastBorrow->extension_status === 'menunggu'
                        ? 'menunggu'
                        : $lastBorrow->status,

                    'date'       => $lastBorrow->created_at,

                    'notes'      => $lastBorrow->rejection_reason
                        ?? ($lastBorrow->extension_reason ?? null),

                    'link'       => route('borrowings.index'),
                ]);
            }

            $lastReserve = Reservation::with('book')
                ->where('member_id', $member->id)
                ->latest()
                ->first();

            if ($lastReserve) {
                $requests->push([
                    'type'       => 'Reservasi Buku',
                    'title'      => $lastReserve->book?->title ?? 'Buku',
                    'status'     => $lastReserve->display_status,
                    'status_raw' => $lastReserve->status,
                    'date'       => $lastReserve->created_at,
                    'notes'      => $lastReserve->rejection_reason,
                    'link'       => route(
                        'user.reservations.show',
                        $lastReserve->id
                    ),
                ]);
            }

            $latestRequest = $requests
                ->sortByDesc('date')
                ->first();
        }

        // Buku populer (di-cache ID lokal selama 5 menit)
        $popularBookIds = Cache::remember('user_home_popular_book_ids', 300, function () {
            return Book::where('stok', '>', 0)
                ->orderByRaw("CASE WHEN NULLIF(TRIM(cover), '') IS NOT NULL THEN 0 ELSE 1 END")
                ->orderByDesc('stok')
                ->take(10)
                ->pluck('id')
                ->toArray();
        });

        $popularBooks = Book::with('category')
            ->whereIn('id', $popularBookIds)
            ->get()
            ->sortBy(fn($b) => array_search($b->id, $popularBookIds))
            ->values();

        // Rekomendasi buku untuk user
        $recommendations = app(RecommendationService::class)
            ->getForUser($user, 8);

        // ── Kategori ────────────────────────────────────────────
        $categoryOrder = [
            'Buku Pelajaran',
            'Buku Umum',
        ];

        $categories = Category::whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(
                fn($category) =>
                array_search(
                    $category->name,
                    $categoryOrder,
                    true
                )
            )
            ->values();

        // Aktivitas / Pengumuman
        $announcements = Activity::latest()->get();

        $unreadAnnouncements = collect();
        $unreadAnnouncement = null;
        if ($user) {
            $readIds = \Illuminate\Support\Facades\DB::table('activity_reads')
                ->where('user_id', $user->id)
                ->pluck('activity_id')
                ->toArray();
            $unreadAnnouncements = $announcements->whereNotIn('id', $readIds)->values();
            $unreadAnnouncement = $unreadAnnouncements->first();
        }

        return view('user.home', compact(
            'popularBooks',
            'recommendations',
            'categories',
            'user',
            'member',
            'activeLoansCount',
            'activeReservesCount',
            'historyCount',
            'latestRequest',
            'announcements',
            'unreadAnnouncements',
            'unreadAnnouncement'
        ));
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));

        if ($keyword === '') {
            return response()->json([
                'books' => []
            ]);
        }

        $books = Book::with('category')
            ->whereRaw(
                'LOWER(judul_buku) LIKE ?',
                [mb_strtolower($keyword) . '%']
            )
            ->orderBy('judul_buku')
            ->take(8)
            ->get()
            ->map(fn($book) => [
                'title'    => $book->title,
                'author'   => $book->author ?: 'Penulis tidak diketahui',
                'category' => $book->category?->name ?: 'Koleksi',
                'cover'    => $book->cover
                    ? asset('storage/' . $book->cover)
                    : null,
                'url'      => route(
                    'user.catalog',
                    ['search' => $book->title]
                ),
            ]);

        return response()->json([
            'books' => $books
        ]);
    }
}
