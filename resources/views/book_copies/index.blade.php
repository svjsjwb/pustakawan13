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
        @if(session('success'))

            <div class="alert-success">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M20 6 9 17l-5-5"/>

                </svg>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- =====================================================
             ALERT ERROR
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


            <a
                href="{{ route('books.copies.create', $book) }}"
                class="book-btn-save book-copy-add-btn">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <line
                        x1="12"
                        y1="5"
                        x2="12"
                        y2="19"/>

                    <line
                        x1="5"
                        y1="12"
                        x2="19"
                        y2="12"/>

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

                                'available' =>
                                    'Tersedia',

                                'reserved' =>
                                    'Direservasi',

                                'borrowed' =>
                                    'Dipinjam',

                                'lost' =>
                                    'Hilang',

                                'damaged' =>
                                    'Rusak',

                                'maintenance' =>
                                    'Maintenance',

                            ];


                            $statusClass = [

                                'available' =>
                                    'available',

                                'reserved' =>
                                    'reserved',

                                'borrowed' =>
                                    'borrowed',

                                'lost' =>
                                    'danger',

                                'damaged' =>
                                    'danger',

                                'maintenance' =>
                                    'maintenance',

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

                                <span
                                    class="copy-status {{ $statusClass[$copy->status] ?? '' }}">

                                    {{ $statusLabel[$copy->status] ?? $copy->status }}

                                </span>

                            </td>


                            {{-- =================================================
                                 LOKASI
                            ================================================== --}}
                            <td>

                                @if($copy->shelf)

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

                                            {{ $copy->side === 'front'
                                                ? 'Depan'
                                                : 'Belakang'
                                            }}

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
                                    <a
                                        href="{{ route(
                                            'books.copies.edit',
                                            [$book, $copy]
                                        ) }}"
                                        class="copy-edit-btn"
                                        title="Edit eksemplar">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round">

                                            <path
                                                d="M12 20h9"/>

                                            <path
                                                d="M16.5 3.5a2.121 2.121 0 0 1 3 3L8 18l-4 1 1-4Z"/>

                                        </svg>

                                        <span>
                                            Edit
                                        </span>

                                    </a>


                                    {{-- ================================
                                         HAPUS
                                    ================================= --}}
                                    <form
                                        action="{{ route(
                                            'books.copies.destroy',
                                            [$book, $copy]
                                        ) }}"
                                        method="POST"
                                        class="copy-delete-form"
                                        onsubmit="return confirm(
                                            'Yakin ingin menghapus eksemplar ini?'
                                        );">

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="copy-delete-btn"
                                            title="Hapus eksemplar">

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">

                                                <polyline
                                                    points="3 6 5 6 21 6"/>

                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>

                                                <path
                                                    d="M10 11v6"/>

                                                <path
                                                    d="M14 11v6"/>

                                                <path
                                                    d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>

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

                            <td
                                colspan="5"
                                class="copy-empty">

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

            <a
                href="{{ route('books.index') }}"
                class="book-btn-cancel">

                Kembali ke Manajemen Buku

            </a>

        </div>

    </div>

</div>

@endsection