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
                <button type="button" class="book-toolbar-btn book-toolbar-add" id="openBookCreateModal"
                    aria-haspopup="dialog" aria-controls="bookCreateModal">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">

                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />

                    </svg>

                    Tambah Buku Baru
                </button>
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

        @if (session('error'))
            <div class="alert-error" style="display: flex; align-items: center; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; flex-shrink: 0; color: #dc2626;">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif


        <div class="books-content-card">

            {{-- TOOLBAR PENCARIAN + FILTER KATEGORI --}}
            <form method="GET" action="{{ route('books.index') }}" id="books-filter-form" class="book-toolbar">

                <div class="book-search-wrapper">

                    <svg class="book-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">

                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />

                    </svg>

                    <input type="text" id="books-search" name="search" class="book-search-input"
                        value="{{ request('search') }}"
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
                            {{ ($selectedCategory = $categories->firstWhere('id', request('category_id')))?->name ?? 'Semua Kategori' }}
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


                        <button type="button" class="books-category-option{{ !request('category_id') ? ' active' : '' }}" data-value="">

                            <span>Semua Kategori</span>

                            <span class="books-category-count">
                                {{ $totalBooksCount ?? $books->total() }}
                            </span>

                        </button>


                        @foreach ($categories as $category)
                            <button type="button" class="books-category-option{{ request('category_id') == $category->id ? ' active' : '' }}" data-value="{{ $category->id }}">

                                <span>
                                    {{ $category->name }}
                                </span>

                                <span class="books-category-count">
                                    {{ $category->books_count ?? 0 }}
                                </span>

                            </button>
                        @endforeach

                    </div>


                    {{-- Hidden select tetap dipakai JavaScript sebagai sumber filter --}}
                    <select id="books-filter-kategori" name="category_id" class="books-category-filter books-category-hidden-select"
                        aria-hidden="true" tabindex="-1">

                        <option value="">
                            Semua Kategori
                        </option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

            </form>


            {{-- TABLE HEADER --}}
            <div class="books-table-header">

                <div class="books-table-title">
                    <h2>Daftar Koleksi Buku</h2>
                </div>

                <div class="books-table-controls">

                    <span class="book-total" id="books-total">

                        Total: {{ $totalBooksCount ?? $books->total() }} Buku

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
                                    @if($book->subcategory)
                                        <div style="font-size: 0.8rem; color: #64748b;">{{ $book->subcategory->name }}</div>
                                    @endif
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


            {{-- PAGINASI --}}
            @if ($books->total() > 0)
                <div class="books-pagination" id="books-pagination">
                    {{ $books->onEachSide(1)->links('partials.books-pagination') }}
                </div>
            @endif

        </div>

    </div>

    <div id="bookCreateModal"
        class="book-create-modal-overlay{{ $errors->any() && !isset($editBook) ? ' is-open' : '' }}"
        aria-hidden="{{ $errors->any() && !isset($editBook) ? 'false' : 'true' }}">
        <div class="book-create-modal-dialog" role="dialog" aria-modal="true"
            aria-labelledby="bookCreateModalTitle">
            @include('books.create', ['isBookModal' => true])
        </div>
    </div>

    @isset($editBook)
        <div id="bookEditModal" class="book-create-modal-overlay is-open" aria-hidden="false">
            <div class="book-create-modal-dialog" role="dialog" aria-modal="true"
                aria-labelledby="bookEditModalTitle">
                @include('books.edit', ['book' => $editBook, 'isEditModal' => true])
            </div>
        </div>
    @endisset

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

    @push('scripts')
        <script>
            (() => {
                const modal = document.getElementById('bookCreateModal');
                const openButton = document.getElementById('openBookCreateModal');

                if (!modal || !openButton) return;

                const closeButton = modal.querySelector('.book-create-modal-close');
                const dialog = modal.querySelector('.book-create-modal-dialog');

                window.openBookCreateModal = () => {
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    closeButton?.focus();
                };

                window.closeBookCreateModal = () => {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    openButton.focus();
                };

                openButton.addEventListener('click', window.openBookCreateModal);
                modal.addEventListener('click', event => {
                    if (event.target === modal) window.closeBookCreateModal();
                });
                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        if (document.getElementById('isbnScannerModal')?.classList.contains('is-open')) {
                            return;
                        }
                        window.closeBookCreateModal();
                    }
                });

                if (modal.classList.contains('is-open')) {
                    document.body.style.overflow = 'hidden';
                    closeButton?.focus();
                }

                dialog.addEventListener('click', event => event.stopPropagation());
            })();

            (() => {
                const modal = document.getElementById('bookEditModal');
                if (!modal) return;

                const indexUrl = @json(route('books.index'));
                window.closeBookEditModal = event => {
                    event?.preventDefault();
                    window.location.href = indexUrl;
                };

                modal.addEventListener('click', event => {
                    if (event.target === modal) window.closeBookEditModal();
                });
                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        const copyCreateInline = document.getElementById('copyCreateInline');
                        const copyEditInline = document.getElementById('copyEditInline');
                        if ((copyCreateInline && !copyCreateInline.hidden) ||
                            (copyEditInline && !copyEditInline.hidden)) {
                            closeCopyInline();
                            return;
                        }
                        if (document.getElementById('copyDeleteModal')?.classList.contains('show')) {
                            closeCopyDeleteModal();
                            return;
                        }
                        window.closeBookEditModal();
                    }
                });
                document.body.style.overflow = 'hidden';
                modal.querySelector('.book-create-modal-close')?.focus();
            })();

            let copyDeleteForm = null;
            window.openCopyDeleteModal = button => {
                copyDeleteForm = button.closest('.copy-delete-form');
                if (!copyDeleteForm) return;

                const row = button.closest('tr');
                const modal = document.getElementById('copyDeleteModal');
                const reason = document.getElementById('copyDeleteReason');
                const copyNumber = row?.querySelector('.copy-barcode')?.textContent.trim() || '-';
                if (!modal || !reason) return;

                document.getElementById('deleteCopyNumber').textContent = copyNumber;
                reason.value = '';
                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
                reason.focus();
            };

            window.closeCopyDeleteModal = () => {
                const modal = document.getElementById('copyDeleteModal');
                if (!modal) return;
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                copyDeleteForm = null;
            };

            window.confirmCopyDelete = () => {
                const reason = document.getElementById('copyDeleteReason');
                const value = reason?.value.trim();
                if (!copyDeleteForm || !reason || !value) {
                    reason?.focus();
                    return;
                }

                let field = copyDeleteForm.querySelector('input[name="reason"]');
                if (!field) {
                    field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = 'reason';
                    copyDeleteForm.appendChild(field);
                }
                field.value = value;
                copyDeleteForm.submit();
            };

            const copyDeleteModal = document.getElementById('copyDeleteModal');
            if (copyDeleteModal) {
                document.body.appendChild(copyDeleteModal);
            }
            copyDeleteModal?.addEventListener('click', event => {
                if (event.target.id === 'copyDeleteModal') window.closeCopyDeleteModal();
            });

            // ── BOOK DELETE MODAL (GLOBAL FALLBACK) ────────────────
            let activeBookDeleteForm = null;
            window.openBookDeleteModal = function (button) {
                activeBookDeleteForm = button.closest('.delete-book-form');
                if (!activeBookDeleteForm) return;

                const row = button.closest('tr');
                const titleElement = row ? row.querySelector('td:nth-child(1) strong') : null;
                const bookName = titleElement ? titleElement.textContent.trim() : 'buku ini';
                const stockElement = row ? row.querySelector('td:nth-child(5)') : null;
                const bookStock = stockElement ? stockElement.textContent.trim() : '-';

                const modal = document.getElementById('bookDeleteModal');
                const modalBookName = document.getElementById('deleteBookName') || document.getElementById('modalBookName');
                const modalBookStock = document.getElementById('deleteBookStock');
                const reasonInput = document.getElementById('bookDeleteReason');

                if (modalBookName) modalBookName.textContent = bookName;
                if (modalBookStock) modalBookStock.textContent = bookStock;
                if (reasonInput) reasonInput.value = '';

                if (modal) {
                    modal.classList.add('show', 'is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => reasonInput?.focus(), 100);
                }
            };

            window.closeBookDeleteModal = function () {
                const modal = document.getElementById('bookDeleteModal');
                if (modal) {
                    modal.classList.remove('show', 'is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }
                activeBookDeleteForm = null;
            };

            window.confirmBookDelete = function () {
                const reasonInput = document.getElementById('bookDeleteReason');
                const reason = reasonInput ? reasonInput.value.trim() : '';

                if (!reason) {
                    alert('Silakan masukkan alasan penarikan buku.');
                    reasonInput?.focus();
                    return;
                }

                if (!activeBookDeleteForm) return;

                let hiddenInput = activeBookDeleteForm.querySelector('input[name="reason"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'reason';
                    activeBookDeleteForm.appendChild(hiddenInput);
                }
                hiddenInput.value = reason;

                activeBookDeleteForm.submit();
            };

            const bookDeleteModalEl = document.getElementById('bookDeleteModal');
            if (bookDeleteModalEl) {
                document.body.appendChild(bookDeleteModalEl);
                bookDeleteModalEl.addEventListener('click', event => {
                    if (event.target === bookDeleteModalEl) window.closeBookDeleteModal();
                });
            }

            // ── AUTOMATIC SEARCH BOOKS (FALLBACK) ────────────────
            (() => {
                const searchInput = document.getElementById('books-search');
                const filterForm = document.getElementById('books-filter-form');
                if (!searchInput || !filterForm) return;

                let timer = null;
                const liveSearch = () => {
                    const url = new URL(filterForm.action || window.location.href, window.location.origin);
                    const formData = new FormData(filterForm);
                    for (const [k, v] of formData.entries()) {
                        if (v) url.searchParams.set(k, v);
                        else url.searchParams.delete(k);
                    }
                    window.history.replaceState({}, '', url.toString());

                    const tbody = document.getElementById('books-table-body');
                    if (tbody) tbody.style.opacity = '0.5';

                    fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTbody = doc.getElementById('books-table-body');
                        if (newTbody && tbody) tbody.innerHTML = newTbody.innerHTML;

                        const newPag = doc.getElementById('books-pagination');
                        const curPag = document.getElementById('books-pagination');
                        if (curPag) {
                            if (newPag) {
                                curPag.innerHTML = newPag.innerHTML;
                                curPag.style.display = '';
                            } else {
                                curPag.style.display = 'none';
                            }
                        }
                    })
                    .catch(e => console.error(e))
                    .finally(() => {
                        if (tbody) tbody.style.opacity = '1';
                    });
                };

                searchInput.addEventListener('input', function () {
                    const kw = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#books-table-body tr:not(#no-search-results)');
                    if (kw) {
                        rows.forEach(r => {
                            const txt = (r.dataset.search || r.textContent).toLowerCase();
                            r.style.display = txt.includes(kw) ? '' : 'none';
                        });
                    } else {
                        rows.forEach(r => r.style.display = '');
                    }
                    clearTimeout(timer);
                    timer = setTimeout(liveSearch, 350);
                });

                searchInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(timer);
                        liveSearch();
                    }
                });
            })();
        </script>
    @endpush

@endsection
