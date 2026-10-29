<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryFloor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\CollectionWithdrawal;

class BookCopyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Book $book)
    {
        $copies = $book->copies()
            ->with('shelf.zone.floor')
            ->orderBy('id')
            ->get();

        return view(
            'book_copies.index',
            compact(
                'book',
                'copies'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(Book $book)
    {
        $floors = LibraryFloor::with(
            'zones.shelves'
        )
            ->orderBy('floor_number')
            ->get();

        $nextCopyNumber = (int) (
            $book->copies()
            ->where('copy', 'like', 'c.%')
            ->get()
            ->map(function ($copy) {
                return (int) str_replace('c.', '', $copy->copy);
            })
            ->max() ?? 0
        ) + 1;

        return view(
            'book_copies.create',
            compact(
                'book',
                'floors',
                'nextCopyNumber'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POSITION KEY
    |--------------------------------------------------------------------------
    */

    private function positionKey(
        int $section,
        string $side,
        int $row,
        int $column
    ): string {

        return implode('|', [
            $section,
            $side,
            $row,
            $column,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE SLOT
    |--------------------------------------------------------------------------
    */

    private function getAllSlots(): array
    {
        $slots = [];

        $slotIndex = 0;

        foreach ([1, 2] as $section) {

            foreach (['front', 'back'] as $side) {

                foreach ([1, 2, 3] as $row) {

                    foreach (range(1, 30) as $column) {

                        $slotIndex++;

                        $slots[] = [
                            'index' => $slotIndex,

                            'section' => $section,

                            'side' => $side,

                            'row' => $row,

                            'column' => $column,

                            'key' => $this->positionKey(
                                $section,
                                $side,
                                $row,
                                $column
                            ),
                        ];
                    }
                }
            }
        }

        return $slots;
    }


    /*
    |--------------------------------------------------------------------------
    | AUTO POSITION
    |--------------------------------------------------------------------------
    */

    private function findAutomaticPosition(
        int $shelfId,
        int $bookId,
        ?int $ignoreCopyId = null
    ): ?array {

        $copiesQuery = BookCopy::query()
            ->where(
                'shelf_id',
                $shelfId
            )
            ->whereNotNull('section')
            ->whereNotNull('side')
            ->whereNotNull('row')
            ->whereNotNull('column');

        if ($ignoreCopyId !== null) {

            $copiesQuery->where(
                'id',
                '!=',
                $ignoreCopyId
            );
        }


        $existingCopies = $copiesQuery->get([
            'id',
            'book_id',
            'section',
            'side',
            'row',
            'column',
        ]);


        $occupied = [];

        foreach ($existingCopies as $copy) {

            $key = $this->positionKey(
                (int) $copy->section,
                $copy->side,
                (int) $copy->row,
                (int) $copy->column
            );

            $occupied[$key] = [
                'book_id' => (int) $copy->book_id,
                'index' => null,
            ];
        }


        $slots = $this->getAllSlots();


        foreach ($slots as $slot) {

            if (isset($occupied[$slot['key']])) {

                $occupied[$slot['key']]['index'] =
                    $slot['index'];
            }
        }


        $sameBookIndexes = [];

        foreach ($occupied as $occupiedPosition) {

            if (
                $occupiedPosition['book_id'] === $bookId
            ) {

                $sameBookIndexes[] =
                    $occupiedPosition['index'];
            }
        }


        $emptySlots = [];

        foreach ($slots as $slot) {

            if (!isset($occupied[$slot['key']])) {

                $emptySlots[] = $slot;
            }
        }


        if (empty($emptySlots)) {

            return null;
        }


        if (empty($sameBookIndexes)) {

            return $emptySlots[0];
        }


        usort(
            $emptySlots,
            function (
                array $a,
                array $b
            ) use (
                $sameBookIndexes
            ) {

                $distanceA =
                    PHP_INT_MAX;

                $distanceB =
                    PHP_INT_MAX;


                foreach (
                    $sameBookIndexes
                    as $sameIndex
                ) {

                    $distanceA = min(
                        $distanceA,
                        abs(
                            $a['index'] -
                                $sameIndex
                        )
                    );

                    $distanceB = min(
                        $distanceB,
                        abs(
                            $b['index'] -
                                $sameIndex
                        )
                    );
                }


                if (
                    $distanceA ===
                    $distanceB
                ) {

                    return
                        $a['index'] <=>
                        $b['index'];
                }


                return
                    $distanceA <=>
                    $distanceB;
            }
        );


        return $emptySlots[0];
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC STOCK
    |--------------------------------------------------------------------------
    */

    private function syncBookStock(
        Book $book
    ): void {

        $availableStock =
            $book->copies()
            ->where(
                'status',
                'available'
            )
            ->count();


        $book->update([
            'stok' => $availableStock,
        ]);
    }

    // auto-generate copy number
    private function generateCopyNumber(Book $book): string
    {
        $lastNumber = $book->copies()
            ->where('copy', 'like', 'c.%')
            ->get()
            ->map(function ($copy) {
                return (int) str_replace('c.', '', $copy->copy);
            })
            ->max();

        return 'c.' . (($lastNumber ?? 0) + 1);
    }
    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Book $book
    ) {
        $validated = $request->validate([
            'condition' => [
                'required',
                Rule::in([
                    'baik',
                    'rusak',
                    'hilang',
                ]),
            ],

            'shelf_id' => [
                'required',
                'integer',
                'exists:shelves,id',
            ],
        ]);

        $position = $this->findAutomaticPosition(
            (int) $validated['shelf_id'],
            (int) $book->id
        );

        if ($position === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'shelf_id' =>
                    'Rak yang dipilih sudah penuh. Silakan pilih rak lain.',
                ]);
        }

        $copyNumber = $this->generateCopyNumber($book);

        DB::transaction(function () use (
            $book,
            $validated,
            $position,
            $copyNumber
        ) {
            $copy = $book->copies()->create([
                'copy' => $copyNumber,

                'status' => 'available',

                'condition' => $validated['condition'],

                'shelf_id' => $validated['shelf_id'],

                'section' => $position['section'],

                'side' => $position['side'],

                'row' => $position['row'],

                'column' => $position['column'],
            ]);

            // Sinkronkan status berdasarkan kondisi fisik
            $copy->syncStatus();

            // Sinkronkan stok buku
            $this->syncBookStock($book);
        });

        return redirect()
            ->route(
                'books.copies.index',
                $book
            )
            ->with(
                'success',
                "Eksemplar {$copyNumber} berhasil ditambahkan."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        Book $book,
        BookCopy $copy
    ) {

        abort_unless(
            $copy->book_id === $book->id,
            404
        );


        $floors =
            LibraryFloor::with(
                'zones.shelves'
            )
            ->orderBy(
                'floor_number'
            )
            ->get();


        return view(
            'book_copies.edit',
            compact(
                'book',
                'copy',
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
        Book $book,
        BookCopy $copy
    ) {

        abort_unless(
            $copy->book_id === $book->id,
            404
        );


        $validated = $request->validate([
            'condition' => [
                'required',
                Rule::in([
                    'baik',
                    'rusak',
                    'hilang',
                ]),
            ],

            'shelf_id' => [
                'required',
                'integer',
                'exists:shelves,id',
            ],
        ]);


        $oldShelfId =
            $copy->shelf_id !== null
            ? (int) $copy->shelf_id
            : null;


        $newShelfId =
            (int) $validated['shelf_id'];

        if (
            in_array(
                $copy->status,
                ['borrowed', 'reserved']
            ) &&
            $validated['condition'] !== $copy->condition
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'condition' =>
                    'Eksemplar yang sedang dipinjam atau direservasi tidak dapat diubah kondisinya.',
                ]);
        }

        $updateData = [
            'condition' => $validated['condition'],
            'shelf_id' => $newShelfId,
        ];


        if (
            $oldShelfId !==
            $newShelfId
        ) {

            $position =
                $this->findAutomaticPosition(
                    $newShelfId,
                    (int) $book->id,
                    $copy->id
                );


            if (
                $position === null
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'shelf_id' =>
                        'Rak yang dipilih sudah penuh. Silakan pilih rak lain.',
                    ]);
            }


            $updateData['section'] =
                $position['section'];

            $updateData['side'] =
                $position['side'];

            $updateData['row'] =
                $position['row'];

            $updateData['column'] =
                $position['column'];
        }


        DB::transaction(
            function () use (
                $book,
                $copy,
                $updateData
            ) {

                $copy->update($updateData);

                $copy->syncStatus();

                $this->syncBookStock($book);
            }
        );


        return redirect()
            ->route(
                'books.copies.index',
                $book
            )
            ->with(
                'success',
                'Eksemplar buku berhasil diperbarui.'
            );
    }


    /*
|--------------------------------------------------------------------------
| DESTROY
|--------------------------------------------------------------------------
|
| Hapus eksemplar dengan alasan.
|
*/

    public function destroy(
        Request $request,
        Book $book,
        BookCopy $copy
    ) {

        /*
    |--------------------------------------------------------------------------
    | PASTIKAN COPY MILIK BUKU
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $copy->book_id === $book->id,
            404
        );


        /*
    |--------------------------------------------------------------------------
    | VALIDASI ALASAN
    |--------------------------------------------------------------------------
    */

        $validated =
            $request->validate([
                'reason' => [
                    'required',
                    'string',
                    'max:500',
                ],
            ]);


        /*
    |--------------------------------------------------------------------------
    | JANGAN HAPUS YANG SEDANG DIPINJAM
    |--------------------------------------------------------------------------
    */

        if (
            $copy->status === 'borrowed'
        ) {

            return back()
                ->withErrors([
                    'copy' =>
                    'Eksemplar yang sedang dipinjam tidak dapat dihapus.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | JANGAN HAPUS YANG SEDANG DIRESEVASI
    |--------------------------------------------------------------------------
    */

        if (
            $copy->status === 'reserved'
        ) {

            return back()
                ->withErrors([
                    'copy' =>
                    'Eksemplar yang sedang direservasi tidak dapat dihapus.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS EKSEMPLAR
    |--------------------------------------------------------------------------
    |
    | Aturan laporan:
    |
    | 1. Hapus 1 eksemplar dari buku yang masih punya copy lain:
    |    - Eksemplar dihapus +1
    |    - Buku dihapus +0
    |
    | 2. Hapus eksemplar terakhir:
    |    - Eksemplar dihapus +1
    |    - Buku dihapus +1
    |    - Buku ikut dihapus karena sudah tidak memiliki eksemplar.
    |
    | Record withdrawal dibuat SEBELUM data induk dihapus agar
    | informasi buku dan eksemplar tetap dapat dicatat di laporan.
    */

        $bookDeleted = DB::transaction(
            function () use (
                $book,
                $copy,
                $validated
            ): bool {

                // Hitung copy lain sebelum copy ini dihapus.
                $remainingCopyCount = $book->copies()
                    ->where('id', '!=', $copy->id)
                    ->count();


                /*
            |------------------------------------------------------------------
            | CATAT EKSEMPLAR YANG DIHAPUS
            |------------------------------------------------------------------
            */

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
                    $validated['reason'],

                    'withdrawn_at' =>
                    now(),

                ]);


                /*
            |------------------------------------------------------------------
            | HAPUS COPY
            |------------------------------------------------------------------
            */

                $copy->delete();


                /*
            |------------------------------------------------------------------
            | JIKA INI COPY TERAKHIR, HAPUS BUKUNYA JUGA
            |------------------------------------------------------------------
            */

                if ($remainingCopyCount === 0) {

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


                    $book->delete();


                    // Menandakan bahwa redirect harus kembali ke daftar buku.
                    return true;
                }


                /*
            |------------------------------------------------------------------
            | MASIH ADA COPY LAIN → SYNC STOCK
            |------------------------------------------------------------------
            */

                $this->syncBookStock(
                    $book
                );

                return false;
            }
        );


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        if ($bookDeleted) {

            return redirect()
                ->route(
                    'books.index'
                )
                ->with(
                    'success',
                    'Eksemplar terakhir berhasil dihapus dan buku ikut dihapus dari koleksi.'
                );
        }


        return redirect()
            ->route(
                'books.copies.index',
                $book
            )
            ->with(
                'success',
                'Eksemplar buku berhasil dihapus dari koleksi.'
            );
    }
}
