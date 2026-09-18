<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | URUTAN & DATA KATEGORI
        |--------------------------------------------------------------------------
        */

        $categoryOrder = [
            'Pendidikan',
            'Anak-Anak',
            'Remaja',
            'Dewasa',
        ];

        $categories = Category::whereIn('name', $categoryOrder)
            ->with('subcategories')
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, $categoryOrder, true))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | QUERY BUKU
        |--------------------------------------------------------------------------
        |
        | withCount copies untuk mengetahui status eksemplar secara akurat.
        | Satu judul bisa punya eksemplar tersedia & dipinjam sekaligus.
        */

        $query = Book::with(['category', 'subcategory'])
            ->withCount([
                'copies as available_copies_count' => function ($q) {
                    $q->where('status', 'available');
                },
                'copies as borrowed_copies_count' => function ($q) {
                    $q->where('status', 'borrowed');
                },
                'copies as reserved_copies_count' => function ($q) {
                    $q->where('status', 'reserved');
                },
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category') && $request->category !== 'Semua' && $request->category !== 'Semua Kategori') {
            if (is_numeric($request->category)) {
                $query->where('category_id', $request->category);
            } else {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        |
        | Menggunakan status eksemplar (copies) bukan kolom stok.
        */

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            if ($request->status === 'Tersedia') {
                $query->whereHas('copies', function ($q) {
                    $q->where('status', 'available');
                });
            } elseif ($request->status === 'Dipinjam' || $request->status === 'Sedang Dipinjam') {
                $query->whereHas('copies', function ($q) {
                    $q->whereIn('status', ['borrowed', 'reserved']);
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%");
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input('per_page', 25);
        $books   = $query->paginate($perPage)->withQueryString();

        return view('catalog.index', compact('books', 'categories'));
    }
}
