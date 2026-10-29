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
                    Perbarui informasi eksemplar buku
                    <strong>{{ $book->title }}</strong>.
                </p>

            </div>


            {{-- =====================================================
             ERROR
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
                     COPY
                    ================================================== --}}
                    <div class="book-form-group">

                        <label for="copy">
                            Copy
                        </label>

                        <input
                            type="text"
                            id="copy"
                            value="{{ $copy->copy }}"
                            readonly
                            class="copy-readonly">

                    </div>


                    {{-- =================================================
                     KONDISI FISIK
                    ================================================== --}}
                    <div class="book-form-group">

                        <label for="condition">

                            Kondisi Fisik

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            id="condition"
                            name="condition"
                            required
                            class="@error('condition') is-invalid @enderror">

                            <option
                                value="baik"
                                @selected(old('condition', $copy->condition ?? 'baik') === 'baik')>

                                Baik

                            </option>

                            <option
                                value="rusak"
                                @selected(old('condition', $copy->condition ?? 'baik') === 'rusak')>

                                Rusak

                            </option>

                            <option
                                value="hilang"
                                @selected(old('condition', $copy->condition ?? 'baik') === 'hilang')>

                                Hilang

                            </option>

                        </select>

                        @error('condition')

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
                     RAK READONLY
                    ================================================== --}}
                    <div class="book-form-group">

                        <label for="shelf_display">
                            Rak
                        </label>

                        <input
                            type="text"
                            id="shelf_display"
                            value="{{ $copy->shelf?->name ?? 'Rak belum ditentukan' }}"
                            readonly
                            class="copy-readonly">

                        <small style="display: block; margin-top: 6px; color: #777;">
                            Rak tidak dapat diubah dari Edit Eksemplar.
                            Untuk memindahkan buku, gunakan menu <strong>Edit Buku</strong>.
                        </small>

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

                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2-2Z" />

                            <polyline points="17 21 17 13 7 13 7 21" />

                            <polyline points="7 3 7 8 15 8 15 3" />

                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection