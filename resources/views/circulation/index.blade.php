@extends('layouts.app')

@section('title', 'Sirkulasi Peminjaman Buku')

@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/circulation.css') }}">

<style>
    /* =========================================================
       NAVIGATOR BULAN & FILTER BAR SIRKULASI
    ========================================================= */

    .circulation-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        margin-bottom: 14px;
        padding: 0;
        background: transparent;
        border: none;
    }

    .month-navigator {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 3px 6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        gap: 4px;
    }

    .btn-nav-month {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        font-size: 16px;
        font-weight: 700;
        transition: all 0.15s ease;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .btn-nav-month:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .month-display-label {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        padding: 0 10px;
        min-width: 130px;
        text-align: center;
        user-select: none;
    }

    .btn-today-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        color: #287b7b;
        background: #e6f4f4;
        border: 1px solid #a8d5d5;
        border-radius: 6px;
        padding: 5px 10px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-today-badge:hover {
        background: #287b7b;
        color: #ffffff;
    }

    .borrowing-search-wrap {
        flex-grow: 1;
        max-width: 240px;
    }

    .borrowing-search-wrap input {
        width: 100%;
        padding: 7px 12px;
        font-size: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #ffffff;
        outline: none;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease;
    }

    .borrowing-search-wrap input:focus {
        border-color: #287b7b;
        box-shadow:
            0 0 0 2px rgba(40, 123, 123, 0.15);
    }

    .period-info-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        color: #4b5563;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 4px;
        margin-top: 4px;
    }


    /* =========================================================
       RESPONSIVE FILTER
    ========================================================= */

    @media (max-width: 700px) {

        .circulation-filter-bar {
            align-items: stretch;
            flex-direction: column;
        }

        .borrowing-search-wrap {
            width: 100%;
            max-width: none;
        }

        .borrowing-search-wrap input {
            width: 100%;
        }
    }
</style>

@endpush


@section('content')

<section
    class="page"
    id="page-circulation">


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

    @if($errors->any())

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


    {{-- =====================================================
         MAIN TWO COLUMN
    ====================================================== --}}

    <div class="two-col">


        {{-- =================================================
             KIRI - FORM PEMINJAMAN
        ================================================== --}}

        <div class="card form-card">

            <div class="card-pad">

                <h3>
                    Pinjam Buku
                </h3>

                <p class="loan-description">
                    Pilih anggota, buku, dan tanggal peminjaman.
                </p>


                {{-- FORM PEMINJAMAN --}}

                <form
                    action="{{ route('circulation.store') }}"
                    method="POST"
                    id="circulationForm">

                    @csrf


                    {{-- ANGGOTA --}}

                    <div class="field">

                        <label for="member_id">
                            Anggota
                        </label>

                        <select
                            name="member_id"
                            id="member_id"
                            required>

                            <option value="">
                                -- Pilih Anggota --
                            </option>

                            @foreach($members as $member)

                            <option
                                value="{{ $member->id }}"
                                @selected(
                                    old('member_id') == $member->id
                                )>

                                {{ $member->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BUKU --}}

                    <div class="field">

                        <label for="book_id">
                            Buku
                        </label>

                        <select
                            name="book_id"
                            id="book_id"
                            required>

                            <option value="">
                                -- Pilih Buku --
                            </option>

                            @foreach($books as $book)

                            <option
                                value="{{ $book->id }}"
                                @selected(
                                    old('book_id') == $book->id
                                )
                                @disabled(
                                    $book->available_stock <= 0
                                )>

                                {{ $book->title }}

                                —

                                @if($book->available_stock > 0)

                                    Stok {{ $book->available_stock }}

                                @else

                                    STOK HABIS

                                @endif

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- TANGGAL --}}

                    <div class="field-row">

                        <div class="field">

                            <label for="borrowed_at">
                                Tanggal Pinjam
                            </label>

                            <input
                                type="date"
                                name="borrowed_at"
                                id="borrowed_at"
                                value="{{ old(
                                    'borrowed_at',
                                    now()->format('Y-m-d')
                                ) }}"
                                required>

                        </div>


                        <div class="field">

                            <label for="due_at">
                                Tanggal Pengembalian
                            </label>

                            <input
                                type="date"
                                name="due_at"
                                id="due_at"
                                value="{{ old(
                                    'due_at',
                                    now()->addDays(7)->format('Y-m-d')
                                ) }}"
                                required>

                        </div>

                    </div>


                    {{-- SUBMIT --}}

                    <button
                        type="submit"
                        class="loan-submit">

                        Proses Peminjaman

                    </button>

                </form>

            </div>

        </div>


        {{-- =================================================
             KANAN - DAFTAR PEMINJAMAN
        ================================================== --}}

        <div class="card borrowing-card">

            <div class="card-pad">


                {{-- HEADER DAFTAR --}}

                <div class="borrowing-header">

                    <div>

                        <h3>
                            Daftar Peminjaman
                        </h3>

                        <p
                            class="loan-description"
                            style="margin-bottom: 0;">

                            Laporan transaksi peminjaman buku
                            periode bulanan.

                        </p>

                    </div>

                </div>


                {{-- FILTER BAR --}}

                <div class="circulation-filter-bar">


                    {{-- NAVIGASI BULAN --}}

                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            flex-wrap: wrap;
                        ">

                        <div class="month-navigator">

                            {{-- BULAN SEBELUMNYA --}}

                            <a
                                href="{{ route(
                                    'circulation',
                                    [
                                        'month' => $prevMonth,
                                        'year' => $prevYear
                                    ]
                                ) }}"
                                class="btn-nav-month"
                                title="Bulan Sebelumnya">

                                &#8249;

                            </a>


                            {{-- BULAN AKTIF --}}

                            <span class="month-display-label">

                                {{ $monthLabel }}

                            </span>


                            {{-- BULAN BERIKUTNYA --}}

                            <a
                                href="{{ route(
                                    'circulation',
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


                        {{-- BULAN INI --}}

                        @if(!$isCurrentMonth)

                        <a
                            href="{{ route('circulation') }}"
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

                                /*
                                 * =================================================
                                 * STATUS TERLAMBAT
                                 * =================================================
                                 *
                                 * Status terlambat hanya diberikan
                                 * kepada peminjaman yang masih berstatus
                                 * dipinjam dan sudah melewati jatuh tempo.
                                 */

                                $isLate =
                                    $borrowing->status === 'dipinjam'
                                    &&
                                    now()->startOfDay()->gt(
                                        \Carbon\Carbon::parse(
                                            $borrowing->due_at
                                        )->startOfDay()
                                    );


                                /*
                                 * =================================================
                                 * STATUS YANG DITAMPILKAN
                                 * =================================================
                                 */

                                if ($isLate) {

                                    $displayStatus = 'terlambat';

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

                                    @forelse(
                                        $borrowing->details
                                        as $detail
                                    )

                                    <span>

                                        {{ $detail->book->title ?? '-' }}

                                    </span>

                                    @if(!$loop->last)

                                    <br>

                                    @endif

                                    @empty

                                    -

                                    @endforelse

                                </td>


                                {{-- TANGGAL PINJAM --}}

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $borrowing->borrowed_at
                                    )->format('d/m/Y') }}

                                </td>


                                {{-- TANGGAL PENGEMBALIAN --}}

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $borrowing->due_at
                                    )->format('d/m/Y') }}

                                </td>


                                {{-- =================================================
                                     STATUS
                                ================================================== --}}

                                <td>

                                    {{-- SEDANG DIPINJAM --}}

                                    @if(
                                        $displayStatus === 'dipinjam'
                                    )

                                    <span class="status-badge aktif">

                                        <span class="status-dot"></span>

                                        Sedang Dipinjam

                                    </span>


                                    {{-- DIPERPANJANG --}}

                                    @elseif(
                                        $displayStatus === 'diperpanjang'
                                    )

                                    <span class="status-badge diperpanjang">

                                        <span class="status-dot"></span>

                                        Diperpanjang

                                    </span>


                                    {{-- TERLAMBAT --}}

                                    @elseif(
                                        $displayStatus === 'terlambat'
                                    )

                                    <span class="status-badge terlambat">

                                        <span class="status-dot"></span>

                                        Terlambat

                                    </span>


                                    {{-- SELESAI --}}

                                    @elseif(
                                        $displayStatus === 'dikembalikan'
                                    )

                                    <span class="status-badge kembali">

                                        <span class="status-dot"></span>

                                        Selesai

                                    </span>


                                    {{-- FALLBACK --}}

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


                                {{-- =================================================
                                     AKSI
                                ================================================== --}}

                                <td>

                                    @if(
                                        $borrowing->status !==
                                        'dikembalikan'
                                    )

                                    <div class="borrowing-action-buttons">


                                        {{-- PERPANJANG --}}

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


                                        {{-- KEMBALIKAN --}}

                                        <form
                                            action="{{ route(
                                                'circulation.return',
                                                $borrowing
                                            ) }}"
                                            method="POST"
                                            class="return-form"
                                            onsubmit="
                                                return confirm(
                                                    'Yakin buku ini sudah dikembalikan?'
                                                );
                                            ">

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
     MODAL PERPANJANG PEMINJAMAN
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


    {{-- CARD MODAL --}}

    <div class="extend-modal-card">


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

            <h3>

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


        {{-- JATUH TEMPO --}}

        <div class="extend-current-date">

            <span>
                Pengembalian saat ini
            </span>

            <strong id="extendCurrentDue">
                -
            </strong>

        </div>


        {{-- FORM PERPANJANG --}}

        <form
            id="extendLoanForm"
            method="POST">

            @csrf

            @method('PATCH')


            {{-- INPUT HARI --}}

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


            {{-- BUTTON --}}

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
     JAVASCRIPT LIVE SEARCH
========================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function() {

        const searchInput =
            document.getElementById(
                'borrowingSearch'
            );


        const rows =
            document.querySelectorAll(
                '#borrowingTable tbody tr.borrowing-row'
            );


        if (!searchInput) {
            return;
        }


        searchInput.addEventListener(
            'input',
            function() {

                const keyword =
                    this.value
                    .toLowerCase()
                    .trim();


                rows.forEach(
                    function(row) {

                        const text =
                            row.textContent
                            .toLowerCase();


                        if (
                            text.includes(keyword)
                        ) {

                            row.style.display =
                                '';

                        } else {

                            row.style.display =
                                'none';

                        }

                    }
                );

            }
        );

    }
);

</script>


{{-- =========================================================
     JAVASCRIPT MODAL PERPANJANG
========================================================= --}}

<script>

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


    /* =====================================================
       CEK ELEMENT
    ====================================================== */

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


    /* =====================================================
       DATA BUKU
    ====================================================== */

    title.textContent =
        bookTitle || '-';


    /* =====================================================
       TANGGAL JATUH TEMPO
    ====================================================== */

    due.textContent =
        currentDue || '-';


    /* =====================================================
       ACTION FORM
    ====================================================== */

    form.action =
        `/circulation/${borrowingId}/extend`;


    /* =====================================================
       DEFAULT
    ====================================================== */

    input.value = 7;


    /* =====================================================
       BUKA MODAL
    ====================================================== */

    modal.classList.add(
        'open'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.style.overflow =
        'hidden';


    /* =====================================================
       FOCUS INPUT
    ====================================================== */

    setTimeout(
        function() {

            input.focus();

            input.select();

        },
        100
    );

}


/* =========================================================
   TUTUP MODAL
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


    document.body.style.overflow =
        '';

}


/* =========================================================
   ESCAPE
========================================================= */

document.addEventListener(
    'keydown',
    function(event) {

        if (
            event.key !== 'Escape'
        ) {

            return;

        }


        const modal =
            document.getElementById(
                'extendLoanModal'
            );


        if (
            modal &&
            modal.classList.contains('open')
        ) {

            closeExtendModal();

        }

    }
);


/* =========================================================
   VALIDASI 1–30 HARI
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function() {

        const form =
            document.getElementById(
                'extendLoanForm'
            );


        const input =
            document.getElementById(
                'extension_days'
            );


        if (
            !form ||
            !input
        ) {

            return;

        }


        form.addEventListener(
            'submit',
            function(event) {

                const days =
                    Number(
                        input.value
                    );


                /* =========================================
                   VALIDASI
                ========================================== */

                if (
                    !Number.isInteger(days) ||
                    days < 1 ||
                    days > 30
                ) {

                    event.preventDefault();


                    alert(
                        'Jumlah perpanjangan harus antara 1 sampai 30 hari.'
                    );


                    input.focus();

                    return;

                }


                /* =========================================
                   KONFIRMASI
                ========================================== */

                const confirmed =
                    confirm(
                        `Perpanjang masa peminjaman ${days} hari?`
                    );


                if (!confirmed) {

                    event.preventDefault();

                }

            }
        );

    }
);

</script>

@endsection