<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan katalog koleksi buku khusus untuk Role User (Pengguna / Pembaca).
     * Terpisah sepenuhnya dari Katalog Admin.
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh kategori beserta jumlah buku yang terdaftar
        $categories = Category::withCount('books')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Inisialisasi query buku dengan relasi kategori dan rak
        $query = Book::with(['category', 'rack']);

        // 3. Filter berdasarkan Kategori
        if ($request->filled('category') && $request->category !== 'Semua' && $request->category !== 'semua') {
            $categoryParam = $request->category;
            $query->whereHas('category', function ($q) use ($categoryParam) {
                $q->where('name', $categoryParam)
                  ->orWhere('id', $categoryParam);
            });
        }

        // 4. Filter berdasarkan Status Ketersediaan Stok
        if ($request->filled('status') && $request->status !== 'Semua' && $request->status !== 'Semua Status') {
            if ($request->status === 'Tersedia' || $request->status === 'tersedia') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->status === 'Dipinjam' || $request->status === 'dipinjam') {
                $query->where('available_stock', '<=', 0);
            }
        }

        // 5. Filter Pencarian Cepat (Judul, Pengarang, Penerbit, ISBN, Sinopsis)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 6. Pengurutan Data (Sorting)
        $sortBy = $request->input('sort', 'terbaru');
        switch ($sortBy) {
            case 'populer':
                $query->orderByDesc('available_stock')->latest('id');
                break;
            case 'az':
                $query->orderBy('title', 'asc');
                break;
            case 'za':
                $query->orderBy('title', 'desc');
                break;
            case 'tahun':
                $query->orderByDesc('publication_year')->latest('id');
                break;
            case 'terbaru':
            default:
                $query->latest('id');
                break;
        }

        // 7. Hitung statistik koleksi untuk widget informasi
        $totalBooksCount = Book::count();
        $availableBooksCount = Book::where('available_stock', '>', 0)->count();

        // 8. Paginasi hasil query
        $perPage = (int) $request->input('per_page', 15);
        $books = $query->paginate($perPage)->withQueryString();

        // 9. Render view khusus katalog role user
        return view('user.catalog.index', compact(
            'books',
            'categories',
            'totalBooksCount',
            'availableBooksCount',
            'sortBy'
        ));
    }

    /**
     * Mengambil detail buku (JSON / Modal AJAX atau Halaman Tersendiri)
     */
    public function show(Request $request, Book $book)
    {
        $book->load(['category', 'rack', 'copies']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $book,
            ]);
        }

        return view('user.catalog.show', compact('book'));
    }
}
