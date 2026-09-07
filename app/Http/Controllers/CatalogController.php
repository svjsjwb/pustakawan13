<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan katalog buku.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KATEGORI + SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        $categories = Category::with('subcategories')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | QUERY BUKU
        |--------------------------------------------------------------------------
        */

        $query = Book::with([
            'category',
            'subcategory',
        ])->latest();


        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {
            $query->where(
                'category_id',
                $request->category
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('subcategory')) {
            $query->where(
                'subcategory_id',
                $request->subcategory
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            if ($request->status === 'Tersedia') {
                $query->where(function ($q) {
                    $q->where('stok', '>', 0)
                      ->orWhere('available_stock', '>', 0);
                });
            } elseif ($request->status === 'Dipinjam') {
                $query->where(function ($q) {
                    $q->where('stok', '<=', 0)
                      ->orWhere('available_stock', '<=', 0);
                });
            } else {
                $query->where('status', $request->status);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('kode_buku', 'like', "%{$search}%")
                  ->orWhere('no_iventaris', 'like', "%{$search}%")
                  ->orWhere('ddc', 'like', "%{$search}%")
                  ->orWhere('rak', 'like', "%{$search}%");
            });
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input(
            'per_page',
            25
        );

        if (!in_array($perPage, [12, 25, 50, 100])) {
            $perPage = 25;
        }

        $books = $query
            ->paginate($perPage)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'catalog.index',
            compact(
                'books',
                'categories'
            )
        );
    }
}
