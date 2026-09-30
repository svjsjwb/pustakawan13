<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Subcategory;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\BookMetadataService;
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
        $categoryOrder = ['Pendidikan', 'Anak', 'Remaja', 'Dewasa'];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(fn($category) => array_search($category->name, $categoryOrder, true))
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
    public function isbnLookup(Request $request, BookMetadataService $metadataService)
    {
        $request->validate([
            'isbn' => ['required', 'string', 'max:30'],
        ]);

        $isbn = $metadataService->normalizeIsbn($request->string('isbn')->toString());

        if (!$isbn) {
            return response()->json([
                'success' => false,
                'message' => 'Format ISBN tidak valid. Masukkan ISBN-10 atau ISBN-13.',
            ], 422);
        }

        $metadata = $metadataService->findByIsbn($isbn);

        if (!$metadata) {
            return response()->json([
                'success' => false,
                'message' => 'Data buku dengan ISBN tersebut tidak ditemukan. ISBN tetap dapat digunakan untuk pengisian manual.',
                'data' => ['isbn' => $isbn],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Metadata buku berhasil ditemukan dari ' . $metadata['source'] . '.',
            'data' => $metadata,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('isbn')) {
            $request->merge(['isbn' => preg_replace('/[^0-9Xx]/', '', strtoupper($request->input('isbn')))]);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'title' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:books,sku'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:1'],
            'rak' => ['required', 'exists:racks,code'],
            'no_iventaris' => ['nullable', 'string', 'max:255'],
            'kode_buku' => ['nullable', 'string', 'max:255'],
            'ddc' => ['nullable', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->filled('subcategory_id')) {
            $validSubcategory = Subcategory::where('id', $request->subcategory_id)
                ->where('category_id', $request->category_id)
                ->exists();

            if (! $validSubcategory) {
                return back()->withInput()->withErrors([
                    'subcategory_id' => 'Subkategori tidak sesuai dengan kategori yang dipilih.',
                ]);
            }
        }

        $category = Category::findOrFail($request->category_id);
        $subcategory = $request->filled('subcategory_id')
            ? Subcategory::find($request->subcategory_id)
            : null;

        $mainCategory = $category->name;
        $subCategoryName = $subcategory?->name;
        $educationLevel = $mainCategory === 'Pendidikan' ? $subCategoryName : null;

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
        } elseif ($request->filled('cover_url')) {
            $coverPath = $this->downloadRemoteCover($request->string('cover_url')->toString(), $request->input('isbn'));
        }

        DB::transaction(function () use (
            $request,
            $coverPath,
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
                'penulis' => $request->author,
                'isbn' => $request->filled('isbn') ? preg_replace('/[^0-9Xx]/', '', strtoupper($request->isbn)) : null,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'description' => $request->description,
                'cover' => $coverPath,
                'sku' => $request->sku,
                'stok' => $request->stock,
                'status' => 'Tersedia',
                'no_iventaris' => $request->no_iventaris,
                'kode_buku' => $request->kode_buku,
                'ddc' => $request->ddc ?: $request->input('call_number'),
                'rak' => $request->rak,
                'edition' => $request->edition,
            ]);

            // Rak admin (A1) dipetakan ke shelf fisik (A-01).
            if (preg_match('/^([A-Za-z]+)(\d+)$/', $request->rak, $matches)) {
                $shelfCode = strtoupper($matches[1]) . '-' . str_pad(
                    $matches[2],
                    2,
                    '0',
                    STR_PAD_LEFT
                );
            } else {
                throw new \Exception('Format kode rak tidak valid.');
            }

            $shelf = Shelf::where('code', $shelfCode)->first();

            if (! $shelf) {
                throw new \Exception(
                    'Rak ' . $request->rak . ' belum memiliki lokasi shelf.'
                );
            }

            for ($i = 0; $i < $request->stock; $i++) {
                $position = null;

                foreach ([1, 2] as $section) {
                    foreach (['front', 'back'] as $side) {
                        foreach (range(1, 3) as $row) {
                            foreach (range(1, 30) as $column) {
                                $exists = BookCopy::where('shelf_id', $shelf->id)
                                    ->where('section', $section)
                                    ->where('side', $side)
                                    ->where('row', $row)
                                    ->where('column', $column)
                                    ->exists();

                                if (! $exists) {
                                    $position = compact('section', 'side', 'row', 'column');
                                    break 4;
                                }
                            }
                        }
                    }
                }

                if (! $position) {
                    throw new \Exception('Rak ' . $request->rak . ' sudah penuh.');
                }

                BookCopy::create([
                    'book_id' => $book->id,
                    'barcode' => null,
                    'status' => 'available',
                    'shelf_id' => $shelf->id,
                    'section' => $position['section'],
                    'side' => $position['side'],
                    'row' => $position['row'],
                    'column' => $position['column'],
                ]);
            }
        });

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }



    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Book $book)
    {
        $categoryOrder = ['Pendidikan', 'Anak', 'Remaja', 'Dewasa'];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(fn($category) => array_search($category->name, $categoryOrder, true))
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
    public function update(Request $request, Book $book)
    {
        if ($request->filled('isbn')) {
            $request->merge(['isbn' => preg_replace('/[^0-9Xx]/', '', strtoupper($request->input('isbn')))]);
        }

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book->id)],
            'title' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('books', 'sku')->ignore($book->id)],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:1'],
            'rak' => ['required', 'exists:racks,code'],
            'no_iventaris' => ['nullable', 'string', 'max:255'],
            'kode_buku' => ['nullable', 'string', 'max:255'],
            'ddc' => ['nullable', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->filled('subcategory_id')) {
            $validSubcategory = Subcategory::where('id', $request->subcategory_id)
                ->where('category_id', $request->category_id)
                ->exists();

            if (! $validSubcategory) {
                return back()->withInput()->withErrors([
                    'subcategory_id' => 'Subkategori tidak sesuai dengan kategori yang dipilih.',
                ]);
            }
        }

        /*
         * Stok di tabel books = jumlah copy yang saat ini AVAILABLE.
         * Jumlah total fisik tetap direpresentasikan oleh book_copies.
         */
        $copyQuery = BookCopy::where('book_id', $book->id);
        $copyCount = (clone $copyQuery)->count();

        $occupiedCount = (clone $copyQuery)
            ->whereIn('status', [
                'reserved',
                'borrowed',
                'lost',
                'damaged',
                'maintenance',
            ])
            ->count();

        if ($request->stock < $occupiedCount) {
            return back()->withInput()->withErrors([
                'stock' => 'Stok tidak boleh lebih kecil dari jumlah eksemplar yang sedang dipinjam/dipesan atau tidak tersedia.',
            ]);
        }

        $category = Category::findOrFail($request->category_id);
        $subcategory = $request->filled('subcategory_id')
            ? Subcategory::find($request->subcategory_id)
            : null;

        $mainCategory = $category->name;
        $subCategoryName = $subcategory?->name;
        $educationLevel = $mainCategory === 'Pendidikan' ? $subCategoryName : null;

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
        } elseif ($request->filled('cover_url')) {
            $coverPath = $this->downloadRemoteCover($request->string('cover_url')->toString(), $request->input('isbn'));
        }

        DB::transaction(function () use (
            $request,
            $book,
            $coverPath,
            $copyCount,
            $occupiedCount,
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
                'penulis' => $request->author,
                'isbn' => $request->filled('isbn') ? preg_replace('/[^0-9Xx]/', '', strtoupper($request->isbn)) : null,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'description' => $request->description,
                'sku' => $request->sku,
                'no_iventaris' => $request->no_iventaris,
                'kode_buku' => $request->kode_buku,
                'ddc' => $request->ddc ?: $request->input('call_number'),
                'rak' => $request->rak,
                'edition' => $request->edition,
                ...($coverPath ? ['cover' => $coverPath] : []),
            ]);

            if ($request->stock > $copyCount) {
                $difference = $request->stock - $copyCount;

                if (preg_match('/^([A-Za-z]+)(\d+)$/', $request->rak, $matches)) {
                    $shelfCode = strtoupper($matches[1]) . '-' . str_pad(
                        $matches[2],
                        2,
                        '0',
                        STR_PAD_LEFT
                    );
                } else {
                    throw new \Exception('Format kode rak tidak valid.');
                }

                $shelf = Shelf::where('code', $shelfCode)->first();

                if (! $shelf) {
                    throw new \Exception(
                        'Rak ' . $request->rak . ' belum memiliki lokasi shelf.'
                    );
                }

                for ($i = 0; $i < $difference; $i++) {
                    $position = null;

                    foreach ([1, 2] as $section) {
                        foreach (['front', 'back'] as $side) {
                            foreach (range(1, 3) as $row) {
                                foreach (range(1, 30) as $column) {
                                    $exists = BookCopy::where('shelf_id', $shelf->id)
                                        ->where('section', $section)
                                        ->where('side', $side)
                                        ->where('row', $row)
                                        ->where('column', $column)
                                        ->exists();

                                    if (! $exists) {
                                        $position = compact('section', 'side', 'row', 'column');
                                        break 4;
                                    }
                                }
                            }
                        }
                    }

                    if (! $position) {
                        throw new \Exception('Rak ' . $request->rak . ' sudah penuh.');
                    }

                    BookCopy::create([
                        'book_id' => $book->id,
                        'barcode' => null,
                        'status' => 'available',
                        'shelf_id' => $shelf->id,
                        'section' => $position['section'],
                        'side' => $position['side'],
                        'row' => $position['row'],
                        'column' => $position['column'],
                    ]);
                }
            } elseif ($request->stock < $copyCount) {
                $difference = $copyCount - $request->stock;

                $copiesToDelete = BookCopy::where('book_id', $book->id)
                    ->where('status', 'available')
                    ->latest('id')
                    ->take($difference)
                    ->get();

                if ($copiesToDelete->count() < $difference) {
                    throw new \Exception(
                        'Jumlah copy yang dapat dihapus tidak mencukupi karena sebagian eksemplar sedang tidak tersedia.'
                    );
                }

                foreach ($copiesToDelete as $copy) {
                    $copy->delete();
                }
            }

            $availableCount = BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->count();

            $book->update([
                'stok' => $availableCount,
                'status' => $availableCount > 0 ? 'Tersedia' : 'Dipinjam',
            ]);
        });

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }



    private function downloadRemoteCover(string $url, ?string $isbn): ?string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = [
            'books.google.com',
            'books.googleusercontent.com',
            'googleusercontent.com',
            'covers.openlibrary.org',
        ];

        $allowed = false;
        foreach ($allowedHosts as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get($url);
            if (!$response->successful() || !$response->body()) {
                return null;
            }

            $contentType = strtolower($response->header('Content-Type', ''));
            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            $filename = 'covers/' . ($isbn ?: uniqid('book_', true)) . '.' . $extension;
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Book $book)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($book, $validated) {
            \App\Models\CollectionWithdrawal::create([
                'type' => 'book',
                'book_id' => $book->id,
                'book_copy_id' => null,
                'book_title' => $book->judul_buku,
                'barcode' => null,
                'quantity' => $book->copies()->count(),
                'reason' => $validated['reason'],
                'withdrawn_at' => now(),
            ]);

            // Schema baru tidak memiliki deleted_at/SoftDeletes.
            // Histori penarikan disimpan sebelum hard delete.
            $book->delete();
        });

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditarik dari koleksi.');
    }
}