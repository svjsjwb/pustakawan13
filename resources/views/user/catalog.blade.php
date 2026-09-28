@extends('layouts.user')
@section('title', 'Katalog Koleksi Buku – Perpustakaan Digital')
@section('content_class', 'catalog-content-override')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-catalog.css') }}">
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
@endpush

@section('content')

{{-- 1. HERO SECTION DENGAN STAT CARDS & COUNTER ANIMATION (EMERALD GREEN) --}}
<section class="saas-hero">
    <div class="saas-hero-bg-pattern"></div>
    <div class="saas-hero-content">
        <div class="saas-hero-text">
            <h1 class="saas-hero-title">
                Katalog <span class="highlight">Koleksi Buku</span>
            </h1>
            <p class="saas-hero-desc">
                Jelajahi beragam koleksi buku pilihan dari berbagai kategori untuk menemukan bacaan yang sesuai dengan minat dan kebutuhan Anda.
            </p>
        </div>

        {{-- Ringkasan total koleksi --}}
        <div class="saas-stats-grid">
            <div class="saas-stat-card">
                <div class="saas-stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="saas-stat-val counter" data-target="{{ $stats['total_titles'] ?? 0 }}">0</div>
                <div class="saas-stat-label">Total Koleksi Buku</div>
            </div>
        </div>
    </div>
</section>

{{-- 2. SEARCH BAR (60px) DENGAN FOCUS GLOW & GRADIENT BUTTON --}}
<div class="saas-search-container">
    <form method="GET" action="{{ route('user.catalog') }}" class="saas-search-form" id="searchFilterForm">
        <svg class="saas-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text"
               name="search"
               id="saasSearchInput"
               class="saas-search-input"
               value="{{ $search }}"
               placeholder="Cari judul buku, penulis, penerbit, atau ISBN..."
               autocomplete="off">

        {{-- Preserve category & status in hidden inputs if set --}}
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif

        <button type="submit" class="saas-search-submit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span>Cari Buku</span>
        </button>
    </form>
</div>

{{-- 3. FILTER KATEGORI BERTINGKAT & STATUS DROPDOWN --}}
<div class="saas-filters-row">
    {{-- Category dropdowns --}}
    <div class="saas-category-dropdowns">
        {{-- Semua Kategori Button --}}
        @php
            $isAllActive = empty($mainCategory) && empty($subCategory);
        @endphp
        <div class="pd-select-wrapper pd-category-wrapper {{ $isAllActive ? 'has-active-category' : '' }}" id="wrapperAllCategories">
            <button type="button"
                    class="pd-trigger {{ $isAllActive ? 'has-value' : '' }}"
                    id="triggerAllCategories"
                    onclick="selectSubcategory('', '')"
                    aria-label="Semua Kategori">
                <span class="pd-trigger-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </span>
                <span class="pd-trigger-label">Semua Kategori</span>
                <span class="pd-trigger-count">{{ $stats['total_titles'] ?? 0 }}</span>
            </button>
        </div>

        {{-- Dropdowns for Pendidikan, Anak-Anak, Remaja, Dewasa --}}
        @foreach($catalogHierarchy as $groupName => $subcategories)
            @php
                $isGroupActive = ($mainCategory === $groupName);
                $activeSubName = $isGroupActive ? $subCategory : '';
                $groupTotal = $mainCounts[$groupName] ?? 0;
            @endphp

            <div class="pd-select-wrapper pd-category-wrapper {{ $isGroupActive ? 'has-active-category' : '' }}"
                 data-category-group="{{ $groupName }}"
                 id="catWrapper_{{ Str::slug($groupName) }}">
                <button type="button"
                        class="pd-trigger {{ $isGroupActive ? 'has-value' : '' }}"
                        aria-haspopup="true"
                        aria-expanded="false"
                        id="catTrigger_{{ Str::slug($groupName) }}">
                    <span class="pd-trigger-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span class="pd-trigger-label" id="catLabel_{{ Str::slug($groupName) }}">
                        @if($isGroupActive && $activeSubName)
                            {{ $groupName }}: {{ $activeSubName }} ✓
                        @else
                            {{ $groupName }}
                        @endif
                    </span>
                    <span class="pd-trigger-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </button>

                <div class="pd-panel pd-category-panel" role="menu" aria-label="{{ $groupName }}">
                    <div class="pd-panel-header">{{ $groupName }} ({{ $groupTotal }} Buku)</div>
                    @foreach($subcategories as $subKey => $subLabel)
                        @php
                            $isItemActive = ($isGroupActive && $subCategory === $subKey);
                            $count = $subCounts[$groupName . '::' . $subKey] ?? 0;
                        @endphp
                        <button type="button"
                                class="pd-item pd-subcat-btn {{ $isItemActive ? 'is-selected active-subcat' : '' }}"
                                data-main="{{ $groupName }}"
                                data-sub="{{ $subKey }}"
                                onclick="selectSubcategory('{{ $groupName }}', '{{ $subKey }}', event)">
                            <span class="pd-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16M4 12h16M4 18h10"></path>
                                </svg>
                            </span>
                            <span class="pd-item-label">{{ $subLabel }}</span>
                            <span class="pd-item-check"><span class="check-text">✓</span></span>
                            <span class="pd-category-count">{{ $count }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Premium Custom Dropdowns: Status & Sorting --}}
    <div class="saas-dropdowns-group">

        {{-- STATUS FILTER DROPDOWN --}}
        <div class="pd-select-wrapper pd-filter-control" id="pdStatusWrapper"
             data-filter-key="status"
             data-current-value="{{ $status }}">
            <button type="button" class="pd-trigger {{ $status ? 'has-value' : '' }}" id="pdStatusTrigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon" id="pdStatusIcon">
                    @if($status === 'tersedia')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @elseif($status === 'habis')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    @endif
                </span>
                <span class="pd-trigger-label" id="pdStatusLabel">
                    @if($status === 'tersedia') Tersedia
                    @elseif($status === 'habis') Habis
                    @else Semua Status
                    @endif
                </span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <div class="pd-panel" role="listbox" aria-label="Filter Status Buku" id="pdStatusPanel">
                <div class="pd-item {{ empty($status) ? 'is-selected' : '' }}" role="option" data-value="" data-label="Semua Status" onclick="selectStatus('', 'Semua Status')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </span>
                    <span class="pd-item-label">Semua Status</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
                <div class="pd-item status-green {{ $status === 'tersedia' ? 'is-selected' : '' }}" role="option" data-value="tersedia" data-label="Tersedia" onclick="selectStatus('tersedia', 'Tersedia')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                    <span class="pd-item-label">Tersedia</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
                <div class="pd-item status-red {{ $status === 'habis' ? 'is-selected' : '' }}" role="option" data-value="habis" data-label="Habis" onclick="selectStatus('habis', 'Habis')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                    <span class="pd-item-label">Habis</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
            </div>
        </div>

        {{-- SORT FILTER DROPDOWN --}}
        <div class="pd-select-wrapper pd-filter-control" id="pdSortWrapper"
             data-filter-key="sort"
             data-current-value="{{ $activeSort }}">
            <button type="button" class="pd-trigger {{ ($activeSort && $activeSort !== 'terbaru') ? 'has-value' : '' }}" id="pdSortTrigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                </span>
                <span class="pd-trigger-label" id="pdSortLabel">
                    @if($activeSort === 'terlama') Terlama
                    @elseif($activeSort === 'az') Judul (A-Z)
                    @elseif($activeSort === 'za') Judul (Z-A)
                    @else Terbaru
                    @endif
                </span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <div class="pd-panel" role="listbox" aria-label="Urutkan Berdasarkan" id="pdSortPanel">
                <div class="pd-item {{ $activeSort === 'terbaru' ? 'is-selected' : '' }}" role="option" data-value="terbaru" data-label="Terbaru" onclick="selectSort('terbaru', 'Terbaru')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    </span>
                    <span class="pd-item-label">Terbaru</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
                <div class="pd-item {{ $activeSort === 'terlama' ? 'is-selected' : '' }}" role="option" data-value="terlama" data-label="Terlama" onclick="selectSort('terlama', 'Terlama')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                    <span class="pd-item-label">Terlama</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
                <div class="pd-divider"></div>
                <div class="pd-item {{ $activeSort === 'az' ? 'is-selected' : '' }}" role="option" data-value="az" data-label="Judul (A-Z)" onclick="selectSort('az', 'Judul (A-Z)')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l4-4 4 4"/><path d="M7 4v8"/><path d="M11 17h6l-6 4h6"/></svg>
                    </span>
                    <span class="pd-item-label">Judul (A–Z)</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
                <div class="pd-item {{ $activeSort === 'za' ? 'is-selected' : '' }}" role="option" data-value="za" data-label="Judul (Z-A)" onclick="selectSort('za', 'Judul (Z-A)')">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h6l-6 4h6"/><path d="M11 4v8l4-4 4 4V4"/></svg>
                    </span>
                    <span class="pd-item-label">Judul (Z–A)</span>
                    <span class="pd-item-check"><span class="check-text">✓</span></span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Result Meta / Reset indicator (AJAX Container) --}}
<div class="saas-active-meta" id="catalogMetaContainer">
    @include('user.partials.catalog-meta')
</div>

{{-- 4. 5-COLUMN RESPONSIVE BOOK GRID (AJAX Container) --}}
<div id="catalogGridContainer">
    @include('user.partials.catalog-grid')
</div>


{{-- ==========================================================================
     5. MODAL DETAIL BUKU (1000px) DENGAN 3D BUKU SOLID 6 SISI (36px)
     ========================================================================== --}}
<div class="saas-modal-backdrop" id="saasBookModalBackdrop" onclick="handleBackdropClick(event)">
    <div class="saas-modal-dialog" id="saasBookModalDialog" role="dialog" aria-modal="true">
        {{-- Favorite action; modal closes through Kembali, overlay, or Escape. --}}
        <button type="button" class="saas-btn-favorite-modal saas-modal-favorite-top" id="modalFavBtn" onclick="toggleModalFavorite()" title="Tambah / Hapus Favorit" aria-label="Tambah / Hapus Favorit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </button>

        <div class="saas-modal-layout">
            {{-- SISI KIRI: BUKU 3D SOLID 6 SISI 360° DRAG MOUSE --}}
            <div class="saas-modal-3d-stage">
                <div class="saas-3d-hint">
                    <span>🔄</span> Drag mouse untuk memutar 360°
                </div>

                {{-- 3D Book Viewport --}}
                <div class="saas-book-viewport" id="book3dViewport">
                    <div class="saas-book-object" id="book3dObject">
                        {{-- 1. Front Cover (Z = +18px) --}}
                        <div class="saas-face-front">
                            <img id="modalBookCoverImg" src="" alt="Book Cover">
                            <div id="modalBookCoverPh" class="saas-card-cover-fallback" style="display:none; height:100%;">
                                <span class="fallback-letter" id="modalBookCoverPhLetter">B</span>
                                <span class="fallback-title" id="modalBookCoverPhTitle"></span>
                            </div>
                        </div>

                        {{-- 2. Back Cover (Z = -18px) --}}
                        <div class="saas-face-back">
                            <div>
                                <div style="font-weight: 800; font-size: 11px; color: #a8d5d1; margin-bottom: 8px;">PERPUSTAKAAN DIGITAL</div>
                                <div class="blurb" id="modalBookBackBlurb">
                                    Buku koleksi digital resmi dengan standar indeksasi dan katalogisasi terpadu.
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 8.5px; color: #a8d5d1; opacity: 0.8;">OFFICIAL DIGITAL EDITION</div>
                                <div class="barcode-mock"></div>
                            </div>
                        </div>

                        {{-- 3. Spine (Left side, thickness 36px) --}}
                        <div class="saas-face-spine">
                            <span class="saas-spine-text" id="modalBookSpineText">PUSTAKAWAN DIGITAL</span>
                        </div>

                        {{-- 4. Pages Edge (Right side, thickness 36px) --}}
                        <div class="saas-face-pages-right"></div>

                        {{-- 5. Top Edge (Thickness 36px) --}}
                        <div class="saas-face-pages-top"></div>

                        {{-- 6. Bottom Edge (Thickness 36px) --}}
                        <div class="saas-face-pages-bottom"></div>
                    </div>

                    {{-- Contact Shadow Underneath --}}
                    <div class="saas-book-contact-shadow" id="book3dShadow"></div>
                </div>

                {{-- 3D Reset Button --}}
                <button type="button" class="saas-reset-3d-btn" onclick="reset3dBookRotation()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                    <span>Reset Sudut 3D</span>
                </button>
            </div>

            {{-- SISI KANAN: METADATA BUKU & TOMBOL AKSI --}}
            <div class="saas-modal-detail-pane">
                <span class="saas-modal-cat-tag" id="modalBookCategory">Kategori</span>
                <h2 class="saas-modal-book-title" id="modalBookTitle">Judul Buku</h2>
                <div class="saas-modal-book-author">
                    <span id="modalBookAuthor">Penulis Buku</span>
                </div>

                {{-- Meta Grid --}}
                <div class="saas-modal-meta-grid">
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Penerbit</span>
                        <span class="saas-modal-meta-val" id="modalBookPublisher">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Tahun Terbit</span>
                        <span class="saas-modal-meta-val" id="modalBookYear">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">ISBN</span>
                        <span class="saas-modal-meta-val" id="modalBookIsbn" style="font-family: monospace;">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Lokasi Rak</span>
                        <span class="saas-modal-meta-val" id="modalBookRak">-</span>
                    </div>
                    <div class="saas-modal-meta-item" style="grid-column: 1 / -1;">
                        <span class="saas-modal-meta-label">Ketersediaan Stok</span>
                        <span class="saas-modal-meta-val" id="modalBookStock" style="color: var(--primary); font-size: 15px;">-</span>
                    </div>
                </div>

                {{-- Sinopsis --}}
                <div class="saas-modal-synopsis">
                    <div class="saas-modal-synopsis-label">Sinopsis Buku</div>
                    <div class="saas-modal-synopsis-text" id="modalBookDescription">
                        Deskripsi lengkap buku akan ditampilkan di sini.
                    </div>
                </div>

                {{-- Action Buttons: Reservasi, Pinjam, Kembali --}}
                <div class="saas-modal-actions">
                    <form method="POST" action="{{ route('user.reservations.store') }}" id="modalReserveForm">
                        @csrf
                        <input type="hidden" name="book_id" id="modalReserveBookId">
                        <button type="submit" class="saas-btn-primary" id="modalReserveBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Reservasi Sekarang</span>
                        </button>
                        <div class="saas-reservation-feedback" id="modalReserveFeedback" hidden>
                            Buku telah masuk ke daftar reservasi Anda.
                            <a href="{{ route('reservations.index') }}">Lihat Reservasi</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('user.loans.store') }}" id="modalBorrowForm">
                        @csrf
                        <input type="hidden" name="book_id" id="modalBorrowBookId">
                        <button type="submit" class="saas-btn-primary saas-btn-borrow" id="modalBorrowBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>Pinjam Sekarang</span>
                        </button>
                    </form>

                    {{-- Tombol Kembali --}}
                    <button type="button" class="saas-btn-secondary" onclick="closeCatalogModal()">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Kembali ke Katalog</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


{{-- ==========================================================================
     SCRIPTS: ANIMASI COUNTER, DRAG BUKU 3D SOLID 360°, MODAL LOGIC
     ========================================================================== --}}
@push('scripts')
{{-- ── Premium Dropdown Engine ──────────────────────────────────────────── --}}
<script>
(function() {
    'use strict';

    // Backdrop singleton
    let backdrop = document.createElement('div');
    backdrop.className = 'pd-backdrop';
    document.body.appendChild(backdrop);

    let openWrapper = null;

    function closeAll() {
        document.querySelectorAll('.pd-select-wrapper.is-open').forEach(function(w) {
            w.classList.remove('is-open');
            var t = w.querySelector('.pd-trigger');
            if (t) { t.classList.remove('is-open'); t.setAttribute('aria-expanded', 'false'); }
        });
        backdrop.classList.remove('active');
        openWrapper = null;
    }

    function openWrapper_(wrapper) {
        if (openWrapper && openWrapper !== wrapper) closeAll();
        wrapper.classList.add('is-open');
        var trigger = wrapper.querySelector('.pd-trigger');
        if (trigger) { trigger.classList.add('is-open'); trigger.setAttribute('aria-expanded', 'true'); }
        backdrop.classList.add('active');
        openWrapper = wrapper;

        // Viewport edge detection — right-align if panel would overflow
        var panel = wrapper.querySelector('.pd-panel');
        if (panel) {
            panel.classList.remove('align-right');
            var rect = panel.getBoundingClientRect();
            if (rect.right > window.innerWidth - 16) {
                panel.classList.add('align-right');
            }
        }
    }

    backdrop.addEventListener('click', closeAll);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAll();
    });

    function initWrapper(wrapper) {
        var trigger  = wrapper.querySelector('.pd-trigger');
        var panel    = wrapper.querySelector('.pd-panel');
        var items    = wrapper.querySelectorAll('.pd-item');
        var filterKey = wrapper.dataset.filterKey;

        if (!trigger || !panel) return;

        // Toggle open/close
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (wrapper.classList.contains('is-open')) {
                closeAll();
            } else {
                openWrapper_(wrapper);
            }
        });

        // Item click — navigate
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!filterKey) return;
                var value = item.dataset.value;
                closeAll();
                applyFilter(filterKey, value);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.pd-select-wrapper').forEach(initWrapper);
    });
})();
</script>
<script>
// ── 1. Counter Animation for Stat Cards ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');
    const speed = 40;

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = Math.max(1, Math.ceil(target / speed));

            if (count < target) {
                counter.innerText = Math.min(target, count + inc);
                setTimeout(updateCount, 25);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
});

// ── 2. Realtime AJAX Catalog Filter Engine (SaaS 2026) ─────────────────────────
window.catalogState = {
    search: "{{ $search }}",
    main_category: "{{ $mainCategory }}",
    sub_category: "{{ $subCategory }}",
    status: "{{ $status }}",
    sort: "{{ $activeSort }}",
    page: 1
};

function fetchCatalog(newParams = {}, pushHistory = true) {
    Object.assign(window.catalogState, newParams);

    const gridContainer = document.getElementById('catalogGridContainer');
    const metaContainer = document.getElementById('catalogMetaContainer');
    if (gridContainer) gridContainer.classList.add('loading');

    const params = new URLSearchParams();
    if (window.catalogState.search) params.set('search', window.catalogState.search);
    if (window.catalogState.main_category) params.set('main_category', window.catalogState.main_category);
    if (window.catalogState.sub_category) params.set('sub_category', window.catalogState.sub_category);
    if (window.catalogState.status) params.set('status', window.catalogState.status);
    if (window.catalogState.sort && window.catalogState.sort !== 'terbaru') params.set('sort', window.catalogState.sort);
    if (window.catalogState.page && window.catalogState.page > 1) params.set('page', window.catalogState.page);

    params.set('ajax', '1');

    const catalogUrl = "{{ route('user.catalog') }}";
    fetch(catalogUrl + '?' + params.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (gridContainer && data.grid_html) {
                gridContainer.innerHTML = data.grid_html;
            }
            if (metaContainer && data.meta_html) {
                metaContainer.innerHTML = data.meta_html;
            }

            // Sync visual states for subcategories
            syncCategoryUiState(window.catalogState.main_category, window.catalogState.sub_category);

            // Sync visual state for status dropdown
            syncStatusUiState(window.catalogState.status);

            // Sync visual state for sort dropdown
            syncSortUiState(window.catalogState.sort);

            // Update browser URL
            if (pushHistory) {
                params.delete('ajax');
                const cleanQuery = params.toString();
                const newUrl = window.location.pathname + (cleanQuery ? ('?' + cleanQuery) : '');
                window.history.pushState(window.catalogState, '', newUrl);
            }
        }
    })
    .catch(err => {
        console.error('Error filtering catalog:', err);
    })
    .finally(() => {
        if (gridContainer) gridContainer.classList.remove('loading');
    });
}

function selectSubcategory(mainCategory, subCategory, e) {
    if (e) e.stopPropagation();

    // Close any open dropdowns
    document.querySelectorAll('.pd-select-wrapper.is-open').forEach(w => {
        w.classList.remove('is-open');
        w.querySelector('.pd-trigger')?.classList.remove('is-open');
    });
    const bd = document.querySelector('.pd-backdrop');
    if (bd) bd.classList.remove('active');

    // Only one subcategory can be active! Clean old filters and query database
    fetchCatalog({
        main_category: mainCategory,
        sub_category: subCategory,
        page: 1
    });
}

function selectStatus(status, label) {
    // Close open dropdowns
    document.querySelectorAll('.pd-select-wrapper.is-open').forEach(w => w.classList.remove('is-open'));
    const bd = document.querySelector('.pd-backdrop');
    if (bd) bd.classList.remove('active');

    fetchCatalog({ status: status, page: 1 });
}

function selectSort(sort, label) {
    // Close open dropdowns
    document.querySelectorAll('.pd-select-wrapper.is-open').forEach(w => w.classList.remove('is-open'));
    const bd = document.querySelector('.pd-backdrop');
    if (bd) bd.classList.remove('active');

    fetchCatalog({ sort: sort, page: 1 });
}

function resetAllCatalogFilters() {
    const searchInput = document.getElementById('saasSearchInput');
    if (searchInput) searchInput.value = '';

    fetchCatalog({
        search: '',
        main_category: '',
        sub_category: '',
        status: '',
        sort: 'terbaru',
        page: 1
    });
}

function clearCategoryFilter() {
    fetchCatalog({
        main_category: '',
        sub_category: '',
        page: 1
    });
}

function clearStatusFilter() {
    fetchCatalog({
        status: '',
        page: 1
    });
}

function syncCategoryUiState(mainCat, subCat) {
    // 1. Remove active state from all subcategory buttons
    document.querySelectorAll('.pd-subcat-btn').forEach(btn => {
        btn.classList.remove('is-selected', 'active-subcat');
    });

    // 2. Remove active state from all category wrappers and reset trigger labels
    const groups = ['Pendidikan', 'Anak-Anak', 'Remaja', 'Dewasa'];
    groups.forEach(g => {
        const slug = g.toLowerCase().replace(/[^a-z0-9]/g, '-');
        const wrapper = document.getElementById('catWrapper_' + slug);
        const trigger = document.getElementById('catTrigger_' + slug);
        const label = document.getElementById('catLabel_' + slug);

        if (wrapper) wrapper.classList.remove('has-active-category');
        if (trigger) trigger.classList.remove('has-value');
        if (label) label.textContent = g;
    });

    const allCatWrapper = document.getElementById('wrapperAllCategories');
    const allCatTrigger = document.getElementById('triggerAllCategories');

    // 3. If a subcategory is selected, highlight only that one
    if (mainCat && subCat) {
        if (allCatWrapper) allCatWrapper.classList.remove('has-active-category');
        if (allCatTrigger) allCatTrigger.classList.remove('has-value');

        const activeBtn = document.querySelector(`.pd-subcat-btn[data-main="${mainCat}"][data-sub="${subCat}"]`);
        if (activeBtn) {
            activeBtn.classList.add('is-selected', 'active-subcat');
        }

        const slug = mainCat.toLowerCase().replace(/[^a-z0-9]/g, '-');
        const activeWrapper = document.getElementById('catWrapper_' + slug);
        const activeTrigger = document.getElementById('catTrigger_' + slug);
        const activeLabel = document.getElementById('catLabel_' + slug);

        if (activeWrapper) activeWrapper.classList.add('has-active-category');
        if (activeTrigger) activeTrigger.classList.add('has-value');
        if (activeLabel) activeLabel.textContent = `${mainCat}: ${subCat} ✓`;
    } else {
        // "Semua Kategori" is active
        if (allCatWrapper) allCatWrapper.classList.add('has-active-category');
        if (allCatTrigger) allCatTrigger.classList.add('has-value');
    }
}

function syncStatusUiState(status) {
    const trigger = document.getElementById('pdStatusTrigger');
    const label = document.getElementById('pdStatusLabel');
    if (label) {
        if (status === 'tersedia') label.textContent = 'Tersedia';
        else if (status === 'habis') label.textContent = 'Habis';
        else label.textContent = 'Semua Status';
    }
    if (trigger) {
        trigger.classList.toggle('has-value', Boolean(status));
    }
    document.querySelectorAll('#pdStatusPanel .pd-item').forEach(item => {
        const val = item.getAttribute('data-value') || '';
        item.classList.toggle('is-selected', val === (status || ''));
    });
}

function syncSortUiState(sort) {
    const trigger = document.getElementById('pdSortTrigger');
    const label = document.getElementById('pdSortLabel');
    if (label) {
        if (sort === 'terlama') label.textContent = 'Terlama';
        else if (sort === 'az') label.textContent = 'Judul (A-Z)';
        else if (sort === 'za') label.textContent = 'Judul (Z-A)';
        else label.textContent = 'Terbaru';
    }
    if (trigger) {
        trigger.classList.toggle('has-value', Boolean(sort && sort !== 'terbaru'));
    }
    document.querySelectorAll('#pdSortPanel .pd-item').forEach(item => {
        const val = item.getAttribute('data-value') || '';
        item.classList.toggle('is-selected', val === (sort || 'terbaru'));
    });
}

// Search hanya berjalan saat klik tombol "Cari Buku" (form submit), TIDAK saat mengetik
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('searchFilterForm');
    const searchInput = document.getElementById('saasSearchInput');

    // Submit form via AJAX saat klik "Cari Buku"
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchCatalog({ search: searchInput.value.trim(), page: 1 });
        });
    }

    // Intercept pagination clicks inside gridContainer
    const gridContainer = document.getElementById('catalogGridContainer');
    if (gridContainer) {
        gridContainer.addEventListener('click', (e) => {
            const link = e.target.closest('.ucat-pagination-wrapper a, .pagination a');
            if (link && link.href) {
                e.preventDefault();
                const url = new URL(link.href);
                const pageNum = url.searchParams.get('page') || 1;
                fetchCatalog({ page: pageNum });
                window.scrollTo({ top: 350, behavior: 'smooth' });
            }
        });
    }
});

// Handle browser Back/Forward
window.addEventListener('popstate', (e) => {
    if (e.state) {
        fetchCatalog(e.state, false);
    } else {
        const url = new URL(window.location.href);
        fetchCatalog({
            search: url.searchParams.get('search') || '',
            main_category: url.searchParams.get('main_category') || '',
            sub_category: url.searchParams.get('sub_category') || '',
            status: url.searchParams.get('status') || '',
            sort: url.searchParams.get('sort') || 'terbaru',
            page: url.searchParams.get('page') || 1,
        }, false);
    }
});

// ── 3. Solid 3D Book 360° Drag Rotation Engine ──────────────────────────────
let currentRotY = -25;
let currentRotX = 10;
let isDragging = false;
let startPointerX = 0;
let startPointerY = 0;
const bookObject = document.getElementById('book3dObject');
const bookViewport = document.getElementById('book3dViewport');
const bookShadow = document.getElementById('book3dShadow');

function update3dTransform() {
    if (bookObject) {
        bookObject.style.transform = `rotateY(${currentRotY}deg) rotateX(${currentRotX}deg)`;
    }
    if (bookShadow) {
        // Subtle shadow skew/scale based on rotation
        const skewAmount = Math.sin((currentRotY * Math.PI) / 180) * 12;
        bookShadow.style.transform = `translateZ(-20px) skewX(${skewAmount}deg)`;
    }
}

function reset3dBookRotation() {
    currentRotY = -25;
    currentRotX = 10;
    if (bookObject) {
        bookObject.style.transition = 'transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
        update3dTransform();
        setTimeout(() => {
            bookObject.style.transition = 'transform 0.04s ease-out';
        }, 400);
    }
}

if (bookViewport) {
    bookViewport.addEventListener('pointerdown', (e) => {
        isDragging = true;
        startPointerX = e.clientX;
        startPointerY = e.clientY;
        bookViewport.setPointerCapture(e.pointerId);
    });

    bookViewport.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        const deltaX = e.clientX - startPointerX;
        const deltaY = e.clientY - startPointerY;

        startPointerX = e.clientX;
        startPointerY = e.clientY;

        // Smooth horizontal 360 free rotation
        currentRotY += deltaX * 0.7;

        // Clamped vertical pitch (-45 to 45 deg) to avoid flip glitch
        currentRotX = Math.max(-45, Math.min(45, currentRotX - deltaY * 0.5));

        update3dTransform();
    });

    const endDrag = (e) => {
        if (isDragging) {
            isDragging = false;
            try { bookViewport.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    };
    bookViewport.addEventListener('pointerup', endDrag);
    bookViewport.addEventListener('pointercancel', endDrag);
}

// ── 4. Modal Detail Buku Logic ──────────────────────────────────────────────
let activeBookId = null;
let activeIsFav = false;

function openCatalogModal(el) {
    const d = el.dataset;
    activeBookId = d.id;
    activeIsFav = d.fav === '1';

    document.getElementById('modalBookTitle').textContent = d.title || '-';
    document.getElementById('modalBookAuthor').textContent = d.author || 'Penulis Anonim';
    document.getElementById('modalBookCategory').textContent = d.category || 'Umum';
    document.getElementById('modalBookPublisher').textContent = d.publisher || '-';
    document.getElementById('modalBookYear').textContent = d.year || '-';
    document.getElementById('modalBookIsbn').textContent = d.isbn || '-';
    document.getElementById('modalBookRak').textContent = d.rak || '-';
    document.getElementById('modalBookDescription').textContent = d.description || 'Deskripsi belum tersedia.';

    const stock = parseInt(d.stock) || 0;
    const stockEl = document.getElementById('modalBookStock');
    const reserveBtn = document.getElementById('modalReserveBtn');
    const reserveBookIdField = document.getElementById('modalReserveBookId');
    const borrowBtn = document.getElementById('modalBorrowBtn');
    const borrowBookIdField = document.getElementById('modalBorrowBookId');

    if (reserveBookIdField) reserveBookIdField.value = activeBookId;
    if (borrowBookIdField) borrowBookIdField.value = activeBookId;

    if (borrowBtn) {
        borrowBtn.disabled = false;
        borrowBtn.classList.remove('is-borrowed');
        borrowBtn.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>Pinjam Sekarang</span>
        `;
    }

    if (d.reserved === '1') {
        setReservationSuccess();
    } else if (stock > 0) {
        const feedback = document.getElementById('modalReserveFeedback');
        if (feedback) feedback.hidden = true;
        stockEl.textContent = stock + ' Eksemplar Tersedia';
        stockEl.style.color = 'var(--primary)';
        if (reserveBtn) {
            reserveBtn.disabled = false;
            reserveBtn.classList.remove('is-reserved');
            reserveBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Reservasi Sekarang</span>
            `;
        }
    } else {
        const feedback = document.getElementById('modalReserveFeedback');
        if (feedback) feedback.hidden = true;
        stockEl.textContent = 'Stok Buku Habis';
        stockEl.style.color = '#DC2626';
        if (reserveBtn) {
            reserveBtn.disabled = true;
            reserveBtn.classList.remove('is-reserved');
            reserveBtn.innerHTML = `<span>Stok Buku Habis</span>`;
        }
    }

    if (borrowBtn && d.borrowed === '1') {
        borrowBtn.disabled = true;
        borrowBtn.classList.add('is-borrowed');
        borrowBtn.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Sedang Dipinjam</span>
        `;
    } else if (borrowBtn && stock <= 0) {
        borrowBtn.disabled = true;
        borrowBtn.innerHTML = `<span>Tidak Tersedia</span>`;
    }

    // Populate 3D Book Cover, Spine & Blurb
    const coverImg = document.getElementById('modalBookCoverImg');
    const coverPh = document.getElementById('modalBookCoverPh');
    const spineText = document.getElementById('modalBookSpineText');
    const backBlurb = document.getElementById('modalBookBackBlurb');

    if (d.cover) {
        coverImg.src = d.cover;
        coverImg.style.display = 'block';
        coverPh.style.display = 'none';
    } else {
        coverImg.style.display = 'none';
        coverPh.style.display = 'flex';
        document.getElementById('modalBookCoverPhLetter').textContent = (d.title || 'B').substring(0, 1).toUpperCase();
        document.getElementById('modalBookCoverPhTitle').textContent = d.title || '';
    }

    if (spineText) spineText.textContent = (d.title || 'PUSTAKAWAN DIGITAL').toUpperCase();
    if (backBlurb) backBlurb.textContent = d.description || 'Buku koleksi digital resmi dengan standar katalogisasi terpadu.';

    reset3dBookRotation();
    updateModalFavButton(activeIsFav);

    const backdrop = document.getElementById('saasBookModalBackdrop');
    backdrop?.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function setReservationSuccess() {
    const reserveBtn = document.getElementById('modalReserveBtn');
    const feedback = document.getElementById('modalReserveFeedback');
    const card = document.querySelector(`.saas-book-card[data-id="${activeBookId}"]`);

    if (!reserveBtn) return;

    reserveBtn.disabled = true;
    reserveBtn.classList.add('is-reserved');
    reserveBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>Sudah Direservasi</span>
    `;
    if (feedback) feedback.hidden = false;
    if (card) card.dataset.reserved = '1';
}

function closeCatalogModal() {
    const backdrop = document.getElementById('saasBookModalBackdrop');
    backdrop?.classList.remove('active');
    document.body.style.overflow = '';
}

function handleBackdropClick(e) {
    if (e.target === document.getElementById('saasBookModalBackdrop')) {
        closeCatalogModal();
    }
}

function triggerReservationModal(bookId, cardEl) {
    openCatalogModal(cardEl);
}

function updateModalFavButton(isFav) {
    const btn = document.getElementById('modalFavBtn');
    if (!btn) return;
    btn.classList.toggle('favorited', isFav);
    const svg = btn.querySelector('svg');
    if (svg) svg.setAttribute('fill', isFav ? 'currentColor' : 'none');
}

function toggleModalFavorite() {
    if (!activeBookId) return;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    const cardFavBtn = document.querySelector(`.saas-book-card[data-id="${activeBookId}"] .saas-card-fav-btn`);

    fetch('/user/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ book_id: activeBookId })
    })
    .then(res => res.json())
    .then(data => {
        activeIsFav = data.favorited;
        updateModalFavButton(activeIsFav);

        const card = document.querySelector(`.saas-book-card[data-id="${activeBookId}"]`);
        if (card) card.dataset.fav = activeIsFav ? '1' : '0';

        if (cardFavBtn) {
            cardFavBtn.classList.toggle('favorited', activeIsFav);
            const svg = cardFavBtn.querySelector('svg');
            if (svg) svg.setAttribute('fill', activeIsFav ? 'currentColor' : 'none');
        }

        if (window.showToast) {
            window.showToast(data.message, activeIsFav ? 'success' : 'info');
        }
    })
    .catch(() => {
        if (window.showToast) window.showToast('Gagal mengubah status favorit', 'error');
    });
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCatalogModal();
});

document.getElementById('modalReserveForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const reserveBtn = document.getElementById('modalReserveBtn');
    if (!reserveBtn || reserveBtn.disabled) return;

    reserveBtn.disabled = true;

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ book_id: document.getElementById('modalReserveBookId').value })
        });
        const data = await response.json();

        if (!response.ok) {
            reserveBtn.disabled = false;
            window.showToast?.(data.message || 'Reservasi tidak dapat diproses.', 'error');
            return;
        }

        setReservationSuccess();
        window.showToast?.('Reservasi berhasil disimpan.', 'success');
    } catch (error) {
        reserveBtn.disabled = false;
        window.showToast?.('Gagal menyimpan reservasi. Coba lagi.', 'error');
    }
});

document.getElementById('modalBorrowForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const borrowBtn = document.getElementById('modalBorrowBtn');
    if (!borrowBtn || borrowBtn.disabled) return;

    borrowBtn.disabled = true;

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ book_id: document.getElementById('modalBorrowBookId').value })
        });
        const data = await response.json();

        if (!response.ok) {
            borrowBtn.disabled = false;
            window.showToast?.(data.message || 'Peminjaman tidak dapat diproses.', 'error');
            return;
        }

        window.showToast?.('Buku berhasil dipinjam.', 'success');
        window.location.href = data.redirect_url;
    } catch (error) {
        borrowBtn.disabled = false;
        window.showToast?.('Gagal memproses peminjaman. Coba lagi.', 'error');
    }
});
</script>
@endpush
