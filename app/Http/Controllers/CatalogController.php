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

                $query->where(
                    'available_stock',
                    '>',
                    0
                );
            }

            elseif (
                $request->status === 'Dipinjam'
            ) {

                $query->where(
                    'available_stock',
                    '<=',
                    0
                );
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