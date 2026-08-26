@extends('layouts.app')

@section('title', 'Sirkulasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/circulation.css') }}">
@endpush

@section('content')

<section class="page" id="page-circulation">
    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

    <div class="alert-success">

        {{ session('success') }}

    </div>

    @endif


    {{-- ERROR MESSAGE --}}

    @if(session('error'))

    <div class="alert-error">

        {{ session('error') }}

    </div>

    @endif


    {{-- VALIDATION ERROR --}}

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


    <div class="two-col">

        {{-- =========================================
         FORM PEMINJAMAN
    ========================================== --}}

        <div class="card card-pad">

            <h3>
                Pinjamkan Buku
            </h3>


            <p class="loan-description">
                Pilih anggota dan buku untuk memproses
                peminjaman baru.
            </p>


            <form
                action="{{ route('circulation.store') }}"
                method="POST">

                @csrf


                {{-- RESERVASI --}}

                <div class="field">

                    <label>
                        Reservasi
                    </label>


                    <select
                        name="reservation_id"
                        id="reservation_id">

                        <option value="">
                            -- Peminjaman Biasa --
                        </option>


                        @foreach($reservations as $reservation)

                        @if($reservation->bookCopy)

                        <option
                            value="{{ $reservation->id }}"
                            @selected(
                            old('reservation_id')==$reservation->id
                            )
                            >

                            {{ $reservation->member->name }}

                            —

                            {{ $reservation->book->title }}

                            —

                            {{ $reservation->bookCopy->barcode }}

                        </option>

                        @endif

                        @endforeach

                    </select>

                </div>


                {{-- ANGGOTA --}}

                <div class="field">

                    <label>
                        Anggota
                    </label>


                    <select
                        name="member_id"
                        required>

                        <option value="">
                            Pilih Anggota
                        </option>


                        @foreach($members as $member)

                        <option
                            value="{{ $member->id }}"
                            @selected(
                            old('member_id')==$member->id
                            )
                            >

                            {{ $member->name }}

                        </option>

                        @endforeach

                    </select>

                </div>


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
                        <li>{{ $error }}</li>
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

                            <h3>Pinjam Buku</h3>

                            <p class="loan-description">
                                Pilih anggota, buku, dan tanggal peminjaman.
                            </p>


                            {{-- =================================================
                     FORM PEMINJAMAN
                ================================================== --}}

                            <form
                                action="{{ route('circulation.store') }}"
                                method="POST"
                                id="circulationForm">

                                @csrf


                                {{-- ================================
                         ANGGOTA
                    ================================= --}}

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
                                            @selected(old('member_id')==$member->id)
                                            >
                                            {{ $member->name }}
                                        </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- ================================
                         BUKU
                    ================================= --}}

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
                                            @selected(old('book_id')==$book->id)
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

                                </div>


                                {{-- ================================
                         TANGGAL
                    ================================= --}}

                                <div class="field-row">

                                    <div class="field">

                                        <label for="borrowed_at">
                                            Tanggal Pinjam
                                        </label>

                                        <input
                                            type="date"
                                            name="borrowed_at"
                                            id="borrowed_at"
                                            value="{{ old('borrowed_at', now()->format('Y-m-d')) }}"
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
                                            value="{{ old('due_at', now()->addDays(7)->format('Y-m-d')) }}"
                                            required>

                                    </div>

                                </div>


                                {{-- =================================================
                         SUBMIT
                    ================================================== --}}

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


                        {{-- =================================================
                 HEADER DAFTAR
            ================================================== --}}

                        <div class="borrowing-header">

                            <div>

                                <h3>
                                    Daftar Peminjaman
                                </h3>

                                <p>
                                    Daftar anggota yang sedang meminjam buku.
                                </p>

                            </div>


                            {{-- SEARCH --}}

                            <div class="search-box">

                                <input
                                    type="text"
                                    id="borrowingSearch"
                                    placeholder="Cari peminjaman..."
                                    autocomplete="off">

                            </div>

                        </div>


                        {{-- =================================================
                 TABLE
            ================================================== --}}

                        <div class="table-wrap">

                            <table id="borrowingTable">

                                <thead>

                                    <tr>

                                        <th>ANGGOTA</th>

                                        <th>BUKU</th>

                                        <th>PINJAM</th>

                                        <th>TANGGAL PENGEMBALIAN</th>

                                        <th>STATUS</th>

                                        <th>AKSI</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @forelse($borrowings as $borrowing)

                                    @php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | STATUS PEMINJAMAN
                                    |--------------------------------------------------------------------------
                                    */

                                    $isLate =
                                    $borrowing->status === 'dipinjam'
                                    &&
                                    now()->startOfDay()->gt(
                                    \Carbon\Carbon::parse(
                                    $borrowing->due_at
                                    )->startOfDay()
                                    );


                                    $displayStatus =
                                    $isLate
                                    ? 'terlambat'
                                    : $borrowing->status;

                                    @endphp


                                    <tr class="borrowing-row">


                                        {{-- =========================================
                                     ANGGOTA
                                ========================================== --}}

                                        <td class="member-cell">

                                            {{ $borrowing->member->name ?? '-' }}

                                        </td>


                                        {{-- =========================================
                                     BUKU
                                ========================================== --}}

                                        <td class="book-cell">

                                            @forelse($borrowing->details as $detail)

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


                                        {{-- =========================================
                                     TANGGAL PINJAM
                                ========================================== --}}

                                        <td>

                                            {{ \Carbon\Carbon::parse(
                                        $borrowing->borrowed_at
                                    )->format('d/m/Y') }}

                                        </td>


                                        {{-- =========================================
                                     TANGGAL PENGEMBALIAN
                                ========================================== --}}

                                        <td>

                                            {{ \Carbon\Carbon::parse(
                                        $borrowing->due_at
                                    )->format('d/m/Y') }}

                                        </td>


                                        {{-- =========================================
                                     STATUS
                                ========================================== --}}

                                        <td>

                                            @if($displayStatus === 'dipinjam')

                                            <span class="status-badge aktif">
                                                Aktif
                                            </span>

                                            @elseif($displayStatus === 'terlambat')

                                            <span class="status-badge terlambat">
                                                Terlambat
                                            </span>

                                            @else

                                            <span class="status-badge kembali">
                                                Selesai
                                            </span>

                                            @endif

                                        </td>


                                        {{-- =========================================
                                     AKSI
                                ========================================== --}}

                                        <td>

                                            @if($borrowing->status !== 'dikembalikan')

                                            <form
                                                action="{{ route(
                                                'circulation.return',
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
                                            Belum ada transaksi peminjaman.
                                        </td>

                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

</section>


{{-- =========================================================
     JAVASCRIPT
     SEARCH DAFTAR PEMINJAMAN
========================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {


        /*
        |--------------------------------------------------------------------------
        | SEARCH DAFTAR PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById('borrowingSearch');


        const rows =
            document.querySelectorAll(
                '#borrowingTable tbody tr.borrowing-row'
            );


        if (searchInput) {

            searchInput.addEventListener(
                'input',
                function() {

                    const keyword =
                        this.value
                        .toLowerCase()
                        .trim();


                    rows.forEach(function(row) {

                        const text =
                            row.textContent
                            .toLowerCase();


                        if (text.includes(keyword)) {

                            row.style.display = '';

                        } else {

                            row.style.display = 'none';

                        }

                    });

                }
            );

        }

    });
</script>

@endsection