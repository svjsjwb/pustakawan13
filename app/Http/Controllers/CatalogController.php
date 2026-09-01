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
         * |--------------------------------------------------------------------------
         * | KATEGORI UTAMA
         * |--------------------------------------------------------------------------
         * |
         * | Mengambil kategori level 1.
         * | parent_id = NULL berarti kategori utama.
         * |
         */

        $categories = Category::with([
            'subcategories'
        ])
            ->whereNull('parent_id')
            ->where('level', 1)
            ->orderBy('name')
            ->get();

        /*
         * |--------------------------------------------------------------------------
         * | QUERY BUKU
         * |--------------------------------------------------------------------------
         * |
         * | Relasi yang digunakan:
         * |
         * | Book
         * | ├── Category
         * | └── Subcategory
         * |
         */

        $query = Book::with([
            'category',
            'subcategory',
        ])
            ->latest();

        /*
         * |--------------------------------------------------------------------------
         * | FILTER KATEGORI
         * |--------------------------------------------------------------------------
         */

        if ($request->filled('category')) {
            $query->where(
                'category_id',
                $request->category
            );
        }

        /*
         * |--------------------------------------------------------------------------
         * | FILTER SUBKATEGORI
         * |--------------------------------------------------------------------------
         */

        if ($request->filled('subcategory')) {
            $query->where(
                'subcategory_id',
                $request->subcategory
            );
        }

        /*
         * |--------------------------------------------------------------------------
         * | FILTER STATUS
         * |--------------------------------------------------------------------------
         * |
         * | Database menggunakan:
         * |
         * | stok
         * | status
         * |
         * | Bukan available_stock.
         * |
         */

        if ($request->filled('status')) {
            /*
             * |--------------------------------------------------------------------------
             * | TERSEDIA
             * |--------------------------------------------------------------------------
             */

            if ($request->status === 'Tersedia') {
                $query->where(
                    'stok',
                    '>',
                    0
                );
            }
            /*
             * |--------------------------------------------------------------------------
             * | DIPINJAM
             * |--------------------------------------------------------------------------
             */ elseif ($request->status === 'Dipinjam') {
                $query->where(
                    'stok',
                    '<=',
                    0
                );
            }
            /*
             * |--------------------------------------------------------------------------
             * | STATUS LANGSUNG DARI DATABASE
             * |--------------------------------------------------------------------------
             * |
             * | Jika status yang dikirim bukan "Tersedia"
             * | atau "Dipinjam", gunakan kolom status.
             * |
             */ else {
                $query->where(
                    'status',
                    $request->status
                );
            }
        }

        /*
         * |--------------------------------------------------------------------------
         * | SEARCH
         * |--------------------------------------------------------------------------
         * |
         * | Pencarian berdasarkan:
         * |
         * | - judul buku
         * | - penulis
         * | - SKU
         * | - kode buku
         * | - nomor inventaris
         * | - DDC
         * |
         */

        if ($request->filled('search')) {
            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {
                $q->where(
                    'judul_buku',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'penulis',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'sku',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'kode_buku',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'no_iventaris',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'ddc',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'rak',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        /*
         * |--------------------------------------------------------------------------
         * | JUMLAH DATA PER HALAMAN
         * |--------------------------------------------------------------------------
         */

        $perPage = (int) $request->input(
            'per_page',
            25
        );

        /*
         * |--------------------------------------------------------------------------
         * | BATASI PILIHAN PAGINATION
         * |--------------------------------------------------------------------------
         */

        if (!in_array(
            $perPage,
            [12, 25, 50, 100],
            true
        )) {
            $perPage = 25;
        }

        /*
         * |--------------------------------------------------------------------------
         * | PAGINATION
         * |--------------------------------------------------------------------------
         */

        $books = $query
            ->paginate($perPage)
            ->withQueryString();

        /*
         * |--------------------------------------------------------------------------
         * | TOTAL BUKU
         * |--------------------------------------------------------------------------
         */

        $totalBooks = Book::count();

        /*
         * |--------------------------------------------------------------------------
         * | TOTAL BUKU TERSEDIA
         * |--------------------------------------------------------------------------
         */

        $availableBooks = Book::where(
            'stok',
            '>',
            0
        )->count();

        /*
         * |--------------------------------------------------------------------------
         * | TOTAL BUKU DIPINJAM
         * |--------------------------------------------------------------------------
         */

        $borrowedBooks = Book::where(
            'stok',
            '<=',
            0
        )->count();

        /*
         * |--------------------------------------------------------------------------
         * | TAMPILKAN VIEW
         * |--------------------------------------------------------------------------
         */

        return view(
            'catalog.index',
            compact(
                'books',
                'categories',
                'totalBooks',
                'availableBooks',
                'borrowedBooks'
            )
        );
    }
}
