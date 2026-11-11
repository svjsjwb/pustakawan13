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
            'Buku Pelajaran',
            'Buku Umum',
        ];

        $categories = Category::whereIn('name', $categoryOrder)
            ->with('subcategories')
            ->withCount('books')
            ->orderByRaw("
                CASE name
                    WHEN 'Buku Pelajaran' THEN 1
                    WHEN 'Buku Umum' THEN 2
                    ELSE 3
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
            ->orderByRaw('created_at DESC NULLS LAST, id DESC');


        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI (BERBASIS ID)
        |--------------------------------------------------------------------------
        */

        $categoryId = $request->input('category_id') ?? $request->input('category');
        if (
            $categoryId &&
            $categoryId !== 'Semua' &&
            $categoryId !== 'Semua Kategori'
        ) {
            if (!is_numeric($categoryId)) {
                $categoryId = Category::where('name', $categoryId)->value('id');
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER SUBKATEGORI (BERBASIS ID)
        |--------------------------------------------------------------------------
        */

        $subcategoryId = $request->input('subcategory_id') ?? $request->input('subcategory');
        if ($subcategoryId && $subcategoryId !== 'Semua') {
            if (!is_numeric($subcategoryId)) {
                $subQuery = \App\Models\Subcategory::where('name', $subcategoryId);
                if (!empty($categoryId)) {
                    $subQuery->where('category_id', $categoryId);
                }
                $subcategoryId = $subQuery->value('id');
            }

            if ($subcategoryId) {
                $query->where('subcategory_id', $subcategoryId);
            }
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
                            'ilike',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'penulis',
                            'ilike',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'isbn',
                            'ilike',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'sku',
                            'ilike',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'no_iventaris',
                            'ilike',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'kode_buku',
                            'ilike',
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