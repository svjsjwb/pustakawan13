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

        {{-- =========================================================
        HERO
        ========================================================== --}}

        <div class="catalog-hero">

            <div class="catalog-hero-content">

                <div class="catalog-hero-label">

                    <span class="catalog-hero-dot"></span>

                    Koleksi Buku

                </div>

                <h1>
                    Katalog Buku
                </h1>

                <p>
                    Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                    kategori, atau ketersediaan stok.
                </p>

            </div>

        </div>


        {{-- =========================================================
        FILTER
        ========================================================== --}}

        @php

            $selectedCategoryId = request('category');
            $selectedSubcategoryId = request('subcategory');

            $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);

            $selectedSubcategory = null;

            if ($selectedCategory && $selectedSubcategoryId) {
                $selectedSubcategory = $selectedCategory->subcategories->firstWhere('id', $selectedSubcategoryId);
            }

        @endphp


        <form method="GET" action="{{ route('catalog') }}" class="catalog-filter-form" id="catalog-filter-form">

            {{-- =====================================================
            SEARCH + STATUS
            ====================================================== --}}

            <div class="catalog-search-row">

                {{-- SEARCH --}}

                <div class="catalog-search">

                    <span class="catalog-search-icon">
                        🔍
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul atau pengarang..." autocomplete="off">

                </div>


                {{-- STATUS --}}

                <select name="status" class="catalog-status-select">

                    <option value="">
                        Semua Status
                    </option>

                    <option value="Tersedia" {{ request('status') === 'Tersedia' ? 'selected' : '' }}>
                        Tersedia
                    </option>

                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>
                        Sedang Dipinjam
                    </option>

                </select>

            </div>


            {{-- =====================================================
            CATEGORY FILTER
            ====================================================== --}}

            <div class="catalog-category-filter">


                {{-- =================================================
                BARIS KATEGORI UTAMA
                ================================================== --}}

                <div class="catalog-category-row">

                    {{-- SEMUA KATEGORI --}}

                    <a href="{{ route(
                        'catalog',
                        array_filter([
                            'search' => request('search'),
                            'status' => request('status'),
                            'per_page' => request('per_page'),
                        ]),
                    ) }}"
                        class="catalog-category-chip
                        {{ !$selectedCategoryId && !$selectedSubcategoryId ? 'active' : '' }}">

                        <span>
                            Semua Kategori
                        </span>

                    </a>


                    {{-- KATEGORI UTAMA --}}

                    @foreach ($categories as $category)
                        <a href="{{ route(
                            'catalog',
                            array_filter([
                                'search' => request('search'),
                                'category' => $category->id,
                                'status' => request('status'),
                                'per_page' => request('per_page'),
                            ]),
                        ) }}"
                            class="catalog-category-chip
                            {{ (string) $selectedCategoryId === (string) $category->id ? 'active' : '' }}">

                            <span>
                                {{ $category->name }}
                            </span>

                        </a>
                    @endforeach

                </div>


                {{-- =================================================
                SUBKATEGORI
                ================================================== --}}

                @if ($selectedCategory && $selectedCategory->subcategories->count())

                    <div class="catalog-subcategory-row">


                        {{-- LABEL KATEGORI --}}

                        <div class="catalog-subcategory-label">

                            <span>
                                {{ $selectedCategory->name }}
                            </span>

                            <span class="catalog-subcategory-arrow">
                                ›
                            </span>

                        </div>


                        {{-- SEMUA DALAM KATEGORI --}}

                        <a href="{{ route(
                            'catalog',
                            array_filter([
                                'search' => request('search'),
                                'category' => $selectedCategory->id,
                                'status' => request('status'),
                                'per_page' => request('per_page'),
                            ]),
                        ) }}"
                            class="catalog-subcategory-chip
                            {{ !$selectedSubcategoryId ? 'active' : '' }}">

                            Semua {{ $selectedCategory->name }}

                        </a>


                        {{-- SUBKATEGORI --}}

                        @foreach ($selectedCategory->subcategories as $subcategory)
                            <a href="{{ route(
                                'catalog',
                                array_filter([
                                    'search' => request('search'),
                                    'category' => $selectedCategory->id,
                                    'subcategory' => $subcategory->id,
                                    'status' => request('status'),
                                    'per_page' => request('per_page'),
                                ]),
                            ) }}"
                                class="catalog-subcategory-chip
                                {{ (string) $selectedSubcategoryId === (string) $subcategory->id ? 'active' : '' }}">

                                {{ $subcategory->name }}

                            </a>
                        @endforeach

                    </div>

                @endif

            </div>

        </form>


        {{-- =========================================================
        BOOK GRID
        ========================================================== --}}

        <div class="book-grid">

            @forelse($books as $book)
                @php

                    $availableCopies = $book->available_copies_count ?? ($book->available_stock ?? 0);

                    $borrowedCopies = $book->borrowed_copies_count ?? 0;

                    $reservedCopies = $book->reserved_copies_count ?? 0;

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS UTAMA UNTUK POPUP
                    |--------------------------------------------------------------------------
                    |
                    | Prioritas:
                    | 1. Dipinjam
                    | 2. Direservasi
                    | 3. Tersedia
                    |
                    */

                    if ($borrowedCopies > 0) {
                        $bookStatus = 'Dipinjam';
                    } elseif ($reservedCopies > 0) {
                        $bookStatus = 'Direservasi';
                    } else {
                        $bookStatus = 'Tersedia';
                    }

                @endphp


                <div class="book-card" data-title="{{ $book->title }}" data-author="{{ $book->author ?? '-' }}"
                    data-category="{{ $book->category->name ?? '-' }}" data-stock="{{ $availableCopies }}"
                    {{-- STATUS SUDAH DIPERBAIKI --}} data-status="{{ $bookStatus }}" {{-- DATA TERPISAH UNTUK JUMLAH STATUS --}}
                    data-borrowed="{{ $borrowedCopies }}" data-reserved="{{ $reservedCopies }}"
                    data-available="{{ $availableCopies }}"
                    data-description="{{ $book->description ?? 'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.' }}"
                    data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"
                    data-publisher="{{ $book->publisher ?? '-' }}" data-year="{{ $book->publication_year ?? '-' }}"
                    data-isbn="{{ $book->isbn ?? '-' }}" data-call-number="{{ $book->call_number ?? '-' }}">


                    {{-- =================================================
                    COVER
                    ================================================== --}}

                    <div class="book-cover
                        {{ $book->cover ? 'has-image' : '' }}">

                        @if ($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}"
                                class="book-cover-img">
                        @endif

                    </div>


                    {{-- =================================================
                    INFORMATION
                    ================================================== --}}

                    <div class="book-info">

                        <strong>
                            {{ $book->title }}
                        </strong>

                        <span>
                            {{ $book->author ?? '-' }}
                        </span>


                        <div class="book-meta">

                            {{-- =================================================
                            KATEGORI SELALU KIRI BAWAH
                            ================================================== --}}

                            <span class="book-category">

                                {{ $book->category->name ?? '-' }}

                            </span>


                            {{-- =================================================
                            STATUS SELALU KANAN BAWAH
                            ================================================== --}}

                            <div class="book-status-list">


                                {{-- DIPINJAM --}}

                                @if ($borrowedCopies > 0)
                                    <span class="book-status borrowed">

                                        Dipinjam
                                        ({{ $borrowedCopies }})
                                    </span>
                                @endif


                                {{-- DIRESERVASI --}}

                                @if ($reservedCopies > 0)
                                    <span class="book-status reserved">

                                        Reservasi
                                        ({{ $reservedCopies }})

                                    </span>
                                @endif


                                {{-- TERSEDIA --}}

                                @if ($borrowedCopies === 0 && $reservedCopies === 0)
                                    <span class="book-status available">

                                        Tersedia
                                        ({{ $availableCopies }})

                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="catalog-empty">

                    Belum ada koleksi buku yang sesuai
                    dengan pencarian atau filter.

                </div>
            @endforelse

        </div>


        {{-- =========================================================
        PAGINATION
        ========================================================== --}}

        @if ($books->total() > 0)
            <div class="catalog-pagination">

                {{ $books->links('partials.pagination') }}

            </div>
        @endif

    </section>


    {{-- =========================================================
    BOOK DETAIL MODAL
    ========================================================== --}}

    <div class="catalog-modal" id="book-modal">

        <div class="catalog-modal-container">


            {{-- CLOSE --}}

            <button type="button" class="catalog-modal-close" id="modal-close" aria-label="Tutup">

                &times;

            </button>


            <div class="catalog-modal-body">


                {{-- =================================================
                MODAL COVER
                ================================================== --}}

                <div class="catalog-modal-cover">

                    <div class="catalog-modal-book" id="modal-book-box">

                        <div class="catalog-modal-brand">

                            TIGA SERANGKAI

                        </div>

                        <div class="catalog-modal-book-title" id="modal-cover-title">
                        </div>

                        <div class="catalog-modal-book-footer">

                            PERPUSTAKAAN

                        </div>

                    </div>


                    <img id="modal-cover-image" src="#" alt="Cover Buku">

                </div>


                {{-- =================================================
                MODAL INFORMATION
                ================================================== --}}

                <div class="catalog-modal-info">


                    {{-- BADGES --}}

                    <div class="catalog-modal-badges">

                        <span class="catalog-modal-category" id="modal-category">
                        </span>

                        <div class="catalog-modal-status-list" id="modal-status-list">
                        </div>

                    </div>


                    {{-- TITLE --}}

                    <h2 id="modal-title">
                    </h2>


                    {{-- AUTHOR --}}

                    <div class="catalog-modal-author" id="modal-author">
                    </div>


                    {{-- DESCRIPTION --}}

                    <div class="catalog-modal-description-title">

                        Sinopsis / Deskripsi

                    </div>

                    <div class="catalog-modal-description" id="modal-description">

                        Informasi buku belum tersedia.

                    </div>


                    {{-- META --}}

                    <div class="catalog-modal-meta">

                        <div>

                            <span>
                                Stok Tersedia
                            </span>

                            <strong id="modal-stock">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Penerbit
                            </span>

                            <strong id="modal-publisher">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                Tahun Terbit
                            </span>

                            <strong id="modal-year">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                No. Panggil
                            </span>

                            <strong id="modal-call-number">
                                -
                            </strong>

                        </div>


                        <div>

                            <span>
                                ISBN
                            </span>

                            <strong id="modal-isbn">
                                -
                            </strong>

                        </div>

                    </div>


                    {{-- ACTION --}}

                    <div class="catalog-modal-actions">

                        <button type="button" class="catalog-modal-button" id="modal-action">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
