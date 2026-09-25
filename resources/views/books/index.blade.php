@extends('layouts.app')

@section('title', 'Manajemen Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="books-page">

    <section class="books-hero">

        <div class="books-hero-content">

            <div class="books-hero-label">

                <span class="books-hero-dot"></span>

                Pusat Perpustakaan

            </div>

            <h1>Manajemen Buku</h1>

            <p>

                Tambahkan, ubah, atau hapus data buku dalam koleksi perpustakaan.

            </p>

        </div>

        <div class="books-hero-action">

            <a

                href="{{ route('books.create') }}"

                class="book-toolbar-btn book-toolbar-add"

            >

                <svg

                    viewBox="0 0 24 24"

                    fill="none"

                    stroke="currentColor"

                    stroke-width="2.2"

                    stroke-linecap="round"

                    stroke-linejoin="round"

                >

                    <line x1="12" y1="5" x2="12" y2="19" />

                    <line x1="5" y1="12" x2="19" y2="12" />

                </svg>

                Tambah Buku Baru

            </a>

        </div>

    </section>

    @if(session('success'))

        <div class="alert-success">

            <svg

                viewBox="0 0 24 24"

                fill="none"

                stroke="currentColor"

                stroke-width="2.5"

                stroke-linecap="round"

                stroke-linejoin="round"

            >

                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />

                <polyline points="22 4 12 14.01 9 11.01" />

            </svg>

            <span>{{ session('success') }}</span>

        </div>

    @endif

    <div class="book-toolbar">

        <div class="book-search-wrapper">

            <svg

                class="book-search-icon"

                viewBox="0 0 24 24"

                fill="none"

                stroke="currentColor"

                stroke-width="2"

                stroke-linecap="round"

                stroke-linejoin="round"

            >

                <circle cx="11" cy="11" r="8" />

                <line x1="21" y1="21" x2="16.65" y2="16.65" />

            </svg>

            <input

                type="text"

                id="books-search"

                class="book-search-input"

                placeholder="Cari buku berdasarkan judul, pengarang, ISBN..."

                autocomplete="off"

            >

        </div>

        <button

            type="button"

            id="books-search-button"

            class="book-toolbar-btn book-search-btn"

        >

            Cari

        </button>

    </div>

    <div class="books-table-header">

        <div class="books-table-title">

            <h2>Daftar Koleksi Buku</h2>

        </div>

        <div class="books-table-controls">

            <div class="category-filter-wrapper">

                <div class="books-category-select-box">

                    <select

                        id="books-filter-kategori"

                        class="books-category-filter"

                    >

                        <option value="">

                            Semua Kategori

                        </option>

                        @foreach($books->pluck('category')->filter()->unique('id')->sortBy('name') as $category)

                            <option value="{{ $category->id }}">

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

                        stroke-linejoin="round"

                    >

                        <polyline points="6 9 12 15 18 9"></polyline>

                    </svg>

                </div>

                <span

                    class="book-total"

                    id="books-total"

                >

                    Total:

                    {{ $books->count() }}

                    Buku

                </span>

            </div>

        </div>

    </div>

    <div class="table-wrapper">

        <table class="books-table">

            <thead>

                <tr>

                    <th>Judul</th>

                    <th>Penulis</th>

                    <th>ISBN</th>

                    <th>Kategori</th>

                    <th>Stok</th>

                    <th>Aksi</th>

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

                        ) }}"

                    >

                        <td>

                            <strong>{{ $book->title }}</strong>

                        </td>

                        <td>

                            {{ $book->author ?? '-' }}

                        </td>

                        <td>

                            {{ $book->isbn ?? '-' }}

                        </td>

                        <td>

                            {{ $book->category->name ?? '-' }}

                        </td>

                        <td>

                            {{ $book->available_stock ?? 0 }}

                        </td>

                        <td>

                            <div class="book-actions">

                                <a

                                    href="{{ route('books.edit', $book) }}"

                                    class="action-edit"

                                    title="Edit Buku"

                                    aria-label="Edit Buku"

                                >

                                    <svg

                                        viewBox="0 0 24 24"

                                        fill="none"

                                        stroke="currentColor"

                                        stroke-width="2"

                                        stroke-linecap="round"

                                        stroke-linejoin="round"

                                    >

                                        <path d="M12 20h9" />

                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />

                                    </svg>

                                </a>

                                <form

                                    action="{{ route('books.destroy', $book) }}"

                                    method="POST"

                                    class="delete-book-form"

                                    onsubmit="return confirmDeleteBook()"

                                >

                                    @csrf

                                    @method('DELETE')

                                    <button

                                        type="submit"

                                        class="book-action-btn delete"

                                        title="Hapus Buku"

                                        aria-label="Hapus Buku"

                                    >

                                        <svg

                                            viewBox="0 0 24 24"

                                            fill="none"

                                            stroke="currentColor"

                                            stroke-width="2"

                                            stroke-linecap="round"

                                            stroke-linejoin="round"

                                        >

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

                            Belum ada data buku.

                        </td>

                    </tr>

                @endforelse

                <tr

                    id="no-search-results"

                    style="display:none;"

                >

                    <td

                        colspan="6"

                        class="empty-books"

                    >

                        Tidak ada buku yang sesuai dengan pencarian atau filter.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <div

        class="books-pagination"

        id="books-pagination"

    >

        <div

            class="books-pagination-links"

            id="books-pagination-links"

        ></div>

        <div

            class="books-pagination-info"

            id="books-pagination-info"

        ></div>

    </div>

</div>

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

    const tableBody =

        document.getElementById('books-table-body');

    const noResults =

        document.getElementById('no-search-results');

    const pagination =

        document.getElementById('books-pagination');

    const paginationLinks =

        document.getElementById('books-pagination-links');

    const paginationInfo =

        document.getElementById('books-pagination-info');

    const rows = Array.from(

        document.querySelectorAll(

            '#books-table-body tr[data-category]'

        )

    );

    const BOOKS_PER_PAGE = 25;

    let currentPage = 1;

    let filteredRows = [...rows];

    function getFilteredRows() {

        const keyword =

            searchInput

                ? searchInput.value.toLowerCase().trim()

                : '';

        const selectedCategory =

            categoryFilter

                ? categoryFilter.value

                : '';

        return rows.filter(function (row) {

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

            return matchSearch && matchCategory;

        });

    }

    function getPageNumbers(current, last) {

        if (last <= 0) {

            return [];

        }

        const start =

            Math.max(1, current - 2);

        const end =

            Math.min(last, current + 2);

        const pages = [];

        for (let page = start; page <= end; page++) {

            pages.push(page);

        }

        return pages;

    }

    function renderPagination() {

        const total =

            filteredRows.length;

        const lastPage =

            Math.max(

                1,

                Math.ceil(total / BOOKS_PER_PAGE)

            );

        if (currentPage > lastPage) {

            currentPage = lastPage;

        }

        paginationLinks.innerHTML = '';

        if (total === 0) {

            paginationInfo.innerHTML =

                'Menampilkan <strong>0</strong> - <strong>0</strong> dari <strong>0</strong> hasil';

            pagination.style.display = 'none';

            return;

        }

        pagination.style.display = 'flex';

        const prevButton =

            document.createElement('button');

        prevButton.type = 'button';

        prevButton.className =

            'books-page-btn' +

            (currentPage === 1

                ? ' disabled'

                : '');

        prevButton.innerHTML =

            '&laquo; Prev';

        if (currentPage > 1) {

            prevButton.addEventListener(

                'click',

                function () {

                    currentPage--;

                    renderBooks();

                }

            );

        }

        paginationLinks.appendChild(

            prevButton

        );

        getPageNumbers(

            currentPage,

            lastPage

        ).forEach(function (page) {

            const button =

                document.createElement('button');

            button.type = 'button';

            button.className =

                'books-page-btn' +

                (page === currentPage

                    ? ' active'

                    : '');

            button.textContent = page;

            button.addEventListener(

                'click',

                function () {

                    currentPage = page;

                    renderBooks();

                }

            );

            paginationLinks.appendChild(

                button

            );

        });

        const nextButton =

            document.createElement('button');

        nextButton.type = 'button';

        nextButton.className =

            'books-page-btn' +

            (currentPage === lastPage

                ? ' disabled'

                : '');

        nextButton.innerHTML =

            'Next &raquo;';

        if (currentPage < lastPage) {

            nextButton.addEventListener(

                'click',

                function () {

                    currentPage++;

                    renderBooks();

                }

            );

        }

        paginationLinks.appendChild(

            nextButton

        );

        const first =

            ((currentPage - 1) * BOOKS_PER_PAGE) + 1;

        const last =

            Math.min(

                currentPage * BOOKS_PER_PAGE,

                total

            );

        paginationInfo.innerHTML =

            'Menampilkan ' +

            '<strong>' + first + '</strong>' +

            ' - ' +

            '<strong>' + last + '</strong>' +

            ' dari ' +

            '<strong>' + total + '</strong>' +

            ' hasil';

    }

    function renderBooks() {

        filteredRows =

            getFilteredRows();

        const total =

            filteredRows.length;

        const lastPage =

            Math.max(

                1,

                Math.ceil(

                    total / BOOKS_PER_PAGE

                )

            );

        if (currentPage > lastPage) {

            currentPage = lastPage;

        }

        rows.forEach(function (row) {

            row.style.display =

                'none';

        });

        const start =

            (currentPage - 1) *

            BOOKS_PER_PAGE;

        const end =

            start +

            BOOKS_PER_PAGE;

        filteredRows

            .slice(start, end)

            .forEach(function (row) {

                row.style.display =

                    '';

            });

        if (totalElement) {

            totalElement.textContent =

                'Total: ' +

                total +

                ' Buku';

        }

        if (noResults) {

            noResults.style.display =

                total === 0

                    ? ''

                    : 'none';

        }

        renderPagination();

    }

    function resetAndRender() {

        currentPage = 1;

        renderBooks();

    }

    if (searchInput) {

        searchInput.addEventListener(

            'input',

            resetAndRender

        );

    }

    if (searchButton) {

        searchButton.addEventListener(

            'click',

            resetAndRender

        );

    }

    if (searchInput) {

        searchInput.addEventListener(

            'keydown',

            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    resetAndRender();

                }

            }

        );

    }

    if (categoryFilter) {

        categoryFilter.addEventListener(

            'change',

            resetAndRender

        );

    }

    renderBooks();

});

function confirmDeleteBook() {

    return confirm(

        'Yakin ingin menghapus buku ini dari koleksi perpustakaan?'

    );

}

</script>

@endsection
