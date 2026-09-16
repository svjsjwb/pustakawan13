<div class="saas-meta-left">
    @if(!empty($search))
        <span>Hasil untuk pencarian "<strong>{{ $search }}</strong>" — </span>
    @endif
    <span>Menampilkan <strong id="catalogTotalCountText">{{ $books->total() }}</strong> koleksi buku</span>

    @if(!empty($subCategory))
        <span class="saas-active-filter-badge">
            <span class="check-icon">✓</span>
            <span>{{ $mainCategory ? ($mainCategory . ' • ') : '' }}{{ $subCategory }}</span>
            <button type="button" class="btn-remove-chip" onclick="clearCategoryFilter()" title="Hapus filter subkategori">×</button>
        </span>
    @elseif(!empty($mainCategory))
        <span class="saas-active-filter-badge">
            <span class="check-icon">✓</span>
            <span>{{ $mainCategory }}</span>
            <button type="button" class="btn-remove-chip" onclick="clearCategoryFilter()" title="Hapus filter kategori">×</button>
        </span>
    @endif

    @if(!empty($status))
        <span class="saas-active-filter-badge">
            <span>Status: {{ ucfirst($status) }}</span>
            <button type="button" class="btn-remove-chip" onclick="clearStatusFilter()" title="Hapus filter status">×</button>
        </span>
    @endif
</div>

@php
    $hasAnyFilter = !empty($search) || !empty($mainCategory) || !empty($subCategory) || !empty($status) || (!empty($activeSort) && $activeSort !== 'terbaru');
@endphp

@if($hasAnyFilter)
    <button type="button" class="saas-reset-filter" onclick="resetAllCatalogFilters()" id="btnResetFilters">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <span>Reset Semua Filter</span>
    </button>
@endif
