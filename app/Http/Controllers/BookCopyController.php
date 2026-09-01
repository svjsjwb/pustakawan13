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
        )
            ->orderBy('floor_number')
            ->get();

        return view('book_copies.create', compact(
            'book',
            'floors'
        ));
    }


    /**
     * Simpan eksemplar baru.
     */
    public function store(
        Request $request,
        Book $book
    ) {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | BARCODE
            |--------------------------------------------------------------------------
            */

            'barcode' => [
                'nullable',
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
            | SHELF
            |--------------------------------------------------------------------------
            */

            'shelf_id' => [
                'nullable',
                'exists:shelves,id',
            ],


            /*
            |--------------------------------------------------------------------------
            | SECTION
            |--------------------------------------------------------------------------
            |
            | 1 = A-01
            | 2 = A-02
            |
            | Tetap menggunakan sistem section lama.
            |--------------------------------------------------------------------------
            */

            'section' => [
                'required',
                'integer',
                'in:1,2',
            ],


            /*
            |--------------------------------------------------------------------------
            | SIDE / MUKA RAK
            |--------------------------------------------------------------------------
            */

            'side' => [
                'required',
                Rule::in([
                    'front',
                    'back',
                ]),
            ],


            /*
            |--------------------------------------------------------------------------
            | ROW
            |--------------------------------------------------------------------------
            |
            | 3 baris:
            |
            | 1 = atas
            | 2 = tengah
            | 3 = bawah
            |--------------------------------------------------------------------------
            */

            'row' => [
                'nullable',
                'integer',
                'min:1',
                'max:3',
            ],


            /*
            |--------------------------------------------------------------------------
            | COLUMN
            |--------------------------------------------------------------------------
            |
            | Setiap section:
            |
            | 30 kolom.
            |--------------------------------------------------------------------------
            */

            'column' => [
                'nullable',
                'integer',
                'min:1',
                'max:30',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK POSISI
        |--------------------------------------------------------------------------
        |
        | Satu posisi fisik tidak boleh ditempati
        | oleh dua BookCopy.
        |
        | Identitas posisi:
        |
        | shelf_id
        | section
        | side
        | row
        | column
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['shelf_id']) &&
            !empty($validated['row']) &&
            !empty($validated['column'])
        ) {

            $positionExists =
                BookCopy::query()
                ->where(
                    'shelf_id',
                    $validated['shelf_id']
                )
                ->where(
                    'section',
                    $validated['section']
                )
                ->where(
                    'side',
                    $validated['side']
                )
                ->where(
                    'row',
                    $validated['row']
                )
                ->where(
                    'column',
                    $validated['column']
                )
                ->exists();


            if (
                $positionExists
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'column' =>
                        'Posisi rak tersebut sudah ditempati buku lain.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        $book->copies()->create(
            $validated
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
                'Eksemplar buku berhasil ditambahkan.'
            );
    }


    /**
     * Form edit lokasi/status eksemplar.
     */
    public function edit(
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
        | DATA LANTAI / ZONA / RAK
        |--------------------------------------------------------------------------
        */

        $floors = LibraryFloor::with(
            'zones.shelves'
        )
            ->orderBy('floor_number')
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


    /**
     * Update eksemplar.
     */
    public function update(
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
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | BARCODE
            |--------------------------------------------------------------------------
            */

            'barcode' => [
                'nullable',
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
            | SHELF
            |--------------------------------------------------------------------------
            */

            'shelf_id' => [
                'nullable',
                'exists:shelves,id',
            ],


            /*
            |--------------------------------------------------------------------------
            | SECTION
            |--------------------------------------------------------------------------
            */

            'section' => [
                'required',
                'integer',
                'in:1,2',
            ],


            /*
            |--------------------------------------------------------------------------
            | SIDE
            |--------------------------------------------------------------------------
            */

            'side' => [
                'required',
                Rule::in([
                    'front',
                    'back',
                ]),
            ],


            /*
            |--------------------------------------------------------------------------
            | ROW
            |--------------------------------------------------------------------------
            */

            'row' => [
                'nullable',
                'integer',
                'min:1',
                'max:3',
            ],


            /*
            |--------------------------------------------------------------------------
            | COLUMN
            |--------------------------------------------------------------------------
            */

            'column' => [
                'nullable',
                'integer',
                'min:1',
                'max:30',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK DUPLIKASI POSISI
        |--------------------------------------------------------------------------
        |
        | Copy yang sedang diedit dikecualikan.
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['shelf_id']) &&
            !empty($validated['row']) &&
            !empty($validated['column'])
        ) {

            $positionExists =
                BookCopy::query()

                ->where(
                    'shelf_id',
                    $validated['shelf_id']
                )

                ->where(
                    'section',
                    $validated['section']
                )

                ->where(
                    'side',
                    $validated['side']
                )

                ->where(
                    'row',
                    $validated['row']
                )

                ->where(
                    'column',
                    $validated['column']
                )

                ->where(
                    'id',
                    '!=',
                    $copy->id
                )

                ->exists();


            if (
                $positionExists
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'column' =>
                        'Posisi rak tersebut sudah ditempati buku lain.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $copy->update(
            $validated
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
                'Eksemplar buku berhasil diperbarui.'
            );
    }
}
