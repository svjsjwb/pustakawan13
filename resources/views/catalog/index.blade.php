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

            <h1>Katalog Buku</h1>

            <p>
                Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                kategori, atau ketersediaan.
            </p>

        </div>

    </div>


    {{-- =========================
         FILTER
    ========================= --}}

    <div class="filters">

        <input
            type="text"
            placeholder="Cari judul atau pengarang...">

        <select>

            <option>
                Semua Kategori
            </option>

        </select>


        <select>

            <option>
                Semua Status
            </option>

            <option>
                Tersedia
            </option>

            <option>
                Sedang Dipinjam
            </option>

        </select>

    </div>


    {{-- =========================
         BOOK GRID
    ========================= --}}

    <div class="book-grid">

        @forelse($books as $book)

        <div
            class="book-card"

            data-title="{{ $book->title }}"

            data-author="{{ $book->author }}"

            data-category="{{ $book->category->name }}"

            data-stock="{{ $book->available_stock }}"

            data-status="{{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}">

            {{-- COVER --}}

            <div class="book-cover">
                BOOK
            </div>


            {{-- INFORMATION --}}

            <div class="book-info">

                <strong>
                    {{ $book->title }}
                </strong>


                <span>
                    {{ $book->author }}
                </span>


                <div class="book-meta">

                    <span>
                        {{ $book->category->name }}
                    </span>


                    @if($book->available_stock > 0)

                    <span class="book-status available">
                        Tersedia
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
            Belum ada buku.
        </div>

        @endforelse

    </div>


    {{-- =========================
         PAGINATION
    ========================= --}}

    @if($books->hasPages())

    <div class="catalog-pagination">

        {{ $books->links() }}

    </div>

    @endif


</section>



{{-- =========================================================
     BOOK DETAIL MODAL
========================================================= --}}

<div
    class="catalog-modal"
    id="book-modal">

    <div class="catalog-modal-container">


        {{-- CLOSE BUTTON --}}

        <button
            type="button"
            class="catalog-modal-close"
            id="modal-close"
            aria-label="Tutup">
            &times;
        </button>


        <div class="catalog-modal-body">


            {{-- =========================
                 MODAL COVER
            ========================= --}}

            <div class="catalog-modal-cover">

                <div class="catalog-modal-book">

                    <div class="catalog-modal-brand">
                        TIGA SERANGKAI
                    </div>


                    <div
                        class="catalog-modal-book-title"
                        id="modal-cover-title">
                    </div>


                    <div class="catalog-modal-book-footer">
                        PERPUSTAKAAN
                    </div>

                </div>

            </div>



            {{-- =========================
                 MODAL INFORMATION
            ========================= --}}

            <div class="catalog-modal-info">


                {{-- BADGES --}}

                <div class="catalog-modal-badges">

                    <span
                        class="catalog-modal-category"
                        id="modal-category">
                    </span>


                    <span
                        class="catalog-modal-status"
                        id="modal-status">
                    </span>

                </div>


                {{-- TITLE --}}

                <h2 id="modal-title">
                </h2>


                {{-- AUTHOR --}}

                <div
                    class="catalog-modal-author"
                    id="modal-author">
                </div>


                {{-- DESCRIPTION --}}

                <div class="catalog-modal-description-title">
                    Informasi Buku
                </div>


                <div
                    class="catalog-modal-description"
                    id="modal-description">
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
                            Kategori
                        </span>

                        <strong id="modal-category-detail">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Penulis
                        </span>

                        <strong id="modal-author-detail">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Status
                        </span>

                        <strong id="modal-status-detail">
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
                        id="modal-action">
                        Tutup
                    </button>

                </div>


            </div>

        </div>

    </div>

</div>


@endsection