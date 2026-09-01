<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $books = Book::with([
            'category',
            'subcategory',
        ])
            ->latest()
            ->get();

        return view('books.index', compact('books'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::with('subcategories')
            ->orderBy('name')
            ->get();

        $subcategoryData = $categories
            ->mapWithKeys(function ($category) {
                return [
                    $category->id => $category->subcategories
                        ->map(function ($subcategory) {
                            return [
                                'id' => $subcategory->id,
                                'name' => $subcategory->name,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->toArray();

        $racks = Rack::orderBy('code')->get();

        return view(
            'books.create',
            compact(
                'categories',
                'subcategoryData',
                'racks'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:books,sku',
            ],

            'author' => [
                'required',
                'string',
                'max:255',
            ],

            'publisher' => [
                'required',
                'string',
                'max:255',
            ],

            'publication_year' => [
                'required',
                'integer',
            ],

            'isbn' => [
                'required',
                'unique:books,isbn',
            ],

            'call_number' => [
                'required',
                'unique:books,call_number',
            ],

            'stock' => [
                'required',
                'integer',
                'min:1',
            ],

            'rak' => [
                'required',
                'exists:racks,code',
            ],

            'no_iventaris' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kode_buku' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ddc' => [
                'nullable',
                'string',
                'max:255',
            ],

            'edition' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SUBKATEGORI
        |--------------------------------------------------------------------------
        |
        | Subkategori harus benar-benar milik kategori yang dipilih.
        |
        */

        if ($request->filled('subcategory_id')) {

            $validSubcategory = Subcategory::where(
                'id',
                $request->subcategory_id
            )
                ->where(
                    'category_id',
                    $request->category_id
                )
                ->exists();

            if (! $validSubcategory) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'subcategory_id' =>
                        'Subkategori tidak sesuai dengan kategori yang dipilih.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD COVER
        |--------------------------------------------------------------------------
        */

        $cover = null;

        if ($request->hasFile('cover')) {

            $cover = $request
                ->file('cover')
                ->store('covers', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN BUKU
        |--------------------------------------------------------------------------
        */

        Book::create([

            'category_id' => $request->category_id,

            'subcategory_id' => $request->subcategory_id,

            'title' => $request->title,

            'sku' => $request->sku,

            'author' => $request->author,

            'publisher' => $request->publisher,

            'publication_year' => $request->publication_year,

            'isbn' => $request->isbn,

            'call_number' => $request->call_number,

            'stock' => $request->stock,

            'available_stock' => $request->stock,

            'description' => $request->description,

            'cover' => $cover,

            'no_iventaris' => $request->no_iventaris,

            'kode_buku' => $request->kode_buku,

            'ddc' => $request->ddc,

            'rak' => $request->rak,

            'edition' => $request->edition,
        ]);


        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Buku berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Book $book)
    {
        $categories = Category::with('subcategories')
            ->orderBy('name')
            ->get();

        $racks = Rack::orderBy('code')->get();

        return view(
            'books.edit',
            compact(
                'book',
                'categories',
                'racks'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Book $book
    ) {

        $request->validate([

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'author' => [
                'required',
                'string',
                'max:255',
            ],

            'publisher' => [
                'required',
                'string',
                'max:255',
            ],

            'publication_year' => [
                'required',
                'integer',
            ],

            'isbn' => [
                'required',
                'unique:books,isbn,' . $book->id,
            ],

            'call_number' => [
                'required',
                'unique:books,call_number,' . $book->id,
            ],

            'stock' => [
                'required',
                'integer',
                'min:1',
            ],

            'rak' => [
                'required',
                'exists:racks,code',
            ],

            'no_iventaris' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kode_buku' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ddc' => [
                'nullable',
                'string',
                'max:255',
            ],

            'edition' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('subcategory_id')) {

            $validSubcategory = Subcategory::where(
                'id',
                $request->subcategory_id
            )
                ->where(
                    'category_id',
                    $request->category_id
                )
                ->exists();

            if (! $validSubcategory) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'subcategory_id' =>
                        'Subkategori tidak sesuai dengan kategori yang dipilih.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG BUKU YANG SEDANG DIPINJAM
        |--------------------------------------------------------------------------
        */

        $borrowed =
            $book->stock -
            $book->available_stock;


        /*
        |--------------------------------------------------------------------------
        | STOK BARU TIDAK BOLEH DI BAWAH JUMLAH DIPINJAM
        |--------------------------------------------------------------------------
        */

        if ($request->stock < $borrowed) {

            return back()
                ->withInput()
                ->withErrors([
                    'stock' =>
                    'Stok tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG AVAILABLE STOCK
        |--------------------------------------------------------------------------
        */

        $availableStock =
            $request->stock -
            $borrowed;


        /*
        |--------------------------------------------------------------------------
        | COVER
        |--------------------------------------------------------------------------
        */

        $cover = $book->cover;

        if ($request->hasFile('cover')) {

            $cover = $request
                ->file('cover')
                ->store('covers', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $book->update([

            'category_id' => $request->category_id,

            'subcategory_id' => $request->subcategory_id,

            'title' => $request->title,

            'author' => $request->author,

            'publisher' => $request->publisher,

            'publication_year' => $request->publication_year,

            'isbn' => $request->isbn,

            'call_number' => $request->call_number,

            'stock' => $request->stock,

            'available_stock' => $availableStock,

            'description' => $request->description,

            'cover' => $cover,

            'no_iventaris' => $request->no_iventaris,

            'kode_buku' => $request->kode_buku,

            'ddc' => $request->ddc,

            'rak' => $request->rak,

            'edition' => $request->edition,
        ]);


        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Buku berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Buku berhasil dihapus.'
            );
    }
}
