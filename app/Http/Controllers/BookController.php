<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Rack;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->latest()->get();

        return view('books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        $racks = Rack::orderBy('code')->get();

        return view('books.create', compact('categories', 'racks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required',
            'title'            => 'required',
            'author'           => 'required',
            'publisher'        => 'required',
            'publication_year' => 'required',
            'isbn'             => 'required|unique:books',
            'call_number'      => 'required|unique:books',
            'stock'            => 'required|integer|min:1',
            'rak'              => 'required|exists:racks,code',
        ]);

        $cover = null;

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover')->store('covers', 'public');
        }

        Book::create([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'author'           => $request->author,
            'publisher'        => $request->publisher,
            'publication_year' => $request->publication_year,
            'isbn'             => $request->isbn,
            'call_number'      => $request->call_number,
            'stock'            => $request->stock,
            'available_stock'  => $request->stock,
            'description'      => $request->description,
            'cover'            => $cover,
            'no_iventaris'     => $request->no_iventaris,
            'kode_buku'        => $request->kode_buku,
            'ddc'              => $request->ddc,
            'rak'              => $request->rak,
            'edition'          => $request->edition,
        ]);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        $racks = Rack::orderBy('code')->get();

        return view('books.edit', compact('book', 'categories', 'racks'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'publisher'        => 'required|string|max:255',
            'publication_year' => 'required|integer',
            'isbn'             => 'required|unique:books,isbn,' . $book->id,
            'call_number'      => 'required|unique:books,call_number,' . $book->id,
            'stock'            => 'required|integer|min:1',
            'rak'              => 'required|exists:racks,code',
        ]);

        // Jumlah buku yang sedang dipinjam
        $borrowed = $book->stock - $book->available_stock;

        // Stok baru tidak boleh lebih kecil dari jumlah yang sedang dipinjam
        if ($request->stock < $borrowed) {
            return back()
                ->withInput()
                ->withErrors([
                    'stock' => 'Stok tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam.'
                ]);
        }

        // Hitung stok yang tersedia setelah perubahan
        $availableStock = $request->stock - $borrowed;

        $cover = $book->cover;

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover')->store('covers', 'public');
        }

        $book->update([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'author'           => $request->author,
            'publisher'        => $request->publisher,
            'publication_year' => $request->publication_year,
            'isbn'             => $request->isbn,
            'call_number'      => $request->call_number,
            'stock'            => $request->stock,
            'available_stock'  => $availableStock,
            'description'      => $request->description,
            'cover'            => $cover,
            'no_iventaris'     => $request->no_iventaris,
            'kode_buku'        => $request->kode_buku,
            'ddc'              => $request->ddc,
            'rak'              => $request->rak,
            'edition'          => $request->edition,
        ]);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
