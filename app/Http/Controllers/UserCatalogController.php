<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCatalogController extends Controller
{
    private array $categoryTree = [
        'Buku Pendidikan' => [
            'SD' => [
                'Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris'
            ],
            'SMP' => [
                'Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris'
            ],
            'SMA' => [
                'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Ekonomi', 'Sejarah'
            ],
        ],
        'Buku Anak-Anak' => [
            'Cerita Anak' => ['Dongeng', 'Fabel', 'Cerita Bergambar'],
            'Edukasi Anak' => ['Mengenal Huruf', 'Mengenal Angka', 'Pengetahuan Umum'],
            'Aktivitas Anak' => ['Mewarnai', 'Kerajinan', 'Permainan Edukatif'],
        ],
        'Buku Remaja' => [
            'Pengembangan Diri' => ['Motivasi', 'Kepemimpinan', 'Komunikasi'],
            'Pendidikan' => ['Sains', 'Teknologi', 'Bahasa'],
            'Fiksi Remaja' => ['Novel', 'Cerpen', 'Komik Edukatif'],
        ],
        'Buku Dewasa' => [
            'Profesional' => ['Bisnis', 'Manajemen', 'Marketing'],
            'Teknologi' => ['Pemrograman', 'Jaringan', 'Data'],
            'Pengembangan Diri' => ['Karier', 'Kepemimpinan', 'Produktivitas'],
        ],
    ];

    public function index(Request $request)
    {
        $query = Book::with('category');

        // ── Search ────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title',  'like', "%{$search}%")
                  ->orWhere('author',     'like', "%{$search}%")
                  ->orWhere('isbn',       'like', "%{$search}%")
                  ->orWhere('publisher',  'like', "%{$search}%");
            });
        }

        // ── Filter kategori (ID) ──────────────────────────────
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // ── Filter nama kategori ──────────────────────────────
        if ($catName = $request->input('cat_name')) {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', "%{$catName}%"));
        }

        // ── Filter penulis ────────────────────────────────────
        if ($author = $request->input('author')) {
            $query->where('author', 'like', "%{$author}%");
        }

        // ── Filter penerbit ───────────────────────────────────
        if ($publisher = $request->input('publisher')) {
            $query->where('publisher', 'like', "%{$publisher}%");
        }

        // ── Filter tahun ──────────────────────────────────────
        if ($year = $request->input('year')) {
            $query->where('publication_year', $year);
        }

        // ── Filter rak ────────────────────────────────────────
        if ($rak = $request->input('rak')) {
            $query->where('rak', 'like', "%{$rak}%");
        }

        // ── Filter status ─────────────────────────────────────
        if ($status = $request->input('status')) {
            if ($status === 'tersedia') {
                $query->where('available_stock', '>', 0);
            } elseif ($status === 'habis') {
                $query->where('available_stock', '<=', 0);
            }
        }

        // ── Sorting ───────────────────────────────────────────
        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'az'    => $query->orderBy('title', 'asc'),
            'za'    => $query->orderBy('title', 'desc'),
            'terlama' => $query->oldest(),
            default => $query->latest(),
        };

        $books      = $query->paginate(20)->withQueryString();
        $categories = Category::withCount('books')->orderBy('name')->get();

        // Statistik perpustakaan untuk hero section
        $stats = [
            'total_titles'    => Book::count(),
            'available_count' => Book::where('available_stock', '>', 0)->count(),
            'borrowed_count'  => \App\Models\Borrowing::where('status', 'dipinjam')->count(),
        ];

        // Daftar penulis unik untuk filter
        $authors = Book::select('author')->distinct()->whereNotNull('author')
            ->orderBy('author')->pluck('author');

        // Daftar penerbit unik
        $publishers = Book::select('publisher')->distinct()->whereNotNull('publisher')
            ->orderBy('publisher')->pluck('publisher');

        // Tahun terbit
        $years = Book::select('publication_year')->distinct()->whereNotNull('publication_year')
            ->orderByDesc('publication_year')->pluck('publication_year');

        // Favorit session
        $favoriteIds = session('user_favorites', []);
        $member = Member::where('email', Auth::user()->email)->first();
        $reservedBookIds = $member
            ? Reservation::where('member_id', $member->id)
                ->whereIn('status', ['menunggu', 'disetujui', 'siap_diambil'])
                ->pluck('book_id')
                ->all()
            : [];
        $borrowedBookIds = $member
            ? Borrowing::where('member_id', $member->id)
                ->where('status', 'dipinjam')
                ->whereHas('details')
                ->with('details')
                ->get()
                ->flatMap(fn ($borrowing) => $borrowing->details->pluck('book_id'))
                ->unique()
                ->values()
                ->all()
            : [];

        return view('user.catalog', [
            'books'          => $books,
            'categories'     => $categories,
            'categoryTree'   => $this->categoryTree,
            'authors'        => $authors,
            'publishers'     => $publishers,
            'years'          => $years,
            'favoriteIds'    => $favoriteIds,
            'search'         => $request->input('search', ''),
            'activeCategory' => $request->input('category', ''),
            'activeCatName'  => $request->input('cat_name', ''),
            'activeSort'     => $sort,
            'stats'          => $stats,
            'reservedBookIds' => $reservedBookIds,
            'borrowedBookIds' => $borrowedBookIds,
        ]);
    }
}
