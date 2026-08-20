<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryFloor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookCopyController extends Controller
{
    /**
     * Menampilkan semua eksemplar dari sebuah buku.
     */
    public function index(Book $book)
    {
        $copies = $book->copies()
            ->with('shelf.zone.floor')
            ->orderBy('id')
            ->get();

        return view('book_copies.index', compact(
            'book',
            'copies'
        ));
    }

    /**
     * Form tambah eksemplar.
     */
    public function create(Book $book)
    {
        $floors = LibraryFloor::with(
            'zones.shelves'
        )->orderBy('floor_number')->get();

        return view('book_copies.create', compact(
            'book',
            'floors'
        ));
    }

    /**
     * Simpan eksemplar baru.
     */
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                'unique:book_copies,barcode',
            ],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'reserved',
                    'borrowed',
                    'lost',
                    'damaged',
                    'maintenance',
                ]),
            ],

            'shelf_id' => [
                'nullable',
                'exists:shelves,id',
            ],

            'section' => [
                'required',
                'integer',
                'in:1,2',
            ],

            'row' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'column' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $book->copies()->create($validated);

        return redirect()
            ->route('books.copies.index', $book)
            ->with(
                'success',
                'Eksemplar buku berhasil ditambahkan.'
            );
    }

    /**
     * Form edit lokasi/status eksemplar.
     */
    public function edit(Book $book, BookCopy $copy)
    {
        abort_unless(
            $copy->book_id === $book->id,
            404
        );

        $floors = LibraryFloor::with(
            'zones.shelves'
        )->orderBy('floor_number')->get();

        return view('book_copies.edit', compact(
            'book',
            'copy',
            'floors'
        ));
    }

    /**
     * Update eksemplar.
     */
    public function update(
        Request $request,
        Book $book,
        BookCopy $copy
    ) {
        abort_unless(
            $copy->book_id === $book->id,
            404
        );

        $validated = $request->validate([
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('book_copies', 'barcode')
                    ->ignore($copy->id),
            ],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'reserved',
                    'borrowed',
                    'lost',
                    'damaged',
                    'maintenance',
                ]),
            ],

            'shelf_id' => [
                'nullable',
                'exists:shelves,id',
            ],

            'row' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'column' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $copy->update($validated);

        return redirect()
            ->route('books.copies.index', $book)
            ->with(
                'success',
                'Eksemplar buku berhasil diperbarui.'
            );
    }
}
