@extends('layouts.app')

@section('title', 'Manajemen Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="books-page">

    {{-- =====================================================
         HERO HEADER
    ====================================================== --}}

    <section class="books-hero">

        <div class="books-hero-content">

            <div class="books-hero-label">

                <span class="books-hero-dot"></span>

                Pusat Perpustakaan

            </div>


            <h1>
                Manajemen Buku
            </h1>


            <p>
                Tambahkan, ubah, atau hapus data buku dalam koleksi perpustakaan.
            </p>

        </div>


        {{-- =================================================
             TAMBAH BUKU
        ================================================== --}}

        <div class="books-hero-action">

            <a
                href="{{ route('books.create') }}"
                class="book-toolbar-btn book-toolbar-add">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <line
                        x1="12"
                        y1="5"
                        x2="12"
                        y2="19" />

                    <line
                        x1="5"
                        y1="12"
                        x2="19"
                        y2="12" />

                </svg>

                Tambah Buku Baru

            </a>

        </div>

    </section>


    {{-- =====================================================
         ALERT SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="alert-success">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round">

                <path
                    d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />

                <polyline
                    points="22 4 12 14.01 9 11.01" />

            </svg>


            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         SEARCH TOOLBAR
    ====================================================== --}}

    <div class="book-toolbar">


        {{-- SEARCH --}}

        <div class="book-search-wrapper">

            <svg
                class="book-search-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">

                <circle
                    cx="11"
                    cy="11"
                    r="8" />

                <line
                    x1="21"
                    y1="21"
                    x2="16.65"
                    y2="16.65" />

            </svg>


            <input
                type="text"
                id="books-search"
                class="book-search-input"
                placeholder="Cari buku berdasarkan judul, pengarang, ISBN..."
                autocomplete="off">

        </div>


        {{-- SEARCH BUTTON --}}

        <button
            type="button"
            id="books-search-button"
            class="book-toolbar-btn book-search-btn">

            Cari

        </button>


    </div>


    {{-- =====================================================
         TABLE HEADER
    ====================================================== --}}

    <div class="books-table-header">


        {{-- TITLE --}}

        <div class="books-table-title">

            <h2>
                Daftar Koleksi Buku
            </h2>

        </div>


        {{-- FILTER --}}

        <div class="books-table-controls">

            <div class="category-filter-wrapper">


                <div class="books-category-select-box">

                    <select
                        id="books-filter-kategori"
                        class="books-category-filter">

                        <option value="">
                            Semua Kategori
                        </option>


                        @foreach(
                            $books
                                ->pluck('category')
                                ->filter()
                                ->unique('id')
                                ->sortBy('name')
                            as $category
                        )

                            <option
                                value="{{ $category->id }}">

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>


                    <svg
                        class="books-category-chevron"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <polyline
                            points="6 9 12 15 18 9">
                        </polyline>

                    </svg>

                </div>


                <span
                    class="book-total"
                    id="books-total">

                    Total:
                    {{ $books->count() }}
                    Buku

                </span>

            </div>

        </div>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="table-wrapper">

        <table class="books-table">

            <thead>

                <tr>

                    <th>
                        Judul
                    </th>

                    <th>
                        Penulis
                    </th>

                    <th>
                        ISBN
                    </th>

                    <th>
                        Kategori
                    </th>

                    <th>
                        Stok
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody id="books-table-body">

                @forelse($books as $book)

                    <tr
                        data-category="{{ $book->category_id }}"
                        data-search="{{ strtolower(
                            ($book->title ?? '') . ' ' .
                            ($book->author ?? '') . ' ' .
                            ($book->isbn ?? '') . ' ' .
                            ($book->category->name ?? '')
                        ) }}">

                        {{-- =================================================
                             JUDUL
                        ================================================== --}}

                        <td>

                            <strong>
                                {{ $book->title }}
                            </strong>

                        </td>


                        {{-- =================================================
                             PENULIS
                        ================================================== --}}

                        <td>

                            {{ $book->author ?? '-' }}

                        </td>


                        {{-- =================================================
                             ISBN
                        ================================================== --}}

                        <td>

                            {{ $book->isbn ?? '-' }}

                        </td>


                        {{-- =================================================
                             KATEGORI
                        ================================================== --}}

                        <td>

                            {{ $book->category->name ?? '-' }}

                        </td>


                        {{-- =================================================
                             STOK
                        ================================================== --}}

                        <td>

                            {{ $book->available_stock ?? 0 }}

                        </td>


                        {{-- =================================================
                             AKSI
                        ================================================== --}}

                        <td>

                            <div class="book-actions">


                                {{-- EDIT --}}

                                <a
                                    href="{{ route('books.edit', $book) }}"
                                    class="action-edit"
                                    title="Edit Buku"
                                    aria-label="Edit Buku">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">

                                        <path
                                            d="M12 20h9" />

                                        <path
                                            d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />

                                    </svg>

                                </a>


                                {{-- DELETE --}}

                                <form
                                    action="{{ route('books.destroy', $book) }}"
                                    method="POST"
                                    class="delete-book-form"
                                    onsubmit="return confirmDeleteBook()">

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="book-action-btn delete"
                                        title="Hapus Buku"
                                        aria-label="Hapus Buku">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round">

                                            <polyline
                                                points="3 6 5 6 21 6" />

                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />

                                            <path
                                                d="M10 11v6" />

                                            <path
                                                d="M14 11v6" />

                                            <path
                                                d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />

                                        </svg>

                                    </button>

                                </form>


                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="empty-books">

                            Belum ada data buku.

                        </td>

                    </tr>

                @endforelse


                {{-- SEARCH EMPTY --}}

                <tr
                    id="no-search-results"
                    style="display:none;">

                    <td
                        colspan="6"
                        class="empty-books">

                        Tidak ada buku yang sesuai dengan pencarian atau filter.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     SEARCH + FILTER
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('books-search');

    const searchButton =
        document.getElementById('books-search-button');

    const categoryFilter =
        document.getElementById('books-filter-kategori');

    const totalElement =
        document.getElementById('books-total');

    const noResults =
        document.getElementById('no-search-results');


    const rows =
        Array.from(
            document.querySelectorAll(
                '#books-table-body tr[data-category]'
            )
        );


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    function applyBookFilters() {

        const keyword =
            searchInput
                ? searchInput.value
                    .toLowerCase()
                    .trim()
                : '';


        const selectedCategory =
            categoryFilter
                ? categoryFilter.value
                : '';


        let visibleCount = 0;


        rows.forEach(function (row) {

            const category =
                row.dataset.category || '';


            const searchData =
                row.dataset.search || '';


            const matchSearch =
                keyword === '' ||
                searchData.includes(keyword);


            const matchCategory =
                selectedCategory === '' ||
                category === selectedCategory;


            if (
                matchSearch &&
                matchCategory
            ) {

                row.style.display = '';

                visibleCount++;

            } else {

                row.style.display = 'none';

            }

        });


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        if (totalElement) {

            totalElement.textContent =
                'Total: ' +
                visibleCount +
                ' Buku';

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY SEARCH
        |--------------------------------------------------------------------------
        */

        if (noResults) {

            noResults.style.display =
                visibleCount === 0
                    ? ''
                    : 'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH REALTIME
    |--------------------------------------------------------------------------
    */

    if (searchInput) {

        searchInput.addEventListener(
            'input',
            applyBookFilters
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BUTTON CARI
    |--------------------------------------------------------------------------
    */

    if (searchButton) {

        searchButton.addEventListener(
            'click',
            applyBookFilters
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ENTER
    |--------------------------------------------------------------------------
    */

    if (searchInput) {

        searchInput.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    applyBookFilters();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    if (categoryFilter) {

        categoryFilter.addEventListener(
            'change',
            applyBookFilters
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    applyBookFilters();

});


/*
|--------------------------------------------------------------------------
| DELETE CONFIRM
|--------------------------------------------------------------------------
*/

function confirmDeleteBook() {

    return confirm(
        'Yakin ingin menghapus buku ini dari koleksi perpustakaan?'
    );

}

</script>

@endsection