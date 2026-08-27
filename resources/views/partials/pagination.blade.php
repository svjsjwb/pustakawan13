@if ($paginator->total() > 0)
<div class="catalog-pagination-wrapper">
    {{-- LEFT: Tampilkan per halaman --}}
    <div class="catalog-pagination-per-page">
        <label for="per-page-select">Tampilkan per halaman:</label>
        <select id="per-page-select" onchange="window.location.href=this.value;">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $paginator->perPage() == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- CENTER: Menampilkan X - Y dari Z hasil --}}
    <div class="catalog-pagination-info">
        Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong> - <strong>{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong>{{ $paginator->total() }}</strong> hasil
    </div>

    {{-- RIGHT: Navigasi tombol halaman --}}
    <nav class="catalog-pagination-nav" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true">&laquo; Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">&laquo; Prev</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="page-link disabled" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">Next &raquo;</a>
        @else
            <span class="page-link disabled" aria-disabled="true">Next &raquo;</span>
        @endif
    </nav>
</div>
@endif
