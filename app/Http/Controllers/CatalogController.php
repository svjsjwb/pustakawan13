<?php

namespace App\Http\Controllers;

use App\Models\Book;

class CatalogController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->paginate(25);

        return view('catalog.index', compact('books'));
    }
}