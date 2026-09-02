<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Subcategory;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view(
            'books.index',
            compact('books')
        );
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
                    $category->id =>
                        $category->subcategories
                            ->map(function ($subcategory) {

                                return [
                                    'id' =>
                                        $subcategory->id,

                                    'name' =>
                                        $subcategory->name,
                                ];
                            })
                            ->values()
                            ->toArray(),
                ];
            })
            ->toArray();

        $racks = Rack::orderBy('code')
            ->get();

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
        */

        if ($request->filled('subcategory_id')) {

            $validSubcategory =
                Subcategory::where(
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

            $cover =
                $request
                    ->file('cover')
                    ->store(
                        'covers',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN BUKU + BOOK COPY
        |--------------------------------------------------------------------------
        |
        | Semua proses dibuat dalam satu transaction.
        |
        | Contoh:
        |
        | stock = 3
        |
        | maka:
        |
        | books
        |   stock = 3
        |   available_stock = 3
        |
        | book_copies
        |   copy 1 = available
        |   copy 2 = available
        |   copy 3 = available
        |
        */

        DB::transaction(function () use (
            $request,
            $cover
        ) {

            $book = Book::create([

                'category_id' =>
                    $request->category_id,

                'subcategory_id' =>
                    $request->subcategory_id,

                'title' =>
                    $request->title,

                'sku' =>
                    $request->sku,

                'author' =>
                    $request->author,

                'publisher' =>
                    $request->publisher,

                'publication_year' =>
                    $request->publication_year,

                'isbn' =>
                    $request->isbn,

                'call_number' =>
                    $request->call_number,

                'stock' =>
                    $request->stock,

                'available_stock' =>
                    $request->stock,

                'description' =>
                    $request->description,

                'cover' =>
                    $cover,

                'no_iventaris' =>
                    $request->no_iventaris,

                'kode_buku' =>
                    $request->kode_buku,

                'ddc' =>
                    $request->ddc,

                'rak' =>
                    $request->rak,

                'edition' =>
                    $request->edition,
            ]);


            /*
            |--------------------------------------------------------------------------
            | BUAT BOOK COPY OTOMATIS
            |--------------------------------------------------------------------------
            |
            | Satu stock = satu eksemplar fisik.
            |
            | Lokasi belum diisi karena lokasi fisik
            | harus ditentukan melalui menu Kelola Eksemplar.
            |
            */

            for (
                $i = 0;
                $i < $request->stock;
                $i++
            ) {

                BookCopy::create([

                    'book_id' =>
                        $book->id,

                    'barcode' =>
                        null,

                    'status' =>
                        'available',

                    'shelf_id' =>
                        null,

                    'section' =>
                        1,

                    'side' =>
                        'front',

                    'row' =>
                        null,

                    'column' =>
                        null,
                ]);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

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

        $racks = Rack::orderBy('code')
            ->get();

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

            $validSubcategory =
                Subcategory::where(
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

            $cover =
                $request
                    ->file('cover')
                    ->store(
                        'covers',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE BUKU + SINKRONISASI BOOK COPY
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $book,
            $availableStock
        ) {

            /*
            |--------------------------------------------------------------------------
            | UPDATE DATA BUKU
            |--------------------------------------------------------------------------
            */

            $book->update([

                'category_id' =>
                    $request->category_id,

                'subcategory_id' =>
                    $request->subcategory_id,

                'title' =>
                    $request->title,

                'author' =>
                    $request->author,

                'publisher' =>
                    $request->publisher,

                'publication_year' =>
                    $request->publication_year,

                'isbn' =>
                    $request->isbn,

                'call_number' =>
                    $request->call_number,

                'stock' =>
                    $request->stock,

                'available_stock' =>
                    $availableStock,

                'description' =>
                    $request->description,

                'cover' =>
                    $request->hasFile('cover')
                        ? $request
                            ->file('cover')
                            ->store(
                                'covers',
                                'public'
                            )
                        : $book->cover,

                'no_iventaris' =>
                    $request->no_iventaris,

                'kode_buku' =>
                    $request->kode_buku,

                'ddc' =>
                    $request->ddc,

                'rak' =>
                    $request->rak,

                'edition' =>
                    $request->edition,
            ]);


            /*
            |--------------------------------------------------------------------------
            | JUMLAH BOOK COPY SAAT INI
            |--------------------------------------------------------------------------
            */

            $copyCount =
                BookCopy::where(
                    'book_id',
                    $book->id
                )->count();


            /*
            |--------------------------------------------------------------------------
            | JIKA STOCK BERTAMBAH
            |--------------------------------------------------------------------------
            */

            if (
                $request->stock >
                $copyCount
            ) {

                $difference =
                    $request->stock -
                    $copyCount;


                for (
                    $i = 0;
                    $i < $difference;
                    $i++
                ) {

                    BookCopy::create([

                        'book_id' =>
                            $book->id,

                        'barcode' =>
                            null,

                        'status' =>
                            'available',

                        'shelf_id' =>
                            null,

                        'section' =>
                            1,

                        'side' =>
                            'front',

                        'row' =>
                            null,

                        'column' =>
                            null,
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | JIKA STOCK BERKURANG
            |--------------------------------------------------------------------------
            |
            | Jangan hapus copy yang sedang:
            |
            | borrowed
            | reserved
            | lost
            | damaged
            | maintenance
            |
            | Hanya hapus copy AVAILABLE tanpa lokasi
            | jika memang jumlah copy melebihi stock.
            |
            */

            elseif (
                $request->stock <
                $copyCount
            ) {

                $difference =
                    $copyCount -
                    $request->stock;


                $copiesToDelete =
                    BookCopy::where(
                        'book_id',
                        $book->id
                    )
                    ->where(
                        'status',
                        'available'
                    )
                    ->whereNull(
                        'shelf_id'
                    )
                    ->latest('id')
                    ->take($difference)
                    ->get();


                foreach (
                    $copiesToDelete
                    as $copy
                ) {

                    $copy->delete();
                }
            }
        });


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

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