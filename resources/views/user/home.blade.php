@extends('layouts.user')

@section('title', 'Beranda')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endpush

@section('content')

<div class="user-home">

    {{-- =====================================================
         HERO
    ====================================================== --}}

    <section class="user-hero motion-section">

        <div class="user-hero-content motion-fade-up">

            <span class="user-hero-label">
                PERPUSTAKAAN TIGA SERANGKAI
            </span>

            <h1>
                Temukan buku<br>
                berikutnya.
            </h1>

            <p>
                Jelajahi koleksi buku dan temukan bacaan
                yang cocok untukmu.
            </p>

            <div class="user-search">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <circle
                        cx="11"
                        cy="11"
                        r="8">
                    </circle>

                    <line
                        x1="21"
                        y1="21"
                        x2="16.65"
                        y2="16.65">
                    </line>

                </svg>

                <input
                    type="text"
                    id="user-book-search"
                    placeholder="Cari judul, penulis, atau kategori..."
                    autocomplete="off">

            </div>

        </div>


        {{-- =================================================
             HERO BOOK
        ================================================== --}}

        @php
            $heroBook = $popularBooks->first();
        @endphp

        @if($heroBook)

        <div class="user-hero-book motion-book">

            <div class="user-hero-cover">

                @if($heroBook->cover)

                <img
                    src="{{ asset('storage/' . $heroBook->cover) }}"
                    alt="{{ $heroBook->title }}">

                @else

                <div class="user-cover-placeholder">
                    {{ $heroBook->title }}
                </div>

                @endif

            </div>

        </div>

        @endif

    </section>



    {{-- =====================================================
         POPULAR
    ====================================================== --}}

    <section class="user-section">

        <div class="user-section-header motion-title">

            <div>

                <span class="user-section-kicker">
                    PILIHAN PEMBACA
                </span>

                <h2>
                    Sedang Populer
                </h2>

            </div>

            <a href="{{ route('catalog') }}">
                Lihat semua →
            </a>

        </div>


        <div class="user-book-row">

            @foreach($popularBooks as $book)

            <article
                class="user-book-card motion-book-card"
                style="--motion-delay: {{ $loop->index * 70 }}ms"
                data-book-card
                data-title="{{ strtolower($book->title) }}"
                data-author="{{ strtolower($book->author ?? '') }}"
                data-category="{{ strtolower($book->category->name ?? '') }}">

                <button
                    type="button"
                    class="user-book-cover user-book-open"
                    data-id="{{ $book->id }}"
                    data-title="{{ $book->title }}"
                    data-author="{{ $book->author ?? '-' }}"
                    data-category="{{ $book->category->name ?? '-' }}"
                    data-publisher="{{ $book->publisher ?? '-' }}"
                    data-year="{{ $book->publication_year ?? '-' }}"
                    data-isbn="{{ $book->isbn ?? '-' }}"
                    data-stock="{{ $book->available_stock ?? 0 }}"
                    data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                    data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}">

                    @if($book->cover)

                    <img
                        src="{{ asset('storage/' . $book->cover) }}"
                        alt="{{ $book->title }}">

                    @else

                    <div class="user-cover-placeholder">
                        {{ $book->title }}
                    </div>

                    @endif

                </button>


                <div class="user-book-info">

                    <span class="user-book-category">
                        {{ $book->category->name ?? 'Koleksi' }}
                    </span>

                    <h3>
                        {{ $book->title }}
                    </h3>

                    <p>
                        {{ $book->author ?? 'Penulis tidak diketahui' }}
                    </p>

                </div>

            </article>

            @endforeach

        </div>

    </section>



    {{-- =====================================================
         LATEST
    ====================================================== --}}

    <section class="user-section">

        <div class="user-section-header motion-title">

            <div>

                <span class="user-section-kicker">
                    KOLEKSI TERBARU
                </span>

                <h2>
                    Baru Ditambahkan
                </h2>

            </div>

            <a href="{{ route('catalog') }}">
                Lihat semua →
            </a>

        </div>


        <div class="user-book-row">

            @foreach($latestBooks as $book)

            <article
                class="user-book-card motion-book-card"
                style="--motion-delay: {{ $loop->index * 70 }}ms"
                data-book-card
                data-title="{{ strtolower($book->title) }}"
                data-author="{{ strtolower($book->author ?? '') }}"
                data-category="{{ strtolower($book->category->name ?? '') }}">

                <button
                    type="button"
                    class="user-book-cover user-book-open"
                    data-id="{{ $book->id }}"
                    data-title="{{ $book->title }}"
                    data-author="{{ $book->author ?? '-' }}"
                    data-category="{{ $book->category->name ?? '-' }}"
                    data-publisher="{{ $book->publisher ?? '-' }}"
                    data-year="{{ $book->publication_year ?? '-' }}"
                    data-isbn="{{ $book->isbn ?? '-' }}"
                    data-stock="{{ $book->available_stock ?? 0 }}"
                    data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                    data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}">

                    @if($book->cover)

                    <img
                        src="{{ asset('storage/' . $book->cover) }}"
                        alt="{{ $book->title }}">

                    @else

                    <div class="user-cover-placeholder">
                        {{ $book->title }}
                    </div>

                    @endif

                </button>


                <div class="user-book-info">

                    <span class="user-book-category">
                        {{ $book->category->name ?? 'Koleksi' }}
                    </span>

                    <h3>
                        {{ $book->title }}
                    </h3>

                    <p>
                        {{ $book->author ?? 'Penulis tidak diketahui' }}
                    </p>

                </div>

            </article>

            @endforeach

        </div>

    </section>



    {{-- =====================================================
         CATEGORY
    ====================================================== --}}

    <section class="user-category-section">

        <div class="user-section-header motion-title">

            <div>

                <span class="user-section-kicker">
                    JELAJAHI
                </span>

                <h2>
                    Temukan Berdasarkan Kategori
                </h2>

            </div>

        </div>


        <div class="user-category-grid">

            @foreach($categories as $category)

            <a
                href="{{ route('catalog') }}"
                class="user-category-card">

                <span>
                    {{ $category->name }}
                </span>

                <strong>
                    →
                </strong>

            </a>

            @endforeach

        </div>

    </section>

</div>



{{-- =========================================================
     BOOK DETAIL MODAL
========================================================= --}}

<div
    id="userBookModal"
    class="user-book-modal"
    aria-hidden="true">

    <div
        class="user-book-modal-overlay"
        id="userBookModalOverlay">
    </div>


    <div class="user-book-modal-content">

        {{-- CLOSE --}}

        <button
            type="button"
            class="user-book-modal-close"
            id="userBookModalClose"
            aria-label="Tutup">

            ×

        </button>


        {{-- =================================================
             3D BOOK
        ================================================== --}}

        <div class="user-book-3d-section">

            <div class="user-book-3d-hint">
                🖱️ Geser untuk memutar buku
            </div>


            <div
                class="book-3d-stage"
                id="book3dStage">

                <div
                    class="book-3d"
                    id="book3d">

                    {{-- FRONT --}}

                    <div
                        class="book-face book-front"
                        id="book3dFront">

                        <div
                            class="book-cover-fallback"
                            id="book3dFallback">

                            <span id="book3dFallbackTitle">
                                Buku
                            </span>

                        </div>


                        <img
                            id="book3dCover"
                            src=""
                            alt="Cover buku">

                    </div>


                    {{-- BACK --}}

                    <div class="book-face book-back">

                        <span id="book3dBackTitle">
                            Perpustakaan Tiga Serangkai
                        </span>

                    </div>


                    {{-- SPINE --}}

                    <div class="book-face book-left">
                    </div>


                    {{-- PAGE EDGE --}}

                    <div class="book-face book-right">
                    </div>


                    {{-- TOP --}}

                    <div class="book-face book-top">
                    </div>


                    {{-- BOTTOM --}}

                    <div class="book-face book-bottom">
                    </div>

                </div>

            </div>


            <div class="book-3d-controls">

                <button
                    type="button"
                    id="book3dReset">

                    ↻ Reset

                </button>

                <span>
                    Drag untuk memutar
                </span>

            </div>

        </div>


        {{-- =================================================
             DETAIL
        ================================================== --}}

        <div class="user-book-detail">

            <div class="user-book-category">

                <span id="modalBookCategory">
                    KATEGORI
                </span>

            </div>


            <h2 id="modalBookTitle">
                Judul Buku
            </h2>


            <p
                class="user-book-author"
                id="modalBookAuthor">

                Penulis

            </p>


            <div class="user-book-info-grid">

                <div class="user-book-info-box">

                    <span>
                        STOK
                    </span>

                    <strong id="modalBookStock">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        PENERBIT
                    </span>

                    <strong id="modalBookPublisher">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        TAHUN
                    </span>

                    <strong id="modalBookYear">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        ISBN
                    </span>

                    <strong id="modalBookIsbn">
                        -
                    </strong>

                </div>

            </div>


            <div class="user-book-synopsis">

                <span>
                    SINOPSIS
                </span>

                <p id="modalBookDescription">
                    Deskripsi buku belum tersedia.
                </p>

            </div>


            <div class="user-book-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">

                <a
                    href="{{ route('catalog') }}"
                    class="user-book-catalog-btn">

                    Lihat di Katalog →

                </a>

                {{-- Form Buat Reservasi --}}
                <form action="{{ route('user.reservations.store') }}" method="POST" id="modalReservationForm" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="book_id" id="modalReservationBookId" value="">
                    <button
                        type="submit"
                        id="btn-reservasi-modal"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 0 16px; border-radius: 10px; background: #0f4c4c; color: #ffffff; border: none; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#0a3737'"
                        onmouseout="this.style.background='#0f4c4c'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Buat Reservasi
                    </button>
                </form>

                {{-- Form Tambahkan ke Favorit Saya --}}
                <form action="{{ route('favorites.store') }}" method="POST" id="modalFavoriteForm" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="book_id" id="modalBookId" value="">
                    <button
                        type="submit"
                        id="btn-favorit-modal"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 0 16px; border-radius: 10px; background: #ffffff; color: #0f4c4c; border: 1.5px solid #0f4c4c; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f0fdf4'"
                        onmouseout="this.style.background='#ffffff'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#0f4c4c" stroke="#0f4c4c" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Tambahkan ke Favorit Saya
                    </button>
                </form>

            </div>


        </div>

    </div>

</div>



{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

@push('scripts')

<script
    src="{{ asset('js/user-home.js') }}">
</script>

<script
    src="{{ asset('js/userBook3d.js') }}">
</script>

@endpush

@endsection