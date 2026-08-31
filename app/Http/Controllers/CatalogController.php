<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Query sederhana tanpa filter apapun untuk memastikan data muncul
        $query = Book::with('category');

        // Filter Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q
                    ->where('judul_buku', 'like', '%' . $search . '%')
                    ->orWhere('penulis', 'like', '%' . $search . '%');
            });
        }

        // Ambil data buku dengan pagination
        $books = $query->latest()->paginate(12)->withQueryString();

        // Ambil kategori utama (level 1)
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('catalog.index', compact('books', 'categories'));
    }
}
