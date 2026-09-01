<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;

class UserHomeController extends Controller
{
    public function index()
    {
        // Buku terbaru
        $latestBooks = Book::with('category')
            ->latest()
            ->take(8)
            ->get();

        // Buku populer
        // Untuk sementara berdasarkan jumlah stok.
        // Nanti bisa diganti berdasarkan histori peminjaman.
        $popularBooks = Book::with('category')
            ->where('available_stock', '>', 0)
            ->orderByDesc('available_stock')
            ->take(8)
            ->get();

        // Kategori
        $categories = Category::orderBy('name')
            ->take(8)
            ->get();

        return view('user.home', compact(
            'latestBooks',
            'popularBooks',
            'categories'
        ));
    }
}