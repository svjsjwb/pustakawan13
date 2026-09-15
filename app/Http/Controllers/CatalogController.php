<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Book::with('category')->latest();

        if ($request->filled('category') && $request->category !== 'Semua' && $request->category !== 'Semua Kategori') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            if ($request->status === 'Tersedia') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->status === 'Dipinjam' || $request->status === 'Sedang Dipinjam') {
                $query->where('available_stock', '<=', 0);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            if ($search !== '') {
                $query->where('title', 'like', "{$search}%");
            }
        }

        $perPage = (int) $request->input('per_page', 25);
        $books = $query->paginate($perPage)->withQueryString();

        return view('catalog.index', compact('books', 'categories'));
    }
}