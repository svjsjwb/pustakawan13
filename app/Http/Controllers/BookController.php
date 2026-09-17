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
public function isbnLookup(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI & NORMALISASI ISBN
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'isbn' => [
                'required',
                'string',
                'regex:/^[0-9Xx -]{10,17}$/',
            ],
        ]);

        $isbn = strtoupper(
            preg_replace('/[^0-9Xx]/', '', $request->isbn)
        );

        if (!preg_match('/^(?:\d{10}|\d{13})$/', $isbn)) {
            return response()->json([
                'success' => false,
                'message' => 'Format ISBN tidak valid.',
            ], 422);
        }

        $apiKey = config('services.google_books.key');

        /*
        |--------------------------------------------------------------------------
        | HELPER RESPONSE
        |--------------------------------------------------------------------------
        */

        $makeResponse = function (
            ?string $title,
            ?string $author,
            ?string $publisher,
            ?int $publicationYear,
            ?string $ddc,
            ?string $edition,
            ?string $description,
            ?string $cover,
            ?string $sourceUrl,
            ?string $foundIsbn = null
        ) use ($isbn) {
            return response()->json([
                'success' => true,
                'data' => [
                    'isbn' => $foundIsbn ?: $isbn,
                    'title' => $title,
                    'author' => $author,
                    'publisher' => $publisher,
                    'publication_year' => $publicationYear,
                    'ddc' => $ddc,
                    'edition' => $edition,
                    'description' => $description,
                    'cover' => $cover,
                    'source_url' => $sourceUrl,
                ],
            ]);
        };

        /*
        |--------------------------------------------------------------------------
        | 1. GOOGLE BOOKS
        |--------------------------------------------------------------------------
        |
        | Coba beberapa bentuk pencarian karena ada ISBN yang:
        | - tidak ditemukan oleh q=isbn:...
        | - tetapi bisa ditemukan oleh pencarian ISBN biasa.
        |
        */

        if ($apiKey) {
            try {
                $isbnCandidates = [$isbn];

                if (
                    strlen($isbn) === 13 &&
                    (
                        str_starts_with($isbn, '978') ||
                        str_starts_with($isbn, '979')
                    )
                ) {
                    $isbn10 = $this->convertIsbn13ToIsbn10($isbn);

                    if ($isbn10) {
                        $isbnCandidates[] = $isbn10;
                    }
                }

                $isbnCandidates = array_values(array_unique($isbnCandidates));

                foreach ($isbnCandidates as $candidate) {
                    /*
                    |--------------------------------------------------------------------------
                    | SEARCH 1: ISBN FIELD
                    |--------------------------------------------------------------------------
                    */

                    $queries = [
                        'isbn:' . $candidate,
                        $candidate,
                    ];

                    foreach ($queries as $googleQuery) {
                        $response = Http::timeout(10)
                            ->acceptJson()
                            ->get(
                                'https://www.googleapis.com/books/v1/volumes',
                                [
                                    'q' => $googleQuery,
                                    'maxResults' => 10,
                                    'key' => $apiKey,
                                ]
                            );

                        if (!$response->successful()) {
                            \Log::warning(
                                'Google Books API response error',
                                [
                                    'isbn' => $candidate,
                                    'query' => $googleQuery,
                                    'status' => $response->status(),
                                ]
                            );

                            continue;
                        }

                        $items = $response->json('items', []);

                        if (empty($items)) {
                            continue;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | PILIH HASIL YANG ISBN-NYA COCOK
                        |--------------------------------------------------------------------------
                        |
                        | Untuk pencarian biasa, Google Books bisa mengembalikan
                        | buku yang tidak berkaitan. Jadi kita cek identifier dulu.
                        |
                        */

                        $matchedItem = null;

                        foreach ($items as $item) {
                            $identifiers = collect(
                                $item['volumeInfo']['industryIdentifiers'] ?? []
                            )->pluck('identifier')
                             ->map(fn ($value) => preg_replace('/[^0-9Xx]/', '', strtoupper($value)))
                             ->all();

                            if (
                                in_array($candidate, $identifiers, true) ||
                                in_array($isbn, $identifiers, true)
                            ) {
                                $matchedItem = $item;
                                break;
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | KALAU SEARCH ISBN FIELD MENEMUKAN HASIL,
                        | BOLEH GUNAKAN HASIL PERTAMA.
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !$matchedItem &&
                            str_starts_with($googleQuery, 'isbn:') &&
                            isset($items[0]['volumeInfo'])
                        ) {
                            $matchedItem = $items[0];
                        }

                        if (!$matchedItem || !isset($matchedItem['volumeInfo'])) {
                            continue;
                        }

                        $volume = $matchedItem['volumeInfo'];

                        /*
                        |--------------------------------------------------------------------------
                        | PENULIS
                        |--------------------------------------------------------------------------
                        */

                        $authors = collect($volume['authors'] ?? [])
                            ->filter()
                            ->implode(', ');

                        /*
                        |--------------------------------------------------------------------------
                        | PENERBIT
                        |--------------------------------------------------------------------------
                        */

                        $publisher = $volume['publisher'] ?? null;

                        /*
                        |--------------------------------------------------------------------------
                        | TAHUN TERBIT
                        |--------------------------------------------------------------------------
                        */

                        $publicationYear = null;

                        if (!empty($volume['publishedDate'])) {
                            if (
                                preg_match(
                                    '/\b(18|19|20)\d{2}\b/',
                                    $volume['publishedDate'],
                                    $matches
                                )
                            ) {
                                $publicationYear = (int) $matches[0];
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | ISBN HASIL API
                        |--------------------------------------------------------------------------
                        */

                        $foundIsbn = $isbn;

                        $identifiers = collect(
                            $volume['industryIdentifiers'] ?? []
                        );

                        $isbn13Data = $identifiers->firstWhere('type', 'ISBN_13');
                        $isbn10Data = $identifiers->firstWhere('type', 'ISBN_10');

                        if ($isbn13Data) {
                            $foundIsbn = preg_replace(
                                '/[^0-9Xx]/',
                                '',
                                strtoupper($isbn13Data['identifier'])
                            );
                        } elseif ($isbn10Data) {
                            $foundIsbn = preg_replace(
                                '/[^0-9Xx]/',
                                '',
                                strtoupper($isbn10Data['identifier'])
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | COVER
                        |--------------------------------------------------------------------------
                        */

                        $cover = null;

                        if (!empty($volume['imageLinks'])) {
                            $cover =
                                $volume['imageLinks']['thumbnail']
                                ?? $volume['imageLinks']['smallThumbnail']
                                ?? null;

                            if ($cover) {
                                $cover = str_replace(
                                    'http://',
                                    'https://',
                                    $cover
                                );
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | RETURN GOOGLE BOOKS
                        |--------------------------------------------------------------------------
                        */

                        return $makeResponse(
                            $volume['title'] ?? null,
                            $authors ?: null,
                            $publisher,
                            $publicationYear,
                            null,
                            null,
                            $volume['description'] ?? null,
                            $cover,
                            $matchedItem['selfLink'] ?? null,
                            $foundIsbn
                        );
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning(
                    'Google Books ISBN Lookup Failed',
                    [
                        'isbn' => $isbn,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. OPEN LIBRARY
        |--------------------------------------------------------------------------
        */

        try {
            $openLibrary = Http::timeout(10)
                ->acceptJson()
                ->withHeaders([
                    'User-Agent' => 'Pustaka13 Library Management System',
                ])
                ->get(
                    'https://openlibrary.org/api/books',
                    [
                        'bibkeys' => 'ISBN:' . $isbn,
                        'jscmd' => 'data',
                        'format' => 'json',
                    ]
                );

            if ($openLibrary->successful()) {
                $result = $openLibrary->json('ISBN:' . $isbn);

                if ($result) {
                    $title = $result['title'] ?? null;

                    $authors = collect($result['authors'] ?? [])
                        ->pluck('name')
                        ->filter()
                        ->implode(', ');

                    $publisher = collect($result['publishers'] ?? [])
                        ->pluck('name')
                        ->filter()
                        ->first();

                    $publicationYear = null;

                    if (!empty($result['publish_date'])) {
                        if (
                            preg_match(
                                '/\b(18|19|20)\d{2}\b/',
                                $result['publish_date'],
                                $matches
                            )
                        ) {
                            $publicationYear = (int) $matches[0];
                        }
                    }

                    $ddc = collect(
                        $result['classifications']['dewey_decimal_class'] ?? []
                    )
                        ->filter()
                        ->first();

                    $cover =
                        $result['cover']['medium']
                        ?? $result['cover']['large']
                        ?? $result['cover']['small']
                        ?? null;

                    return $makeResponse(
                        $title,
                        $authors ?: null,
                        $publisher ?: null,
                        $publicationYear,
                        $ddc ?: null,
                        null,
                        null,
                        $cover,
                        $result['url'] ?? null,
                        $isbn
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::warning(
                'Open Library ISBN Lookup Failed',
                [
                    'isbn' => $isbn,
                    'message' => $e->getMessage(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. TIDAK DITEMUKAN
        |--------------------------------------------------------------------------
        |
        | ISBN tetap dikembalikan supaya hasil scan tidak hilang.
        |
        */

        return response()->json([
            'success' => false,
            'message' =>
                'Data buku tidak ditemukan otomatis. ISBN sudah terisi, silakan lengkapi data buku secara manual.',
            'data' => [
                'isbn' => $isbn,
            ],
        ], 404);
    }

private function convertIsbn13ToIsbn10(
        string $isbn13
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | ISBN-10 hanya bisa dihitung dari ISBN-13
        | dengan prefix 978 atau 979.
        |--------------------------------------------------------------------------
        */

        if (
            strlen($isbn13) !== 13 ||
            !in_array(
                substr($isbn13, 0, 3),
                ['978', '979'],
                true
            )
        ) {

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL 9 DIGIT SETELAH PREFIX
        |--------------------------------------------------------------------------
        */

        $digits =
            substr(
                $isbn13,
                3,
                9
            );

        if (
            strlen($digits) !== 9 ||
            !ctype_digit($digits)
        ) {

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG CHECK DIGIT
        |--------------------------------------------------------------------------
        */

        $sum = 0;

        for (
            $i = 0;
            $i < 9;
            $i++
        ) {

            $sum +=
                ((int) $digits[$i])
                * (10 - $i);
        }

        $remainder =
            11 -
            ($sum % 11);

        if ($remainder === 10) {

            $checkDigit = 'X';

        } elseif ($remainder === 11) {

            $checkDigit = '0';

        } else {

            $checkDigit =
                (string) $remainder;
        }

        return
            $digits .
            $checkDigit;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'title' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:books,sku'],
            'author' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:1'],
            'rak' => ['required', 'exists:racks,code'],
            'no_iventaris' => ['nullable', 'string', 'max:255'],
            'kode_buku' => ['nullable', 'string', 'max:255'],
            'ddc' => ['nullable', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        DB::transaction(function () use (
            $request,
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
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'title' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('books', 'sku')->ignore($book->id)],
            'author' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:1'],
            'rak' => ['required', 'exists:racks,code'],
            'no_iventaris' => ['nullable', 'string', 'max:255'],
            'kode_buku' => ['nullable', 'string', 'max:255'],
            'ddc' => ['nullable', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        DB::transaction(function () use (
            $request,
            $book,
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
                'sku' => $request->sku,
                'no_iventaris' => $request->no_iventaris,
                'kode_buku' => $request->kode_buku,
                'ddc' => $request->ddc ?: $request->input('call_number'),
                'rak' => $request->rak,
                'edition' => $request->edition,
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
