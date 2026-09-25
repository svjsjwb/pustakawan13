<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\LibraryZone;
use App\Models\Reservation;
use App\Models\Shelf;

class BookLocatorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN BOOK LOCATOR 3D
    |--------------------------------------------------------------------------
    |
    | Controller ini khusus menangani:
    |
    | - lokasi eksemplar buku
    | - rak
    | - zona
    | - lantai
    | - data shelf untuk 3D
    | - data book copy untuk 3D
    |
    | Tidak ada logic pengelolaan reservasi di sini.
    |
    */

    public function show(
        Reservation $reservation
    ) {

        /*
        |--------------------------------------------------------------------------
        | LOAD DATA RESERVASI
        |--------------------------------------------------------------------------
        */

        $reservation->load([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK BOOK COPY
        |--------------------------------------------------------------------------
        */

        if (
            !$reservation->bookCopy
        ) {

            abort(
                404,
                'Eksemplar buku tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TARGET BOOK COPY
        |--------------------------------------------------------------------------
        */

        $targetCopy =
            $reservation->bookCopy;


        /*
        |--------------------------------------------------------------------------
        | TARGET SHELF
        |--------------------------------------------------------------------------
        */

        $targetShelf =
            $targetCopy->shelf;


        if (
            !$targetShelf
        ) {

            abort(
                404,
                'Rak buku belum ditentukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TARGET ZONE
        |--------------------------------------------------------------------------
        */

        $targetZone =
            $targetShelf->zone;


        if (
            !$targetZone
        ) {

            abort(
                404,
                'Zona rak belum ditentukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TARGET FLOOR
        |--------------------------------------------------------------------------
        */

        $targetFloor =
            $targetZone->floor;


        if (
            !$targetFloor
        ) {

            abort(
                404,
                'Lantai rak belum ditentukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEMUA ZONA DI LANTAI TARGET
        |--------------------------------------------------------------------------
        */

        $zoneIds =
            LibraryZone::where(
                'library_floor_id',
                $targetFloor->id
            )
            ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | SEMUA RAK DI LANTAI TARGET
        |--------------------------------------------------------------------------
        */

        $shelves =
            Shelf::whereIn(
                'library_zone_id',
                $zoneIds
            )
            ->with([
                'copies.book',
                'zone.floor',
            ])
            ->orderBy('code')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DATA SHELF UNTUK 3D
        |--------------------------------------------------------------------------
        */

        $shelves3d =
            $shelves
            ->map(
                function ($shelf) {

                    return [

                        'id' =>
                            (int) $shelf->id,

                        'code' =>
                            $shelf->code,

                        'row_count' =>
                            (int) (
                                $shelf->row_count
                                ?? 3
                            ),

                        'column_count' =>
                            (int) (
                                $shelf->column_count
                                ?? 30
                            ),

                    ];

                }
            )
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | DATA BOOK COPY UNTUK 3D
        |--------------------------------------------------------------------------
        */

        $bookCopies =
            $shelves
            ->flatMap(
                function ($shelf) use (
                    $reservation
                ) {

                    return $shelf->copies
                        ->map(
                            function ($copy)
                            use (
                                $shelf,
                                $reservation
                            ) {

                                return [

                                    'id' =>
                                        $copy->id,

                                    'book_id' =>
                                        $copy->book_id,

                                    'title' =>
                                        $copy->book?->title
                                        ??
                                        'Buku',

                                    'barcode' =>
                                        $copy->barcode,

                                    'status' =>
                                        $copy->status,

                                    'shelf_id' =>
                                        $shelf->id,

                                    'shelf' =>
                                        $shelf->code,

                                    'section' =>
                                        (int)
                                        $copy->section,

                                    'row' =>
                                        (int)
                                        $copy->row,

                                    'column' =>
                                        (int)
                                        $copy->column,

                                    'is_target' =>
                                        $copy->id ===
                                        $reservation
                                            ->book_copy_id,

                                ];

                            }
                        );

                }
            )
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | KIRIM KE VIEW BOOK LOCATOR
        |--------------------------------------------------------------------------
        */

        return view(
            'book-locator.show',
            [

                'reservation' =>
                    $reservation,

                'targetCopy' =>
                    $targetCopy,

                'targetShelf' =>
                    $targetShelf,

                'targetZone' =>
                    $targetZone,

                'targetFloor' =>
                    $targetFloor,

                'shelves' =>
                    $shelves,

                'shelves3d' =>
                    $shelves3d,

                'bookCopies' =>
                    $bookCopies,

            ]
        );
    }
}