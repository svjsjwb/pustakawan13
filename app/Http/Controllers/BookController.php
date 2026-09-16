<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $categoryOrder = ['Pendidikan', 'Anak-Anak', 'Remaja', 'Dewasa'];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, $categoryOrder, true))
            ->values();

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
        */

        if ($request->filled('subcategory_id')) {
            $validSubcategory = Subcategory::where('id', $request->subcategory_id)
                ->where('category_id', $request->category_id)
                ->exists();

            if (! $validSubcategory) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subcategory_id' => 'Subkategori tidak sesuai dengan kategori yang dipilih.',
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
            $cover = $request->file('cover')->store('covers', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN BUKU + BOOK COPY
        |--------------------------------------------------------------------------
        */

        $category = Category::find($request->category_id);
        $subcategory = $request->filled('subcategory_id') ? Subcategory::find($request->subcategory_id) : null;
        $mainCategory = $category?->name;
        $subCategoryName = $subcategory?->name;
        $educationLevel = ($mainCategory === 'Pendidikan') ? $subCategoryName : null;

        DB::transaction(function () use (
            $request,
            $cover,
            $mainCategory,
            $subCategoryName,
            $educationLevel
        ) {
            $book = Book::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'main_category' => $mainCategory,
                'sub_category' => $subCategoryName,
                'education_level' => $educationLevel,
                'judul_buku' => $request->title,
                'title' => $request->title,
                'penulis' => $request->author,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'isbn' => $request->isbn,
                'call_number' => $request->call_number,
                'stock' => $request->stock,
                'available_stock' => $request->stock,
                'stok' => $request->stock,
                'status' => 'Tersedia',
                'sku' => $request->sku,
                'no_iventaris' => $request->no_iventaris,
                'kode_buku' => $request->kode_buku,
                'ddc' => $request->ddc ?: $request->call_number,
                'rak' => $request->rak,
                'edition' => $request->edition,
                'description' => $request->description,
                'cover' => $cover,
            ]);

            /*
            |--------------------------------------------------------------------------
            | BUAT BOOK COPY OTOMATIS
            |--------------------------------------------------------------------------
            */

            for ($i = 0; $i < $request->stock; $i++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'barcode' => null,
                    'status' => 'available',
                    'shelf_id' => null,
                    'section' => 1,
                    'side' => 'front',
                    'row' => null,
                    'column' => null,
                ]);
            }
        });

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
        $categoryOrder = ['Pendidikan', 'Anak-Anak', 'Remaja', 'Dewasa'];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, $categoryOrder, true))
            ->values();

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
            'books.edit',
            compact(
                'book',
                'categories',
                'subcategoryData',
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
                'string',
                Rule::unique('books', 'isbn')->ignore($book->id),
            ],

            'call_number' => [
                'required',
                'string',
                Rule::unique('books', 'call_number')->ignore($book->id),
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
            $validSubcategory = Subcategory::where('id', $request->subcategory_id)
                ->where('category_id', $request->category_id)
                ->exists();

            if (! $validSubcategory) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subcategory_id' => 'Subkategori tidak sesuai dengan kategori yang dipilih.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG BUKU YANG SEDANG DIPINJAM
        |--------------------------------------------------------------------------
        */

        $borrowed = $book->stock - $book->available_stock;

        if ($request->stock < $borrowed) {
            return back()
                ->withInput()
                ->withErrors([
                    'stock' => 'Stok tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam.',
                ]);
        }

        $availableStock = $request->stock - $borrowed;

        $cover = $book->cover;
        if ($request->hasFile('cover')) {
            $cover = $request->file('cover')->store('covers', 'public');
        }

        $category = Category::find($request->category_id);
        $subcategory = $request->filled('subcategory_id') ? Subcategory::find($request->subcategory_id) : null;
        $mainCategory = $category?->name;
        $subCategoryName = $subcategory?->name;
        $educationLevel = ($mainCategory === 'Pendidikan') ? $subCategoryName : null;

        /*
        |--------------------------------------------------------------------------
        | UPDATE BUKU + SINKRONISASI BOOK COPY
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $book,
            $availableStock,
            $cover,
            $mainCategory,
            $subCategoryName,
            $educationLevel
        ) {
            $book->update([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'main_category' => $mainCategory,
                'sub_category' => $subCategoryName,
                'education_level' => $educationLevel,
                'judul_buku' => $request->title,
                'title' => $request->title,
                'penulis' => $request->author,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'isbn' => $request->isbn,
                'call_number' => $request->call_number,
                'stock' => $request->stock,
                'available_stock' => $availableStock,
                'stok' => $availableStock,
                'status' => $availableStock > 0 ? 'Tersedia' : 'Dipinjam',
                'no_iventaris' => $request->no_iventaris,
                'kode_buku' => $request->kode_buku,
                'ddc' => $request->ddc ?: $request->call_number,
                'rak' => $request->rak,
                'edition' => $request->edition,
                'description' => $request->description,
                'cover' => $cover,
            ]);

            /*
            |--------------------------------------------------------------------------
            | JUMLAH BOOK COPY SAAT INI
            |--------------------------------------------------------------------------
            */

            $copyCount = BookCopy::where('book_id', $book->id)->count();

            if ($request->stock > $copyCount) {
                $difference = $request->stock - $copyCount;

                for ($i = 0; $i < $difference; $i++) {
                    BookCopy::create([
                        'book_id' => $book->id,
                        'barcode' => null,
                        'status' => 'available',
                        'shelf_id' => null,
                        'section' => 1,
                        'side' => 'front',
                        'row' => null,
                        'column' => null,
                    ]);
                }
            } elseif ($request->stock < $copyCount) {
                $difference = $copyCount - $request->stock;

                $copiesToDelete = BookCopy::where('book_id', $book->id)
                    ->where('status', 'available')
                    ->whereNull('shelf_id')
                    ->latest('id')
                    ->take($difference)
                    ->get();

                foreach ($copiesToDelete as $copy) {
                    $copy->delete();
                }
            }
        });

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
