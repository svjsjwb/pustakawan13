@extends('layouts.app')

@section('title', 'Manajemen Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="page-header">

    <div>
        <h1>
            Manajemen Buku
        </h1>

        <p>
            Tambahkan, ubah, atau hapus data buku dalam koleksi perpustakaan.
        </p>
    </div>

</div>


{{-- =====================================================
     ALERT
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
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
    </svg>

    <span>
        {{ session('success') }}
    </span>

</div>

@endif


{{-- =====================================================
     BOOK TOOLBAR
====================================================== --}}

<div class="book-toolbar">

    <div class="toolbar-left">

        {{-- Tambah Buku Baru --}}
        <a
            href="{{ route('books.create') }}"
            class="book-toolbar-btn book-toolbar-add">
            + Tambah Buku Baru
        </a>


        {{-- Search Buku --}}
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
                    r="8"></circle>

                <line
                    x1="21"
                    y1="21"
                    x2="16.65"
                    y2="16.65"></line>

            </svg>


            <input
                type="text"
                id="books-search"
                class="book-search-input"
                placeholder="Cari buku..."
                autocomplete="off">

        </div>

    </div>

</div>


{{-- =====================================================
     TABLE HEADER
====================================================== --}}

<div class="books-table-header">

    {{-- Judul --}}
    <div class="books-table-title">

        <span>
            Daftar Koleksi Buku
        </span>

    </div>


    {{-- Filter --}}
    <div class="books-table-controls">

        <div class="category-filter-wrapper">

            <select
                id="books-filter-kategori"
                class="books-category-filter">

                <option value="">
                    Semua Kategori
                </option>

                @foreach(
                $books->pluck('category')->unique('id')->filter()
                as $category
                )

                <option value="{{ $category->id }}">
                    {{ $category->name }}
                </option>

                @endforeach

            </select>


            <span class="book-total">
                Total: {{ $books->count() }} Buku
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


        <tbody>

            @forelse($books as $book)

            <tr
                data-category="{{ $book->category_id }}">

                {{-- Judul --}}
                <td>

                    <strong>
                        {{ $book->title }}
                    </strong>

                </td>


                {{-- Penulis --}}
                <td>

                    {{ $book->author ?? '-' }}

                </td>


                {{-- ISBN --}}
                <td>

                    {{ $book->isbn ?? '-' }}

                </td>


                {{-- Kategori --}}
                <td>

                    {{ $book->category->name ?? '-' }}

                </td>


                {{-- Stok --}}
                <td>

                    {{ $book->available_stock ?? 0 }}

                </td>


                {{-- Aksi --}}
                <td>

                    <div class="book-actions">


                        {{-- Kelola Eksemplar --}}
                        <a
                            href="{{ route('books.copies.index', $book) }}"
                            class="action-copy"
                            title="Kelola eksemplar">
                            📚
                        </a>


                        {{-- Edit --}}
                        <a
                            href="{{ route('books.edit', $book) }}"
                            class="action-edit"
                            title="Edit buku">
                            ✎
                        </a>


                        {{-- Hapus --}}
                        <form
                            action="{{ route('books.destroy', $book) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus buku ini?')">

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="action-delete"
                                title="Hapus buku">
                                🗑
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


            {{-- Tidak ditemukan --}}
            <tr
                id="no-search-results"
                style="display: none;">

                <td
                    colspan="6"
                    class="empty-books">
                    Tidak ada buku yang sesuai dengan pencarian.
                </td>

            </tr>

        </tbody>

    </table>

</div>


{{-- =====================================================
     SEARCH & FILTER JAVASCRIPT
====================================================== --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput =
            document.getElementById('books-search');

        const filterCategory =
            document.getElementById('books-filter-kategori');

        const rows =
            document.querySelectorAll(
                '.books-table tbody tr[data-category]'
            );

        const totalCountEl =
            document.querySelector('.book-total');

        const noResultsRow =
            document.getElementById('no-search-results');


        /**
         * ==================================================
         * FILTER DATA BUKU
         * ==================================================
         */
        function applyFilters() {

            const keyword =
                searchInput ?
                searchInput.value.toLowerCase().trim() :
                '';

            const selectedCategory =
                filterCategory ?
                filterCategory.value :
                '';


            let visibleCount = 0;


            rows.forEach(function(row) {

                const category =
                    row.dataset.category || '';


                /*
                 * Ambil seluruh isi baris:
                 *
                 * Judul
                 * Penulis
                 * ISBN
                 * Kategori
                 * Stok
                 */
                const rowText =
                    row.textContent
                    .toLowerCase()
                    .trim();


                const matchSearch =
                    keyword === '' ||
                    rowText.includes(keyword);


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
             * Update jumlah buku
             */
            if (totalCountEl) {

                totalCountEl.textContent =
                    'Total: ' +
                    visibleCount +
                    ' Buku';

            }


            /*
             * Tampilkan pesan jika tidak ditemukan
             */
            if (noResultsRow) {

                if (
                    visibleCount === 0 &&
                    rows.length > 0
                ) {

                    noResultsRow.style.display = '';

                } else {

                    noResultsRow.style.display = 'none';

                }

            }

        }


        /**
         * ==================================================
         * SEARCH LANGSUNG SAAT MENGETIK
         * ==================================================
         */
        if (searchInput) {

            searchInput.addEventListener(
                'input',
                applyFilters
            );

        }


        /**
         * ==================================================
         * FILTER KATEGORI
         * ==================================================
         */
        if (filterCategory) {

            filterCategory.addEventListener(
                'change',
                applyFilters
            );

        }

    });
</script>

@endsection