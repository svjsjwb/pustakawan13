@if ($paginator->hasPages() || $paginator->total() > 0)
    @if ($paginator->hasPages())
        <div class="books-pagination-links">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="books-page-btn disabled" aria-disabled="true">&laquo; Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="books-page-btn" rel="prev">&laquo; Prev</a>
            @endif

            {{-- Elemen Pagination --}}
            @foreach ($elements as $element)
                {{-- Separator Tiga Titik --}}
                @if (is_string($element))
                    <span class="books-page-dots" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="books-page-btn active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="books-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="books-page-btn" rel="next">Next &raquo;</a>
            @else
                <span class="books-page-btn disabled" aria-disabled="true">Next &raquo;</span>
            @endif
        </div>
    @endif

    {{-- Info Hasil --}}
    <div class="books-pagination-info">
        Menampilkan
        <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
        -
        <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
        dari
        <strong>{{ $paginator->total() }}</strong>
        hasil
    </div>
@endif

