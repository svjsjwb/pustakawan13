@extends('layouts.app')

@section('title', 'Katalog Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/catalog.js') }}"></script>
@endpush

@section('content')

<section class="page" id="page-catalog">

    {{-- =========================
         HEADER
    ========================= --}}
    <div class="catalog-header">

        <div>

            <div class="catalog-count">
                {{ $books->total() }} buku terdaftar
            </div>

            <h1>Katalog Buku</h1>

            <p>
                Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                kategori, atau ketersediaan stok.
            </p>

        </div>

    </div>


    {{-- =========================
         FILTER FORM
         HIERARKI KATEGORI 3 LAYER
    ========================= --}}
    <form
        method="GET"
        action="{{ route('catalog') }}"
        class="filters"
    >

        {{-- =========================
             SEARCH
        ========================= --}}
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul atau pengarang..."
        >


        {{-- =========================
             DROPDOWN KATEGORI
        ========================= --}}
        <select
            name="category"
            onchange="this.form.submit()"
        >

            <option value="">
                Semua Kategori
            </option>


            @php

                /*
                |--------------------------------------------------------------------------
                | Ambil kategori Layer 1
                |--------------------------------------------------------------------------
                | Struktur:
                |
                | Layer 1
                | └── Layer 2
                |     └── Layer 3
                |
                */

                $categoriesL1 = isset($categories)
                    ? $categories->whereNull('parent_id')
                    : collect();

            @endphp


            @if($categoriesL1->isNotEmpty())

                @foreach($categoriesL1 as $l1)

                    {{-- =====================================
                         LAYER 1
                    ====================================== --}}
                    <optgroup label="📂 {{ $l1->name }}">

                        {{-- Semua buku dalam Layer 1 --}}
                        <option
                            value="{{ $l1->name }}"
                            {{ request('category') == $l1->name ? 'selected' : '' }}
                        >
                            Semua {{ $l1->name }}
                        </option>


                        {{-- =================================
                             LAYER 2
                        ================================== --}}
                        @foreach($l1->children ?? [] as $l2)


                            {{-- Jika Layer 2 mempunyai anak --}}
                            @if(isset($l2->children) && $l2->children->isNotEmpty())


                                {{-- =========================
                                     LAYER 3
                                ========================== --}}
                                @foreach($l2->children as $l3)

                                    <option
                                        value="{{ $l3->name }}"
                                        {{ request('category') == $l3->name ? 'selected' : '' }}
                                    >
                                        &nbsp;&nbsp;↳ {{ $l2->name }}
                                        → {{ $l3->name }}
                                    </option>

                                @endforeach


                            @else

                                {{-- =========================
                                     LAYER 2 TANPA LAYER 3
                                ========================== --}}
                                <option
                                    value="{{ $l2->name }}"
                                    {{ request('category') == $l2->name ? 'selected' : '' }}
                                >
                                    &nbsp;&nbsp;↳ {{ $l2->name }}
                                </option>

                            @endif

                        @endforeach

                    </optgroup>

                @endforeach


            @else

                {{-- =====================================
                     FALLBACK
                     Jika $categories bukan hierarki
                ====================================== --}}
                @foreach($categories ?? [] as $category)

                    <option
                        value="{{ $category->name }}"
                        {{ request('category') == $category->name ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            @endif

        </select>


        {{-- =========================
             FILTER STATUS
        ========================= --}}
        <select
            name="status"
            onchange="this.form.submit()"
        >

            <option value="">
                Semua Status
            </option>

            <option
                value="Tersedia"
                {{ request('status') == 'Tersedia' ? 'selected' : '' }}
            >
                Tersedia
            </option>

            <option
                value="Dipinjam"
                {{ request('status') == 'Dipinjam' ? 'selected' : '' }}
            >
                Sedang Dipinjam
            </option>

        </select>

    </form>


    {{-- =========================
         BOOK GRID
    ========================= --}}
    <div class="book-grid">

        @forelse($books as $book)

            @php

                /*
                |--------------------------------------------------------------------------
                | HIERARKI KATEGORI
                |--------------------------------------------------------------------------
                |
                | category
                |    ↓
                | Layer 3
                |    ↓
                | parent
                |    ↓
                | Layer 2
                |    ↓
                | parent
                |    ↓
                | Layer 1
                |
                */

                $catL1 = $book->category?->parent?->parent?->name;

                $catL2 = $book->category?->parent?->name;

                $catL3 = $book->category?->name;


                /*
                |--------------------------------------------------------------------------
                | BADGE KATEGORI
                |--------------------------------------------------------------------------
                |
                | Tetap dibuat singkat agar desain card
                | tidak berubah.
                |
                */

                $badgeCategory = $catL2 ?? $catL3 ?? '-';


                /*
                |--------------------------------------------------------------------------
                | FULL CATEGORY
                |--------------------------------------------------------------------------
                |
                | Contoh:
                |
                | Anak > Novel > Petualangan Anak
                |
                */

                $fullCategory = collect([
                    $catL1,
                    $catL2,
                    $catL3
                ])
                ->filter()
                ->implode(' > ');


                if (empty($fullCategory)) {

                    $fullCategory = $catL3 ?? '-';

                }

            @endphp


            {{-- =========================
                 BOOK CARD
            ========================= --}}
            <div
                class="book-card"

                data-title="{{ $book->title }}"

                data-author="{{ $book->author }}"

                data-category="{{ $fullCategory }}"

                data-stock="{{ $book->available_stock }}"

                data-status="{{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}"

                data-description="{{ $book->description ?? 'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.' }}"

                data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"

                data-publisher="{{ $book->publisher ?? '-' }}"

                data-year="{{ $book->publication_year ?? '-' }}"

                data-isbn="{{ $book->isbn ?? '-' }}"

                data-call-number="{{ $book->call_number ?? '-' }}"
            >


                {{-- =========================
                     COVER
                ========================= --}}
                <div
                    class="book-cover {{ $book->cover ? 'has-image' : '' }}"
                >

                    @if($book->cover)

                        <img
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            class="book-cover-img"
                        >

                    @else

                        <span>
                            TIGA SERANGKAI
                        </span>

                    @endif

                </div>


                {{-- =========================
                     INFORMATION
                ========================= --}}
                <div class="book-info">

                    {{-- TITLE --}}
                    <strong>
                        {{ $book->title }}
                    </strong>


                    {{-- AUTHOR --}}
                    <span>
                        {{ $book->author }}
                    </span>


                    {{-- =========================
                         BOOK META
                    ========================= --}}
                    <div class="book-meta">

                        {{-- CATEGORY --}}
                        <span
                            title="{{ $fullCategory }}"
                        >
                            {{ $badgeCategory }}
                        </span>


                        {{-- STATUS --}}
                        @if($book->available_stock > 0)

                            <span
                                class="book-status available"
                            >
                                Tersedia
                                ({{ $book->available_stock }})
                            </span>

                        @else

                            <span
                                class="book-status borrowed"
                            >
                                Dipinjam
                            </span>

                        @endif

                    </div>

                </div>

            </div>


        @empty

            {{-- =========================
                 EMPTY DATA
            ========================= --}}
            <div class="catalog-empty">

                Belum ada koleksi buku yang sesuai
                dengan pencarian atau filter.

            </div>

        @endforelse

    </div>


    {{-- =========================
         PAGINATION
    ========================= --}}
    @if($books->total() > 0)

        <div class="catalog-pagination">

            {{ $books->links('partials.pagination') }}

        </div>

    @endif

</section>



{{-- =========================================================
     BOOK DETAIL MODAL
========================================================= --}}
<div
    class="catalog-modal"
    id="book-modal"
>

    <div class="catalog-modal-container">


        {{-- =========================
             CLOSE BUTTON
        ========================= --}}
        <button
            type="button"
            class="catalog-modal-close"
            id="modal-close"
            aria-label="Tutup"
        >
            &times;
        </button>


        <div class="catalog-modal-body">


            {{-- =========================
                 MODAL COVER
            ========================= --}}
            <div class="catalog-modal-cover">

                {{-- DEFAULT BOOK COVER --}}
                <div
                    class="catalog-modal-book"
                    id="modal-book-box"
                >

                    <div class="catalog-modal-brand">
                        TIGA SERANGKAI
                    </div>


                    <div
                        class="catalog-modal-book-title"
                        id="modal-cover-title"
                    >
                    </div>


                    <div class="catalog-modal-book-footer">
                        PERPUSTAKAAN
                    </div>

                </div>


                {{-- REAL COVER IMAGE --}}
                <img
                    id="modal-cover-image"
                    src="#"
                    alt="Cover Buku"
                    style="
                        display:none;
                        width:170px;
                        height:235px;
                        object-fit:cover;
                        border-radius:10px;
                        box-shadow:-6px 12px 25px rgba(0,0,0,0.20);
                    "
                >

            </div>



            {{-- =========================
                 MODAL INFORMATION
            ========================= --}}
            <div class="catalog-modal-info">


                {{-- =========================
                     BADGES
                ========================= --}}
                <div class="catalog-modal-badges">

                    {{-- CATEGORY --}}
                    <span
                        class="catalog-modal-category"
                        id="modal-category"
                    >
                    </span>


                    {{-- STATUS --}}
                    <span
                        class="catalog-modal-status"
                        id="modal-status"
                    >
                    </span>

                </div>


                {{-- =========================
                     TITLE
                ========================= --}}
                <h2 id="modal-title"></h2>


                {{-- =========================
                     AUTHOR
                ========================= --}}
                <div
                    class="catalog-modal-author"
                    id="modal-author"
                >
                </div>


                {{-- =========================
                     DESCRIPTION
                ========================= --}}
                <div class="catalog-modal-description-title">

                    Sinopsis / Deskripsi

                </div>


                <div
                    class="catalog-modal-description"
                    id="modal-description"
                >
                    Informasi buku belum tersedia.
                </div>


                {{-- =========================
                     BOOK META
                ========================= --}}
                <div class="catalog-modal-meta">


                    {{-- STOK --}}
                    <div>

                        <span>
                            Stok Tersedia
                        </span>

                        <strong id="modal-stock">
                            -
                        </strong>

                    </div>


                    {{-- PENERBIT --}}
                    <div>

                        <span>
                            Penerbit
                        </span>

                        <strong id="modal-publisher">
                            -
                        </strong>

                    </div>


                    {{-- TAHUN --}}
                    <div>

                        <span>
                            Tahun Terbit
                        </span>

                        <strong id="modal-year">
                            -
                        </strong>

                    </div>


                    {{-- NO PANGGIL --}}
                    <div>

                        <span>
                            No. Panggil
                        </span>

                        <strong id="modal-call-number">
                            -
                        </strong>

                    </div>


                    {{-- ISBN --}}
                    <div>

                        <span>
                            ISBN
                        </span>

                        <strong id="modal-isbn">
                            -
                        </strong>

                    </div>


                </div>


                {{-- =========================
                     ACTION BUTTON
                ========================= --}}
                <div class="catalog-modal-actions">

                    <button
                        type="button"
                        class="catalog-modal-button"
                        id="modal-action"
                    >
                        Tutup
                    </button>

                </div>


            </div>

        </div>

    </div>

</div>

@endsection