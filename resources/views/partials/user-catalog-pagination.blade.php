@if ($paginator->hasPages())
    <nav class="ucat-pagination-nav" role="navigation" aria-label="Navigasi Halaman Katalog">
        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span class="ucat-page-btn disabled" aria-disabled="true">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="ucat-page-btn" rel="prev">Previous</a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="ucat-page-dots" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- Array Halaman --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="ucat-page-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="ucat-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="ucat-page-btn" rel="next">Next</a>
        @else
            <span class="ucat-page-btn disabled" aria-disabled="true">Next</span>
        @endif
    </nav>
@endif
