@extends('layouts.app')

@section('title', 'Katalog Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/catalog.js') }}"></script>
@endpush

@section('content')

<section class="page" id="page-catalog">

    {{-- =========================
         HEADER
    ========================= --}}

    <div class="catalog-header">

        <div>

            <div class="catalog-count">
                {{ $books->total() }} buku terdaftar
            </div>

            <h1>
                Katalog Buku
            </h1>

            <p>
                Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                kategori, atau ketersediaan stok.
            </p>

        </div>

    </div>


    {{-- =========================
         FILTER FORM
    ========================= --}}

    <form
        method="GET"
        action="{{ route('catalog') }}"
        class="filters"
    >

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul atau pengarang..."
        >


        <select
            name="category"
            onchange="this.form.submit()"
        >

            <option value="">
                Semua Kategori
            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->name }}"
                    {{ request('category') == $category->name ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>


        <select
            name="status"
            onchange="this.form.submit()"
        >

            <option value="">
                Semua Status
            </option>

            <option
                value="Tersedia"
                {{ request('status') == 'Tersedia' ? 'selected' : '' }}
            >
                Tersedia
            </option>

            <option
                value="Dipinjam"
                {{ request('status') == 'Dipinjam' ? 'selected' : '' }}
            >
                Sedang Dipinjam
            </option>

        </select>

    </form>


    {{-- =========================
         BOOK GRID
    ========================= --}}

    <div class="book-grid">

        @forelse($books as $book)

            <div
                class="book-card"

                data-title="{{ $book->title }}"

                data-author="{{ $book->author ?? '-' }}"

                data-category="{{ $book->category->name ?? '-' }}"

                data-stock="{{ $book->available_stock ?? 0 }}"

                data-status="{{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}"

                data-description="{{ $book->description ?? 'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.' }}"

                data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}"

                data-publisher="{{ $book->publisher ?? '-' }}"

                data-year="{{ $book->publication_year ?? '-' }}"

                data-isbn="{{ $book->isbn ?? '-' }}"

                data-call-number="{{ $book->call_number ?? '-' }}"
            >

                {{-- =========================
                     COVER
                ========================= --}}

                <div
                    class="book-cover {{ $book->cover ? 'has-image' : '' }}"
                >

                    @if($book->cover)

                        <img
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            class="book-cover-img"
                        >

                    @endif

                </div>


                {{-- =========================
                     INFORMATION
                ========================= --}}

                <div class="book-info">

                    <strong>
                        {{ $book->title }}
                    </strong>

                    <span>
                        {{ $book->author ?? '-' }}
                    </span>


                    <div class="book-meta">

                        <span>
                            {{ $book->category->name ?? '-' }}
                        </span>


                        @if($book->available_stock > 0)

                            <span class="book-status available">
                                Tersedia ({{ $book->available_stock }})
                            </span>

                        @else

                            <span class="book-status borrowed">
                                Dipinjam
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div class="catalog-empty">

                Belum ada koleksi buku yang sesuai
                dengan pencarian atau filter.

            </div>

        @endforelse

    </div>


    {{-- =========================
         PAGINATION
    ========================= --}}

    @if($books->total() > 0)

        <div class="catalog-pagination">

            {{ $books->links('partials.pagination') }}

        </div>

    @endif

</section>


{{-- =========================================================
     BOOK DETAIL MODAL
========================================================= --}}

<div
    class="catalog-modal"
    id="book-modal"
>

    <div class="catalog-modal-container">


        {{-- =========================
             CLOSE BUTTON
        ========================= --}}

        <button
            type="button"
            class="catalog-modal-close"
            id="modal-close"
            aria-label="Tutup"
        >
            &times;
        </button>


        <div class="catalog-modal-body">


            {{-- =========================
                 MODAL COVER
            ========================= --}}

            <div class="catalog-modal-cover">

                {{-- COVER DEFAULT --}}

                <div
                    class="catalog-modal-book"
                    id="modal-book-box"
                >

                    <div class="catalog-modal-brand">
                        TIGA SERANGKAI
                    </div>


                    <div
                        class="catalog-modal-book-title"
                        id="modal-cover-title"
                    >
                    </div>


                    <div class="catalog-modal-book-footer">
                        PERPUSTAKAAN
                    </div>

                </div>


                {{-- COVER IMAGE --}}

                <img
                    id="modal-cover-image"
                    src="#"
                    alt="Cover Buku"
                    style="
                        display: none;
                        width: 170px;
                        height: 235px;
                        object-fit: cover;
                        border-radius: 10px;
                        box-shadow:
                            -6px 12px 25px
                            rgba(0,0,0,0.20);
                    "
                >

            </div>


            {{-- =========================
                 MODAL INFORMATION
            ========================= --}}

            <div class="catalog-modal-info">


                {{-- BADGES --}}

                <div class="catalog-modal-badges">

                    <span
                        class="catalog-modal-category"
                        id="modal-category"
                    >
                    </span>


                    <span
                        class="catalog-modal-status"
                        id="modal-status"
                    >
                    </span>

                </div>


                {{-- TITLE --}}

                <h2 id="modal-title">
                </h2>


                {{-- AUTHOR --}}

                <div
                    class="catalog-modal-author"
                    id="modal-author"
                >
                </div>


                {{-- DESCRIPTION --}}

                <div class="catalog-modal-description-title">

                    Sinopsis / Deskripsi

                </div>


                <div
                    class="catalog-modal-description"
                    id="modal-description"
                >
                    Informasi buku belum tersedia.
                </div>


                {{-- =========================
                     BOOK META
                ========================= --}}

                <div class="catalog-modal-meta">


                    <div>

                        <span>
                            Stok Tersedia
                        </span>

                        <strong id="modal-stock">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Penerbit
                        </span>

                        <strong id="modal-publisher">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Tahun Terbit
                        </span>

                        <strong id="modal-year">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            No. Panggil
                        </span>

                        <strong id="modal-call-number">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            ISBN
                        </span>

                        <strong id="modal-isbn">
                            -
                        </strong>

                    </div>

                </div>


                {{-- =========================
                     ACTION
                ========================= --}}

                <div class="catalog-modal-actions">

                    <button
                        type="button"
                        class="catalog-modal-button"
                        id="modal-action"
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


@endsection