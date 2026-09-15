@extends('layouts.app')

@section('title', 'Edit Eksemplar - ' . $book->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

    <div class="book-form-page">

        {{-- =========================================================
             BREADCRUMB
        ========================================================== --}}
        <nav class="book-breadcrumb">

            <a href="{{ route('books.index') }}">
                Manajemen Buku
            </a>

            <span class="separator">/</span>

            <span class="current">
                Edit Eksemplar
            </span>

        </nav>


        {{-- =========================================================
             NAVIGASI EDIT
        ========================================================== --}}
        @include('books._edit_navigation')


        {{-- =========================================================
             CARD
        ========================================================== --}}
        <div class="book-form-card">

            {{-- =====================================================
                 HEADER
            ====================================================== --}}
            <div class="book-form-header">

                <h2>
                    Edit Eksemplar
                </h2>

                <p class="book-form-subtitle">
                    Kelola salinan fisik untuk buku
                    <strong>{{ $book->title }}</strong>.
                </p>

            </div>


            {{-- =====================================================
                 ALERT SUCCESS
            ====================================================== --}}
            @if (session('success'))
                <div class="alert-success">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">

                        <path d="M20 6 9 17l-5-5" />

                    </svg>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>
            @endif


            {{-- =====================================================
                 ALERT ERROR
            ====================================================== --}}
            @if ($errors->any())

                <div class="form-alert-error">

                    <strong>
                        Terdapat beberapa kesalahan:
                    </strong>

                    <ul>

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =====================================================
                 TOOLBAR EKSEMPLAR
            ====================================================== --}}
            <div class="book-copy-toolbar">

                <div>

                    <span class="book-copy-label">
                        DAFTAR EKSEMPLAR
                    </span>

                    <h3>
                        {{ $copies->count() }} Eksemplar
                    </h3>

                </div>


                <a href="{{ route('books.copies.create', $book) }}" class="book-btn-save book-copy-add-btn">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">

                        <line x1="12" y1="5" x2="12" y2="19" />

                        <line x1="5" y1="12" x2="19" y2="12" />

                    </svg>

                    <span>
                        Tambah Eksemplar
                    </span>

                </a>

            </div>


            {{-- =====================================================
                 TABLE
            ====================================================== --}}
            <div class="book-copy-table-wrapper">

                <table class="book-copy-table">

                    <thead>

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Barcode
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Kondisi
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th width="210">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($copies as $copy)
                            @php

                                $statusLabel = [
                                    'available' => 'Tersedia',
                                    'reserved' => 'Direservasi',
                                    'borrowed' => 'Dipinjam',
                                    'lost' => 'Hilang',
                                    'damaged' => 'Rusak',
                                    'maintenance' => 'Maintenance',
                                ];

                                $statusClass = [
                                    'available' => 'available',
                                    'reserved' => 'reserved',
                                    'borrowed' => 'borrowed',
                                    'lost' => 'danger',
                                    'damaged' => 'danger',
                                    'maintenance' => 'maintenance',
                                ];

                            @endphp


                            <tr>

                                {{-- =================================================
                                     NOMOR
                                ================================================== --}}
                                <td class="copy-number">

                                    {{ $loop->iteration }}

                                </td>


                                {{-- =================================================
                                     BARCODE
                                ================================================== --}}
                                <td>

                                    <strong class="copy-barcode">

                                        {{ $copy->barcode ?? '-' }}

                                    </strong>

                                </td>


                                {{-- =================================================
                                     STATUS
                                ================================================== --}}
                                <td>

                                    <span class="copy-status {{ $statusClass[$copy->status] ?? '' }}">

                                        {{ $statusLabel[$copy->status] ?? $copy->status }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     KONDISI
                                ================================================== --}}
                                <td>

                                    @if ($copy->condition === 'baik')
                                        <span class="copy-status available">
                                            Baik
                                        </span>
                                    @elseif($copy->condition === 'rusak')
                                        <span class="copy-status danger">
                                            Rusak
                                        </span>
                                    @else
                                        <span class="copy-status">
                                            -
                                        </span>
                                    @endif

                                </td>


                                {{-- =================================================
                                     LOKASI
                                ================================================== --}}
                                <td>

                                    @if ($copy->shelf)
                                        <div class="copy-location">

                                            <strong>
                                                {{ $copy->shelf->code }}
                                            </strong>

                                            <span>

                                                {{ $copy->shelf->zone->floor->name }}
                                                /
                                                {{ $copy->shelf->zone->name }}

                                            </span>

                                            <small>

                                                Section
                                                {{ $copy->section }}

                                                ·

                                                {{ $copy->side === 'front' ? 'Depan' : 'Belakang' }}

                                                ·

                                                Baris
                                                {{ $copy->row ?? '-' }}

                                                ·

                                                Kolom
                                                {{ $copy->column ?? '-' }}

                                            </small>

                                        </div>
                                    @else
                                        <span class="copy-no-location">

                                            Lokasi belum diatur

                                        </span>
                                    @endif

                                </td>


                                {{-- =================================================
                                     AKSI
                                ================================================== --}}
                                <td>

                                    <div class="copy-actions">

                                        {{-- ================================
                                             EDIT
                                        ================================= --}}
                                        <a href="{{ route('books.copies.edit', [$book, $copy]) }}" class="copy-edit-btn"
                                            title="Edit eksemplar">

                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">

                                                <path d="M12 20h9" />

                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L8 18l-4 1 1-4Z" />

                                            </svg>

                                            <span>
                                                Edit
                                            </span>

                                        </a>


                                        {{-- ================================
                                             HAPUS
                                        ================================= --}}
                                        <form action="{{ route('books.copies.destroy', [$book, $copy]) }}" method="POST"
                                            class="copy-delete-form">

                                            @csrf

                                            @method('DELETE')

                                            <button type="button" class="copy-delete-btn" title="Hapus eksemplar"
                                                onclick="openCopyDeleteModal(this)">

                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                                    <polyline points="3 6 5 6 21 6" />

                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />

                                                    <path d="M10 11v6" />

                                                    <path d="M14 11v6" />

                                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />

                                                </svg>

                                                <span>
                                                    Hapus
                                                </span>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="6" class="copy-empty">

                                    <div>

                                        <strong>
                                            Belum ada eksemplar
                                        </strong>

                                        <span>
                                            Tambahkan eksemplar pertama
                                            untuk buku ini.
                                        </span>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =====================================================
                 FOOTER
            ====================================================== --}}
            <div class="book-form-actions">

                <a href="{{ route('books.index') }}" class="book-btn-cancel">

                    Kembali ke Manajemen Buku

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL HAPUS EKSEMPLAR
    ========================================================== --}}
    <div id="copyDeleteModal" class="copy-delete-modal-overlay" aria-hidden="true">

        <div class="copy-delete-modal" role="dialog" aria-modal="true" aria-labelledby="copyDeleteModalTitle">

            {{-- ICON --}}

            <div class="copy-delete-modal-icon">

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

            <div class="copy-delete-modal-label">
                KONFIRMASI PENGHAPUSAN
            </div>


            {{-- TITLE --}}

            <h3 id="copyDeleteModalTitle" class="copy-delete-modal-title">
                Hapus Eksemplar
            </h3>


            {{-- DESCRIPTION --}}

            <p class="copy-delete-modal-description">

                Eksemplar ini akan dihapus dari koleksi aktif.

                <br>

                Masukkan alasan penghapusan sebelum melanjutkan.

            </p>


            {{-- COPY INFO --}}

            <div class="copy-delete-modal-info">

                <div>

                    <span>
                        JUDUL BUKU
                    </span>

                    <strong id="deleteCopyBookName">
                        -
                    </strong>

                </div>


                <div>

                    <span>
                        BARCODE
                    </span>

                    <strong id="deleteCopyBarcode">
                        -
                    </strong>

                </div>

            </div>


            {{-- ALASAN --}}

            <div class="copy-delete-modal-reason">

                <label for="copyDeleteReason">

                    Alasan Penghapusan

                    <span class="required">
                        *
                    </span>

                </label>

                <textarea id="copyDeleteReason" rows="3" maxlength="500" placeholder="Masukkan alasan eksemplar dihapus..."
                    required></textarea>

                <small>
                    Alasan wajib diisi.
                </small>

            </div>


            {{-- ACTIONS --}}

            <div class="copy-delete-modal-actions">

                <button type="button" class="copy-delete-modal-cancel" onclick="closeCopyDeleteModal()">
                    Batal
                </button>


                <button type="button" class="copy-delete-modal-confirm" onclick="confirmCopyDelete()">
                    Hapus Eksemplar
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL STYLE
    ========================================================== --}}
    <style>
        .copy-delete-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;

            background: rgba(15, 23, 42, 0.48);

            backdrop-filter: blur(4px);

            opacity: 0;
            visibility: hidden;

            transition:
                opacity .2s ease,
                visibility .2s ease;

            box-sizing: border-box;
        }


        .copy-delete-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }


        .copy-delete-modal {
            width: 100%;
            max-width: 470px;

            padding: 28px;

            background: #ffffff;

            border: 1px solid #e2e8f0;

            border-radius: 16px;

            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.20);

            box-sizing: border-box;

            transform: translateY(8px) scale(.98);

            transition:
                transform .2s ease;
        }


        .copy-delete-modal-overlay.show .copy-delete-modal {
            transform: translateY(0) scale(1);
        }


        .copy-delete-modal-icon {
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 14px;

            border-radius: 50%;

            background: #fff1f1;

            color: #d95353;
        }


        .copy-delete-modal-icon svg {
            width: 23px;
            height: 23px;
        }


        .copy-delete-modal-label {
            text-align: center;

            margin-bottom: 5px;

            color: #287f80;

            font-size: 10px;
            font-weight: 800;

            letter-spacing: .8px;
        }


        .copy-delete-modal-title {
            margin: 0;

            text-align: center;

            color: #173b52;

            font-size: 21px;
            font-weight: 750;
        }


        .copy-delete-modal-description {
            margin: 9px 0 18px;

            text-align: center;

            color: #718091;

            font-size: 12px;
            line-height: 1.6;
        }


        .copy-delete-modal-info {
            display: grid;
            grid-template-columns: 1fr 1fr;

            gap: 10px;

            margin-bottom: 17px;
        }


        .copy-delete-modal-info>div {
            min-width: 0;

            padding: 11px 13px;

            background: #f7fbfb;

            border: 1px solid #e0eceb;

            border-radius: 10px;
        }


        .copy-delete-modal-info span {
            display: block;

            margin-bottom: 4px;

            color: #7b8a96;

            font-size: 9px;
            font-weight: 700;

            letter-spacing: .4px;
        }


        .copy-delete-modal-info strong {
            display: block;

            overflow: hidden;

            color: #173b52;

            font-size: 12px;
            font-weight: 700;

            white-space: nowrap;
            text-overflow: ellipsis;
        }


        .copy-delete-modal-reason {
            width: 100%;

            margin-bottom: 21px;

            text-align: left;
        }


        .copy-delete-modal-reason label {
            display: block;

            margin-bottom: 7px;

            color: #173b52;

            font-size: 13px;
            font-weight: 700;
        }


        .copy-delete-modal-reason .required {
            color: #e84d4d;
        }


        .copy-delete-modal-reason textarea {
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


        .copy-delete-modal-reason textarea:focus {
            border-color: #287f80;

            box-shadow:
                0 0 0 3px rgba(40, 127, 128, .10);
        }


        .copy-delete-modal-reason textarea::placeholder {
            color: #9aa8b4;
        }


        .copy-delete-modal-reason small {
            display: block;

            margin-top: 6px;

            color: #718091;

            font-size: 11px;
        }


        .copy-delete-modal-actions {
            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 10px;
        }


        .copy-delete-modal-cancel,
        .copy-delete-modal-confirm {
            min-width: 120px;

            padding: 10px 16px;

            border-radius: 9px;

            font-family: inherit;

            font-size: 12px;
            font-weight: 700;

            cursor: pointer;

            transition:
                background .2s ease,
                border-color .2s ease,
                transform .15s ease;
        }


        .copy-delete-modal-cancel {
            border: 1px solid #d8e1e5;

            background: #ffffff;

            color: #52616d;
        }


        .copy-delete-modal-cancel:hover {
            background: #f7fafa;
        }


        .copy-delete-modal-confirm {
            border: 1px solid #d95353;

            background: #d95353;

            color: #ffffff;
        }


        .copy-delete-modal-confirm:hover {
            background: #c74646;

            border-color: #c74646;
        }


        .copy-delete-modal-cancel:active,
        .copy-delete-modal-confirm:active {
            transform: scale(.98);
        }


        @media (max-width: 520px) {

            .copy-delete-modal {
                padding: 22px;
            }


            .copy-delete-modal-info {
                grid-template-columns: 1fr;
            }


            .copy-delete-modal-actions {
                flex-direction: column-reverse;
            }


            .copy-delete-modal-cancel,
            .copy-delete-modal-confirm {
                width: 100%;
            }

        }
    </style>


    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}
    <script>
        /*
            |--------------------------------------------------------------------------
            | FORM YANG AKAN DIHAPUS
            |--------------------------------------------------------------------------
            */

        let copyDeleteForm = null;


        /*
        |--------------------------------------------------------------------------
        | BUKA MODAL
        |--------------------------------------------------------------------------
        */

        function openCopyDeleteModal(button) {

            copyDeleteForm =
                button.closest(
                    '.copy-delete-form'
                );


            if (!copyDeleteForm) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AMBIL BARIS
            |--------------------------------------------------------------------------
            */

            const row =
                button.closest('tr');


            /*
            |--------------------------------------------------------------------------
            | AMBIL BARCODE
            |--------------------------------------------------------------------------
            */

            const barcodeElement =
                row ?
                row.querySelector(
                    '.copy-barcode'
                ) :
                null;


            const barcode =
                barcodeElement ?
                barcodeElement.textContent.trim() :
                '-';


            /*
            |--------------------------------------------------------------------------
            | JUDUL BUKU
            |--------------------------------------------------------------------------
            */

            const bookName =
                @json($book->title);


            /*
            |--------------------------------------------------------------------------
            | MASUKKAN DATA KE MODAL
            |--------------------------------------------------------------------------
            */

            const bookNameTarget =
                document.getElementById(
                    'deleteCopyBookName'
                );


            const barcodeTarget =
                document.getElementById(
                    'deleteCopyBarcode'
                );


            if (bookNameTarget) {

                bookNameTarget.textContent =
                    bookName;

            }


            if (barcodeTarget) {

                barcodeTarget.textContent =
                    barcode;

            }


            /*
            |--------------------------------------------------------------------------
            | RESET ALASAN
            |--------------------------------------------------------------------------
            */

            const reasonInput =
                document.getElementById(
                    'copyDeleteReason'
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
                    'copyDeleteModal'
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
            | FOCUS ALASAN
            |--------------------------------------------------------------------------
            */

            if (reasonInput) {

                setTimeout(
                    function() {

                        reasonInput.focus();

                    },
                    100
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        function closeCopyDeleteModal() {

            const modal =
                document.getElementById(
                    'copyDeleteModal'
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


            copyDeleteForm =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI HAPUS
        |--------------------------------------------------------------------------
        */

        function confirmCopyDelete() {

            if (!copyDeleteForm) {
                return;
            }


            const reasonInput =
                document.getElementById(
                    'copyDeleteReason'
                );


            if (!reasonInput) {
                return;
            }


            const reason =
                reasonInput.value.trim();


            /*
            |--------------------------------------------------------------------------
            | ALASAN WAJIB DIISI
            |--------------------------------------------------------------------------
            */

            if (!reason) {

                reasonInput.focus();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | INPUT REASON
            |--------------------------------------------------------------------------
            */

            let reasonField =
                copyDeleteForm.querySelector(
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

                copyDeleteForm.appendChild(
                    reasonField
                );

            }


            reasonField.value =
                reason;


            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            copyDeleteForm.submit();

        }


        /*
        |--------------------------------------------------------------------------
        | DOM READY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                |--------------------------------------------------------------------------
                | ESC
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function(event) {

                        if (
                            event.key === 'Escape'
                        ) {

                            closeCopyDeleteModal();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | BACKDROP
                |--------------------------------------------------------------------------
                */

                const modal =
                    document.getElementById(
                        'copyDeleteModal'
                    );


                if (modal) {

                    modal.addEventListener(
                        'click',
                        function(event) {

                            if (
                                event.target === this
                            ) {

                                closeCopyDeleteModal();

                            }

                        }
                    );

                }

            }
        );
    </script>

@endsection
