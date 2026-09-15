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
        | KATEGORI + SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        $categoryOrder = [
            'Buku Pendidikan',
            'Anak',
            'Remaja',
            'Dewasa',
        ];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->orderByRaw("
                CASE name
                    WHEN 'Buku Pendidikan' THEN 1
                    WHEN 'Anak' THEN 2
                    WHEN 'Remaja' THEN 3
                    WHEN 'Dewasa' THEN 4
                    ELSE 5
                END
            ")
            ->get();


        /*
        |--------------------------------------------------------------------------
        | QUERY BUKU
        |--------------------------------------------------------------------------
        |
        | Status katalog sekarang diambil dari BookCopy:
        |
        | available = eksemplar tersedia
        | borrowed  = eksemplar sedang dipinjam
        | reserved  = eksemplar sedang direservasi
        |
        | Jadi satu judul tetap bisa muncul sebagai "Dipinjam"
        | walaupun masih mempunyai eksemplar lain yang tersedia.
        */

        $query = Book::with([
            'category',
            'subcategory',
        ])
            ->withCount([
                'copies as available_copies_count' => function ($copyQuery) {
                    $copyQuery->where('status', 'available');
                },

                'copies as borrowed_copies_count' => function ($copyQuery) {
                    $copyQuery->where('status', 'borrowed');
                },

                'copies as reserved_copies_count' => function ($copyQuery) {
                    $copyQuery->where('status', 'reserved');
                },
            ])
            ->latest();


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

                $query->whereHas('copies', function ($copyQuery) {

                    $copyQuery->where(
                        'status',
                        'available'
                    );

                });

            } elseif ($request->status === 'Dipinjam') {

                /*
                 * "Sedang Dipinjam" juga mencakup buku yang sedang
                 * mempunyai eksemplar reserved.
                 *
                 * Yang dicari adalah status EKSEMPLAR, bukan
                 * available_stock pada tabel books.
                 */

                $query->whereHas('copies', function ($copyQuery) {

                    $copyQuery->whereIn(
                        'status',
                        [
                            'borrowed',
                            'reserved',
                        ]
                    );

                });
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

                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'author',
                        'like',
                        "%{$search}%"
                    );
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
