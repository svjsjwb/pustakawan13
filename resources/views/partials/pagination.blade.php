@if ($paginator->total() > 0)
<div class="catalog-pagination-wrapper">

    {{-- PAGINATION --}}
    <nav class="catalog-pagination-nav" role="navigation" aria-label="Pagination Navigation">

        {{-- PREV --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true">&laquo; Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">&laquo; Prev</a>
        @endif

        {{-- NOMOR HALAMAN
             Window sama seperti Activities:
             page 1 -> 1 2 3
             page 2 -> 1 2 3 4
             page 3 -> 1 2 3 4 5
             page 4 -> 2 3 4 5 6
        --}}
        @if ($paginator->lastPage() > 0)
            @foreach (
                $paginator->getUrlRange(
                    max(1, $paginator->currentPage() - 2),
                    min($paginator->lastPage(), $paginator->currentPage() + 2)
                ) as $page => $url
            )
                @if ($page == $paginator->currentPage())
                    <span class="page-link active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                @endif
            @endforeach
        @endif

        {{-- NEXT --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">Next &raquo;</a>
        @else
            <span class="page-link disabled" aria-disabled="true">Next &raquo;</span>
        @endif

    </nav>

    {{-- INFO HASIL --}}
    <div class="catalog-pagination-info">
        Menampilkan
        <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
        -
        <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
        dari
        <strong>{{ $paginator->total() }}</strong>
        hasil
    </div>

</div>
@endif
