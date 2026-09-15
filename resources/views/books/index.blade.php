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

        </section>


        {{-- =====================================================
         ALERT SUCCESS
    ====================================================== --}}

        @if (session('success'))
            <div class="alert-success">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
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

        <form method="GET" action="{{ route('books.index') }}" id="booksFilterForm">

            <div class="book-toolbar">

                <div class="toolbar-left">

                    {{-- TAMBAH BUKU --}}

                    <a href="{{ route('books.create') }}" class="book-toolbar-btn book-toolbar-add">
                        + Tambah Buku Baru
                    </a>


                    {{-- SEARCH --}}

                    <div class="book-search-wrapper">

                        <svg class="book-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                            <circle cx="11" cy="11" r="8" />

                            <line x1="21" y1="21" x2="16.65" y2="16.65" />

                        </svg>


                        <input type="text" id="books-search" name="search" class="book-search-input"
                            placeholder="Cari buku..." value="{{ request('search') }}" autocomplete="off">

                    </div>


                    {{-- SUBMIT SEARCH --}}

                    <button type="submit" class="book-toolbar-btn">
                        Cari
                    </button>

                </div>

            </div>


            {{-- =====================================================
         TABLE HEADER
    ====================================================== --}}

            <div class="books-table-header">

                <div class="books-table-title">

                    <span>
                        Daftar Koleksi Buku
                    </span>

                </div>


                <div class="books-table-controls">

                    {{-- FILTER KATEGORI --}}

                    <div class="category-filter-wrapper">

                        <select id="books-filter-kategori" name="category" class="books-category-filter"
                            onchange="document.getElementById('booksFilterForm').requestSubmit()">

                            <option value="">
                                Semua Kategori
                            </option>


                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach

                        </select>


                        {{-- TOTAL DATABASE --}}

                        <span class="book-total">

                            Total:
                            {{ number_format($books->total(), 0, ',', '.') }}
                            Buku

                        </span>

                    </div>


                    </div>

                </div>

            </div>

        </form>


        {{-- =====================================================
         TABLE BUKU
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

                        @forelse ($books as $book)
                            <tr data-category="{{ $book->category_id }}">

                                {{-- JUDUL --}}

                                <td>

                                    <strong>
                                        {{ $book->title }}
                                    </strong>

                                </td>


                                {{-- PENULIS --}}

                                <td>
                                    {{ $book->author ?? '-' }}
                                </td>


                                {{-- ISBN --}}

                                <td>
                                    {{ $book->isbn ?? '-' }}
                                </td>


                                {{-- KATEGORI --}}

                                <td>
                                    {{ $book->category->name ?? '-' }}
                                </td>


                                {{-- STOK --}}

                                <td>
                                    {{ $book->available_stock ?? 0 }}
                                </td>


                                {{-- AKSI --}}

                                <td>

                                    <div class="book-actions">

                                        {{-- EDIT --}}

                                        <a href="{{ route('books.edit', $book) }}" class="action-edit" title="Edit Buku">

                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">

                                                <path d="M12 20h9" />

                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />

                                            </svg>

                                        </a>


                                        {{-- PENARIKAN BUKU --}}

                                        <form action="{{ route('books.destroy', $book) }}" method="POST"
                                            class="delete-book-form">

                                            @csrf

                                            @method('DELETE')

                                            <button type="button" class="book-action-btn delete" title="Tarik Buku"
                                                onclick="openBookDeleteModal(this)">

                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                                    <polyline points="3 6 5 6 21 6" />

                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />

                                                    <path d="M10 11v6" />

                                                    <path d="M14 11v6" />

                                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />

                                                </svg>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="empty-books">

                                    @if (request()->filled('search') || request()->filled('category'))

                                        Tidak ada buku yang sesuai dengan pencarian atau filter.

                                    @else

                                        Belum ada data buku.

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =====================================================
         PAGINATION BUKU
    ====================================================== --}}

            @if ($books->total() > 0)

                <div class="books-pagination">

                    <div class="books-pagination-left">

                        <span class="books-pagination-label">
                            Tampilkan per halaman:
                        </span>


                        <form
                            method="GET"
                            action="{{ route('books.index') }}"
                            class="books-per-page-form"
                        >

                            @if (request()->filled('search'))
                                <input
                                    type="hidden"
                                    name="search"
                                    value="{{ request('search') }}"
                                >
                            @endif


                            @if (request()->filled('category'))
                                <input
                                    type="hidden"
                                    name="category"
                                    value="{{ request('category') }}"
                                >
                            @endif


                            <select
                                name="per_page"
                                class="books-per-page"
                                onchange="this.form.submit()"
                                aria-label="Jumlah buku per halaman"
                            >

                                @foreach ([12, 25, 50, 100] as $option)

                                    <option
                                        value="{{ $option }}"
                                        @selected($books->perPage() == $option)
                                    >
                                        {{ $option }}
                                    </option>

                                @endforeach

                            </select>

                        </form>

                    </div>


                    <div class="books-pagination-center">

                        Menampilkan

                        <strong>
                            {{ $books->firstItem() }}
                        </strong>

                        -

                        <strong>
                            {{ $books->lastItem() }}
                        </strong>

                        dari

                        <strong>
                            {{ $books->total() }}
                        </strong>

                        hasil

                    </div>


                    <div class="books-pagination-links">

                        @if ($books->onFirstPage())

                            <span class="books-page-btn disabled">
                                « Prev
                            </span>

                        @else

                            <a
                                href="{{ $books->previousPageUrl() }}"
                                class="books-page-btn"
                            >
                                « Prev
                            </a>

                        @endif


                        @php
                            $current = $books->currentPage();
                            $last = $books->lastPage();

                            $startPage = max(1, $current - 2);
                            $endPage = min($last, $current + 2);
                        @endphp


                        @if ($startPage > 1)

                            <a
                                href="{{ $books->url(1) }}"
                                class="books-page-btn"
                            >
                                1
                            </a>

                            @if ($startPage > 2)
                                <span class="books-page-dots">...</span>
                            @endif

                        @endif


                        @for ($page = $startPage; $page <= $endPage; $page++)

                            @if ($page == $current)

                                <span class="books-page-btn active">
                                    {{ $page }}
                                </span>

                            @else

                                <a
                                    href="{{ $books->url($page) }}"
                                    class="books-page-btn"
                                >
                                    {{ $page }}
                                </a>

                            @endif

                        @endfor


                        @if ($endPage < $last)

                            @if ($endPage < $last - 1)
                                <span class="books-page-dots">...</span>
                            @endif

                            <a
                                href="{{ $books->url($last) }}"
                                class="books-page-btn"
                            >
                                {{ $last }}
                            </a>

                        @endif


                        @if ($books->hasMorePages())

                            <a
                                href="{{ $books->nextPageUrl() }}"
                                class="books-page-btn"
                            >
                                Next »
                            </a>

                        @else

                            <span class="books-page-btn disabled">
                                Next »
                            </span>

                        @endif

                    </div>

                </div>

            @endif


            {{-- =====================================================
         MODAL KONFIRMASI PENARIKAN BUKU
    ====================================================== --}}

            <div id="bookDeleteModal" class="book-delete-modal-overlay" aria-hidden="true">

                <div class="book-delete-modal" role="dialog" aria-modal="true" aria-labelledby="bookDeleteModalTitle">

                    {{-- ICON --}}

                    <div class="book-delete-modal-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">

                            <polyline points="3 6 5 6 21 6" />

                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />

                            <path d="M10 11v6" />

                            <path d="M14 11v6" />

                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />

                        </svg>

                    </div>


                    {{-- LABEL --}}

                    <div class="book-delete-modal-label">
                        KONFIRMASI PENARIKAN
                    </div>


                    {{-- TITLE --}}

                    <h3 id="bookDeleteModalTitle" class="book-delete-modal-title">
                        Tarik Buku
                    </h3>


                    {{-- DESCRIPTION --}}

                    <p class="book-delete-modal-description">

                        Buku ini akan ditarik dari koleksi aktif.

                        <br>

                        Masukkan alasan penarikan sebelum melanjutkan.

                    </p>


                    {{-- BOOK INFO --}}

                    <div class="book-delete-modal-book">

                        <span>
                            JUDUL BUKU
                        </span>

                        <strong id="deleteBookName">
                            -
                        </strong>

                    </div>


                    {{-- ALASAN PENARIKAN --}}

                    <div class="book-delete-modal-reason">

                        <label for="bookDeleteReason">

                            Alasan Penarikan

                            <span class="required">
                                *
                            </span>

                        </label>

                        <textarea id="bookDeleteReason" rows="3" maxlength="500" placeholder="Masukkan alasan buku ditarik..."
                            required></textarea>

                        <small>
                            Alasan wajib diisi.
                        </small>

                    </div>


                    {{-- ACTIONS --}}

                    <div class="book-delete-modal-actions">

                        <button type="button" class="book-delete-modal-cancel" onclick="closeBookDeleteModal()">
                            Batal
                        </button>


                        <button type="button" class="book-delete-modal-confirm" onclick="confirmBookDelete()">
                            Tarik Buku
                        </button>

                    </div>

                </div>

            </div>

    </div>


    {{-- =========================================================
     MODAL REASON STYLE
========================================================= --}}

    <style>
        .book-delete-modal-reason {
            width: 100%;
            margin-bottom: 24px;
            text-align: left;
        }

        .book-delete-modal-reason label {
            display: block;

            margin-bottom: 7px;

            color: #173b52;

            font-size: 13px;
            font-weight: 700;
        }

        .book-delete-modal-reason .required {
            color: #e84d4d;
        }

        .book-delete-modal-reason textarea {
            display: block;

            width: 100%;
            min-height: 92px;

            padding: 12px 14px;

            box-sizing: border-box;

            resize: vertical;

            border: 1px solid #d4dfe5;
            border-radius: 10px;

            background: #ffffff;

            color: #173b52;

            font-family: inherit;
            font-size: 13px;
            line-height: 1.5;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .book-delete-modal-reason textarea:focus {
            border-color: #278589;

            box-shadow:
                0 0 0 3px rgba(39, 133, 137, .10);
        }

        .book-delete-modal-reason textarea::placeholder {
            color: #9aa8b4;
        }

        .book-delete-modal-reason small {
            display: block;

            margin-top: 6px;

            color: #718091;

            font-size: 11px;
        }
    </style>


    {{-- =========================================================
     JAVASCRIPT
========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT
            |--------------------------------------------------------------------------
            */

            const searchInput =
                document.getElementById('books-search');

            const searchForm =
                document.getElementById('booksFilterForm');

            let searchTimer = null;

            let booksRequest = null;


            /*
            |--------------------------------------------------------------------------
            | AJAX FILTER / SEARCH
            |--------------------------------------------------------------------------
            |
            | Tidak reload halaman.
            | Hanya bagian hasil buku + pagination yang diganti.
            | Jadi halaman tidak "jedag-jedug" dan posisi scroll tetap.
            |
            */

            async function loadBooks(url, pushUrl = true) {

                if (!searchForm) {
                    return;
                }


                if (booksRequest) {

                    booksRequest.abort();

                }


                const controller =
                    new AbortController();

                booksRequest =
                    controller;


                try {

                    const response =
                        await fetch(
                            url,
                            {
                                headers: {
                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                    'Accept':
                                        'text/html'
                                },

                                signal:
                                    controller.signal
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Gagal memuat data buku.'
                        );

                    }


                    const html =
                        await response.text();


                    const parser =
                        new DOMParser();


                    const doc =
                        parser.parseFromString(
                            html,
                            'text/html'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL BAGIAN YANG BERUBAH
                    |--------------------------------------------------------------------------
                    */

                    const newHeader =
                        doc.querySelector(
                            '.books-table-header'
                        );

                    const newTable =
                        doc.querySelector(
                            '.table-wrapper'
                        );

                    const newPagination =
                        doc.querySelector(
                            '.books-pagination'
                        );


                    const currentHeader =
                        document.querySelector(
                            '.books-table-header'
                        );

                    const currentTable =
                        document.querySelector(
                            '.table-wrapper'
                        );

                    const currentPagination =
                        document.querySelector(
                            '.books-pagination'
                        );


                    if (
                        !newHeader ||
                        !newTable
                    ) {

                        throw new Error(
                            'Struktur hasil buku tidak ditemukan.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GANTI HEADER
                    |--------------------------------------------------------------------------
                    */

                    if (currentHeader) {

                        currentHeader.replaceWith(
                            newHeader
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GANTI TABEL
                    |--------------------------------------------------------------------------
                    */

                    if (currentTable) {

                        currentTable.replaceWith(
                            newTable
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GANTI PAGINATION
                    |--------------------------------------------------------------------------
                    */

                    if (currentPagination) {

                        if (newPagination) {

                            currentPagination.replaceWith(
                                newPagination
                            );

                        } else {

                            currentPagination.remove();

                        }

                    } else if (newPagination) {

                        const table =
                            document.querySelector(
                                '.table-wrapper'
                            );


                        if (table) {

                            table.after(
                                newPagination
                            );

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE URL TANPA RELOAD
                    |--------------------------------------------------------------------------
                    */

                    if (pushUrl) {

                        window.history.pushState(
                            {
                                books: true
                            },
                            '',
                            url
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE FORM
                    |--------------------------------------------------------------------------
                    |
                    | Header baru berisi select kategori.
                    | Kita tetap pakai form search yang lama, jadi input
                    | pencarian tidak kehilangan fokus/isi.
                    |
                    */

                    const newCategory =
                        document.getElementById(
                            'books-filter-kategori'
                        );


                    if (newCategory) {

                        newCategory.onchange =
                            function() {

                                searchForm.requestSubmit();

                            };

                    }

                } catch (error) {

                    if (
                        error.name !==
                        'AbortError'
                    ) {

                        console.error(
                            error
                        );

                    }

                } finally {

                    if (
                        booksRequest ===
                        controller
                    ) {

                        booksRequest =
                            null;

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM -> AJAX
            |--------------------------------------------------------------------------
            */

            if (searchForm) {

                searchForm.addEventListener(
                    'submit',
                    function(event) {

                        event.preventDefault();


                        const formData =
                            new FormData(
                                searchForm
                            );


                        const params =
                            new URLSearchParams(
                                formData
                            );


                        /*
                        | Saat search baru dilakukan, mulai dari halaman 1.
                        | Ini bukan reload halaman; hanya hasil AJAX yang diganti.
                        */

                        params.delete('page');


                        const url =
                            searchForm.action
                            +
                            (
                                params.toString()
                                ? '?' + params.toString()
                                : ''
                            );


                        loadBooks(
                            url,
                            true
                        );

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | JUMLAH PER HALAMAN -> AJAX
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'submit',
                function(event) {

                    const form =
                        event.target.closest(
                            '.books-per-page-form'
                        );


                    if (!form) {
                        return;
                    }


                    event.preventDefault();


                    const formData =
                        new FormData(form);


                    const params =
                        new URLSearchParams(
                            formData
                        );


                    const search =
                        searchInput
                        ? searchInput.value.trim()
                        : '';


                    if (search) {

                        params.set(
                            'search',
                            search
                        );

                    }


                    const category =
                        document.getElementById(
                            'books-filter-kategori'
                        );


                    if (
                        category &&
                        category.value
                    ) {

                        params.set(
                            'category',
                            category.value
                        );

                    }


                    params.delete('page');


                    const url =
                        form.action
                        +
                        (
                            params.toString()
                            ? '?' + params.toString()
                            : ''
                        );


                    loadBooks(
                        url,
                        true
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | AUTO SEARCH
            |--------------------------------------------------------------------------
            */

            if (searchInput) {

                searchInput.addEventListener(
                    'input',
                    function() {

                        clearTimeout(
                            searchTimer
                        );


                        searchTimer =
                            setTimeout(
                                function() {

                                    if (
                                        searchForm.requestSubmit
                                    ) {

                                        searchForm.requestSubmit();

                                    }

                                },
                                350
                            );

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PAGINATION -> AJAX
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const link =
                        event.target.closest(
                            '.books-pagination a'
                        );


                    if (!link) {
                        return;
                    }


                    event.preventDefault();


                    loadBooks(
                        link.href,
                        true
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BACK / FORWARD BROWSER
            |--------------------------------------------------------------------------
            */

            window.addEventListener(
                'popstate',
                function() {

                    loadBooks(
                        window.location.href,
                        false
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | ESC UNTUK TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (event.key === 'Escape') {

                        closeBookDeleteModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKDROP
            |--------------------------------------------------------------------------
            */

            const modal =
                document.getElementById(
                    'bookDeleteModal'
                );


            if (modal) {

                modal.addEventListener(
                    'click',
                    function(event) {

                        if (event.target === this) {

                            closeBookDeleteModal();

                        }

                    }
                );

            }

        });


        /*
        |--------------------------------------------------------------------------
        | FORM DELETE YANG DIPILIH
        |--------------------------------------------------------------------------
        */

        let bookDeleteForm = null;


        /*
        |--------------------------------------------------------------------------
        | BUKA MODAL
        |--------------------------------------------------------------------------
        */

        function openBookDeleteModal(button) {

            bookDeleteForm =
                button.closest(
                    '.delete-book-form'
                );


            if (!bookDeleteForm) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CARI BARIS
            |--------------------------------------------------------------------------
            */

            const row =
                button.closest('tr');


            /*
            |--------------------------------------------------------------------------
            | AMBIL JUDUL BUKU
            |--------------------------------------------------------------------------
            */

            const titleElement =
                row ?
                row.querySelector(
                    'td:first-child strong'
                ) :
                null;


            const bookName =
                titleElement ?
                titleElement.textContent.trim() :
                'Buku';


            /*
            |--------------------------------------------------------------------------
            | MASUKKAN JUDUL KE MODAL
            |--------------------------------------------------------------------------
            */

            const nameTarget =
                document.getElementById(
                    'deleteBookName'
                );


            if (nameTarget) {

                nameTarget.textContent =
                    bookName;

            }


            /*
            |--------------------------------------------------------------------------
            | RESET ALASAN
            |--------------------------------------------------------------------------
            */

            const reasonInput =
                document.getElementById(
                    'bookDeleteReason'
                );


            if (reasonInput) {

                reasonInput.value = '';

            }


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN MODAL
            |--------------------------------------------------------------------------
            */

            const modal =
                document.getElementById(
                    'bookDeleteModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add('show');

            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            /*
            |--------------------------------------------------------------------------
            | KUNCI SCROLL
            |--------------------------------------------------------------------------
            */

            document.body.style.overflow =
                'hidden';


            /*
            |--------------------------------------------------------------------------
            | FOCUS INPUT ALASAN
            |--------------------------------------------------------------------------
            */

            if (reasonInput) {

                setTimeout(function() {

                    reasonInput.focus();

                }, 100);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        function closeBookDeleteModal() {

            const modal =
                document.getElementById(
                    'bookDeleteModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.remove('show');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.style.overflow =
                '';


            bookDeleteForm =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI PENARIKAN
        |--------------------------------------------------------------------------
        */

        function confirmBookDelete() {

            if (!bookDeleteForm) {
                return;
            }


            const reasonInput =
                document.getElementById(
                    'bookDeleteReason'
                );


            if (!reasonInput) {
                return;
            }


            const reason =
                reasonInput.value.trim();


            /*
            |--------------------------------------------------------------------------
            | ALASAN WAJIB
            |--------------------------------------------------------------------------
            */

            if (!reason) {

                reasonInput.focus();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | BUAT INPUT REASON PADA FORM
            |--------------------------------------------------------------------------
            */

            let reasonField =
                bookDeleteForm.querySelector(
                    'input[name="reason"]'
                );


            if (!reasonField) {

                reasonField =
                    document.createElement(
                        'input'
                    );

                reasonField.type =
                    'hidden';

                reasonField.name =
                    'reason';

                bookDeleteForm.appendChild(
                    reasonField
                );

            }


            reasonField.value =
                reason;


            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM
            |--------------------------------------------------------------------------
            */

            bookDeleteForm.submit();

        }
    </script>

@endsection
