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

        return view(
            'book_copies.create',
            compact(
                'book',
                'floors'
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

        $stock =
            $book->copies()->count();


        $availableStock =
            $book->copies()
            ->where(
                'status',
                'available'
            )
            ->count();


        $book->update([

            'stock' =>
            $stock,

            'available_stock' =>
            $availableStock,

        ]);
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

        $validated =
            $request->validate([

                /*
                |--------------------------------------------------------------------------
                | BARCODE
                |--------------------------------------------------------------------------
                */

                'barcode' => [
                    'required',
                    'string',
                    'max:100',

                    'unique:book_copies,barcode',
                ],


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | CONDITION
                |--------------------------------------------------------------------------
                */

                'condition' => [
                    'required',

                    Rule::in([
                        'baik',
                        'rusak',
                    ]),
                ],


                /*
                |--------------------------------------------------------------------------
                | RAK
                |--------------------------------------------------------------------------
                */

                'shelf_id' => [
                    'required',
                    'integer',
                    'exists:shelves,id',
                ],

            ]);


        $position =
            $this->findAutomaticPosition(
                (int) $validated['shelf_id'],
                (int) $book->id
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


        DB::transaction(
            function () use (
                $book,
                $validated,
                $position
            ) {

                $book->copies()->create([

                    'barcode' =>
                    $validated['barcode'],

                    'status' =>
                    $validated['status'],

                    'condition' =>
                    $validated['condition'],

                    'shelf_id' =>
                    $validated['shelf_id'],

                    'section' =>
                    $position['section'],

                    'side' =>
                    $position['side'],

                    'row' =>
                    $position['row'],

                    'column' =>
                    $position['column'],

                ]);


                $this->syncBookStock(
                    $book
                );
            }
        );


        return redirect()
            ->route(
                'books.copies.index',
                $book
            )
            ->with(
                'success',
                'Eksemplar buku berhasil ditambahkan.'
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


        $validated =
            $request->validate([

                /*
                |--------------------------------------------------------------------------
                | BARCODE
                |--------------------------------------------------------------------------
                */

                'barcode' => [
                    'required',
                    'string',
                    'max:100',

                    Rule::unique(
                        'book_copies',
                        'barcode'
                    )->ignore(
                        $copy->id
                    ),
                ],


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | CONDITION
                |--------------------------------------------------------------------------
                */

                'condition' => [
                    'required',

                    Rule::in([
                        'baik',
                        'rusak',
                    ]),
                ],


                /*
                |--------------------------------------------------------------------------
                | RAK
                |--------------------------------------------------------------------------
                */

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


        $updateData = [

            'barcode' =>
            $validated['barcode'],

            'status' =>
            $validated['status'],

            'condition' =>
            $validated['condition'],

            'shelf_id' =>
            $newShelfId,

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

                $copy->update(
                    $updateData
                );


                $this->syncBookStock(
                    $book
                );
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
    | SIMPAN DATA HISTORI SEBELUM COPY DIHAPUS
    |--------------------------------------------------------------------------
    |
    | book_title dan barcode disimpan sebagai snapshot.
    |
    | Jadi meskipun BookCopy sudah dihapus,
    | laporan tetap mengetahui eksemplar yang pernah ditarik.
    |
    */

        DB::transaction(
            function () use (
                $book,
                $copy,
                $validated
            ) {

                CollectionWithdrawal::create([

                    'type' =>
                    'copy',

                    'book_id' =>
                    $book->id,

                    'book_copy_id' =>
                    $copy->id,

                    'book_title' =>
                    $book->title,

                    'barcode' =>
                    $copy->barcode,

                    'quantity' =>
                    1,

                    'reason' =>
                    $validated['reason'],

                    'withdrawn_at' =>
                    now(),

                ]);


                /*
            |--------------------------------------------------------------------------
            | HAPUS COPY
            |--------------------------------------------------------------------------
            */

                $copy->delete();


                /*
            |--------------------------------------------------------------------------
            | HITUNG ULANG STOCK
            |--------------------------------------------------------------------------
            */

                $this->syncBookStock(
                    $book
                );
            }
        );


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

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
