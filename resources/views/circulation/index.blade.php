@extends('layouts.app')

@section('title', 'Sirkulasi Peminjaman Buku')

@push('styles')

<link

    rel="stylesheet"

    href="{{ asset('css/circulation.css') }}">
@endpush

@section('content')

<section

    class="page"

    id="page-circulation">

    {{-- =====================================================

         HERO HEADER

    ====================================================== --}}

    <div class="circulation-hero">

        <div class="circulation-hero-content">

            <div class="circulation-hero-label">

                <span class="circulation-hero-dot"></span>

                Pusat Perpustakaan

            </div>

            <h1>

                Peminjaman Buku

            </h1>

            <p>

                Kelola transaksi sirkulasi peminjaman, perpanjangan, dan pengembalian buku.

            </p>

        </div>

        <div class="circulation-hero-action">
            <button type="button" class="circulation-add-button" onclick="openLoanModal()">
                + Tambah Peminjam Buku
            </button>
        </div>

    </div>

    {{-- =====================================================

         ALERT SUCCESS

    ====================================================== --}}

    @if(session('success'))

        <div class="alert-success">

            {{ session('success') }}

        </div>

    @endif

    {{-- =====================================================

         ALERT ERROR

    ====================================================== --}}

    @if(session('error'))

        <div class="alert-error">

            {{ session('error') }}

        </div>

    @endif

    {{-- =====================================================

         VALIDATION ERROR

    ====================================================== --}}

    @if($errors->any() && old('circulation_form_action') !== 'borrow')

        <div class="alert-error">

            <ul class="error-list">

                @foreach($errors->all() as $error)

                    <li>

                        {{ $error }}

                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    @php
        $pendingExtensionRequests = isset($extensionRequests)
            ? collect($extensionRequests)
            : collect($borrowings)->filter(
                fn ($borrowing) => $borrowing->extension_status === 'menunggu'
            );
    @endphp

    @if($pendingExtensionRequests->isNotEmpty())
        <div class="extension-requests-card">
            <div class="extension-requests-header">
                <div>
                    <span class="extension-requests-label">PERLU PERSETUJUAN</span>
                    <h3>Permintaan Perpanjangan</h3>
                    <p>Pengajuan perpanjangan peminjaman yang menunggu persetujuan Admin.</p>
                </div>

                <span class="extension-requests-count">
                    {{ $pendingExtensionRequests->count() }} Pengajuan
                </span>
            </div>

            <div class="extension-requests-list">
                @foreach($pendingExtensionRequests as $request)
                    @php
                        $requestBook = $request->details->first()?->book;
                        $requestedDue = $request->extension_requested_due_at
                            ? \Carbon\Carbon::parse($request->extension_requested_due_at)
                            : null;
                        $currentDue = $request->due_at
                            ? \Carbon\Carbon::parse($request->due_at)
                            : null;
                    @endphp

                    <div class="extension-request-item">
                        <div class="extension-request-main">
                            <div class="extension-request-member">
                                {{ $request->member->name ?? '-' }}
                            </div>

                            <div class="extension-request-book">
                                {{ $requestBook->title ?? 'Buku' }}
                            </div>

                            <div class="extension-request-dates">
                                <span>
                                    Jatuh tempo:
                                    <strong>{{ $currentDue?->format('d/m/Y') ?? '-' }}</strong>
                                </span>

                                <span class="extension-request-arrow">→</span>

                                <span class="extension-request-new-date">
                                    Meminta sampai:
                                    <strong>{{ $requestedDue?->format('d/m/Y') ?? '-' }}</strong>
                                </span>
                            </div>

                            @if($request->extension_reason)
                                <div class="extension-request-reason">
                                    Alasan: {{ $request->extension_reason }}
                                </div>
                            @endif
                        </div>

                        <div class="extension-request-actions">
                            <form
                                action="{{ route('borrowings.rejectExtension', $request) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="extension-request-btn extension-request-btn-reject">
                                    Tolak
                                </button>
                            </form>

                            <form
                                action="{{ route('borrowings.approveExtension', $request) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="extension-request-btn extension-request-btn-approve">
                                    Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div
        id="loanModal"
        class="loan-modal-overlay{{ $errors->any() && old('circulation_form_action') === 'borrow' ? ' open' : '' }}"
        aria-hidden="{{ $errors->any() && old('circulation_form_action') === 'borrow' ? 'false' : 'true' }}">
        <section class="loan-modal-card" role="dialog" aria-modal="true"
            aria-labelledby="loanModalTitle" aria-describedby="loanModalDescription">
            <div class="loan-modal-scroll">
            <header class="loan-modal-header">
                <div>
                    <h2 id="loanModalTitle">Pinjam Buku</h2>
                    <p id="loanModalDescription">Pilih anggota, buku, dan tanggal peminjaman.</p>
                </div>
                <button type="button" class="loan-modal-close" onclick="closeLoanModal()"
                    aria-label="Tutup form peminjaman">&times;</button>
            </header>

            @if($errors->any() && old('circulation_form_action') === 'borrow')
                <div class="loan-modal-errors" role="alert">
                    <strong>Periksa kembali data peminjaman.</strong>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="loan-modal-form-card">
                <form action="{{ route('borrowings.store') }}" method="POST" id="circulationForm">
                    @csrf
                    <input type="hidden" name="circulation_form_action" value="borrow">

                    <div class="field">
                        <label for="member_id">Anggota</label>
                        <select name="member_id" id="member_id" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="bookSearch">Buku</label>
                        <div class="book-picker">
                            <input
                                type="search"
                                id="bookSearch"
                                class="book-picker-search"
                                placeholder="Cari atau pilih buku..."
                                autocomplete="off"
                                role="combobox"
                                aria-autocomplete="list"
                                aria-haspopup="listbox"
                                aria-expanded="false"
                                aria-controls="bookSuggestions"
                                required>
                            <select name="book_id" id="book_id" class="book-picker-select" aria-hidden="true" tabindex="-1">
                                <option value="">-- Pilih Buku --</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}"
                                        @selected(old('book_id') == $book->id)
                                        @disabled($book->available_stock <= 0)>
                                        {{ $book->title }} —
                                        @if($book->available_stock > 0)
                                            Stok {{ $book->available_stock }}
                                        @else
                                            STOK HABIS
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div id="bookSuggestions" class="book-picker-suggestions" role="listbox" hidden></div>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label for="borrowed_at">Tanggal Pinjam</label>
                            <div class="date-input-wrap">
                                <input type="date" name="borrowed_at" id="borrowed_at"
                                    value="{{ old('borrowed_at', now()->format('Y-m-d')) }}" required>
                                <button type="button" class="date-picker-button" data-date-input="borrowed_at"
                                    aria-label="Pilih tanggal pinjam">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path d="M16 3v4M8 3v4M3 11h18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="field">
                            <label for="due_at">Tanggal Pengembalian</label>
                            <div class="date-input-wrap">
                                <input type="date" name="due_at" id="due_at"
                                    value="{{ old('due_at', now()->addDays(7)->format('Y-m-d')) }}" required>
                                <button type="button" class="date-picker-button" data-date-input="due_at"
                                    aria-label="Pilih tanggal pengembalian">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path d="M16 3v4M8 3v4M3 11h18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="loan-modal-actions">
                        <button type="button" class="loan-modal-cancel" onclick="closeLoanModal()">Batal</button>
                        <button type="submit" class="loan-submit">Proses Peminjaman</button>
                    </div>
                </form>
            </div>
            </div>
        </section>
    </div>

    {{-- =====================================================

         MAIN TWO COLUMN

    ====================================================== --}}

    <div class="two-col">

        <div class="card borrowing-card">

            <div class="card-pad">

                {{-- HEADER --}}

                <div class="borrowing-header">

                    <div>

                        <h3>

                            Daftar Peminjaman

                        </h3>

                        <p

                            class="loan-description circulation-period-description">

                            Laporan transaksi peminjaman buku

                            periode bulanan.

                        </p>

                    </div>

                </div>

                {{-- FILTER --}}

                <div class="circulation-filter-bar">

                    {{-- NAVIGASI BULAN --}}

                    <div class="circulation-month-controls">

                        <div class="month-navigator">

                            <a

                                href="{{ route(

                                    'borrowings.index',

                                    [

                                        'month' => $prevMonth,

                                        'year' => $prevYear

                                    ]

                                ) }}"

                                class="btn-nav-month"

                                title="Bulan Sebelumnya">

                                &#8249;

                            </a>

                            <span class="month-display-label">

                                {{ $monthLabel }}

                            </span>

                            <a

                                href="{{ route(

                                    'borrowings.index',

                                    [

                                        'month' => $nextMonth,

                                        'year' => $nextYear

                                    ]

                                ) }}"

                                class="btn-nav-month"

                                title="Bulan Berikutnya">

                                &#8250;

                            </a>

                        </div>

                        @if(!$isCurrentMonth)

                            <a

                                href="{{ route('borrowings.index') }}"

                                class="btn-today-badge"

                                title="Kembali ke Bulan Berjalan">

                                Bulan Ini

                            </a>

                        @endif

                    </div>

                    {{-- SEARCH --}}

                    <div class="borrowing-search-wrap">

                        <input

                            type="text"

                            id="borrowingSearch"

                            placeholder="Cari di tabel..."

                            autocomplete="off">

                    </div>

                </div>

                {{-- TABLE --}}

                <div class="table-wrap">

                    <table id="borrowingTable">

                        <thead>

                            <tr>

                                <th>

                                    ANGGOTA

                                </th>

                                <th>

                                    BUKU

                                </th>

                                <th>

                                    PINJAM

                                </th>

                                <th>

                                    TANGGAL PENGEMBALIAN

                                </th>

                                <th>

                                    STATUS

                                </th>

                                <th>

                                    AKSI

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($borrowings as $borrowing)

                                @php

                                    $isLate =

                                        $borrowing->status === 'dipinjam'

                                        &&

                                        now()->startOfDay()->gt(

                                            \Carbon\Carbon::parse(

                                                $borrowing->due_at

                                            )->startOfDay()

                                        );

                                    if ($isLate) {

                                        $displayStatus =

                                            'terlambat';

                                    } else {

                                        $displayStatus =

                                            $borrowing->status;

                                    }

                                @endphp

                                <tr class="borrowing-row">

                                    {{-- ANGGOTA --}}

                                    <td class="member-cell">

                                        {{ $borrowing->member->name ?? '-' }}

                                    </td>

                                    {{-- BUKU --}}

                                    <td class="book-cell">

                                        @forelse($borrowing->details as $detail)

                                            <span>

                                                {{ $detail->book->title ?? '-' }}

                                            </span>

                                            @if (!$loop->last)

                                                <br>

                                            @endif

                                        @empty

                                            -

                                        @endforelse

                                    </td>

                                    {{-- PINJAM --}}

                                    <td>

                                        {{ \Carbon\Carbon::parse(

                                            $borrowing->borrowed_at

                                        )->format('d/m/Y') }}

                                    </td>

                                    {{-- PENGEMBALIAN --}}

                                    <td>

                                        {{ \Carbon\Carbon::parse(

                                            $borrowing->due_at

                                        )->format('d/m/Y') }}

                                    </td>

                                    {{-- STATUS --}}

                                    <td>

                                        @if(

                                            $displayStatus === 'dipinjam'

                                        )

                                            <span class="status-badge aktif">

                                                <span class="status-dot"></span>

                                                Sedang Dipinjam

                                            </span>

                                        @elseif(

                                            $displayStatus === 'diperpanjang'

                                        )

                                            <span class="status-badge diperpanjang">

                                                <span class="status-dot"></span>

                                                Diperpanjang

                                            </span>

                                        @elseif(

                                            $displayStatus === 'terlambat'

                                        )

                                            <span class="status-badge terlambat">

                                                <span class="status-dot"></span>

                                                Terlambat

                                            </span>

                                        @elseif(

                                            $displayStatus === 'dikembalikan'

                                        )

                                            <span class="status-badge kembali">

                                                <span class="status-dot"></span>

                                                Selesai

                                            </span>

                                        @else

                                            <span class="status-badge aktif">

                                                <span class="status-dot"></span>

                                                {{ ucfirst(

                                                    str_replace(

                                                        '_',

                                                        ' ',

                                                        $displayStatus

                                                    )

                                                ) }}

                                            </span>

                                        @endif

                                    </td>

                                    {{-- AKSI --}}

                                    <td>

                                        @if(

                                            $borrowing->status !==

                                            'dikembalikan'

                                        )

                                            <div class="borrowing-action-buttons">

                                                {{-- =================================

                                                     PERPANJANG

                                                ================================== --}}

                                                <button

                                                    type="button"

                                                    class="btn-extend"

                                                    onclick="openExtendModal(

                                                        {{ $borrowing->id }},

                                                        @js(

                                                            $borrowing->details

                                                                ->first()

                                                                ->book

                                                                ->title

                                                                ?? '-'

                                                        ),

                                                        @js(

                                                            \Carbon\Carbon::parse(

                                                                $borrowing->due_at

                                                            )->format('d/m/Y')

                                                        )

                                                    )">

                                                    <span class="extend-icon">

                                                        ↻

                                                    </span>

                                                    Perpanjang

                                                </button>

                                                {{-- =================================

                                                     KEMBALIKAN

                                                ================================== --}}

                                                <form

                                                    action="{{ route(

                                                        'borrowings.return',

                                                        $borrowing

                                                    ) }}"

                                                    method="POST"

                                                    class="return-form">

                                                    @csrf

                                                    @method('PATCH')

                                                    <button

                                                        type="submit"

                                                        class="btn-secondary">

                                                        Kembalikan

                                                    </button>

                                                </form>

                                            </div>

                                        @else

                                            <span class="completed-text">

                                                Selesai

                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td

                                        colspan="6"

                                        class="empty-state">

                                        Belum ada transaksi peminjaman

                                        pada periode

                                        {{ $monthLabel }}.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- =========================================================

     MODAL INPUT PERPANJANGAN

========================================================= --}}

<div

    id="extendLoanModal"

    class="extend-modal"

    aria-hidden="true">

    {{-- OVERLAY --}}

    <div

        class="extend-modal-overlay"

        onclick="closeExtendModal()">

    </div>

    {{-- CARD --}}

    <div

        class="extend-modal-card"

        role="dialog"

        aria-modal="true"

        aria-labelledby="extendLoanTitle">

        {{-- CLOSE --}}

        <button

            type="button"

            class="extend-modal-close"

            onclick="closeExtendModal()"

            aria-label="Tutup">

            ×

        </button>

        {{-- HEADER --}}

        <div class="extend-modal-header">

            <span class="extend-modal-label">

                PERPANJANG PEMINJAMAN

            </span>

            <h3 id="extendLoanTitle">

                Perpanjang Waktu Baca

            </h3>

            <p>

                Tentukan tambahan waktu peminjaman buku.

            </p>

        </div>

        {{-- INFO BUKU --}}

        <div class="extend-book-info">

            <span>

                BUKU

            </span>

            <strong id="extendBookTitle">

                -

            </strong>

        </div>

        {{-- TANGGAL --}}

        <div class="extend-current-date">

            <span>

                Pengembalian saat ini

            </span>

            <strong id="extendCurrentDue">

                -

            </strong>

        </div>

        {{-- FORM --}}

        <form

            id="extendLoanForm"

            method="POST">

            @csrf

            @method('PATCH')

            <div class="extend-input-group">

                <label for="extension_days">

                    Tambah waktu baca

                </label>

                <div class="extend-input-wrap">

                    <input

                        type="number"

                        name="extension_days"

                        id="extension_days"

                        min="1"

                        max="30"

                        value="7"

                        required>

                    <span>

                        hari

                    </span>

                </div>

                <small>

                    Masukkan antara 1–30 hari.

                </small>

            </div>

            {{-- ACTION --}}

            <div class="extend-modal-actions">

                <button

                    type="button"

                    class="extend-cancel"

                    onclick="closeExtendModal()">

                    Batal

                </button>

                <button

                    type="submit"

                    class="extend-confirm">

                    Perpanjang

                </button>

            </div>

        </form>

    </div>

</div>

{{-- =========================================================

     MODAL KONFIRMASI

========================================================= --}}

<div

    id="extendConfirmModal"

    class="extend-confirm-modal"

    aria-hidden="true">

    {{-- OVERLAY --}}

    <div

        class="extend-confirm-overlay"

        onclick="closeExtendConfirmModal()">

    </div>

    {{-- CARD --}}

    <div

        class="extend-confirm-card"

        role="dialog"

        aria-modal="true"

        aria-labelledby="extendConfirmTitle">

        {{-- ICON --}}

        <div class="extend-confirm-icon">

            <svg

                viewBox="0 0 24 24"

                fill="none"

                stroke="currentColor"

                stroke-width="2"

                stroke-linecap="round"

                stroke-linejoin="round">

                <path

                    d="M20 11a8.1 8.1 0 0 0-14.8-4.5L3 9"

                />

                <path

                    d="M3 4v5h5"

                />

                <path

                    d="M4 13a8.1 8.1 0 0 0 14.8 4.5L21 15"

                />

                <path

                    d="M21 20v-5h-5"

                />

            </svg>

        </div>

        {{-- LABEL --}}

        <span class="extend-confirm-label">

            KONFIRMASI

        </span>

        {{-- TITLE --}}

        <h3 id="extendConfirmTitle">

            Konfirmasi Perpanjangan

        </h3>

        {{-- DESCRIPTION --}}

        <p class="extend-confirm-description">

            Yakin ingin memperpanjang masa peminjaman selama

            <strong id="extendConfirmDays">

                7

            </strong>

            hari?

        </p>

        {{-- BUTTON --}}

        <div class="extend-confirm-actions">

            <button

                type="button"

                class="extend-confirm-cancel"

                onclick="closeExtendConfirmModal()">

                Batal

            </button>

            <button

                type="button"

                class="extend-confirm-submit"

                onclick="submitExtendLoan()">

                Ya, Perpanjang

            </button>

        </div>

    </div>

</div>

{{-- =========================================================

     MODAL KONFIRMASI PENGEMBALIAN

========================================================= --}}

<div

    id="returnConfirmModal"

    class="extend-confirm-modal"

    aria-hidden="true">

    <div

        class="extend-confirm-overlay"

        onclick="closeReturnConfirmModal()">

    </div>

    <div

        class="extend-confirm-card"

        role="dialog"

        aria-modal="true"

        aria-labelledby="returnConfirmTitle"

        aria-describedby="returnConfirmDescription">

        <div class="extend-confirm-icon">

            <svg

                viewBox="0 0 24 24"

                fill="none"

                stroke="currentColor"

                stroke-width="2"

                stroke-linecap="round"

                stroke-linejoin="round">

                <path d="M5 4.75A1.75 1.75 0 0 1 6.75 3H20v16H6.75A1.75 1.75 0 0 0 5 20.75z" />

                <path d="M5 4.75v16" />

                <path d="m9 11 2 2 4-4" />

            </svg>

        </div>

        <span class="extend-confirm-label">

            KONFIRMASI PENGEMBALIAN

        </span>

        <h3 id="returnConfirmTitle">

            Buku sudah dikembalikan?

        </h3>

        <p

            id="returnConfirmDescription"

            class="extend-confirm-description">

            Pastikan buku sudah diterima. Status peminjaman akan diperbarui setelah kamu mengonfirmasi.

        </p>

        <div class="extend-confirm-actions">

            <button

                type="button"

                class="extend-confirm-cancel"

                onclick="closeReturnConfirmModal()">

                Batal

            </button>

            <button

                type="button"

                id="returnConfirmSubmit"

                class="extend-confirm-submit"

                onclick="submitReturnLoan()">

                Ya, kembalikan

            </button>

        </div>

    </div>

</div>

{{-- =========================================================

     JAVASCRIPT

========================================================= --}}

<script>

document.addEventListener(

    'DOMContentLoaded',

    function () {

        const loanModal = document.getElementById('loanModal');
        if (loanModal) {
            loanModal.addEventListener('click', function (event) {
                if (event.target === loanModal) {
                    closeLoanModal();
                }
            });
        }

        if (loanModal && loanModal.classList.contains('open')) {
            document.body.style.overflow = 'hidden';
            document.getElementById('member_id')?.focus();
        }

        document.querySelectorAll('.date-picker-button[data-date-input]').forEach(function(button) {
            button.addEventListener('click', function() {
                const dateInput = document.getElementById(button.dataset.dateInput);
                if (!dateInput) {
                    console.error('Input tanggal untuk date picker tidak ditemukan.');
                    return;
                }

                if (typeof dateInput.showPicker === 'function') {
                    dateInput.showPicker();
                } else {
                    dateInput.focus();
                    dateInput.click();
                }
            });
        });

        const bookSearch = document.getElementById('bookSearch');
        const bookSelect = document.getElementById('book_id');
        const bookSuggestions = document.getElementById('bookSuggestions');
        const circulationForm = document.getElementById('circulationForm');

        if (bookSearch && bookSelect && bookSuggestions) {
            const formatBookLabel = function(option) {
                return option.textContent
                    .replace(/\s+/g, ' ')
                    .replace(/\s*—\s*/g, ' - ')
                    .trim();
            };

            const closeBookSuggestions = function() {
                bookSuggestions.hidden = true;
                bookSearch.setAttribute('aria-expanded', 'false');
            };

            const renderBookSuggestions = function() {
                const query = bookSearch.value.trim().toLocaleLowerCase();
                bookSuggestions.replaceChildren();

                const matchingOptions = Array.from(bookSelect.options).filter(function(option) {
                    return option.value &&
                        option.textContent.toLocaleLowerCase().includes(query);
                });

                if (!matchingOptions.length) {
                    const empty = document.createElement('div');
                    empty.className = 'book-picker-empty';
                    empty.textContent = 'Buku tidak ditemukan.';
                    bookSuggestions.appendChild(empty);
                } else {
                    matchingOptions.forEach(function(option) {
                        const suggestion = document.createElement('button');
                        suggestion.type = 'button';
                        suggestion.className = 'book-picker-option';
                        suggestion.setAttribute('role', 'option');
                        suggestion.setAttribute('aria-selected', String(option.value === bookSelect.value));
                        suggestion.disabled = option.disabled;
                        suggestion.textContent = formatBookLabel(option);
                        suggestion.addEventListener('mousedown', function(event) {
                            event.preventDefault();
                        });
                        suggestion.addEventListener('click', function() {
                            bookSelect.value = option.value;
                            bookSearch.value = formatBookLabel(option);
                            bookSearch.setCustomValidity('');
                            bookSearch.focus();
                            closeBookSuggestions();
                        });
                        bookSuggestions.appendChild(suggestion);
                    });
                }

                bookSuggestions.hidden = false;
                bookSearch.setAttribute('aria-expanded', 'true');
            };

            const selectedBook = bookSelect.options[bookSelect.selectedIndex];
            if (selectedBook && selectedBook.value) {
                bookSearch.value = formatBookLabel(selectedBook);
            }

            bookSearch.addEventListener('focus', renderBookSuggestions);
            bookSearch.addEventListener('input', function() {
                bookSelect.value = '';
                bookSearch.setCustomValidity('Pilih buku dari daftar saran.');
                renderBookSuggestions();
            });
            bookSearch.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeBookSuggestions();
                    return;
                }

                if (event.key === 'ArrowDown' && !bookSuggestions.hidden) {
                    const firstOption = bookSuggestions.querySelector('.book-picker-option:not(:disabled)');
                    if (firstOption) {
                        event.preventDefault();
                        firstOption.focus();
                    }
                }
            });
            bookSuggestions.addEventListener('keydown', function(event) {
                const options = Array.from(bookSuggestions.querySelectorAll('.book-picker-option:not(:disabled)'));
                const currentIndex = options.indexOf(document.activeElement);

                if (event.key === 'ArrowDown' && currentIndex < options.length - 1) {
                    event.preventDefault();
                    options[currentIndex + 1].focus();
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (currentIndex > 0) {
                        options[currentIndex - 1].focus();
                    } else {
                        bookSearch.focus();
                    }
                } else if (event.key === 'Escape') {
                    closeBookSuggestions();
                    bookSearch.focus();
                }
            });
            bookSearch.addEventListener('blur', function() {
                window.setTimeout(closeBookSuggestions, 120);
            });
            circulationForm?.addEventListener('submit', function(event) {
                if (!bookSelect.value) {
                    event.preventDefault();
                    bookSearch.setCustomValidity('Pilih buku dari daftar saran.');
                    bookSearch.reportValidity();
                    bookSearch.focus();
                }
            });
        }

        /* =====================================================

           LIVE SEARCH

        ===================================================== */

        const searchInput =

            document.getElementById(

                'borrowingSearch'

            );

        const rows =
            document.querySelectorAll(
                '#borrowingTable tbody tr.borrowing-row'
            );
        const tableWrap = document.querySelector('#page-circulation .table-wrap');
        const borrowingTable = document.getElementById('borrowingTable');
        const fitBorrowingTableViewport = function() {
            if (!tableWrap || !borrowingTable) {
                return;
            }

            const header = borrowingTable.querySelector('thead');
            const visibleRows = Array.from(borrowingTable.querySelectorAll('tbody tr.borrowing-row'))
                .filter(function(row) {
                    return row.style.display !== 'none';
                })
                .slice(0, 10);
            if (!header) {
                return;
            }

            const visibleHeight = header.getBoundingClientRect().height +
                visibleRows.reduce(function(total, row) {
                    return total + row.getBoundingClientRect().height;
                }, 0);

            tableWrap.style.maxHeight = Math.ceil(visibleHeight) + 'px';
        };
        fitBorrowingTableViewport();
        window.addEventListener('resize', fitBorrowingTableViewport);

        const returnModal =
            document.getElementById(
                'returnConfirmModal'
            );
        document.querySelectorAll('.return-form').forEach(
            function (form) {
                form.addEventListener(
                    'submit',
                    function (event) {
                        if (!returnModal) {
                            console.error(
                                'Modal konfirmasi pengembalian tidak ditemukan.'
                            );
                            return;
                        }

                        event.preventDefault();
                        openReturnConfirmModal(form);
                    }
                );
            }
        );
        if (searchInput) {

            searchInput.addEventListener(

                'input',

                function () {

                    const keyword =

                        this.value

                            .toLowerCase()

                            .trim();

                    rows.forEach(

                        function (row) {

                            const text =

                                row.textContent

                                    .toLowerCase();

                            row.style.display =

                                text.includes(keyword)

                                    ? ''

                                    : 'none';

                        }

                    );
                    fitBorrowingTableViewport();

                }

            );

        }

        /* =====================================================

           FORM PERPANJANGAN

        ===================================================== */

        const extendForm =

            document.getElementById(

                'extendLoanForm'

            );

        const extensionInput =

            document.getElementById(

                'extension_days'

            );

        if (

            extendForm &&

            extensionInput

        ) {

            extendForm.addEventListener(

                'submit',

                function (event) {

                    /*

                     * SELALU tahan submit pertama.

                     *

                     * Form baru benar-benar dikirim

                     * melalui submitExtendLoan().

                     */

                    event.preventDefault();

                    const days =

                        Number(

                            extensionInput.value

                        );

                    /* =========================================

                       VALIDASI

                    ========================================== */

                    if (

                        !Number.isInteger(days) ||

                        days < 1 ||

                        days > 30

                    ) {

                        extensionInput.focus();

                        /*

                         * Untuk sekarang kita pakai

                         * custom browser message bawaan.

                         */

                        extensionInput.setCustomValidity(

                            'Masukkan jumlah hari antara 1 sampai 30.'

                        );

                        extensionInput.reportValidity();

                        extensionInput.setCustomValidity('');

                        return;

                    }

                    /* =========================================

                       BUKA POPUP KONFIRMASI

                    ========================================== */

                    openExtendConfirmModal(

                        days

                    );

                }

            );

        }

        /* =====================================================

           ENTER DI INPUT

        ===================================================== */

        if (extensionInput) {

            extensionInput.addEventListener(

                'input',

                function () {

                    this.setCustomValidity('');

                }

            );

        }

    }

);

/* =========================================================

   VARIABLE FORM

========================================================= */

let extendFormPending = null;
let returnFormPending = null;

function openLoanModal() {
    const modal = document.getElementById('loanModal');
    if (!modal) {
        console.error('Popup peminjaman buku tidak ditemukan.');
        return;
    }

    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    document.getElementById('member_id')?.focus();
}

function closeLoanModal() {
    const modal = document.getElementById('loanModal');
    if (!modal) {
        return;
    }

    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

/* =========================================================

   OPEN MODAL INPUT

========================================================= */

function openExtendModal(

    borrowingId,

    bookTitle,

    currentDue

) {

    const modal =

        document.getElementById(

            'extendLoanModal'

        );

    const form =

        document.getElementById(

            'extendLoanForm'

        );

    const title =

        document.getElementById(

            'extendBookTitle'

        );

    const due =

        document.getElementById(

            'extendCurrentDue'

        );

    const input =

        document.getElementById(

            'extension_days'

        );

    if (

        !modal ||

        !form ||

        !title ||

        !due ||

        !input

    ) {

        console.error(

            'Element modal perpanjangan tidak ditemukan.'

        );

        return;

    }

    /* =========================================

       DATA

    ========================================== */

    title.textContent =

        bookTitle || '-';

    due.textContent =

        currentDue || '-';

    /* =========================================

       ACTION FORM

    ========================================== */

    form.action =

        `/borrowings/${borrowingId}/extend`;

    /* =========================================

       DEFAULT 7 HARI

    ========================================== */

    input.value = 7;

    /* =========================================

       OPEN

    ========================================== */

    modal.classList.add(

        'open'

    );

    modal.setAttribute(

        'aria-hidden',

        'false'

    );

    document.body.style.overflow =

        'hidden';

    /* =========================================

       FOCUS

    ========================================== */

    setTimeout(

        function () {

            input.focus();

            input.select();

        },

        150

    );

}

/* =========================================================

   CLOSE MODAL INPUT

========================================================= */

function closeExtendModal() {

    const modal =

        document.getElementById(

            'extendLoanModal'

        );

    if (!modal) {

        return;

    }

    modal.classList.remove(

        'open'

    );

    modal.setAttribute(

        'aria-hidden',

        'true'

    );

    /*

     * Jangan hapus form pending di sini

     * kalau sedang menuju popup konfirmasi.

     */

    if (!extendFormPending) {

        document.body.style.overflow =

            '';

    }

}

/* =========================================================

   OPEN MODAL KONFIRMASI

========================================================= */

function openExtendConfirmModal(

    days

) {

    const modal =

        document.getElementById(

            'extendConfirmModal'

        );

    const daysElement =

        document.getElementById(

            'extendConfirmDays'

        );

    const inputModal =

        document.getElementById(

            'extendLoanModal'

        );

    if (

        !modal ||

        !daysElement

    ) {

        console.error(

            'Modal konfirmasi perpanjangan tidak ditemukan.'

        );

        return;

    }

    /* =========================================

       SIMPAN FORM

    ========================================== */

    extendFormPending =

        document.getElementById(

            'extendLoanForm'

        );

    if (!extendFormPending) {

        console.error(

            'Form perpanjangan tidak ditemukan.'

        );

        return;

    }

    /* =========================================

       JUMLAH HARI

    ========================================== */

    daysElement.textContent =

        days;

    /* =========================================

       TUTUP MODAL INPUT

    ========================================== */

    if (inputModal) {

        inputModal.classList.remove(

            'open'

        );

        inputModal.setAttribute(

            'aria-hidden',

            'true'

        );

    }

    /* =========================================

       BUKA MODAL KONFIRMASI

    ========================================== */

    modal.classList.add(

        'open'

    );

    modal.setAttribute(

        'aria-hidden',

        'false'

    );

    document.body.style.overflow =

        'hidden';

}

/* =========================================================

   CLOSE MODAL KONFIRMASI

========================================================= */

function closeExtendConfirmModal() {

    const modal =

        document.getElementById(

            'extendConfirmModal'

        );

    if (!modal) {

        return;

    }

    modal.classList.remove(

        'open'

    );

    modal.setAttribute(

        'aria-hidden',

        'true'

    );

    /*

     * Form pending dibatalkan.

     */

    extendFormPending =

        null;

    document.body.style.overflow =

        '';

}

/* =========================================================

   SUBMIT FINAL

========================================================= */

function submitExtendLoan() {

    if (!extendFormPending) {

        console.error(

            'Tidak ada form perpanjangan yang menunggu.'

        );

        return;

    }

    const form =

        extendFormPending;

    /*

     * Simpan referensi dulu.

     */

    extendFormPending =

        null;

    /*

     * Tutup popup.

     */

    const modal =

        document.getElementById(

            'extendConfirmModal'

        );

    if (modal) {

        modal.classList.remove(

            'open'

        );

        modal.setAttribute(

            'aria-hidden',

            'true'

        );

    }

    document.body.style.overflow =

        '';

    /*

     * Submit langsung ke Laravel.

     *

     * HTMLFormElement.submit()

     * sengaja digunakan agar event submit

     * tidak memunculkan popup konfirmasi

     * lagi.

     */

    HTMLFormElement.prototype.submit.call(
        form
    );
}

/* =========================================================

   KONFIRMASI PENGEMBALIAN

========================================================= */

function openReturnConfirmModal(form) {

    const modal =

        document.getElementById(

            'returnConfirmModal'

        );

    const cancelButton = modal
        ? modal.querySelector('.extend-confirm-cancel')
        : null;

    if (!modal || !cancelButton) {

        console.error(

            'Elemen modal konfirmasi pengembalian tidak lengkap.'

        );

        return;

    }

    returnFormPending = form;

    modal.classList.add('open');

    modal.setAttribute('aria-hidden', 'false');

    document.body.style.overflow = 'hidden';

    cancelButton.focus();

}

function closeReturnConfirmModal() {

    const modal =

        document.getElementById(

            'returnConfirmModal'

        );

    if (!modal) {

        return;

    }

    modal.classList.remove('open');

    modal.setAttribute('aria-hidden', 'true');

    returnFormPending = null;

    document.body.style.overflow = '';

}

function submitReturnLoan() {

    if (!returnFormPending) {

        console.error(

            'Tidak ada pengembalian yang menunggu konfirmasi.'

        );

        return;

    }

    const form = returnFormPending;

    returnFormPending = null;

    const modal =

        document.getElementById(

            'returnConfirmModal'

        );

    if (modal) {

        modal.classList.remove('open');

        modal.setAttribute('aria-hidden', 'true');

    }

    document.body.style.overflow = '';

    HTMLFormElement.prototype.submit.call(form);

}

/* =========================================================

   ESCAPE

========================================================= */

document.addEventListener(

    'keydown',

    function (event) {

        if (

            event.key !== 'Escape'

        ) {

            return;

        }
        const returnModal =

            document.getElementById(

                'returnConfirmModal'

            );

        const confirmModal =

            document.getElementById(

                'extendConfirmModal'

            );

        const inputModal =

            document.getElementById(

                'extendLoanModal'

            );
        const loanModal =

            document.getElementById(

                'loanModal'

            );
        if (loanModal && loanModal.classList.contains('open')) {

            closeLoanModal();

            return;

        }
        if (

            returnModal &&

            returnModal.classList.contains('open')

        ) {

            closeReturnConfirmModal();

            return;

        }
        /* =========================================

           PRIORITAS POPUP KONFIRMASI

        ========================================== */

        if (

            confirmModal &&

            confirmModal.classList.contains('open')

        ) {

            closeExtendConfirmModal();

            return;

        }

        /* =========================================

           POPUP INPUT

        ========================================== */

        if (

            inputModal &&

            inputModal.classList.contains('open')

        ) {

            closeExtendModal();

        }

    }

);

</script>

@endsection
