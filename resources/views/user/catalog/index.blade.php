@extends('layouts.user')

@section('title', 'Katalog Koleksi Buku - Perpustakaan Tiga Serangkai')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-catalog.css') }}">
@endpush

@section('content')

<div class="user-catalog-page">

    {{-- =====================================================
         HERO BANNER & QUICK SEARCH
    ====================================================== --}}
    <section class="catalog-hero motion-section">
        <div class="catalog-hero-content motion-fade-up">

            <div class="catalog-hero-header">
                <div>
                    <span class="catalog-hero-label">
                        PERPUSTAKAAN TIGA SERANGKAI &bull; ROLE PENGGUNA
                    </span>
                    <h1>
                        Katalog Koleksi Buku
                    </h1>
                    <p>
                        Jelajahi dan temukan seluruh koleksi buku, modul pelajaran, dan literatur yang tersedia untuk Anda.
                    </p>
                </div>

                {{-- STATS PILLS --}}
                <div class="catalog-stats-pills">
                    <div class="stat-pill">
                        <span class="stat-num">{{ $totalBooksCount }}</span>
                        <span class="stat-label">Total Judul</span>
                    </div>
                    <div class="stat-pill">
                        <span class="stat-num">{{ $availableBooksCount }}</span>
                        <span class="stat-label">Siap Dipinjam</span>
                    </div>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <form method="GET" action="{{ route('user.catalog') }}" class="catalog-search-form" id="catalogSearchForm">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="catalog-search-bar">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>

                    <input
                        type="text"
                        name="search"
                        id="catalog-search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan judul buku, nama pengarang, penerbit, atau ISBN..."
                        autocomplete="off"
                    >

                    @if(request('search'))
                        <a href="{{ route('user.catalog', request()->except('search', 'page')) }}" class="search-clear-btn" title="Hapus Pencarian">
                            &times;
                        </a>
                    @endif

                    <button type="submit" class="search-submit-btn">
                        Cari Buku
                    </button>
                </div>
            </form>

        </div>
    </section>



    {{-- =====================================================
         NAVIGASI CHIP KATEGORI
    ====================================================== --}}
    <section class="catalog-category-bar">
        <div class="category-scroll-container">
            {{-- SEMUA KATEGORI --}}
            <a
                href="{{ route('user.catalog', request()->except('category', 'page')) }}"
                class="category-pill {{ !request('category') || request('category') === 'Semua' ? 'active' : '' }}"
            >
                <span>Semua Kategori</span>
                <span class="pill-badge">{{ $totalBooksCount }}</span>
            </a>

            {{-- LOOP DAFTAR KATEGORI --}}
            @foreach($categories as $cat)
            <a
                href="{{ route('user.catalog', array_merge(request()->except('category', 'page'), ['category' => $cat->name])) }}"
                class="category-pill {{ request('category') == $cat->name || request('category') == $cat->id ? 'active' : '' }}"
            >
                <span>{{ $cat->name }}</span>
                @if($cat->books_count > 0)
                    <span class="pill-badge">{{ $cat->books_count }}</span>
                @endif
            </a>
            @endforeach
        </div>
    </section>



    {{-- =====================================================
         TOOLBAR FILTER & PENGURUTAN
    ====================================================== --}}
    <section class="catalog-toolbar">

        {{-- SISI KIRI: INFO JUMLAH & FILTER AKTIF --}}
        <div class="toolbar-left">
            <div class="result-counter">
                Menampilkan <strong>{{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }}</strong> dari <strong>{{ $books->total() }}</strong> buku
            </div>

            @if(request('search') || request('category') || (request('status') && request('status') !== 'Semua'))
            <div class="active-filter-chips">
                @if(request('search'))
                    <span class="chip-item">
                        Pencarian: "<em>{{ request('search') }}</em>"
                        <a href="{{ route('user.catalog', request()->except('search', 'page')) }}" title="Hapus filter pencarian">&times;</a>
                    </span>
                @endif

                @if(request('category') && request('category') !== 'Semua')
                    <span class="chip-item">
                        Kategori: <strong>{{ request('category') }}</strong>
                        <a href="{{ route('user.catalog', request()->except('category', 'page')) }}" title="Hapus filter kategori">&times;</a>
                    </span>
                @endif

                @if(request('status') && request('status') !== 'Semua')
                    <span class="chip-item">
                        Status: <strong>{{ request('status') }}</strong>
                        <a href="{{ route('user.catalog', request()->except('status', 'page')) }}" title="Hapus filter status">&times;</a>
                    </span>
                @endif

                <a href="{{ route('user.catalog') }}" class="chip-reset-all">
                    Reset Semua
                </a>
            </div>
            @endif
        </div>

        {{-- SISI KANAN: DROPDOWN STATUS & SORTING --}}
        <div class="toolbar-right">
            <form method="GET" action="{{ route('user.catalog') }}" id="catalogFilterForm" class="filter-controls-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                {{-- FILTER STATUS --}}
                <div class="select-group">
                    <label for="filter-status">Ketersediaan:</label>
                    <select name="status" id="filter-status" onchange="document.getElementById('catalogFilterForm').submit()">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>

                {{-- SORTING --}}
                <div class="select-group">
                    <label for="filter-sort">Urutkan:</label>
                    <select name="sort" id="filter-sort" onchange="document.getElementById('catalogFilterForm').submit()">
                        <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                        <option value="populer" {{ request('sort') == 'populer' ? 'selected' : '' }}>Stok Terbanyak / Populer</option>
                        <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>Judul (A - Z)</option>
                        <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Judul (Z - A)</option>
                        <option value="tahun" {{ request('sort') == 'tahun' ? 'selected' : '' }}>Tahun Terbit (Terbaru)</option>
                    </select>
                </div>
            </form>
        </div>

    </section>



    {{-- =====================================================
         GRID DAFTAR BUKU
    ====================================================== --}}
    <section class="catalog-grid-section">

        @if($books->count() > 0)

        <div class="user-catalog-grid">
            @foreach($books as $book)
            <article
                class="catalog-book-card motion-book-card"
                style="--motion-delay: {{ ($loop->index % 10) * 45 }}ms"
            >
                {{-- TOMBOL COVER BUKU (MEMBUKA MODAL 3D) --}}
                <button
                    type="button"
                    class="catalog-book-cover user-book-open"
                    title="Klik untuk membuka detail buku"
                    data-title="{{ $book->title }}"
                    data-author="{{ $book->author ?? '-' }}"
                    data-category="{{ $book->category->name ?? '-' }}"
                    data-publisher="{{ $book->publisher ?? '-' }}"
                    data-year="{{ $book->publication_year ?? '-' }}"
                    data-isbn="{{ $book->isbn ?? '-' }}"
                    data-stock="{{ $book->available_stock ?? 0 }}"
                    data-rack="{{ $book->rak ?? ($book->rack->name ?? '-') }}"
                    data-call-number="{{ $book->call_number ?? '-' }}"
                    data-description="{{ $book->description ?? 'Deskripsi atau ringkasan buku belum tersedia saat ini.' }}"
                    data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"
                >
                    @if($book->cover)
                        <img
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            loading="lazy"
                        >
                    @else
                        <div class="user-cover-placeholder">
                            <span>{{ $book->title }}</span>
                        </div>
                    @endif

                    <div class="cover-overlay-hint">
                        <span>Lihat Detail</span>
                    </div>
                </button>

                {{-- INFORMASI KARTU BUKU --}}
                <div class="catalog-book-info">
                    <div class="catalog-card-header">
                        <span class="catalog-card-category">
                            {{ $book->category->name ?? 'Koleksi' }}
                        </span>

                        @if($book->available_stock > 0)
                            <span class="catalog-status-badge available" title="{{ $book->available_stock }} eksemplar tersedia">
                                Tersedia ({{ $book->available_stock }})
                            </span>
                        @else
                            <span class="catalog-status-badge borrowed">
                                Dipinjam
                            </span>
                        @endif
                    </div>

                    <h3 class="catalog-card-title" title="{{ $book->title }}">
                        {{ $book->title }}
                    </h3>

                    <p class="catalog-card-author">
                        {{ $book->author ?? 'Penulis tidak dicantumkan' }}
                    </p>

                    <div class="catalog-card-footer">
                        @if($book->publication_year)
                            <span class="catalog-card-year">
                                📅 {{ $book->publication_year }}
                            </span>
                        @endif

                        @if($book->rak || $book->call_number)
                            <span class="catalog-card-location" title="Lokasi Rak / No. Panggil">
                                📍 {{ $book->rak ?? $book->call_number }}
                            </span>
                        @endif
                    </div>
                </div>

            </article>
            @endforeach
        </div>


        {{-- =====================================================
             PAGINASI
        ====================================================== --}}
        <div class="catalog-pagination-area">
            {{ $books->links('partials.pagination') }}
        </div>

        @else

        {{-- =====================================================
             EMPTY STATE (TIDAK ADA DATA)
        ====================================================== --}}
        <div class="catalog-empty-card">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <line x1="9" y1="9" x2="15" y2="9"></line>
                    <line x1="9" y1="13" x2="13" y2="13"></line>
                </svg>
            </div>
            <h3>Koleksi Buku Tidak Ditemukan</h3>
            <p>
                Tidak ditemukan buku yang cocok dengan pencarian atau filter yang Anda pilih.
            </p>
            <div class="empty-actions">
                <a href="{{ route('user.catalog') }}" class="btn-empty-reset">
                    ↻ Reset Semua Filter
                </a>
            </div>
        </div>

        @endif

    </section>

</div>



{{-- =========================================================
     MODAL DETAIL BUKU & PREVIEW 3D INTERAKTIF
========================================================= --}}
<div
    id="userBookModal"
    class="user-book-modal"
    aria-hidden="true"
>
    <div class="user-book-modal-overlay" id="userBookModalOverlay"></div>

    <div class="user-book-modal-content">

        {{-- TOMBOL CLOSE --}}
        <button
            type="button"
            class="user-book-modal-close"
            id="userBookModalClose"
            aria-label="Tutup Modal"
        >
            &times;
        </button>

        {{-- =================================================
             SISI KIRI: CANVAS BUKU 3D
        ================================================== --}}
        <div class="user-book-3d-section">
            <div class="user-book-3d-hint">
                🖱️ Geser untuk memutar buku 3D
            </div>

            <div class="book-3d-stage" id="book3dStage">
                <div class="book-3d" id="book3d">

                    {{-- MUKA DEPAN --}}
                    <div class="book-face book-front" id="book3dFront">
                        <div class="book-cover-fallback" id="book3dFallback">
                            <span id="book3dFallbackTitle">Buku</span>
                        </div>
                        <img id="book3dCover" src="" alt="Cover buku">
                    </div>

                    {{-- MUKA BELAKANG --}}
                    <div class="book-face book-back">
                        <span id="book3dBackTitle">Perpustakaan Tiga Serangkai</span>
                    </div>

                    {{-- PUNGGUNG BUKU (SPINE) --}}
                    <div class="book-face book-left"></div>

                    {{-- TEPI LEMBARAN HALAMAN --}}
                    <div class="book-face book-right"></div>

                    {{-- TEPI ATAS --}}
                    <div class="book-face book-top"></div>

                    {{-- TEPI BAWAH --}}
                    <div class="book-face book-bottom"></div>

                </div>
            </div>

            <div class="book-3d-controls">
                <button type="button" id="book3dReset">
                    ↻ Reset Sudut
                </button>
                <span>Drag mouse/touch untuk memutar</span>
            </div>
        </div>


        {{-- =================================================
             SISI KANAN: DETAIL METADATA BUKU
        ================================================== --}}
        <div class="user-book-detail">

            <div class="user-book-category">
                <span id="modalBookCategory">KATEGORI</span>
            </div>

            <h2 id="modalBookTitle">Judul Buku</h2>

            <p class="user-book-author" id="modalBookAuthor">
                Penulis
            </p>

            <div class="user-book-info-grid">

                <div class="user-book-info-box">
                    <span>STATUS STOK</span>
                    <strong id="modalBookStock">-</strong>
                </div>

                <div class="user-book-info-box">
                    <span>PENERBIT</span>
                    <strong id="modalBookPublisher">-</strong>
                </div>

                <div class="user-book-info-box">
                    <span>TAHUN TERBIT</span>
                    <strong id="modalBookYear">-</strong>
                </div>

                <div class="user-book-info-box">
                    <span>ISBN</span>
                    <strong id="modalBookIsbn">-</strong>
                </div>

                <div class="user-book-info-box">
                    <span>LOKASI RAK</span>
                    <strong id="modalBookRack">-</strong>
                </div>

                <div class="user-book-info-box">
                    <span>NO. PANGGIL</span>
                    <strong id="modalBookCallNumber">-</strong>
                </div>

            </div>

            <div class="user-book-synopsis">
                <span>SINOPSIS / DESKRIPSI</span>
                <p id="modalBookDescription">
                    Deskripsi buku belum tersedia.
                </p>
            </div>

            <div class="user-book-actions">
                <button type="button" class="user-book-close-btn" id="modalCloseActionBtn">
                    Tutup
                </button>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script src="{{ asset('js/user-catalog.js') }}"></script>
<script src="{{ asset('js/userBook3d.js') }}"></script>
@endpush

@endsection
