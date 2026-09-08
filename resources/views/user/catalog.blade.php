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
            <div class="saas-hero-badge">
                <span class="pulse-dot"></span>
                <span>Perpustakaan Digital</span>
            </div>
            <h1 class="saas-hero-title">
                Katalog <span class="highlight">Koleksi Buku</span>
            </h1>
            <p class="saas-hero-desc">
                Eksplorasi ribuan literatur akademik, fiksi, ensiklopedia, dan riset terkini secara interaktif dengan visual 3D dan ketersediaan real-time.
            </p>
        </div>

        {{-- Small Stat Cards on Right --}}
        <div class="saas-stats-grid">
            <div class="saas-stat-card">
                <div class="saas-stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="saas-stat-val counter" data-target="{{ $stats['total_titles'] ?? 0 }}">0</div>
                <div class="saas-stat-label">Total Judul</div>
            </div>

            <div class="saas-stat-card">
                <div class="saas-stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="saas-stat-val counter" data-target="{{ $stats['available_count'] ?? 0 }}">0</div>
                <div class="saas-stat-label">Tersedia</div>
            </div>

            <div class="saas-stat-card">
                <div class="saas-stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="saas-stat-val counter" data-target="{{ $stats['borrowed_count'] ?? 0 }}">0</div>
                <div class="saas-stat-label">Sedang Dipinjam</div>
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
        @php
            $categoryGroups = [
                'Semua Kategori' => ['Semua Buku'],
                'Anak-Anak' => ['Fiksi', 'Nonfiksi'],
                'Remaja' => ['Fiksi', 'Nonfiksi'],
                'Dewasa' => ['Fiksi', 'Nonfiksi'],
                'Pendidikan' => ['SD', 'SMP', 'SMA'],
            ];
        @endphp

        @foreach($categoryGroups as $groupName => $subcategories)
            @php
                $groupCategory = $categories->first(function ($category) use ($groupName) {
                    return strcasecmp($category->name, $groupName) === 0
                        || strcasecmp($category->name, 'Buku ' . $groupName) === 0;
                });
                $isAllCategories = $groupName === 'Semua Kategori';
                $groupCount = $isAllCategories ? ($stats['total_titles'] ?? 0) : ($groupCategory?->books_count ?? 0);
                $isGroupActive = $isAllCategories
                    ? empty($activeCategory)
                    : ($groupCategory && (string) $activeCategory === (string) $groupCategory->id);
                $groupParams = array_merge(request()->except('category', 'cat_name', 'page'), $isAllCategories ? [] : [
                    'category' => $groupCategory?->id,
                ]);
            @endphp

            <div class="pd-select-wrapper pd-category-wrapper {{ $isGroupActive ? 'has-active-category' : '' }}"
                 data-category-group="{{ $groupName }}">
                <button type="button"
                        class="pd-trigger {{ $isGroupActive ? 'has-value' : '' }}"
                        aria-haspopup="true"
                        aria-expanded="false">
                    <span class="pd-trigger-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span class="pd-trigger-label">{{ $isAllCategories ? $groupName : 'Kategori ' . $groupName }}</span>
                    <span class="pd-trigger-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </button>

                <div class="pd-panel pd-category-panel" role="menu" aria-label="{{ $groupName }}">
                    @foreach($subcategories as $subcategory)
                        <a href="{{ route('user.catalog', $groupParams) }}"
                           class="pd-item {{ $isGroupActive ? 'is-selected' : '' }}"
                           role="menuitem">
                            <span class="pd-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h10"></path></svg>
                            </span>
                            <span class="pd-item-label">{{ $subcategory }}</span>
                            <span class="pd-category-count">{{ $groupCount }}</span>
                        </a>
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
             data-current-value="{{ request('status', '') }}">
            <button type="button" class="pd-trigger {{ request('status') ? 'has-value' : '' }}" id="pdStatusTrigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon" id="pdStatusIcon">
                    {{-- Icon changes based on selected --}}
                    @if(request('status') === 'tersedia')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @elseif(request('status') === 'habis')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    @endif
                </span>
                <span class="pd-trigger-label" id="pdStatusLabel">
                    @if(request('status') === 'tersedia') Tersedia
                    @elseif(request('status') === 'habis') Habis
                    @else Semua Status
                    @endif
                </span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <div class="pd-panel" role="listbox" aria-label="Filter Status Buku" id="pdStatusPanel">
                <div class="pd-item {{ empty(request('status')) ? 'is-selected' : '' }}" role="option" data-value="" data-label="Semua Status">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </span>
                    <span class="pd-item-label">Semua Status</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-green {{ request('status') === 'tersedia' ? 'is-selected' : '' }}" role="option" data-value="tersedia" data-label="Tersedia">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                    <span class="pd-item-label">Tersedia</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-red {{ request('status') === 'habis' ? 'is-selected' : '' }}" role="option" data-value="habis" data-label="Habis">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                    <span class="pd-item-label">Habis</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
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
                <div class="pd-item {{ $activeSort === 'terbaru' ? 'is-selected' : '' }}" role="option" data-value="terbaru" data-label="Terbaru">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    </span>
                    <span class="pd-item-label">Terbaru</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item {{ $activeSort === 'terlama' ? 'is-selected' : '' }}" role="option" data-value="terlama" data-label="Terlama">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                    <span class="pd-item-label">Terlama</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-divider"></div>
                <div class="pd-item {{ $activeSort === 'az' ? 'is-selected' : '' }}" role="option" data-value="az" data-label="Judul (A-Z)">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l4-4 4 4"/><path d="M7 4v8"/><path d="M11 17h6l-6 4h6"/></svg>
                    </span>
                    <span class="pd-item-label">Judul (A–Z)</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item {{ $activeSort === 'za' ? 'is-selected' : '' }}" role="option" data-value="za" data-label="Judul (Z-A)">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h6l-6 4h6"/><path d="M11 4v8l4-4 4 4V4"/></svg>
                    </span>
                    <span class="pd-item-label">Judul (Z–A)</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Result Meta / Reset indicator --}}
<div class="saas-active-meta">
    <div>
        @if($search)
            Hasil untuk pencarian "<strong>{{ $search }}</strong>" —
        @endif
        Menampilkan <strong>{{ $books->total() }}</strong> koleksi buku
    </div>

    @if($search || $activeCategory || request('status') || (request('sort') && request('sort') !== 'terbaru'))
        <a href="{{ route('user.catalog') }}" class="saas-reset-filter">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Reset Semua Filter</span>
        </a>
    @endif
</div>

{{-- 4. 5-COLUMN RESPONSIVE BOOK GRID --}}
@if($books->isEmpty())
    <div class="eg-card eg-empty-state">
        <div class="eg-empty-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <h3 class="eg-empty-title">Tidak Ada Buku Ditemukan</h3>
        <p class="eg-empty-desc">
            Buku yang Anda cari tidak sesuai dengan kata kunci atau filter saat ini. Silakan coba kata kunci lain atau bersihkan filter.
        </p>
        <a href="{{ route('user.catalog') }}" class="saas-btn-primary" style="max-width: 220px; margin: 0 auto;">
            Lihat Semua Koleksi
        </a>
    </div>
@else
    <div class="saas-book-grid">
        @foreach($books as $book)
            @php
                $isFav = in_array($book->id, $favoriteIds);
                $isAvailable = ($book->available_stock ?? 0) > 0;
            @endphp
            <div class="saas-book-card"
                 onclick="openCatalogModal(this)"
                 data-id="{{ $book->id }}"
                 data-title="{{ $book->title }}"
                 data-author="{{ $book->author ?? '-' }}"
                 data-category="{{ $book->category->name ?? 'Umum' }}"
                 data-publisher="{{ $book->publisher ?? '-' }}"
                 data-year="{{ $book->publication_year ?? '-' }}"
                 data-isbn="{{ $book->isbn ?? '-' }}"
                 data-rak="{{ $book->rak ?? '-' }}"
                 data-stock="{{ $book->available_stock ?? 0 }}"
                 data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                 data-cover="{{ $book->cover ? asset('storage/'.$book->cover) : '' }}"
                 data-fav="{{ $isFav ? '1' : '0' }}"
                 data-borrowed="{{ in_array($book->id, $borrowedBookIds ?? []) ? '1' : '0' }}"
                 data-reserved="{{ in_array($book->id, $reservedBookIds ?? []) ? '1' : '0' }}">

                {{-- Cover & Badges --}}
                <div class="saas-card-cover-wrapper">
                    @if($book->cover)
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             alt="{{ $book->title }}"
                             class="saas-card-cover-img"
                             loading="lazy">
                    @else
                        <div class="saas-card-cover-fallback">
                            <span class="fallback-letter">{{ strtoupper(substr($book->title, 0, 1)) }}</span>
                            <span class="fallback-title">{{ Str::limit($book->title, 34) }}</span>
                        </div>
                    @endif

                    {{-- Category Badge --}}
                    <span class="saas-card-badge">
                        {{ Str::limit($book->category->name ?? 'Umum', 18) }}
                    </span>

                    {{-- Availability Badge --}}
                    <span class="saas-stock-badge {{ $isAvailable ? 'available' : 'unavailable' }}">
                        {{ $isAvailable ? 'Tersedia' : 'Habis' }}
                    </span>

                    {{-- Hover Overlay Action Buttons --}}
                    <div class="saas-card-overlay">
                        <button type="button" class="saas-overlay-btn btn-detail" onclick="event.stopPropagation(); openCatalogModal(this.closest('.saas-book-card'))">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span>Detail 3D</span>
                        </button>

                        <button type="button" class="saas-overlay-btn btn-reserve" onclick="event.stopPropagation(); triggerReservationModal({{ $book->id }}, this.closest('.saas-book-card'))">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Reservasi</span>
                        </button>
                    </div>
                </div>

                {{-- Book Info --}}
                <div class="saas-card-body">
                    <h3 class="saas-card-title" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="saas-card-author">✍️ {{ $book->author ?? 'Penulis Anonim' }}</p>
                    <p class="saas-card-publisher">🏢 {{ $book->publisher ?? 'Penerbit -' }}</p>

                    <div class="saas-card-footer">
                        <span style="font-weight: 600; color: {{ $isAvailable ? 'var(--primary)' : '#DC2626' }};">
                            {{ $isAvailable ? ($book->available_stock . ' eksemplar') : 'Stok Kosong' }}
                        </span>
                        @if($book->rak)
                            <span style="color: var(--saas-text-muted); font-weight: 500;">📍 {{ $book->rak }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
        <div class="ucat-pagination-wrapper">
            {{ $books->links('partials.user-catalog-pagination') }}
        </div>
    @endif
@endif


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
                    <span>✍️</span>
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

// ── 2. Filter Helper ────────────────────────────────────────────────────────
function applyFilter(key, value) {
    const url = new URL(window.location.href);
    if (value) {
        url.searchParams.set(key, value);
    } else {
        url.searchParams.delete(key);
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

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
