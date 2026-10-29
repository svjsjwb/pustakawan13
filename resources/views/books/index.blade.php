@extends('layouts.app')

@section('title', 'Manajemen Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/books.js') }}"></script>
@endpush

@section('content')

    <div class="books-page">

        {{-- HERO HEADER --}}
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
                <a href="{{ route('books.create') }}" class="book-toolbar-btn book-toolbar-add">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">

                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />

                    </svg>

                    Tambah Buku Baru
                </a>
            </div>
        </section>


        {{-- ALERT NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert-success">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />

                </svg>

                <span>{{ session('success') }}</span>

            </div>
        @endif


        <div class="books-content-card">

            {{-- TOOLBAR PENCARIAN + FILTER KATEGORI --}}
            <div class="book-toolbar">

                <div class="book-search-wrapper">

                    <svg class="book-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">

                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />

                    </svg>

                    <input type="text" id="books-search" class="book-search-input"
                        placeholder="Cari buku berdasarkan judul, pengarang, ISBN..." autocomplete="off">

                </div>


                {{-- FILTER KATEGORI --}}
                <div class="books-category-dropdown" id="books-category-dropdown">

                    <button type="button" class="books-category-trigger" id="books-category-trigger" aria-expanded="false">

                        <svg class="books-category-filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />

                        </svg>

                        <span id="books-category-label">
                            Semua Kategori
                        </span>

                        <svg class="books-category-trigger-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                            <polyline points="6 9 12 15 18 9"></polyline>

                        </svg>

                    </button>


                    <div class="books-category-menu" id="books-category-menu">

                        <div class="books-category-menu-title">
                            PILIH KATEGORI
                        </div>


                        <button type="button" class="books-category-option active" data-value="">

                            <span>Semua Kategori</span>

                            <span class="books-category-count">
                                {{ $books->count() }}
                            </span>

                        </button>


                        @foreach ($books->pluck('category')->filter()->unique('id')->sortBy(function ($category) {
                return match ($category->name) {
                    'Buku Pelajaran' => 1,
                    'Buku Umum' => 2,
                    default => 99,
                };
            }) as $category)
                            @php
                                $categoryCount = $books->where('category_id', $category->id)->count();
                            @endphp

                            <button type="button" class="books-category-option" data-value="{{ $category->id }}">

                                <span>
                                    {{ $category->name }}
                                </span>

                                <span class="books-category-count">
                                    {{ $categoryCount }}
                                </span>

                            </button>
                        @endforeach

                    </div>


                    {{-- Hidden select tetap dipakai JavaScript sebagai sumber filter --}}
                    <select id="books-filter-kategori" class="books-category-filter books-category-hidden-select"
                        aria-hidden="true" tabindex="-1">

                        <option value="">
                            Semua Kategori
                        </option>

                        @foreach ($books->pluck('category')->filter()->unique('id')->sortBy('name') as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

            </div>


            {{-- TABLE HEADER --}}
            <div class="books-table-header">

                <div class="books-table-title">
                    <h2>Daftar Koleksi Buku</h2>
                </div>

                <div class="books-table-controls">

                    <span class="book-total" id="books-total">

                        Total: {{ $books->count() }} Buku

                    </span>

                </div>

            </div>


            {{-- DATA TABLE --}}
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
                            <tr data-category="{{ $book->category_id }}"
                                data-search="{{ strtolower(($book->title ?? '') . ' ' . ($book->author ?? '') . ' ' . ($book->isbn ?? '') . ' ' . ($book->category->name ?? '')) }}">

                                <td>
                                    <strong>
                                        {{ $book->title }}
                                    </strong>
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


                                {{-- AKSI --}}
                                <td>

                                    <div class="book-actions">

                                        {{-- EDIT --}}
                                        <a href="{{ route('books.edit', $book) }}" class="action-edit" title="Edit Buku"
                                            aria-label="Edit Buku">

                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                                <path d="M12 20h9" />

                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />

                                            </svg>

                                        </a>


                                        {{-- HAPUS --}}
                                        <form action="{{ route('books.destroy', $book) }}" method="POST"
                                            class="delete-book-form">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="book-action-btn delete" title="Hapus Buku"
                                                aria-label="Hapus Buku" onclick="openBookDeleteModal(this)">

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

                                    Belum ada data buku.

                                </td>

                            </tr>
                        @endforelse


                        {{-- HASIL PENCARIAN KOSONG --}}
                        <tr id="no-search-results" style="display:none;">

                            <td colspan="6" class="empty-books">

                                Tidak ada buku yang sesuai dengan pencarian atau filter.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- PAGINASI CLIENT-SIDE --}}
            <div class="books-pagination" id="books-pagination">

                <div class="books-pagination-links" id="books-pagination-links">
                </div>

                <div class="books-pagination-info" id="books-pagination-info">
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL HAPUS BUKU
    ========================================================== --}}

    <div id="bookDeleteModal" class="book-delete-modal-overlay" aria-hidden="true">

        <div class="book-delete-modal" role="dialog" aria-modal="true" aria-labelledby="bookDeleteModalTitle">


            {{-- ICON --}}
            <div class="book-delete-modal-icon">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">

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

                Tarik Buku dari Koleksi

            </h3>


            {{-- DESCRIPTION --}}
            <p class="book-delete-modal-description">

                Buku ini akan dihapus dari koleksi aktif.
                <br>
                Masukkan alasan penarikan sebelum melanjutkan.

            </p>


            {{-- BOOK INFO --}}
            <div class="book-delete-modal-info">

                <div>

                    <span>JUDUL BUKU</span>

                    <strong id="deleteBookName">
                        -
                    </strong>

                </div>


                <div>

                    <span>JUMLAH EKSEMPLAR</span>

                    <strong id="deleteBookStock">
                        -
                    </strong>

                </div>

            </div>


            {{-- REASON --}}
            <div class="book-delete-modal-reason">

                <label for="bookDeleteReason">

                    Alasan Penarikan

                    <span class="required">
                        *
                    </span>

                </label>


                <textarea id="bookDeleteReason" maxlength="500"
                    placeholder="Contoh: Buku rusak berat, hilang, atau ditarik dari koleksi." required></textarea>


                <small>
                    Maksimal 500 karakter.
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

@endsection
