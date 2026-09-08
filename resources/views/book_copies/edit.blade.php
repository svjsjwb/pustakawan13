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

        <a href="{{ route('books.edit', $book) }}">
            Edit Buku
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
         FORM CARD
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
                Perbarui informasi dan lokasi salinan fisik buku
                <strong>{{ $book->title }}</strong>.
            </p>

        </div>


        {{-- =====================================================
             ERROR
        ====================================================== --}}
        @if($errors->any())

            <div class="form-alert-error">

                <strong>
                    Terdapat beberapa kesalahan:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =====================================================
             FORM
        ====================================================== --}}
        <form
            action="{{ route('books.copies.update', [$book, $copy]) }}"
            method="POST">

            @csrf
            @method('PUT')


            <div class="book-form-grid">

                {{-- =================================================
                     SECTION 1
                ================================================== --}}
                <div class="form-section-heading">

                    <span class="form-section-icon">
                        1
                    </span>

                    <span class="form-section-title">
                        Informasi Eksemplar
                    </span>

                </div>


                {{-- =================================================
                     BARCODE
                ================================================== --}}
                <div class="book-form-group">

                    <label for="barcode">

                        Barcode

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="barcode"
                        name="barcode"
                        value="{{ old('barcode', $copy->barcode) }}"
                        placeholder="cth. BK-00001-001"
                        autocomplete="off"
                        class="@error('barcode') is-invalid @enderror">

                    @error('barcode')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     STATUS
                ================================================== --}}
                <div class="book-form-group">

                    <label for="status">

                        Status

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        id="status"
                        name="status"
                        class="@error('status') is-invalid @enderror">

                        <option value="available"
                            @selected(old('status', $copy->status) === 'available')>
                            Tersedia
                        </option>

                        <option value="reserved"
                            @selected(old('status', $copy->status) === 'reserved')>
                            Direservasi
                        </option>

                        <option value="borrowed"
                            @selected(old('status', $copy->status) === 'borrowed')>
                            Dipinjam
                        </option>

                        <option value="lost"
                            @selected(old('status', $copy->status) === 'lost')>
                            Hilang
                        </option>

                        <option value="damaged"
                            @selected(old('status', $copy->status) === 'damaged')>
                            Rusak
                        </option>

                        <option value="maintenance"
                            @selected(old('status', $copy->status) === 'maintenance')>
                            Maintenance
                        </option>

                    </select>

                    @error('status')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     SECTION 2
                ================================================== --}}
                <div class="form-section-heading">

                    <span class="form-section-icon">
                        2
                    </span>

                    <span class="form-section-title">
                        Lokasi Buku
                    </span>

                </div>


                {{-- =================================================
                     RAK
                ================================================== --}}
                <div class="book-form-group">

                    <label for="shelf_id">
                        Rak
                    </label>

                    <select
                        id="shelf_id"
                        name="shelf_id"
                        class="@error('shelf_id') is-invalid @enderror">

                        <option value="">
                            -- Pilih Rak --
                        </option>

                        @foreach($floors as $floor)

                            @foreach($floor->zones as $zone)

                                <optgroup
                                    label="{{ $floor->name }} — {{ $zone->name }}">

                                    @foreach($zone->shelves as $shelf)

                                        <option
                                            value="{{ $shelf->id }}"
                                            @selected(
                                                old(
                                                    'shelf_id',
                                                    $copy->shelf_id
                                                ) == $shelf->id
                                            )>

                                            {{ $shelf->code }}

                                        </option>

                                    @endforeach

                                </optgroup>

                            @endforeach

                        @endforeach

                    </select>

                    @error('shelf_id')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     SECTION
                ================================================== --}}
                <div class="book-form-group">

                    <label for="section">

                        Section

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        id="section"
                        name="section"
                        class="@error('section') is-invalid @enderror">

                        <option value="1"
                            @selected(
                                old('section', $copy->section) == 1
                            )>

                            Section 1

                        </option>

                        <option value="2"
                            @selected(
                                old('section', $copy->section) == 2
                            )>

                            Section 2

                        </option>

                    </select>

                    @error('section')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     MUKA RAK
                ================================================== --}}
                <div class="book-form-group">

                    <label for="side">

                        Muka Rak

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        id="side"
                        name="side"
                        class="@error('side') is-invalid @enderror">

                        <option value="front"
                            @selected(
                                old('side', $copy->side) === 'front'
                            )>

                            Depan

                        </option>

                        <option value="back"
                            @selected(
                                old('side', $copy->side) === 'back'
                            )>

                            Belakang

                        </option>

                    </select>

                    @error('side')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     BARIS
                ================================================== --}}
                <div class="book-form-group">

                    <label for="row">
                        Baris
                    </label>

                    <select
                        id="row"
                        name="row"
                        class="@error('row') is-invalid @enderror">

                        <option value="">
                            -- Pilih Baris --
                        </option>

                        <option value="1"
                            @selected(
                                old('row', $copy->row) == 1
                            )>

                            Baris 1 — Atas

                        </option>

                        <option value="2"
                            @selected(
                                old('row', $copy->row) == 2
                            )>

                            Baris 2 — Tengah

                        </option>

                        <option value="3"
                            @selected(
                                old('row', $copy->row) == 3
                            )>

                            Baris 3 — Bawah

                        </option>

                    </select>

                    @error('row')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- =================================================
                     KOLOM
                ================================================== --}}
                <div class="book-form-group">

                    <label for="column">
                        Kolom
                    </label>

                    <input
                        type="number"
                        id="column"
                        name="column"
                        value="{{ old('column', $copy->column) }}"
                        min="1"
                        max="30"
                        placeholder="1 - 30"
                        class="@error('column') is-invalid @enderror">

                    @error('column')

                        <span class="form-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>

            </div>


            {{-- =====================================================
                 ACTION
            ====================================================== --}}
            <div class="book-form-actions">

                <a
                    href="{{ route('books.copies.index', $book) }}"
                    class="book-btn-cancel">

                    Batal

                </a>


                <button
                    type="submit"
                    class="book-btn-save">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>

                        <polyline points="17 21 17 13 7 13 7 21"/>

                        <polyline points="7 3 7 8 15 8"/>

                    </svg>

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection