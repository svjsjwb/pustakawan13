<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categoryOrder = [
            'Buku Pendidikan',
            'Anak',
            'Remaja',
            'Dewasa',
        ];

        $categories = Category::whereIn('name', $categoryOrder)
            ->with('subcategories')
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

        $query = Book::with([
                'category',
                'subcategory'
            ])
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

        if (
            $request->filled('category') &&
            $request->category !== 'Semua' &&
            $request->category !== 'Semua Kategori'
        ) {

            if (is_numeric($request->category)) {

                $query->where(
                    'category_id',
                    $request->category
                );

            } else {

                $query->whereHas(
                    'category',
                    function ($q) use ($request) {

                        $q->where(
                            'name',
                            $request->category
                        );

                    }
                );

            }

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

        if (
            $request->filled('status') &&
            $request->status !== 'Semua Status'
        ) {

            if (
                $request->status ===
                'Tersedia'
            ) {

                $query->whereHas(
                    'copies',
                    function ($q) {

                        $q->where(
                            'status',
                            'available'
                        );

                    }
                );

            } elseif (
                $request->status === 'Dipinjam' ||
                $request->status === 'Sedang Dipinjam'
            ) {

                $query->whereHas(
                    'copies',
                    function ($q) {

                        $q->whereIn(
                            'status',
                            [
                                'borrowed',
                                'reserved'
                            ]
                        );

                    }
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | LIVE SEARCH
        |--------------------------------------------------------------------------
        |
        | Search:
        | - Judul
        | - Penulis
        | - ISBN
        | - SKU
        | - Nomor inventaris
        | - Kode buku
        | - Kategori
        | - Subkategori
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search =
                trim(
                    (string) $request->search
                );


            if ($search !== '') {

                $query->where(
                    function ($q) use ($search) {

                        $q->where(
                            'judul_buku',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'penulis',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'isbn',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'sku',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'no_iventaris',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'kode_buku',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhereHas(
                            'category',
                            function ($categoryQuery) use ($search) {

                                $categoryQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );

                            }
                        )

                        ->orWhereHas(
                            'subcategory',
                            function ($subcategoryQuery) use ($search) {

                                $subcategoryQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );

                            }
                        );

                    }
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage =
            (int) $request->input(
                'per_page',
                25
            );


        if (
            !in_array(
                $perPage,
                [10, 25, 50, 100],
                true
            )
        ) {

            $perPage = 25;

        }


        $books =
            $query
                ->paginate($perPage)
                ->withQueryString();


        return view(
            'catalog.index',
            compact(
                'books',
                'categories'
            )
        );
    }
}