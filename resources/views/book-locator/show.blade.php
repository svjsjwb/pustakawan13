@extends('layouts.app')

@section('title', 'Lokasi Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/book-locator.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/book-locator.js') }}"></script>
@vite('resources/js/book-locator-3d.js')
@endpush

@section('content')

@php

/*
|--------------------------------------------------------------------------
| BOOK COPY TUJUAN
|--------------------------------------------------------------------------
*/

$targetCopy = $reservation->bookCopy;

$targetShelf = $targetCopy->shelf;


/*
|--------------------------------------------------------------------------
| KELOMPOKKAN SHELF MENJADI RAK FISIK
|--------------------------------------------------------------------------
|
| A-01 + A-02 = Rak A
| B-01 + B-02 = Rak B
| C-01 + C-02 = Rak C
|
*/

$physicalRacks = $shelves->groupBy(function ($shelf) {

    return substr($shelf->code, 0, 1);

});


/*
|--------------------------------------------------------------------------
| DATA SHELF UNTUK JAVASCRIPT 3D
|--------------------------------------------------------------------------
*/

$shelves3d = $shelves->map(function ($shelf) {

    return [

        'id' => (int) $shelf->id,

        'code' => $shelf->code,

        'row_count' =>
            (int) ($shelf->row_count ?? 3),

        'column_count' =>
            (int) ($shelf->column_count ?? 30),

    ];

})->values()->toArray();

@endphp


<section class="page locator-page">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="locator-header">

        <h1>
            Lokasi Buku
        </h1>

        <p>
            Temukan lokasi fisik buku yang kamu reservasi.
        </p>

    </div>



    {{-- =========================================================
         LAYOUT
    ========================================================== --}}

    <div class="locator-layout">


        {{-- =====================================================
             INFORMASI BUKU
        ====================================================== --}}

        <div class="locator-info">

            <h2>
                {{ $reservation->book->title }}
            </h2>


            <div class="locator-detail">

                <span>
                    Barcode
                </span>

                <strong>
                    {{ $targetCopy->barcode ?? '-' }}
                </strong>

            </div>


            <div class="locator-detail">

                <span>
                    Lantai
                </span>

                <strong>
                    {{ $targetShelf->zone->floor->name ?? '-' }}
                </strong>

            </div>


            <div class="locator-detail">

                <span>
                    Zona
                </span>

                <strong>
                    {{ $targetShelf->zone->name ?? '-' }}
                </strong>

            </div>


            <div class="locator-detail">

                <span>
                    Rak
                </span>

                <strong>
                    {{ $targetShelf->code ?? '-' }}
                </strong>

            </div>


            <div class="locator-detail">

                <span>
                    Baris
                </span>

                <strong>
                    {{ $targetCopy->row ?? '-' }}
                </strong>

            </div>


            <div class="locator-detail">

                <span>
                    Kolom
                </span>

                <strong>
                    {{ $targetCopy->column ?? '-' }}
                </strong>

            </div>

        </div>



        {{-- =====================================================
             PETA LOKASI
        ====================================================== --}}

        <div class="locator-map-card">

            <h2 class="locator-map-title">
                Peta Lokasi
            </h2>



            {{-- =================================================
                 3D LOCATOR
            ================================================== --}}

            <div
                id="locator3d"
                class="locator-3d"
                data-shelf-rows="{{ $targetShelf->row_count ?? 3 }}"
                data-shelf-columns="{{ $targetShelf->column_count ?? 30 }}"
                data-shelf-id="{{ $targetShelf->id }}"
                data-target-copy-id="{{ $reservation->book_copy_id }}">
            </div>



            {{-- =================================================
                 DATA UNTUK JAVASCRIPT 3D
            ================================================== --}}

            <script>
                window.bookLocatorShelves = @json($shelves3d);

                window.bookLocatorCopies = @json($bookCopies);

                window.bookLocatorTargetCopyId = {{ (int) $reservation->book_copy_id }};


                console.log('====================================');
                console.log('3D LOCATOR DATA');
                console.log('Jumlah Shelf:', window.bookLocatorShelves.length);
                console.log('Jumlah BookCopy:', window.bookLocatorCopies.length);
                console.log('Target BookCopy:', window.bookLocatorTargetCopyId);
                console.log('Shelves:', window.bookLocatorShelves);
                console.log('BookCopies:', window.bookLocatorCopies);
                console.log('====================================');
            </script>



            {{-- =================================================
                 <!-- PETA 2D kondisi hidden -->
            ================================================== --}}

            <!-- <div class="locator-map">

                @foreach($physicalRacks as $rackCode => $rackShelves)

                    @php

                        /*
                        |--------------------------------------------------------------------------
                        | URUTKAN SECTION
                        |--------------------------------------------------------------------------
                        */

                        $rackShelves =
                            $rackShelves
                            ->sortBy('code')
                            ->values();


                        /*
                        |--------------------------------------------------------------------------
                        | SECTION KIRI
                        |--------------------------------------------------------------------------
                        */

                        $leftShelf =
                            $rackShelves->get(0);


                        /*
                        |--------------------------------------------------------------------------
                        | SECTION KANAN
                        |--------------------------------------------------------------------------
                        */

                        $rightShelf =
                            $rackShelves->get(1);


                        /*
                        |--------------------------------------------------------------------------
                        | POSISI RAK FISIK
                        |--------------------------------------------------------------------------
                        */

                        $rackLeft =
                            (($leftShelf->position_x ?? 0) * 45)
                            + 30;


                        $rackTop =
                            (($leftShelf->position_y ?? 0) * 45)
                            + 40;


                        /*
                        |--------------------------------------------------------------------------
                        | UKURAN
                        |--------------------------------------------------------------------------
                        */

                        $sectionWidth = 420;

                        $sectionHeight = 150;

                        $dividerWidth = 10;


                        $rackWidth =
                            ($sectionWidth * 2)
                            + $dividerWidth;


                        /*
                        |--------------------------------------------------------------------------
                        | TARGET RAK
                        |--------------------------------------------------------------------------
                        */

                        $isTargetRack =
                            $leftShelf->id === $targetShelf->id
                            ||
                            (
                                $rightShelf &&
                                $rightShelf->id === $targetShelf->id
                            );

                    @endphp



                    {{-- =================================================
                         RAK FISIK
                    ================================================== --}}

                    <div
                        class="
                            locator-physical-rack
                            {{ $isTargetRack ? 'target-rack' : '' }}
                        "
                        style="
                            left: {{ $rackLeft }}px;
                            top: {{ $rackTop }}px;
                            width: {{ $rackWidth }}px;
                            height: {{ $sectionHeight }}px;
                        ">


                        {{-- =================================================
                             SECTION KIRI
                        ================================================== --}}

                        <div
                            class="
                                locator-shelf-section
                                locator-shelf-left
                            "
                            data-shelf-id="{{ $leftShelf->id }}"
                            data-shelf-code="{{ $leftShelf->code }}">

                            <div class="locator-shelf-code">
                                {{ $leftShelf->code }}
                            </div>


                            @php

                                $leftCopies =
                                    $leftShelf
                                    ->copies
                                    ->keyBy(function ($copy) {

                                        return
                                            $copy->row .
                                            '-' .
                                            $copy->column;

                                    });

                            @endphp


                            <div
                                class="locator-shelf-grid"
                                style="
                                    --rows: {{ $leftShelf->row_count }};
                                    --columns: {{ $leftShelf->column_count }};
                                ">


                                @for(
                                    $row = 1;
                                    $row <= $leftShelf->row_count;
                                    $row++
                                )

                                    @for(
                                        $column = 1;
                                        $column <= $leftShelf->column_count;
                                        $column++
                                    )

                                        @php

                                            $copy =
                                                $leftCopies->get(
                                                    $row . '-' . $column
                                                );


                                            $isTargetBook =
                                                $copy &&
                                                $copy->id === $targetCopy->id;

                                        @endphp


                                        <div
                                            class="
                                                locator-book-slot
                                                {{ $copy ? 'occupied' : 'empty' }}
                                                {{ $isTargetBook ? 'target-book' : '' }}
                                            "

                                            @if($copy)

                                                data-copy-id="{{ $copy->id }}"
                                                data-title="{{ $copy->book->title }}"
                                                data-barcode="{{ $copy->barcode }}"
                                                data-status="{{ $copy->status }}"
                                                data-shelf="{{ $leftShelf->code }}"
                                                data-row="{{ $row }}"
                                                data-column="{{ $column }}"
                                                data-copy-number="{{ (int) substr($copy->barcode, -3) }}"
                                                data-copy-total="{{ $copy->book->stock }}"

                                            @endif

                                            title="{{ $copy?->book?->title ?? 'Slot kosong' }}"
                                        >

                                            @if($copy)

                                                @if($isTargetBook)

                                                    📍

                                                @else

                                                    📕

                                                @endif

                                            @endif

                                        </div>

                                    @endfor

                                @endfor

                            </div>

                        </div>



                        {{-- =================================================
                             SEKAT TENGAH
                        ================================================== --}}

                        <div class="locator-rack-divider">

                            <span></span>

                        </div>



                        {{-- =================================================
                             SECTION KANAN
                        ================================================== --}}

                        @if($rightShelf)

                            <div
                                class="
                                    locator-shelf-section
                                    locator-shelf-right
                                "
                                data-shelf-id="{{ $rightShelf->id }}"
                                data-shelf-code="{{ $rightShelf->code }}">

                                <div class="locator-shelf-code">
                                    {{ $rightShelf->code }}
                                </div>


                                @php

                                    $rightCopies =
                                        $rightShelf
                                        ->copies
                                        ->keyBy(function ($copy) {

                                            return
                                                $copy->row .
                                                '-' .
                                                $copy->column;

                                        });

                                @endphp


                                <div
                                    class="locator-shelf-grid"
                                    style="
                                        --rows: {{ $rightShelf->row_count }};
                                        --columns: {{ $rightShelf->column_count }};
                                    ">


                                    @for(
                                        $row = 1;
                                        $row <= $rightShelf->row_count;
                                        $row++
                                    )

                                        @for(
                                            $column = 1;
                                            $column <= $rightShelf->column_count;
                                            $column++
                                        )

                                            @php

                                                $copy =
                                                    $rightCopies->get(
                                                        $row . '-' . $column
                                                    );


                                                $isTargetBook =
                                                    $copy &&
                                                    $copy->id === $targetCopy->id;

                                            @endphp


                                            <div
                                                class="
                                                    locator-book-slot
                                                    {{ $copy ? 'occupied' : 'empty' }}
                                                    {{ $isTargetBook ? 'target-book' : '' }}
                                                "

                                                @if($copy)

                                                    data-copy-id="{{ $copy->id }}"
                                                    data-title="{{ $copy->book->title }}"
                                                    data-barcode="{{ $copy->barcode }}"
                                                    data-status="{{ $copy->status }}"
                                                    data-shelf="{{ $rightShelf->code }}"
                                                    data-row="{{ $row }}"
                                                    data-column="{{ $column }}"
                                                    data-copy-number="{{ (int) substr($copy->barcode, -3) }}"
                                                    data-copy-total="{{ $copy->book->stock }}"

                                                @endif

                                                title="{{ $copy?->book?->title ?? 'Slot kosong' }}"
                                            >

                                                @if($copy)

                                                    @if($isTargetBook)

                                                        📍

                                                    @else

                                                        📕

                                                    @endif

                                                @endif

                                            </div>

                                        @endfor

                                    @endfor

                                </div>

                            </div>

                        @endif



                        {{-- =================================================
                             LABEL RAK
                        ================================================== --}}

                        <div class="locator-physical-rack-label">

                            Rak {{ $rackCode }}

                        </div>

                    </div>

                @endforeach

            </div> -->

        </div>

    </div>

</section>



{{-- =============================================================
     MODAL DETAIL EKSEMPLAR
============================================================= --}}

<div
    id="locatorCopyModal"
    class="locator-modal"
    hidden>

    <div class="locator-modal-overlay"></div>


    <div class="locator-modal-card">


        <button
            type="button"
            class="locator-modal-close"
            id="locatorModalClose">

            &times;

        </button>


        <div class="locator-modal-eyebrow">

            EKSEMPLAR BUKU

        </div>


        <h2 id="locatorModalTitle">

            Detail Eksemplar

        </h2>


        <div class="locator-modal-detail">

            <span>

                Judul Buku

            </span>

            <strong id="locatorModalBookTitle">

                -

            </strong>

        </div>


        <div class="locator-modal-detail">

            <span>

                Eksemplar

            </span>

            <strong id="locatorModalCopyNumber">

                -

            </strong>

        </div>


        <div class="locator-modal-detail">

            <span>

                Barcode

            </span>

            <strong id="locatorModalBarcode">

                -

            </strong>

        </div>


        <div class="locator-modal-detail">

            <span>

                Status

            </span>

            <strong id="locatorModalStatus">

                -

            </strong>

        </div>


        <div class="locator-modal-detail">

            <span>

                Rak

            </span>

            <strong id="locatorModalShelf">

                -

            </strong>

        </div>


        <div class="locator-modal-detail">

            <span>

                Posisi

            </span>

            <strong id="locatorModalPosition">

                -

            </strong>

        </div>

    </div>

</div>

@endsection