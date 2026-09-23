@extends('layouts.app')

@section('title', 'Katalog Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}?v={{ file_exists(public_path('css/catalog.css')) ? filemtime(public_path('css/catalog.css')) : time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/catalog.js') }}?v={{ file_exists(public_path('js/catalog.js')) ? filemtime(public_path('js/catalog.js')) : time() }}"></script>
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
            $selectedStatus = request('status');

            $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
            $selectedSubcategory = null;

            if ($selectedCategory && $selectedSubcategoryId) {
                $selectedSubcategory = $selectedCategory->subcategories->firstWhere('id', $selectedSubcategoryId);
            }

            // Hitung data dinamis untuk dropdown
            $totalBooksCount = \App\Models\Book::count();
            $availableBooksCount = \App\Models\Book::whereHas('copies', function ($q) {
                $q->where('status', 'available');
            })->count();
            $borrowedBooksCount = \App\Models\Book::whereHas('copies', function ($q) {
                $q->whereIn('status', ['borrowed', 'reserved']);
            })->count();

            // Label tombol filter kategori
            if ($selectedCategory && $selectedSubcategory) {
                $categoryBtnLabel = $selectedCategory->name . ': ' . $selectedSubcategory->name;
            } elseif ($selectedCategory) {
                $categoryBtnLabel = $selectedCategory->name;
            } else {
                $categoryBtnLabel = 'Semua Kategori';
            }

            // Label tombol filter status
            if ($selectedStatus === 'Tersedia') {
                $statusBtnLabel = 'Tersedia';
            } elseif ($selectedStatus === 'Dipinjam') {
                $statusBtnLabel = 'Sedang Dipinjam';
            } else {
                $statusBtnLabel = 'Semua Status';
            }
        @endphp

        <form method="GET" action="{{ route('catalog') }}" class="catalog-filter-form" id="catalog-filter-form">

            {{-- Hidden inputs untuk menjaga state filter saat live search --}}
            <input type="hidden" name="category" id="catalog-category-input" value="{{ $selectedCategoryId }}">
            <input type="hidden" name="subcategory" id="catalog-subcategory-input" value="{{ $selectedSubcategoryId }}">
            <input type="hidden" name="status" id="catalog-status-input" value="{{ $selectedStatus }}">
            <select class="catalog-status-select" id="catalog-status-filter" style="display: none;">
                <option value="">Semua Status</option>
                <option value="Tersedia" {{ $selectedStatus === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="Dipinjam" {{ $selectedStatus === 'Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
            </select>

            {{-- =====================================================
            SEARCH + FILTER ROW (SEJAJAR HORIZONTAL)
            ====================================================== --}}
            <div class="catalog-filter-bar catalog-search-row">

                {{-- SEARCH BUKU --}}
                <div class="catalog-search">
                    <span class="catalog-search-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul, penulis maupun ISBN..." autocomplete="off">
                </div>

                {{-- DROPDOWN FILTER: SEMUA KATEGORI --}}
                <div class="catalog-dropdown-wrapper catalog-category-filter" id="catalog-category-wrapper">

                    <button type="button" class="catalog-filter-btn {{ $selectedCategoryId || $selectedSubcategoryId ? 'active' : '' }}"
                        id="catalog-category-btn" aria-expanded="false" aria-haspopup="true">
                        <span class="catalog-filter-btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                            </svg>
                        </span>
                        <span class="catalog-filter-btn-text" id="catalog-category-btn-text">{{ $categoryBtnLabel }}</span>
                        <span class="catalog-filter-btn-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </span>
                    </button>

                    {{-- PANEL DROPDOWN KATEGORI --}}
                    <div class="catalog-filter-dropdown" id="catalog-category-dropdown" aria-hidden="true">
                        <div class="catalog-dropdown-title">PILIH KATEGORI</div>

                        {{-- GRID KATEGORI UTAMA --}}
                        <div class="catalog-category-dropdown-grid">
                            {{-- SEMUA KATEGORI --}}
                            <a href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'status' => request('status'),
                                'per_page' => request('per_page'),
                            ])) }}"
                                class="catalog-category-chip {{ !$selectedCategoryId && !$selectedSubcategoryId ? 'active' : '' }}"
                                data-category-id="">
                                <span class="chip-label">Semua Kategori</span>
                                <span class="chip-badge">{{ $totalBooksCount }}</span>
                            </a>

                            {{-- KATEGORI BERDASARKAN DATABASE --}}
                            @foreach ($categories as $category)
                                @php
                                    $catCount = $category->books()->count();
                                @endphp
                                <a href="{{ route('catalog', array_filter([
                                    'search' => request('search'),
                                    'category' => $category->id,
                                    'status' => request('status'),
                                    'per_page' => request('per_page'),
                                ])) }}"
                                    class="catalog-category-chip {{ (string) $selectedCategoryId === (string) $category->id ? 'active' : '' }}"
                                    data-category-id="{{ $category->id }}"
                                    data-category-name="{{ $category->name }}">
                                    <span class="chip-label">{{ $category->name }}</span>
                                    <span class="chip-badge">{{ $catCount }}</span>
                                </a>
                            @endforeach
                        </div>

                        {{-- SUBKATEGORI ACCORDION / CONTAINER --}}
                        <div class="catalog-dropdown-subcategories" id="catalog-dropdown-subcategories"
                            style="{{ $selectedCategory && $selectedCategory->subcategories->count() ? 'display: block;' : 'display: none;' }}">
                            @foreach ($categories as $category)
                                @if ($category->subcategories->count())
                                    <div class="catalog-subcategory-group" data-group-category-id="{{ $category->id }}"
                                        style="{{ (string) $selectedCategoryId === (string) $category->id ? 'display: block;' : 'display: none;' }}">

                                        <div class="catalog-dropdown-subtitle">
                                            <span>{{ $category->name }}:</span>
                                        </div>

                                        <div class="catalog-subcategory-chips-wrap">
                                            <a href="{{ route('catalog', array_filter([
                                                'search' => request('search'),
                                                'category' => $category->id,
                                                'status' => request('status'),
                                                'per_page' => request('per_page'),
                                            ])) }}"
                                                class="catalog-subcategory-chip {{ (string) $selectedCategoryId === (string) $category->id && !$selectedSubcategoryId ? 'active' : '' }}">
                                                Semua {{ $category->name }}
                                            </a>

                                            @foreach ($category->subcategories as $subcategory)
                                                <a href="{{ route('catalog', array_filter([
                                                    'search' => request('search'),
                                                    'category' => $category->id,
                                                    'subcategory' => $subcategory->id,
                                                    'status' => request('status'),
                                                    'per_page' => request('per_page'),
                                                ])) }}"
                                                    class="catalog-subcategory-chip {{ (string) $selectedSubcategoryId === (string) $subcategory->id ? 'active' : '' }}">
                                                    {{ $subcategory->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>

                </div>

                {{-- DROPDOWN FILTER: SEMUA STATUS --}}
                <div class="catalog-dropdown-wrapper" id="catalog-status-wrapper">

                    <button type="button" class="catalog-filter-btn {{ $selectedStatus ? 'active' : '' }}"
                        id="catalog-status-btn" aria-expanded="false" aria-haspopup="true">
                        <span class="catalog-filter-btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                        <span class="catalog-filter-btn-text" id="catalog-status-btn-text">{{ $statusBtnLabel }}</span>
                        <span class="catalog-filter-btn-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </span>
                    </button>

                    {{-- PANEL DROPDOWN STATUS --}}
                    <div class="catalog-filter-dropdown catalog-status-dropdown" id="catalog-status-dropdown" aria-hidden="true">
                        <div class="catalog-dropdown-title">PILIH STATUS</div>

                        <div class="catalog-status-list">
                            {{-- SEMUA STATUS --}}
                            <a href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'category' => request('category'),
                                'subcategory' => request('subcategory'),
                                'per_page' => request('per_page'),
                            ])) }}"
                                class="catalog-status-chip {{ !$selectedStatus ? 'active' : '' }}"
                                data-status="">
                                <span class="chip-label">Semua Status</span>
                                <span class="chip-badge">{{ $totalBooksCount }}</span>
                            </a>

                            {{-- TERSEDIA --}}
                            <a href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'category' => request('category'),
                                'subcategory' => request('subcategory'),
                                'status' => 'Tersedia',
                                'per_page' => request('per_page'),
                            ])) }}"
                                class="catalog-status-chip {{ $selectedStatus === 'Tersedia' ? 'active' : '' }}"
                                data-status="Tersedia">
                                <span class="chip-label">Tersedia</span>
                                <span class="chip-badge">{{ $availableBooksCount }}</span>
                            </a>

                            {{-- SEDANG DIPINJAM --}}
                            <a href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'category' => request('category'),
                                'subcategory' => request('subcategory'),
                                'status' => 'Dipinjam',
                                'per_page' => request('per_page'),
                            ])) }}"
                                class="catalog-status-chip {{ $selectedStatus === 'Dipinjam' ? 'active' : '' }}"
                                data-status="Dipinjam">
                                <span class="chip-label">Sedang Dipinjam</span>
                                <span class="chip-badge">{{ $borrowedBooksCount }}</span>
                            </a>
                        </div>
                    </div>

                </div>

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