@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">

    <style>
        /* =========================================================
           ISBN SCANNER
        ========================================================= */

        .isbn-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: stretch;
        }

        .isbn-input-wrapper .input {
            flex: 1;
            min-width: 0;
        }

        .isbn-scan-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 0 16px;

            border: 1px solid #287f80;
            border-radius: 10px;

            background: #287f80;
            color: #ffffff;

            font-size: 13px;
            font-weight: 700;

            cursor: pointer;

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }

        .isbn-scan-btn:hover {
            background: #236f70;
            box-shadow: 0 5px 14px rgba(40, 127, 128, .18);
        }

        .isbn-scan-btn:active {
            transform: translateY(1px);
        }

        .isbn-scan-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .isbn-scan-btn svg {
            width: 18px;
            height: 18px;
        }


        /* =========================================================
           ISBN SCANNER MODAL
        ========================================================= */

        .isbn-scanner-modal {
            position: fixed;
            inset: 0;

            z-index: 9999;

            display: none;
            align-items: center;
            justify-content: center;

            padding: 20px;
        }

        .isbn-scanner-modal.is-open {
            display: flex;
        }

        .isbn-scanner-backdrop {
            position: absolute;
            inset: 0;

            background: rgba(15, 23, 42, .72);

            backdrop-filter: blur(5px);
        }

        .isbn-scanner-dialog {
            position: relative;
            z-index: 1;

            width: min(520px, 100%);

            overflow: hidden;

            border: 1px solid #d9ebea;
            border-radius: 18px;

            background: #ffffff;

            box-shadow:
                0 25px 70px rgba(15, 23, 42, .28);
        }

        .isbn-scanner-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;

            gap: 20px;

            padding: 22px 22px 18px;
        }

        .isbn-scanner-label {
            display: inline-flex;
            align-items: center;

            margin-bottom: 7px;

            color: #287f80;

            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .isbn-scanner-header h3 {
            margin: 0;

            color: #172b4d;

            font-size: 20px;
            font-weight: 800;
        }

        .isbn-scanner-header p {
            margin: 6px 0 0;

            color: #64748b;

            font-size: 13px;
        }

        .isbn-scanner-close {
            flex: 0 0 auto;

            width: 36px;
            height: 36px;

            border: 0;
            border-radius: 50%;

            background: #f1f5f9;
            color: #475569;

            font-size: 24px;
            line-height: 1;

            cursor: pointer;
        }

        .isbn-scanner-close:hover {
            background: #e2e8f0;
        }


        /* =========================================================
           CAMERA
        ========================================================= */

        .isbn-scanner-camera {
            position: relative;

            margin: 0 22px;

            overflow: hidden;

            border-radius: 14px;

            background: #0f172a;

            aspect-ratio: 4 / 3;
        }

        #isbnReader {
            width: 100%;
            height: 100%;
        }

        #isbnReader video {
            width: 100% !important;
            height: 100% !important;

            object-fit: cover;
        }

        #isbnReader img {
            display: none !important;
        }

        #isbnReader__dashboard {
            display: none !important;
        }

        .isbn-scanner-frame {
            position: absolute;
            inset: 0;

            pointer-events: none;
        }

        .isbn-scanner-frame::before {
            content: "";

            position: absolute;

            left: 9%;
            right: 9%;

            top: 38%;
            height: 24%;

            border: 2px solid rgba(255, 255, 255, .9);

            border-radius: 8px;

            box-shadow:
                0 0 0 9999px rgba(15, 23, 42, .16);
        }

        .isbn-scanner-frame::after {
            content: "";

            position: absolute;

            left: 12%;
            right: 12%;

            top: 50%;

            height: 2px;

            background: #ffffff;

            opacity: .8;
        }

        .isbn-scanner-status {
            margin: 14px 22px;

            padding: 11px 13px;

            border-radius: 9px;

            background: #f7fbfb;

            color: #64748b;

            font-size: 13px;

            text-align: center;
        }

        .isbn-scanner-cancel {
            width: calc(100% - 44px);

            margin: 0 22px 22px;

            padding: 11px 16px;

            border: 1px solid #e2e8f0;
            border-radius: 10px;

            background: #ffffff;
            color: #475569;

            font-size: 13px;
            font-weight: 700;

            cursor: pointer;
        }

        .isbn-scanner-cancel:hover {
            background: #f8fafc;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 600px) {

            .isbn-input-wrapper {
                flex-direction: column;
            }

            .isbn-scan-btn {
                min-height: 44px;
            }

            .isbn-scanner-modal {
                padding: 10px;
            }

            .isbn-scanner-dialog {
                border-radius: 14px;
            }

        }
    </style>
@endpush


@section('content')

    <div class="book-form-page">

        {{-- =====================================================
             BREADCRUMB
        ====================================================== --}}

        <nav class="book-breadcrumb">

            <a href="{{ route('books.index') }}">
                Manajemen Buku
            </a>

            <span class="separator">/</span>

            <span class="current">
                Tambah Buku Baru
            </span>

        </nav>


        {{-- =====================================================
             FORM CARD
        ====================================================== --}}

        <div class="book-form-card">

            <div class="book-form-header">

                <h2>
                    Tambah Buku Baru
                </h2>

                <p class="book-form-subtitle">
                    Lengkapi formulir di bawah ini untuk menambahkan
                    koleksi buku baru ke sistem perpustakaan.
                </p>

            </div>


            {{-- =================================================
                 ERROR
            ================================================== --}}

            @if ($errors->any())

                <div class="form-alert-error">

                    <strong>
                        Terdapat beberapa kesalahan pengisian:
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


            {{-- =================================================
                 FORM
            ================================================== --}}

            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">

                @csrf


                <div class="book-form-grid">


                    {{-- =================================================
                         SECTION 1
                    ================================================== --}}

                    <div class="form-section-heading">

                        <div class="form-section-icon">
                            1
                        </div>

                        <span class="form-section-title">
                            Informasi Utama Buku
                        </span>

                    </div>


                    {{-- JUDUL --}}

                    <div class="book-form-group book-form-full">

                        <label for="title">
                            Judul Buku
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="input @error('title') is-invalid @enderror"
                            placeholder="cth. Laskar Pelangi / Sejarah Peradaban Dunia"
                            value="{{ old('title') }}"
                            required
                        >

                        @error('title')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- SKU --}}

                    <div class="book-form-group">

                        <label for="sku">
                            SKU
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            class="input @error('sku') is-invalid @enderror"
                            placeholder="cth. BK-2026-00001"
                            value="{{ old('sku') }}"
                            required
                        >

                        <small class="form-help">
                            Kode unik internal perpustakaan.
                        </small>

                        @error('sku')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- NO INVENTARIS --}}

                    <div class="book-form-group">

                        <label for="no_iventaris">
                            No. Iventaris
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_iventaris"
                            name="no_iventaris"
                            class="input @error('no_iventaris') is-invalid @enderror"
                            placeholder="cth. 123456789"
                            value="{{ old('no_iventaris') }}"
                            required
                        >

                        @error('no_iventaris')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- KODE BUKU --}}

                    <div class="book-form-group">

                        <label for="kode_buku">
                            Kode Buku
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kode_buku"
                            name="kode_buku"
                            class="input @error('kode_buku') is-invalid @enderror"
                            placeholder="cth. 808.123456789"
                            value="{{ old('kode_buku') }}"
                            required
                        >

                        @error('kode_buku')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- PENULIS --}}

                    <div class="book-form-group">

                        <label for="author">
                            Penulis / Pengarang
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="author"
                            name="author"
                            class="input @error('author') is-invalid @enderror"
                            placeholder="cth. Andrea Hirata / Prof. Dr. Soekarno"
                            value="{{ old('author') }}"
                            required
                        >

                        @error('author')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- DDC --}}

                    <div class="book-form-group">

                        <label for="ddc">
                            DDC
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="ddc"
                            name="ddc"
                            class="input @error('ddc') is-invalid @enderror"
                            placeholder="cth. 808.123456789"
                            value="{{ old('ddc') }}"
                            required
                        >

                        @error('ddc')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                         KATEGORI
                    ================================================== --}}

                    <div class="book-form-group">

                        <label for="category_id">
                            Kategori Buku
                            <span class="required">*</span>
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="input @error('category_id') is-invalid @enderror"
                            required
                        >

                            <option
                                value=""
                                disabled
                                {{ old('category_id') ? '' : 'selected' }}
                            >
                                -- Pilih Kategori --
                            </option>

                            @foreach ($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                         SUBKATEGORI
                    ================================================== --}}

                    <div class="book-form-group">

                        <label for="subcategory_id">
                            Subkategori Buku
                            <span class="required">*</span>
                        </label>

                        <select
                            id="subcategory_id"
                            name="subcategory_id"
                            class="input @error('subcategory_id') is-invalid @enderror"
                            disabled
                        >

                            <option value="">
                                -- Pilih Subkategori --
                            </option>

                        </select>

                        @error('subcategory_id')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                         RAK
                    ================================================== --}}

                    <div class="book-form-group">

                        <label for="rak">
                            Rak
                            <span class="required">*</span>
                        </label>

                        <select
                            id="rak"
                            name="rak"
                            class="input @error('rak') is-invalid @enderror"
                            required
                        >

                            <option
                                value=""
                                disabled
                                {{ old('rak') ? '' : 'selected' }}
                            >
                                -- Pilih Rak --
                            </option>

                            @foreach ($racks as $rack)

                                <option
                                    value="{{ $rack->code }}"
                                    {{ old('rak') == $rack->code ? 'selected' : '' }}
                                >
                                    {{ $rack->code }} – {{ $rack->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('rak')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- EDISI --}}

                    <div class="book-form-group">

                        <label for="edition">
                            Edisi
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="edition"
                            name="edition"
                            class="input @error('edition') is-invalid @enderror"
                            placeholder="cth. Edisi 1"
                            value="{{ old('edition') }}"
                            required
                        >

                        @error('edition')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- ISBN --}}

                    <div class="book-form-group">

                        <label for="isbn">
                            ISBN
                            <span class="required">*</span>
                        </label>

                        <div class="isbn-input-wrapper">

                            <input
                                type="text"
                                id="isbn"
                                name="isbn"
                                class="input @error('isbn') is-invalid @enderror"
                                placeholder="Scan atau masukkan ISBN"
                                value="{{ old('isbn') }}"
                                autocomplete="off"
                                required
                            >

                            <button
                                type="button"
                                id="scanIsbnButton"
                                class="isbn-scan-btn"
                            >

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                    <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                    <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                    <path d="M7 21H5a2 2 0 0 1-2-2v-2" />

                                    <path d="M7 8v8" />
                                    <path d="M10 8v8" />
                                    <path d="M13 8v8" />
                                    <path d="M16 8v8" />

                                </svg>

                                <span>
                                    Scan ISBN
                                </span>

                            </button>

                        </div>

                        <small class="form-help">
                            Scan barcode ISBN pada buku untuk mengisi data buku secara otomatis.
                        </small>

                        @error('isbn')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- CALL NUMBER --}}

                    <div class="book-form-group">

                        <label for="call_number">
                            No. Panggil (Call Number)
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="call_number"
                            name="call_number"
                            class="input @error('call_number') is-invalid @enderror"
                            placeholder="cth. 813.01 HIR l"
                            value="{{ old('call_number') }}"
                            required
                        >

                        @error('call_number')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                         SECTION 2
                    ================================================== --}}

                    <div class="form-section-heading">

                        <div class="form-section-icon">
                            2
                        </div>

                        <span class="form-section-title">
                            Penerbitan & Persediaan
                        </span>

                    </div>


                    {{-- PENERBIT --}}

                    <div class="book-form-group">

                        <label for="publisher">
                            Penerbit
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="publisher"
                            name="publisher"
                            class="input @error('publisher') is-invalid @enderror"
                            placeholder="cth. PT Gramedia Pustaka Utama"
                            value="{{ old('publisher') }}"
                            required
                        >

                        @error('publisher')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- TAHUN --}}

                    <div class="book-form-group">

                        <label for="publication_year">
                            Tahun Terbit
                            <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="publication_year"
                            name="publication_year"
                            class="input @error('publication_year') is-invalid @enderror"
                            placeholder="cth. 2024"
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            value="{{ old('publication_year', date('Y')) }}"
                            required
                        >

                        @error('publication_year')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- STOK --}}

                    <div class="book-form-group">

                        <label for="stock">
                            Jumlah Stok Buku
                            <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            class="input @error('stock') is-invalid @enderror"
                            placeholder="cth. 10"
                            min="1"
                            value="{{ old('stock', 1) }}"
                            required
                        >

                        @error('stock')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                         SECTION 3
                    ================================================== --}}

                    <div class="form-section-heading">

                        <div class="form-section-icon">
                            3
                        </div>

                        <span class="form-section-title">
                            Sinopsis & Sampul Buku
                        </span>

                    </div>


                    {{-- DESKRIPSI --}}

                    <div class="book-form-group book-form-full">

                        <label for="description">
                            Sinopsis / Deskripsi Buku
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="input @error('description') is-invalid @enderror"
                            rows="4"
                            placeholder="Tuliskan ringkasan singkat atau poin penting mengenai isi buku ini..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- COVER --}}

                    <div class="book-form-group book-form-full">

                        <label for="cover">
                            Cover / Sampul Buku
                        </label>

                        <div class="cover-upload-area">

                            <div
                                class="cover-preview-box"
                                id="coverPreviewContainer"
                            >

                                <div
                                    class="cover-placeholder-icon"
                                    id="coverPlaceholder"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >

                                        <rect
                                            width="18"
                                            height="18"
                                            x="3"
                                            y="3"
                                            rx="2"
                                        />

                                        <circle
                                            cx="9"
                                            cy="9"
                                            r="2"
                                        />

                                        <path
                                            d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"
                                        />

                                    </svg>

                                    <span>
                                        No Cover
                                    </span>

                                </div>


                                <img
                                    id="coverPreviewImage"
                                    src="#"
                                    alt="Preview Cover"
                                    style="display: none;"
                                >

                            </div>


                            <div class="cover-upload-details">

                                <input
                                    type="file"
                                    id="cover"
                                    name="cover"
                                    class="cover-file-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                >

                                <label
                                    for="cover"
                                    class="cover-upload-btn"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >

                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />

                                        <polyline points="17 8 12 3 7 8" />

                                        <line
                                            x1="12"
                                            x2="12"
                                            y1="3"
                                            y2="15"
                                        />

                                    </svg>

                                    <span>
                                        Pilih Berkas Gambar...
                                    </span>

                                </label>


                                <p class="book-cover-help">

                                    Format didukung:

                                    <strong>
                                        JPG, JPEG, PNG, WEBP
                                    </strong>.

                                    Ukuran maksimum:

                                    <strong>
                                        2 MB
                                    </strong>.

                                </p>


                                @error('cover')
                                    <span class="form-error">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}

                <div class="book-form-actions">

                    <a
                        href="{{ route('books.index') }}"
                        class="book-btn-cancel"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="book-btn-save"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M19 21H5a2 2 0 0 1 2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />

                            <polyline points="17 21 17 13 7 13 7 21" />

                            <polyline points="7 3 7 8 15 8" />

                        </svg>

                        <span>
                            Simpan Buku
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         ISBN SCANNER MODAL
    ========================================================== --}}

    <div
        id="isbnScannerModal"
        class="isbn-scanner-modal"
        aria-hidden="true"
    >

        <div class="isbn-scanner-backdrop"></div>

        <div class="isbn-scanner-dialog">

            <div class="isbn-scanner-header">

                <div>

                    <span class="isbn-scanner-label">
                        SCAN ISBN
                    </span>

                    <h3>
                        Pindai Barcode Buku
                    </h3>

                    <p>
                        Arahkan kamera ke barcode ISBN pada buku.
                    </p>

                </div>

                <button
                    type="button"
                    id="closeIsbnScanner"
                    class="isbn-scanner-close"
                    aria-label="Tutup"
                >
                    &times;
                </button>

            </div>


            <div class="isbn-scanner-camera">

                <div id="isbnReader"></div>

                <div class="isbn-scanner-frame">
                    <span></span>
                </div>

            </div>


            <div
                id="isbnScannerStatus"
                class="isbn-scanner-status"
            >
                Arahkan barcode ke dalam kotak.
            </div>


            <button
                type="button"
                id="cancelIsbnScanner"
                class="isbn-scanner-cancel"
            >
                Tutup Scanner
            </button>

        </div>

    </div>


    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}

    @push('scripts')

        {{-- HTML5 QR Code / Barcode Scanner --}}
        <script src="https://unpkg.com/html5-qrcode"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | ELEMENT
                |--------------------------------------------------------------------------
                */

                const categorySelect =
                    document.getElementById('category_id');

                const subcategorySelect =
                    document.getElementById('subcategory_id');

                const coverInput =
                    document.getElementById('cover');

                const previewImage =
                    document.getElementById('coverPreviewImage');

                const placeholder =
                    document.getElementById('coverPlaceholder');


                /*
                |--------------------------------------------------------------------------
                | DATA SUBKATEGORI DARI LARAVEL
                |--------------------------------------------------------------------------
                */

                const subcategories =
                    @json($subcategoryData);


                /*
                |--------------------------------------------------------------------------
                | UPDATE SUBKATEGORI
                |--------------------------------------------------------------------------
                */

                function updateSubcategories(
                    categoryId,
                    selectedId = ''
                ) {

                    if (!subcategorySelect) {
                        return;
                    }

                    subcategorySelect.innerHTML = '';


                    const defaultOption =
                        document.createElement('option');

                    defaultOption.value = '';

                    defaultOption.textContent =
                        '-- Pilih Subkategori --';

                    defaultOption.selected =
                        selectedId === '' ||
                        selectedId === null;

                    subcategorySelect.appendChild(
                        defaultOption
                    );


                    if (!categoryId) {

                        subcategorySelect.disabled =
                            true;

                        subcategorySelect.required =
                            false;

                        return;
                    }


                    const categorySubcategories =
                        subcategories[String(categoryId)] || [];


                    if (
                        !Array.isArray(
                            categorySubcategories
                        ) ||
                        categorySubcategories.length === 0
                    ) {

                        subcategorySelect.disabled =
                            true;

                        subcategorySelect.required =
                            false;

                        defaultOption.textContent =
                            '-- Tidak ada Subkategori --';

                        return;
                    }


                    subcategorySelect.disabled =
                        false;

                    subcategorySelect.required =
                        true;


                    categorySubcategories.forEach(
                        function(subcategory) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                subcategory.id;

                            option.textContent =
                                subcategory.name;


                            if (
                                String(
                                    subcategory.id
                                ) ===
                                String(
                                    selectedId
                                )
                            ) {

                                option.selected =
                                    true;
                            }


                            subcategorySelect.appendChild(
                                option
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | EVENT KATEGORI
                |--------------------------------------------------------------------------
                */

                if (categorySelect) {

                    categorySelect.addEventListener(
                        'change',
                        function() {

                            updateSubcategories(
                                this.value,
                                ''
                            );

                        }
                    );


                    updateSubcategories(
                        categorySelect.value,
                        @json(old('subcategory_id', ''))
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | PREVIEW COVER
                |--------------------------------------------------------------------------
                */

                if (
                    coverInput &&
                    previewImage
                ) {

                    coverInput.addEventListener(
                        'change',
                        function(event) {

                            const file =
                                event.target.files[0];


                            if (!file) {

                                previewImage.src =
                                    '#';

                                previewImage.style.display =
                                    'none';


                                if (placeholder) {

                                    placeholder.style.display =
                                        'flex';

                                }

                                return;
                            }


                            const reader =
                                new FileReader();


                            reader.onload =
                                function(event) {

                                    previewImage.src =
                                        event.target.result;

                                    previewImage.style.display =
                                        'block';


                                    if (placeholder) {

                                        placeholder.style.display =
                                            'none';

                                    }

                                };


                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ISBN SCANNER ELEMENT
                |--------------------------------------------------------------------------
                */

                const isbnInput =
                    document.getElementById(
                        'isbn'
                    );

                const scanIsbnButton =
                    document.getElementById(
                        'scanIsbnButton'
                    );

                const isbnScannerModal =
                    document.getElementById(
                        'isbnScannerModal'
                    );

                const closeIsbnScanner =
                    document.getElementById(
                        'closeIsbnScanner'
                    );

                const cancelIsbnScanner =
                    document.getElementById(
                        'cancelIsbnScanner'
                    );

                const isbnScannerStatus =
                    document.getElementById(
                        'isbnScannerStatus'
                    );

                let isbnScanner = null;

                let isbnScannerRunning = false;


                /*
                |--------------------------------------------------------------------------
                | NORMALISASI ISBN
                |--------------------------------------------------------------------------
                */

                function normalizeIsbn(value) {

                    if (!value) {
                        return null;
                    }

                    let isbn =
                        String(value)
                            .trim()
                            .replace(/[^0-9Xx]/g, '')
                            .toUpperCase();


                    /*
                    | ISBN-13 normal
                    */

                    if (
                        isbn.length === 13 &&
                        (
                            isbn.startsWith('978') ||
                            isbn.startsWith('979')
                        )
                    ) {

                        return isbn;
                    }


                    /*
                    | ISBN-10
                    */

                    if (isbn.length === 10) {

                        return isbn;
                    }


                    /*
                    | Tetap izinkan 13 digit
                    | untuk diproses server.
                    */

                    if (isbn.length === 13) {

                        return isbn;
                    }


                    return null;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDASI ISBN
                |--------------------------------------------------------------------------
                */

                function isValidIsbn(value) {

                    const isbn =
                        normalizeIsbn(value);

                    if (!isbn) {
                        return false;
                    }

                    return (
                        /^\d{10}$/.test(isbn) ||
                        /^\d{13}$/.test(isbn)
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | OPEN SCANNER
                |--------------------------------------------------------------------------
                */

                async function openIsbnScanner() {

                    if (!isbnScannerModal) {
                        return;
                    }


                    if (
                        typeof Html5Qrcode ===
                        'undefined'
                    ) {

                        alert(
                            'Library scanner belum berhasil dimuat. Periksa koneksi internet.'
                        );

                        return;
                    }


                    isbnScannerModal.classList.add(
                        'is-open'
                    );

                    isbnScannerModal.setAttribute(
                        'aria-hidden',
                        'false'
                    );


                    if (isbnScannerStatus) {

                        isbnScannerStatus.textContent =
                            'Memulai kamera...';

                    }


                    if (!isbnScanner) {

                        isbnScanner =
                            new Html5Qrcode(
                                'isbnReader'
                            );

                    }


                    try {

                        await isbnScanner.start(

                            {
                                facingMode: 'environment'
                            },

                            {
                                fps: 10,

                                qrbox: {
                                    width: 320,
                                    height: 120
                                },

                                aspectRatio: 1.333334,

                                formatsToSupport: [

                                    Html5QrcodeSupportedFormats
                                        .EAN_13,

                                    Html5QrcodeSupportedFormats
                                        .EAN_8

                                ]
                            },


                            async function(decodedText) {

                                const isbn =
                                    normalizeIsbn(
                                        decodedText
                                    );


                                if (
                                    isbnScannerStatus
                                ) {

                                    isbnScannerStatus.textContent =
                                        'Barcode terbaca: ' +
                                        decodedText;

                                }


                                console.log(
                                    'HASIL BARCODE:',
                                    decodedText
                                );

                                console.log(
                                    'HASIL ISBN:',
                                    isbn
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | ISBN VALID
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    !isValidIsbn(
                                        isbn
                                    )
                                ) {

                                    if (
                                        isbnScannerStatus
                                    ) {

                                        isbnScannerStatus.textContent =
                                            'Barcode terbaca, tetapi bukan ISBN yang valid.';

                                    }

                                    return;
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | STOP SCANNER
                                |--------------------------------------------------------------------------
                                */

                                await stopIsbnScanner();


                                /*
                                |--------------------------------------------------------------------------
                                | ISI ISBN
                                |--------------------------------------------------------------------------
                                */

                                if (isbnInput) {

                                    isbnInput.value =
                                        isbn;

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | LOOKUP
                                |--------------------------------------------------------------------------
                                */

                                await lookupIsbn(
                                    isbn
                                );

                            },


                            function() {

                                /*
                                | Error setiap frame
                                | tidak ditampilkan agar UI
                                | tidak berkedip.
                                */

                            }

                        );


                        isbnScannerRunning =
                            true;


                        if (
                            isbnScannerStatus
                        ) {

                            isbnScannerStatus.textContent =
                                'Arahkan barcode ISBN ke dalam kotak.';

                        }

                    } catch (error) {

                        console.error(
                            'ISBN scanner error:',
                            error
                        );


                        if (
                            isbnScannerStatus
                        ) {

                            isbnScannerStatus.textContent =
                                'Kamera tidak dapat digunakan. Pastikan izin kamera diberikan.';

                        }

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | STOP SCANNER
                |--------------------------------------------------------------------------
                */

                async function stopIsbnScanner() {

                    if (
                        !isbnScanner ||
                        !isbnScannerRunning
                    ) {

                        return;
                    }


                    try {

                        await isbnScanner.stop();

                    } catch (error) {

                        console.warn(
                            'Scanner stop:',
                            error
                        );

                    }


                    isbnScannerRunning =
                        false;

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE SCANNER
                |--------------------------------------------------------------------------
                */

                async function closeIsbnScannerModal() {

                    await stopIsbnScanner();


                    if (isbnScannerModal) {

                        isbnScannerModal.classList.remove(
                            'is-open'
                        );

                        isbnScannerModal.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | LOOKUP ISBN
                |--------------------------------------------------------------------------
                */

                async function lookupIsbn(
                    isbn
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | URL HARUS DI LUAR TRY
                    | supaya bisa digunakan di catch.
                    |--------------------------------------------------------------------------
                    */

                    const lookupUrl =
                        `/books/isbn-lookup?isbn=${encodeURIComponent(isbn)}`;


                    if (
                        isbnScannerStatus
                    ) {

                        isbnScannerStatus.textContent =
                            'Mengambil data buku...';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DISABLE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    if (scanIsbnButton) {

                        scanIsbnButton.disabled =
                            true;


                        const buttonText =
                            scanIsbnButton.querySelector(
                                'span'
                            );

                        if (buttonText) {

                            buttonText.textContent =
                                'Mencari...';

                        }

                    }


                    try {

                        console.log(
                            'ISBN LOOKUP URL:',
                            lookupUrl
                        );


                        const response =
                            await fetch(
                                lookupUrl,
                                {
                                    method: 'GET',

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest'
                                    },

                                    credentials:
                                        'same-origin',

                                    cache:
                                        'no-store'
                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | AMBIL JSON
                        |--------------------------------------------------------------------------
                        */

                        let result = null;

                        try {

                            result =
                                await response.json();

                        } catch (jsonError) {

                            console.error(
                                'Response bukan JSON:',
                                jsonError
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | DATA TIDAK DITEMUKAN
                        |--------------------------------------------------------------------------
                        |
                        | Ini BUKAN error aplikasi.
                        | ISBN tetap dipertahankan.
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !response.ok ||
                            !result ||
                            !result.success
                        ) {

                            if (isbnInput) {

                                isbnInput.value =
                                    result?.data?.isbn ||
                                    isbn;

                            }


                            await closeIsbnScannerModal();


                            if (
                                isbnScannerStatus
                            ) {

                                isbnScannerStatus.textContent =
                                    'ISBN terbaca. Data buku tidak ditemukan otomatis, silakan isi data secara manual.';

                            }


                            const titleInput =
                                document.getElementById(
                                    'title'
                                );


                            if (titleInput) {

                                titleInput.focus();

                                titleInput.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                            }


                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | DATA DITEMUKAN
                        |--------------------------------------------------------------------------
                        */

                        const data =
                            result.data || {};


                        /*
                        |--------------------------------------------------------------------------
                        | AUTO FILL
                        |--------------------------------------------------------------------------
                        */

                        setInputValue(
                            'isbn',
                            data.isbn
                        );

                        setInputValue(
                            'title',
                            data.title
                        );

                        setInputValue(
                            'author',
                            data.author
                        );

                        setInputValue(
                            'publisher',
                            data.publisher
                        );

                        setInputValue(
                            'publication_year',
                            data.publication_year
                        );

                        setInputValue(
                            'ddc',
                            data.ddc
                        );

                        setInputValue(
                            'edition',
                            data.edition
                        );

                        setInputValue(
                            'description',
                            data.description
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | COVER
                        |--------------------------------------------------------------------------
                        */

                        if (data.cover) {

                            const coverPreview =
                                document.getElementById(
                                    'coverPreviewImage'
                                );

                            const coverPlaceholder =
                                document.getElementById(
                                    'coverPlaceholder'
                                );


                            if (
                                coverPreview
                            ) {

                                coverPreview.src =
                                    data.cover;

                                coverPreview.style.display =
                                    'block';

                            }


                            if (
                                coverPlaceholder
                            ) {

                                coverPlaceholder.style.display =
                                    'none';

                            }

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | TUTUP MODAL
                        |--------------------------------------------------------------------------
                        */

                        await closeIsbnScannerModal();


                        /*
                        |--------------------------------------------------------------------------
                        | FOCUS JUDUL
                        |--------------------------------------------------------------------------
                        */

                        const titleInput =
                            document.getElementById(
                                'title'
                            );


                        if (titleInput) {

                            titleInput.focus();

                            titleInput.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                        }

                    } catch (error) {

                        console.error(
                            'ISBN lookup error:',
                            error
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ISBN TETAP DIPERTAHANKAN
                        |--------------------------------------------------------------------------
                        */

                        if (isbnInput) {

                            isbnInput.value =
                                isbn;

                        }


                        await closeIsbnScannerModal();


                        /*
                        |--------------------------------------------------------------------------
                        | BUKAN ERROR FATAL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            isbnScannerStatus
                        ) {

                            isbnScannerStatus.textContent =
                                'ISBN terbaca, tetapi data buku tidak dapat diambil otomatis. Silakan isi data secara manual.';

                        }


                        const titleInput =
                            document.getElementById(
                                'title'
                            );


                        if (titleInput) {

                            titleInput.focus();

                            titleInput.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                        }

                    } finally {

                        /*
                        |--------------------------------------------------------------------------
                        | ENABLE BUTTON
                        |--------------------------------------------------------------------------
                        */

                        if (scanIsbnButton) {

                            scanIsbnButton.disabled =
                                false;


                            const buttonText =
                                scanIsbnButton.querySelector(
                                    'span'
                                );


                            if (buttonText) {

                                buttonText.textContent =
                                    'Scan ISBN';

                            }

                        }

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | HELPER INPUT
                |--------------------------------------------------------------------------
                */

                function setInputValue(
                    id,
                    value
                ) {

                    const element =
                        document.getElementById(
                            id
                        );


                    if (
                        element &&
                        value !== null &&
                        value !== undefined &&
                        value !== ''
                    ) {

                        element.value =
                            value;

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON SCAN
                |--------------------------------------------------------------------------
                */

                if (scanIsbnButton) {

                    scanIsbnButton.addEventListener(
                        'click',
                        function() {

                            openIsbnScanner();

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE BUTTON
                |--------------------------------------------------------------------------
                */

                if (closeIsbnScanner) {

                    closeIsbnScanner.addEventListener(
                        'click',
                        closeIsbnScannerModal
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CANCEL BUTTON
                |--------------------------------------------------------------------------
                */

                if (cancelIsbnScanner) {

                    cancelIsbnScanner.addEventListener(
                        'click',
                        closeIsbnScannerModal
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | BACKDROP
                |--------------------------------------------------------------------------
                */

                const scannerBackdrop =
                    document.querySelector(
                        '.isbn-scanner-backdrop'
                    );


                if (scannerBackdrop) {

                    scannerBackdrop.addEventListener(
                        'click',
                        closeIsbnScannerModal
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ESC
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function(event) {

                        if (
                            event.key === 'Escape' &&
                            isbnScannerModal &&
                            isbnScannerModal.classList.contains(
                                'is-open'
                            )
                        ) {

                            closeIsbnScannerModal();

                        }

                    }
                );

            });
        </script>

    @endpush

@endsection