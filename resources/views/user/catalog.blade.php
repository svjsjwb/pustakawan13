@extends('layouts.user')
@section('title', 'Katalog Koleksi Buku – Perpustakaan Digital')
@section('content_class', 'catalog-content-override')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-catalog.css') }}?v={{ file_exists(public_path('css/user-catalog.css')) ? filemtime(public_path('css/user-catalog.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}?v={{ file_exists(public_path('css/premium-dropdown.css')) ? filemtime(public_path('css/premium-dropdown.css')) : time() }}">
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
        @if(!empty($selectedCategoryId))
            <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">
        @endif
        @if(!empty($selectedSubcategoryId))
            <input type="hidden" name="subcategory_id" value="{{ $selectedSubcategoryId }}">
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
            $currentCatId = (string) ($selectedCategoryId ?? '');
            $currentSubId = (string) ($selectedSubcategoryId ?? '');
            $isAllActive = empty($currentCatId) && empty($currentSubId);
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

        {{-- Dropdowns for categories and subcategories from DB --}}
        @foreach($categories as $category)
            @php
                $catId = (string) $category->id;
                $isGroupActive = ($currentCatId === $catId);
                $groupTotal = $mainCounts[$category->id] ?? ($category->books_count ?? 0);
            @endphp

            <div class="pd-select-wrapper pd-category-wrapper {{ $isGroupActive ? 'has-active-category' : '' }}"
                 data-category-id="{{ $category->id }}"
                 data-category-label="{{ $category->name }}"
                 id="catWrapper_{{ $category->id }}">
                <button type="button"
                        class="pd-trigger {{ $isGroupActive ? 'has-value' : '' }}"
                        aria-haspopup="true"
                        aria-expanded="false"
                        id="catTrigger_{{ $category->id }}">
                    <span class="pd-trigger-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span class="pd-trigger-label" id="catLabel_{{ $category->id }}">
                        {{ $category->name }}
                    </span>
                    <span class="pd-trigger-count">{{ $groupTotal }}</span>
                    <span class="pd-trigger-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </button>

                <div class="pd-panel pd-category-panel" role="menu" aria-label="{{ $category->name }}">
                    <div class="pd-panel-header">{{ strtoupper($category->name) }}</div>
                    <div class="pd-category-options-list">
                        <button type="button"
                                class="pd-item pd-category-option pd-category-parent-btn {{ $isGroupActive && empty($currentSubId) ? 'is-selected active-subcat' : '' }}"
                                data-category-id="{{ $category->id }}"
                                data-sub-id=""
                                onclick="selectSubcategory('{{ $category->id }}', '', event)">
                            <span class="pd-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                </svg>
                            </span>
                            <span class="pd-item-label">Semua {{ $category->name }}</span>
                            <span class="pd-category-count">{{ $groupTotal }}</span>
                        </button>
                        @foreach($category->subcategories as $subcategory)
                            @php
                                $subId = (string) $subcategory->id;
                                $isItemActive = ($isGroupActive && $currentSubId === $subId);
                                $count = $subCounts[$subcategory->id] ?? 0;
                            @endphp
                            <button type="button"
                                    class="pd-item pd-category-option pd-subcat-btn {{ $isItemActive ? 'is-selected active-subcat' : '' }}"
                                    data-category-id="{{ $category->id }}"
                                    data-sub-id="{{ $subcategory->id }}"
                                    onclick="selectSubcategory('{{ $category->id }}', '{{ $subcategory->id }}', event)">
                                <span class="pd-item-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 6h16M4 12h16M4 18h10"></path>
                                    </svg>
                                </span>
                                <span class="pd-item-label">{{ $subcategory->name }}</span>
                                <span class="pd-category-count">{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>
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
            <button type="button" class="pd-trigger {{ $status ? 'has-value' : '' }} {{ $status === 'tersedia' ? 'filter-status-green' : ($status === 'habis' ? 'filter-status-red' : '') }}" id="pdStatusTrigger" aria-haspopup="listbox" aria-expanded="false">
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
@include('user.partials.catalog-book-modal')
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
    category_id: "{{ $selectedCategoryId }}",
    subcategory_id: "{{ $selectedSubcategoryId }}",
    main_category: "{{ $mainCategory }}",
    sub_category: "{{ $subCategory }}",
    status: "{{ $status }}",
    sort: "{{ $activeSort }}",
    page: 1
};

let catalogRequestId = 0;

function updateCatalogBadgeMarquees() {
    document.querySelectorAll('.saas-card-badge').forEach(function(badge) {
        const text = badge.querySelector('.saas-card-badge-copy-text');
        const badgeRow = badge.parentElement;
        const stockBadge = badgeRow && badgeRow.querySelector('.saas-stock-badge');
        if (!text || !stockBadge) return;

        const badgeStyle = window.getComputedStyle(badge);
        const textWidth = text.getBoundingClientRect().width;
        const paddingWidth = parseFloat(badgeStyle.paddingLeft) + parseFloat(badgeStyle.paddingRight);
        const rowStyle = window.getComputedStyle(badgeRow);
        const gapWidth = parseFloat(rowStyle.columnGap) || 0;
        const maxBadgeWidth = badgeRow.clientWidth - stockBadge.getBoundingClientRect().width - gapWidth;

        badge.style.width = Math.max(0, Math.min(textWidth + paddingWidth, maxBadgeWidth)) + 'px';
        badge.classList.add('is-marquee');

        const copy = text.closest('.saas-card-badge-copy');
        const copyStyle = window.getComputedStyle(copy);
        const cycleWidth = textWidth + parseFloat(copyStyle.paddingRight);
        badge.style.setProperty('--saas-marquee-duration', (cycleWidth / 18) + 's');
    });
}

document.addEventListener('DOMContentLoaded', updateCatalogBadgeMarquees);
window.addEventListener('resize', updateCatalogBadgeMarquees, { passive: true });
function fetchCatalog(newParams = {}, pushHistory = true) {
    Object.assign(window.catalogState, newParams);

    const gridContainer = document.getElementById('catalogGridContainer');
    const metaContainer = document.getElementById('catalogMetaContainer');
    if (gridContainer) gridContainer.classList.add('loading');

    const params = new URLSearchParams();
    if (window.catalogState.search) params.set('search', window.catalogState.search);
    if (window.catalogState.category_id) params.set('category_id', window.catalogState.category_id);
    if (window.catalogState.subcategory_id) params.set('subcategory_id', window.catalogState.subcategory_id);
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
                updateCatalogBadgeMarquees();
            }
            if (metaContainer && data.meta_html) {
                metaContainer.innerHTML = data.meta_html;
            }

            // Sync visual states for categories & subcategories based on IDs
            syncCategoryUiState(window.catalogState.category_id, window.catalogState.subcategory_id);

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

function selectSubcategory(categoryId, subcategoryId, e) {
    if (e) e.stopPropagation();

    // Close any open dropdowns
    document.querySelectorAll('.pd-select-wrapper.is-open').forEach(w => {
        w.classList.remove('is-open');
        w.querySelector('.pd-trigger')?.classList.remove('is-open');
    });
    const bd = document.querySelector('.pd-backdrop');
    if (bd) bd.classList.remove('active');

    // Filter using category_id and subcategory_id
    fetchCatalog({
        category_id: categoryId ? String(categoryId) : '',
        subcategory_id: subcategoryId ? String(subcategoryId) : '',
        main_category: '',
        sub_category: '',
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
        category_id: '',
        subcategory_id: '',
        main_category: '',
        sub_category: '',
        status: '',
        sort: 'terbaru',
        page: 1
    });
}

function clearCategoryFilter() {
    fetchCatalog({
        category_id: '',
        subcategory_id: '',
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

function syncCategoryUiState(catId, subId) {
    const hasCategory = Boolean(catId || subId);

    document.querySelectorAll('.pd-category-wrapper[data-category-id]').forEach(wrapper => {
        const wrapperCatId = wrapper.dataset.categoryId;
        const trigger = wrapper.querySelector('.pd-trigger');
        const isActiveGroup = Boolean(catId) && String(wrapperCatId) === String(catId);

        wrapper.classList.toggle('has-active-category', isActiveGroup);
        trigger?.classList.toggle('has-value', isActiveGroup);

        wrapper.querySelectorAll('.pd-category-option').forEach(option => {
            const optSubId = option.dataset.subId !== undefined ? option.dataset.subId : (option.dataset.subcategoryId || '');
            const isSelected = isActiveGroup
                && String(optSubId) === String(subId || '');
            option.classList.toggle('is-selected', isSelected);
            option.classList.toggle('active-subcat', isSelected);
        });
    });

    const allCatWrapper = document.getElementById('wrapperAllCategories');
    const allCatTrigger = document.getElementById('triggerAllCategories');
    allCatWrapper?.classList.toggle('has-active-category', !hasCategory);
    allCatTrigger?.classList.toggle('has-value', !hasCategory);
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
        trigger.classList.remove('filter-status-green', 'filter-status-red');
        if (status === 'tersedia') trigger.classList.add('filter-status-green');
        if (status === 'habis') trigger.classList.add('filter-status-red');
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
            category_id: url.searchParams.get('category_id') || url.searchParams.get('category') || '',
            subcategory_id: url.searchParams.get('subcategory_id') || url.searchParams.get('subcategory') || '',
            status: url.searchParams.get('status') || '',
            sort: url.searchParams.get('sort') || 'terbaru',
            page: url.searchParams.get('page') || 1,
        }, false);
    }
});

// ── 3. Solid 3D Book 360° Drag Rotation Engine ──────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const url = new URL(window.location.href);
    const requestedBookId = new URLSearchParams(window.location.search).get('open_book');
    if (!requestedBookId) return;

    const matchingCard = Array.from(document.querySelectorAll('.saas-book-card'))
        .find(card => card.dataset.id === requestedBookId);
    if (matchingCard) {
        openCatalogModal(matchingCard);
        url.searchParams.delete('open_book');
        window.history.replaceState(window.history.state, '', url.pathname + url.search + url.hash);
    }
});
</script>
@include('user.partials.catalog-book-modal-scripts')
@endpush
