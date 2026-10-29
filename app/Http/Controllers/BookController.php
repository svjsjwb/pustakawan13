<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\CollectionWithdrawal;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Shelf;
use App\Models\LibraryFloor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\BookMetadataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * ============================================================
     * GENERATE NEXT COPY NUMBER
     * ============================================================
     *
     * Copy tidak boleh menggunakan kembali nomor yang pernah ada.
     *
     * Contoh:
     * c.1
     * c.2
     * c.3
     *
     * Jika c.3 pernah ditarik dari koleksi,
     * copy berikutnya tetap c.4.
     */
    private function generateNextCopyNumber(Book $book): string
    {
        $activeLastNumber = $book->copies()
            ->where('copy', 'like', 'c.%')
            ->get()
            ->map(function (BookCopy $copy) {
                return (int) str_replace('c.', '', $copy->copy);
            })
            ->max() ?? 0;

        $withdrawnLastNumber = CollectionWithdrawal::query()
            ->where('book_id', $book->id)
            ->whereNotNull('copy')
            ->where('copy', 'like', 'c.%')
            ->get()
            ->map(function (CollectionWithdrawal $withdrawal) {
                return (int) str_replace('c.', '', $withdrawal->copy);
            })
            ->max() ?? 0;

        return 'c.' . (
            max(
                $activeLastNumber,
                $withdrawnLastNumber
            ) + 1
        );
    }


    /**
     * ============================================================
     * FIND AUTOMATIC BOOK COPY POSITION
     * ============================================================
     *
     * Struktur posisi:
     *
     * Section 1 / 2
     * Side front / back
     * Row 1 - 3
     * Column 1 - 30
     */
    private function findBookCopyPosition(int $shelfId): ?array
    {
        foreach ([1, 2] as $section) {

            foreach (['front', 'back'] as $side) {

                foreach (range(1, 3) as $row) {

                    foreach (range(1, 30) as $column) {

                        $exists = BookCopy::query()
                            ->where('shelf_id', $shelfId)
                            ->where('section', $section)
                            ->where('side', $side)
                            ->where('row', $row)
                            ->where('column', $column)
                            ->exists();

                        if (! $exists) {

                            return [
                                'section' => $section,
                                'side' => $side,
                                'row' => $row,
                                'column' => $column,
                            ];
                        }
                    }
                }
            }
        }

        return null;
    }


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
        $categoryOrder = [
            'Buku Pelajaran',
            'Buku Umum',
        ];

        $categories = Category::with('subcategories')
            ->whereIn('name', $categoryOrder)
            ->get()
            ->sortBy(
                fn($category) => array_search(
                    $category->name,
                    $categoryOrder,
                    true
                )
            )
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


        /*
         * =========================================================
         * LOKASI BUKU
         * Lantai → Zona → Rak
         * =========================================================
         */
        $floors = LibraryFloor::with([
            'zones.shelves'
        ])
            ->orderBy('floor_number')
            ->get();


        return view(
            'books.create',
            compact(
                'categories',
                'subcategoryData',
                'floors'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ISBN LOOKUP
    |--------------------------------------------------------------------------
    */

    public function isbnLookup(
        Request $request,
        BookMetadataService $metadataService
    ) {
        $request->validate([
            'isbn' => [
                'required',
                'string',
                'max:30',
            ],
        ]);


        $isbn = $metadataService->normalizeIsbn(
            $request
                ->string('isbn')
                ->toString()
        );


        if (! $isbn) {

            return response()->json([
                'success' => false,

                'message' =>
                'Format ISBN tidak valid. Masukkan ISBN-10 atau ISBN-13.',
            ], 422);
        }


        $metadata = $metadataService->findByIsbn($isbn);


        if (! $metadata) {

            return response()->json([
                'success' => false,

                'message' =>
                'Data buku dengan ISBN tersebut tidak ditemukan. ISBN tetap dapat digunakan untuk pengisian manual.',

                'data' => [
                    'isbn' => $isbn,
                ],
            ], 404);
        }


        return response()->json([
            'success' => true,

            'message' =>
            'Metadata buku berhasil ditemukan dari ' .
                $metadata['source'] .
                '.',

            'data' => $metadata,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
         * =========================================================
         * NORMALISASI ISBN
         * =========================================================
         */

        if ($request->filled('isbn')) {

            $request->merge([
                'isbn' => preg_replace(
                    '/[^0-9Xx]/',
                    '',
                    strtoupper(
                        $request->input('isbn')
                    )
                ),
            ]);
        }


        /*
         * =========================================================
         * VALIDASI
         * =========================================================
         */

        $validated = $request->validate([

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
            ],

            'isbn' => [
                'nullable',
                'string',
                'max:20',
                'unique:books,isbn',
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
                'nullable',
                'string',
                'max:255',
            ],

            'publication_year' => [
                'nullable',
                'integer',
                'min:1000',
                'max:' . (date('Y') + 1),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'stock' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
             * Lokasi fisik langsung menggunakan Shelf.
             */
            'shelf_id' => [
                'required',
                'exists:shelves,id',
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

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ]);


        /*
         * =========================================================
         * VALIDASI SUBKATEGORI
         * =========================================================
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
         * =========================================================
         * CATEGORY
         * =========================================================
         */

        $category = Category::findOrFail(
            $request->category_id
        );


        $subcategory = $request->filled('subcategory_id')
            ? Subcategory::find(
                $request->subcategory_id
            )
            : null;


        $mainCategory = $category->name;

        $subCategoryName = $subcategory?->name;

        $educationLevel =
            $mainCategory === 'Buku Pelajaran'
            ? $subCategoryName
            : null;


        /*
         * =========================================================
         * COVER
         * =========================================================
         */

        $coverPath = null;


        if ($request->hasFile('cover')) {

            $coverPath = $request
                ->file('cover')
                ->store(
                    'covers',
                    'public'
                );
        } elseif ($request->filled('cover_url')) {

            $coverPath = $this->downloadRemoteCover(
                $request
                    ->string('cover_url')
                    ->toString(),

                $request->input('isbn')
            );
        }


        /*
         * =========================================================
         * CREATE BOOK + BOOK COPIES
         * =========================================================
         */

        DB::transaction(
            function () use (
                $request,
                $coverPath,
                $mainCategory,
                $subCategoryName,
                $educationLevel
            ) {

                /*
                 * -------------------------------------------------
                 * CREATE BOOK
                 * -------------------------------------------------
                 */

                $book = Book::create([

                    'category_id' =>
                    $request->category_id,

                    'subcategory_id' =>
                    $request->subcategory_id,

                    'main_category' =>
                    $mainCategory,

                    'sub_category' =>
                    $subCategoryName,

                    'education_level' =>
                    $educationLevel,

                    'judul_buku' =>
                    $request->title,

                    'penulis' =>
                    $request->author,

                    'isbn' =>
                    $request->filled('isbn')
                        ? preg_replace(
                            '/[^0-9Xx]/',
                            '',
                            strtoupper(
                                $request->isbn
                            )
                        )
                        : null,

                    'publisher' =>
                    $request->publisher,

                    'publication_year' =>
                    $request->publication_year,

                    'description' =>
                    $request->description,

                    'cover' =>
                    $coverPath,

                    'sku' =>
                    $request->sku,

                    'stok' => 0,

                    'status' =>
                    'Tersedia',

                    'no_iventaris' =>
                    $request->no_iventaris,

                    'kode_buku' =>
                    $request->kode_buku,

                    'ddc' =>
                    $request->ddc,

                    'edition' =>
                    $request->edition,
                ]);


                /*
                 * -------------------------------------------------
                 * AMBIL SHELF
                 * -------------------------------------------------
                 */

                $shelf = Shelf::findOrFail(
                    $request->shelf_id
                );


                /*
                 * -------------------------------------------------
                 * BUAT BOOK COPY
                 * -------------------------------------------------
                 */

                for (
                    $i = 0;
                    $i < $request->stock;
                    $i++
                ) {

                    $position =
                        $this->findBookCopyPosition(
                            (int) $shelf->id
                        );


                    if (! $position) {

                        throw new \Exception(
                            'Rak ' .
                                $shelf->name .
                                ' sudah penuh.'
                        );
                    }


                    BookCopy::create([

                        'book_id' =>
                        $book->id,

                        'copy' =>
                        $this->generateNextCopyNumber(
                            $book
                        ),

                        'status' =>
                        'available',

                        'condition' =>
                        'baik',

                        'shelf_id' =>
                        $shelf->id,

                        'section' =>
                        $position['section'],

                        'side' =>
                        $position['side'],

                        'row' =>
                        $position['row'],

                        'column' =>
                        $position['column'],
                    ]);
                }


                /*
                 * -------------------------------------------------
                 * SYNC STOCK
                 * -------------------------------------------------
                 */

                $availableCount =
                    BookCopy::where(
                        'book_id',
                        $book->id
                    )
                    ->where(
                        'status',
                        'available'
                    )
                    ->count();


                $book->update([

                    'stok' =>
                    $availableCount,

                    'status' =>
                    $availableCount > 0
                        ? 'Tersedia'
                        : 'Dipinjam',
                ]);
            }
        );


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
        $categoryOrder = [
            'Buku Pelajaran',
            'Buku Umum',
        ];


        $categories = Category::with(
            'subcategories'
        )
            ->whereIn(
                'name',
                $categoryOrder
            )
            ->get()
            ->sortBy(
                fn($category) => array_search(
                    $category->name,
                    $categoryOrder,
                    true
                )
            )
            ->values();


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


        /*
         * =========================================================
         * LOKASI BUKU
         * Lantai → Zona → Rak
         * =========================================================
         */

        $floors = LibraryFloor::with([
            'zones.shelves'
        ])
            ->orderBy('floor_number')
            ->get();


        return view(
            'books.edit',
            compact(
                'book',
                'categories',
                'subcategoryData',
                'floors'
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

        /*
         * =========================================================
         * NORMALISASI ISBN
         * =========================================================
         */

        if ($request->filled('isbn')) {

            $request->merge([
                'isbn' => preg_replace(
                    '/[^0-9Xx]/',
                    '',
                    strtoupper(
                        $request->input('isbn')
                    )
                ),
            ]);
        }


        /*
         * =========================================================
         * VALIDASI
         * =========================================================
         */

        $request->validate([

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
            ],

            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique(
                    'books',
                    'isbn'
                )->ignore($book->id),
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
                Rule::unique(
                    'books',
                    'sku'
                )->ignore($book->id),
            ],

            'author' => [
                'required',
                'string',
                'max:255',
            ],

            'publisher' => [
                'nullable',
                'string',
                'max:255',
            ],

            'publication_year' => [
                'nullable',
                'integer',
                'min:1000',
                'max:' . (date('Y') + 1),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'stock' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
             * Rak adalah rak utama buku.
             * Jika rak diganti, seluruh eksemplar buku dipindahkan.
             */
            'shelf_id' => [
                'required',
                'exists:shelves,id',
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

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ]);


        /*
         * =========================================================
         * VALIDASI SUBKATEGORI
         * =========================================================
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
         * =========================================================
         * HITUNG STOK AVAILABLE SAAT INI
         * =========================================================
         *
         * books.stok = jumlah copy AVAILABLE.
         *
         * BookCopy tetap menjadi sumber data fisik.
         */

        $copyQuery = BookCopy::where(
            'book_id',
            $book->id
        );


        $availableCountBeforeUpdate =
            (clone $copyQuery)
            ->where(
                'status',
                'available'
            )
            ->count();


        /*
         * Rak aktif buku saat ini.
         * Lokasi fisik tetap bersumber dari BookCopy.
         */
        $currentShelfId =
            $book->copies()
            ->whereNotNull('shelf_id')
            ->orderBy('id')
            ->value('shelf_id');


        /*
         * =========================================================
         * CATEGORY
         * =========================================================
         */

        $category = Category::findOrFail(
            $request->category_id
        );


        $subcategory =
            $request->filled('subcategory_id')
            ? Subcategory::find(
                $request->subcategory_id
            )
            : null;


        $mainCategory =
            $category->name;


        $subCategoryName =
            $subcategory?->name;


        $educationLevel =
            $mainCategory === 'Buku Pelajaran'
            ? $subCategoryName
            : null;


        /*
         * =========================================================
         * COVER
         * =========================================================
         */

        $coverPath = null;


        if ($request->hasFile('cover')) {

            $coverPath = $request
                ->file('cover')
                ->store(
                    'covers',
                    'public'
                );
        } elseif ($request->filled('cover_url')) {

            $coverPath =
                $this->downloadRemoteCover(
                    $request
                        ->string('cover_url')
                        ->toString(),

                    $request->input('isbn')
                );
        }


        /*
         * =========================================================
         * UPDATE BOOK + STOCK
         * =========================================================
         */

        DB::transaction(
            function () use (
                $request,
                $book,
                $coverPath,
                $availableCountBeforeUpdate,
                $currentShelfId,
                $mainCategory,
                $subCategoryName,
                $educationLevel
            ) {

                /*
                 * -------------------------------------------------
                 * UPDATE INFORMASI BUKU
                 * -------------------------------------------------
                 */

                $book->update([

                    'category_id' =>
                    $request->category_id,

                    'subcategory_id' =>
                    $request->subcategory_id,

                    'main_category' =>
                    $mainCategory,

                    'sub_category' =>
                    $subCategoryName,

                    'education_level' =>
                    $educationLevel,

                    'judul_buku' =>
                    $request->title,

                    'penulis' =>
                    $request->author,

                    'isbn' =>
                    $request->filled('isbn')
                        ? preg_replace(
                            '/[^0-9Xx]/',
                            '',
                            strtoupper(
                                $request->isbn
                            )
                        )
                        : null,

                    'publisher' =>
                    $request->publisher,

                    'publication_year' =>
                    $request->publication_year,

                    'description' =>
                    $request->description,

                    'sku' =>
                    $request->sku,

                    'no_iventaris' =>
                    $request->no_iventaris,

                    'kode_buku' =>
                    $request->kode_buku,

                    'ddc' =>
                    $request->ddc,

                    'edition' =>
                    $request->edition,

                    ...(
                        $coverPath
                        ? [
                            'cover' =>
                            $coverPath
                        ]
                        : []
                    ),
                ]);


                /*
                 * -------------------------------------------------
                 * RAK UTAMA BUKU
                 * -------------------------------------------------
                 *
                 * Satu judul buku hanya boleh memiliki satu rak.
                 * Jika rak pada Edit Buku berubah, SEMUA BookCopy
                 * dipindahkan ke rak baru dan posisi fisiknya dihitung
                 * ulang.
                 */

                $targetShelf = Shelf::findOrFail(
                    $request->shelf_id
                );

                $targetShelfId = (int) $targetShelf->id;
                $oldShelfId = $currentShelfId !== null
                    ? (int) $currentShelfId
                    : null;


                if (
                    $oldShelfId === null ||
                    $oldShelfId !== $targetShelfId
                ) {

                    $bookCopies = BookCopy::where(
                        'book_id',
                        $book->id
                    )
                        ->orderBy('id')
                        ->get();


                    foreach ($bookCopies as $copy) {

                        $position =
                            $this->findBookCopyPosition(
                                $targetShelfId
                            );


                        if (! $position) {

                            throw new \Exception(
                                'Rak ' .
                                    $targetShelf->name .
                                    ' tidak memiliki kapasitas yang cukup untuk memindahkan seluruh eksemplar buku ini.'
                            );
                        }


                        $copy->update([
                            'shelf_id' => $targetShelfId,
                            'section' => $position['section'],
                            'side' => $position['side'],
                            'row' => $position['row'],
                            'column' => $position['column'],
                        ]);
                    }
                }


                /*
                 * -------------------------------------------------
                 * STOK BERTAMBAH
                 * -------------------------------------------------
                 *
                 * Copy baru selalu mengikuti rak utama buku.
                 * Tidak ada lagi pemilihan rak khusus untuk copy baru.
                 */

                if (
                    $request->stock >
                    $availableCountBeforeUpdate
                ) {

                    $difference =
                        $request->stock -
                        $availableCountBeforeUpdate;


                    for (
                        $i = 0;
                        $i < $difference;
                        $i++
                    ) {

                        $position =
                            $this->findBookCopyPosition(
                                $targetShelfId
                            );


                        if (! $position) {

                            throw new \Exception(
                                'Rak ' .
                                    $targetShelf->name .
                                    ' sudah penuh.'
                            );
                        }


                        BookCopy::create([

                            'book_id' =>
                            $book->id,

                            'copy' =>
                            $this->generateNextCopyNumber(
                                $book
                            ),

                            'status' =>
                            'available',

                            'condition' =>
                            'baik',

                            'shelf_id' =>
                            $targetShelfId,

                            'section' =>
                            $position['section'],

                            'side' =>
                            $position['side'],

                            'row' =>
                            $position['row'],

                            'column' =>
                            $position['column'],
                        ]);
                    }


                    /*
                 * -------------------------------------------------
                 * STOK BERKURANG
                 * -------------------------------------------------
                 *
                 * Hanya copy AVAILABLE yang boleh dihapus.
                 *
                 * Borrowed / Reserved / Maintenance / Lost
                 * tetap dipertahankan.
                 */
                } elseif (
                    $request->stock <
                    $availableCountBeforeUpdate
                ) {

                    $difference =
                        $availableCountBeforeUpdate -
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
                        ->latest('id')
                        ->take($difference)
                        ->get();


                    if (
                        $copiesToDelete->count() <
                        $difference
                    ) {

                        throw new \Exception(
                            'Jumlah copy yang dapat dihapus tidak mencukupi karena sebagian eksemplar sedang tidak tersedia.'
                        );
                    }


                    foreach (
                        $copiesToDelete as $copy
                    ) {

                        CollectionWithdrawal::create([

                            'type' =>
                            'copy',

                            'book_id' =>
                            $book->id,

                            'book_copy_id' =>
                            $copy->id,

                            'book_title' =>
                            $book->judul_buku,

                            'copy' =>
                            $copy->copy,

                            'quantity' =>
                            1,

                            'reason' =>
                            'Pengurangan jumlah eksemplar melalui pengaturan stok buku.',

                            'withdrawn_at' =>
                            now(),
                        ]);


                        $copy->delete();
                    }
                }


                /*
                 * -------------------------------------------------
                 * SYNC STOCK
                 * -------------------------------------------------
                 */

                $availableCount =
                    BookCopy::where(
                        'book_id',
                        $book->id
                    )
                    ->where(
                        'status',
                        'available'
                    )
                    ->count();


                $book->update([

                    'stok' =>
                    $availableCount,

                    'status' =>
                    $availableCount > 0
                        ? 'Tersedia'
                        : 'Dipinjam',
                ]);
            }
        );


        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Buku berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REMOTE COVER
    |--------------------------------------------------------------------------
    */

    private function downloadRemoteCover(
        string $url,
        ?string $isbn
    ): ?string {

        /*
         * Validasi URL.
         */

        if (
            ! filter_var(
                $url,
                FILTER_VALIDATE_URL
            )
        ) {
            return null;
        }


        /*
         * Hanya izinkan host sumber cover
         * yang sudah ditentukan.
         */

        $host = strtolower(
            (string) parse_url(
                $url,
                PHP_URL_HOST
            )
        );


        $allowedHosts = [

            'books.google.com',

            'books.googleusercontent.com',

            'googleusercontent.com',

            'covers.openlibrary.org',
        ];


        $allowed = false;


        foreach (
            $allowedHosts as $allowedHost
        ) {

            if (
                $host === $allowedHost ||
                str_ends_with(
                    $host,
                    '.' . $allowedHost
                )
            ) {

                $allowed = true;

                break;
            }
        }


        if (! $allowed) {
            return null;
        }


        try {

            $response = Http::timeout(10)
                ->get($url);


            if (
                ! $response->successful() ||
                ! $response->body()
            ) {

                return null;
            }


            $contentType = strtolower(
                $response->header(
                    'Content-Type',
                    ''
                )
            );


            $extension = match (true) {

                str_contains(
                    $contentType,
                    'png'
                ) => 'png',

                str_contains(
                    $contentType,
                    'webp'
                ) => 'webp',

                default => 'jpg',
            };


            $filename =
                'covers/' .
                (
                    $isbn
                    ?: uniqid(
                        'book_',
                        true
                    )
                ) .
                '.' .
                $extension;


            Storage::disk('public')
                ->put(
                    $filename,
                    $response->body()
                );


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

    /*
|--------------------------------------------------------------------------
| DESTROY
|--------------------------------------------------------------------------
*/

    public function destroy(
        Request $request,
        Book $book
    ) {

        $validated = $request->validate([
            'reason' => [
                'required',
                'string',
                'max:500',
            ],
        ]);


        DB::transaction(
            function () use (
                $book,
                $validated
            ) {

                /*
             * =====================================================
             * HITUNG JUMLAH EKSEMPLAR
             * =====================================================
             *
             * BookCopy adalah sumber data fisik.
             *
             * Contoh:
             * Buku A
             * ├── c.1
             * ├── c.2
             * ├── c.3
             * └── c.4
             *
             * Maka $copyCount = 4.
             */

                $copyCount = $book->copies()->count();


                /*
             * =====================================================
             * 1. CATAT BUKU DIHAPUS
             * =====================================================
             *
             * Satu judul buku yang dihapus dihitung sebagai:
             *
             * buku dihapus = 1
             *
             * BUKAN jumlah eksemplarnya.
             */

                CollectionWithdrawal::create([

                    'type' =>
                    'book',

                    'book_id' =>
                    $book->id,

                    'book_copy_id' =>
                    null,

                    'book_title' =>
                    $book->judul_buku,

                    'copy' =>
                    null,

                    'quantity' =>
                    1,

                    'reason' =>
                    $validated['reason'],

                    'withdrawn_at' =>
                    now(),
                ]);


                /*
             * =====================================================
             * 2. CATAT EKSEMPLAR YANG IKUT DIHAPUS
             * =====================================================
             *
             * Kalau buku mempunyai:
             *
             * c.1
             * c.2
             * c.3
             * c.4
             *
             * Maka:
             *
             * eksemplar dihapus = 4
             *
             * Kita menggunakan 1 record dengan quantity = 4,
             * bukan membuat 4 record terpisah.
             */

                if ($copyCount > 0) {

                    CollectionWithdrawal::create([

                        'type' =>
                        'copy',

                        'book_id' =>
                        $book->id,

                        'book_copy_id' =>
                        null,

                        'book_title' =>
                        $book->judul_buku,

                        'copy' =>
                        null,

                        'quantity' =>
                        $copyCount,

                        'reason' =>
                        $validated['reason'],

                        'withdrawn_at' =>
                        now(),
                    ]);
                }


                /*
             * =====================================================
             * 3. HAPUS BUKU
             * =====================================================
             *
             * Setelah histori tersimpan, buku di-hard delete.
             *
             * BookCopy terkait akan ikut terhapus sesuai
             * foreign key database.
             */

                $book->delete();
            }
        );


        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Buku berhasil ditarik dari koleksi.'
            );
    }
}
