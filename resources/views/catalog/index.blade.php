@extends(auth()->check() && auth()->user()->role === 'user' ? 'layouts.user' : 'layouts.app')

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

            <h1>
                Katalog Buku
            </h1>

            <p>
                Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                kategori, atau ketersediaan stok.
            </p>

        </div>

    </div>


    {{-- =========================
         FILTER FORM
    ========================= --}}

    <form
        method="GET"
        action="{{ route('catalog') }}"
        class="filters"
        id="catalog-filter-form">

        {{-- SEARCH --}}
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul atau pengarang..."
        >


        {{-- =========================
             CATEGORY DROPDOWN
        ========================= --}}

        @php
        $selectedCategoryId = request('category');
        $selectedSubcategoryId = request('subcategory');

        $selectedCategory = $categories->firstWhere(
            'id',
            $selectedCategoryId
        );

        $selectedSubcategory = null;

        if ($selectedCategory && $selectedSubcategoryId) {
            $selectedSubcategory = $selectedCategory->subcategories
                ->firstWhere('id', $selectedSubcategoryId);
        }
        @endphp


        <div class="catalog-category-dropdown">

            {{-- TOMBOL UTAMA --}}
            <button
                type="button"
                class="catalog-category-trigger">

                <span>
                    @if($selectedSubcategory)
                    {{ $selectedSubcategory->name }}
                    @elseif($selectedCategory)
                    {{ $selectedCategory->name }}
                    @else
                    Semua Kategori
                    @endif
                </span>

                <span class="catalog-category-arrow">
                    ⌄
                </span>

            </button>


            {{-- MENU KATEGORI --}}
            <div class="catalog-category-menu">

                {{-- SEMUA KATEGORI --}}
                <a
                    href="{{ route('catalog', array_filter([
                        'search' => request('search'),
                        'status' => request('status'),
                        'per_page' => request('per_page'),
                    ])) }}"
                    class="catalog-category-option
                    {{ !$selectedCategoryId && !$selectedSubcategoryId ? 'active' : '' }}">

                    Semua Kategori

                </a>


                @foreach($categories as $category)

                <div class="catalog-category-item">

                    {{-- KATEGORI UTAMA --}}
                    <a
                        href="{{ route('catalog', array_filter([
                            'search' => request('search'),
                            'category' => $category->id,
                            'status' => request('status'),
                            'per_page' => request('per_page'),
                        ])) }}"
                        class="catalog-category-option
                        {{ (string) $selectedCategoryId === (string) $category->id && !$selectedSubcategoryId ? 'active' : '' }}">

                        <span>
                            {{ $category->name }}
                        </span>

                        @if($category->subcategories->count())
                        <span class="catalog-category-arrow">
                            ›
                        </span>
                        @endif

                    </a>


                    {{-- SUBKATEGORI (DROPDOWN LEVEL 2) --}}
                    @if($category->subcategories->count())

                    <div class="catalog-subcategory-menu">

                        {{-- SEMUA SUBKATEGORI DALAM KATEGORI INI --}}
                        <a
                            href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'category' => $category->id,
                                'status' => request('status'),
                                'per_page' => request('per_page'),
                            ])) }}"
                            class="catalog-subcategory-option
                            {{ (string) $selectedCategoryId === (string) $category->id && !$selectedSubcategoryId ? 'active' : '' }}">

                            Semua {{ $category->name }}

                        </a>


                        {{-- SUBKATEGORI --}}
                        @foreach($category->subcategories as $subcategory)

                        <a
                            href="{{ route('catalog', array_filter([
                                'search' => request('search'),
                                'category' => $category->id,
                                'subcategory' => $subcategory->id,
                                'status' => request('status'),
                                'per_page' => request('per_page'),
                            ])) }}"
                            class="catalog-subcategory-option
                            {{ (string) $selectedSubcategoryId === (string) $subcategory->id ? 'active' : '' }}">

                            └─ {{ $subcategory->name }}

                        </a>

                        @endforeach

                    </div>

                    @endif

                </div>

                @endforeach

            </div>

        </div>


        {{-- =========================
             STATUS
        ========================= --}}

        <select
            name="status"
            onchange="this.form.submit()">

            <option value="">
                Semua Status
            </option>

            <option
                value="Tersedia"
                {{ request('status') === 'Tersedia' ? 'selected' : '' }}>
                Tersedia
            </option>

            <option
                value="Dipinjam"
                {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>
                Sedang Dipinjam
            </option>

        </select>

    </form>


    {{-- =========================
         BOOK GRID
    ========================= --}}

    <div class="book-grid">

        @forelse($books as $book)

        <div
            class="book-card"
            data-id="{{ $book->id }}"
            data-title="{{ $book->title ?? $book->judul_buku ?? '-' }}"
            data-author="{{ $book->author ?? $book->penulis ?? '-' }}"
            data-category="{{ $book->category->name ?? '-' }}"
            data-stock="{{ $book->available_stock ?? $book->stok ?? 0 }}"
            data-status="{{ ($book->available_stock ?? $book->stok ?? 0) > 0 ? 'Tersedia' : 'Dipinjam' }}"
            data-description="{{ $book->description ?? 'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.' }}"
            data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"
            data-publisher="{{ $book->publisher ?? '-' }}"
            data-year="{{ $book->publication_year ?? '-' }}"
            data-isbn="{{ $book->isbn ?? '-' }}"
            data-call-number="{{ $book->call_number ?? '-' }}">

            {{-- =========================
                 COVER
            ========================= --}}

            <div class="book-cover {{ $book->cover ? 'has-image' : '' }}">

                @if($book->cover)
                <img
                    src="{{ asset('storage/' . $book->cover) }}"
                    alt="{{ $book->title ?? $book->judul_buku ?? 'Cover' }}"
                    class="book-cover-img">
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

                <strong>
                    {{ $book->title ?? $book->judul_buku ?? '-' }}
                </strong>

                <span>
                    {{ $book->author ?? $book->penulis ?? '-' }}
                </span>


                <div class="book-meta">

                    <span>
                        {{ $book->category->name ?? '-' }}
                    </span>

                    @if(($book->available_stock ?? $book->stok ?? 0) > 0)
                    <span class="book-status available">
                        Tersedia ({{ $book->available_stock ?? $book->stok ?? 0 }})
                    </span>
                    @else
                    <span class="book-status borrowed">
                        Dipinjam
                    </span>
                    @endif

                </div>

            </div>

        </div>

        @empty

        <div class="catalog-empty">
            Belum ada koleksi buku yang sesuai dengan pencarian atau filter.
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
    id="book-modal">

    <div class="catalog-modal-container">

        {{-- =========================
             CLOSE BUTTON
        ========================= --}}

        <button
            type="button"
            class="catalog-modal-close"
            id="modal-close"
            aria-label="Tutup">
            &times;
        </button>


        <div class="catalog-modal-body">

            {{-- =========================
                 MODAL COVER
            ========================= --}}

            <div class="catalog-modal-cover">

                {{-- COVER DEFAULT --}}
                <div
                    class="catalog-modal-book"
                    id="modal-book-box">

                    <div class="catalog-modal-brand">
                        TIGA SERANGKAI
                    </div>

                    <div
                        class="catalog-modal-book-title"
                        id="modal-cover-title">
                    </div>

                    <div class="catalog-modal-book-footer">
                        PERPUSTAKAAN
                    </div>

                </div>

                {{-- COVER IMAGE --}}
                <img
                    id="modal-cover-image"
                    src="#"
                    alt="Cover Buku"
                    style="
                        display: none;
                        width: 170px;
                        height: 235px;
                        object-fit: cover;
                        border-radius: 10px;
                        box-shadow:
                            -6px 12px 25px
                            rgba(0,0,0,0.20);
                    ">

            </div>


            {{-- =========================
                 MODAL INFORMATION
            ========================= --}}

            <div class="catalog-modal-info">

                {{-- BADGES --}}
                <div class="catalog-modal-badges">

                    <span
                        class="catalog-modal-category"
                        id="modal-category">
                    </span>

                    <span
                        class="catalog-modal-status"
                        id="modal-status">
                    </span>

                </div>


                {{-- TITLE --}}
                <h2 id="modal-title"></h2>


                {{-- AUTHOR --}}
                <div
                    class="catalog-modal-author"
                    id="modal-author">
                </div>


                {{-- DESCRIPTION --}}
                <div class="catalog-modal-description-title">
                    Sinopsis / Deskripsi
                </div>

                <div
                    class="catalog-modal-description"
                    id="modal-description">
                    Informasi buku belum tersedia.
                </div>


                {{-- =========================
                     BOOK META
                ========================= --}}

                <div class="catalog-modal-meta">

                    <div>
                        <span>Stok Tersedia</span>
                        <strong id="modal-stock">-</strong>
                    </div>

                    <div>
                        <span>Penerbit</span>
                        <strong id="modal-publisher">-</strong>
                    </div>

                    <div>
                        <span>Tahun Terbit</span>
                        <strong id="modal-year">-</strong>
                    </div>

                    <div>
                        <span>No. Panggil</span>
                        <strong id="modal-call-number">-</strong>
                    </div>

                    <div>
                        <span>ISBN</span>
                        <strong id="modal-isbn">-</strong>
                    </div>

                </div>


                {{-- =========================
                     ACTION
                ========================= --}}

                <div class="catalog-modal-actions" style="display: flex; gap: 10px; align-items: center; justify-content: flex-end;">

                    @if(auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.reservations.store') }}" method="POST" id="catalogReservationForm" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="book_id" id="catalogModalReservationBookId" value="">
                        <button
                            type="submit"
                            id="catalog-btn-reservasi"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 0 16px; border-radius: 10px; background: #0f4c4c; color: #ffffff; border: none; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='#0a3737'"
                            onmouseout="this.style.background='#0f4c4c'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Buat Reservasi
                        </button>
                    </form>
                    @endif

                    <button
                        type="button"
                        class="catalog-modal-button"
                        id="modal-action">
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
