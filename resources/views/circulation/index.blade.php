@extends('layouts.app')

@section('title', 'Sirkulasi Peminjaman Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/circulation.css') }}">
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
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .borrowing-search-wrap input:focus {
            border-color: #287b7b;
            box-shadow: 0 0 0 2px rgba(40, 123, 123, 0.15);
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
    </style>
@endpush

@section('content')

<section class="page" id="page-circulation">

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
                    id="circulationForm"
                >

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
                            required
                        >

                            <option value="">
                                -- Pilih Anggota --
                            </option>

                            @foreach($members as $member)

                                <option
                                    value="{{ $member->id }}"
                                    @selected(old('member_id') == $member->id)
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
                            required
                        >

                            <option value="">
                                -- Pilih Buku --
                            </option>

                            @foreach($books as $book)

                                <option
                                    value="{{ $book->id }}"
                                    @selected(old('book_id') == $book->id)
                                    @disabled($book->available_stock <= 0)
                                >

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
                                required
                            >

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
                                required
                            >

                        </div>

                    </div>


                    {{-- =================================================
                         SUBMIT
                    ================================================== --}}

                    <button
                        type="submit"
                        class="loan-submit"
                    >
                        Proses Peminjaman
                    </button>

                </form>

            </div>

        </div>


        {{-- =================================================
             KANAN - DAFTAR PEMINJAMAN (ARSIP BULANAN)
        ================================================== --}}

        <div class="card borrowing-card">

            <div class="card-pad">

                {{-- =================================================
                     HEADER DAFTAR
                ================================================== --}}

                <div class="borrowing-header">

                    <div>

                        <h3>
                            Daftar Peminjaman
                        </h3>

                        <p class="loan-description" style="margin-bottom: 0;">
                            Laporan transaksi peminjaman buku periode bulanan.
                        </p>

                    </div>

                </div>


                {{-- =================================================
                     NAVIGASI BULAN OTOMATIS (< Bulan Ini >) & SEARCH
                ================================================== --}}

                <div class="circulation-filter-bar">

                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">

                        {{-- Komponen Navigasi Bulan --}}
                        <div class="month-navigator">

                            {{-- Tombol Bulan Sebelumnya (<) --}}
                            <a
                                href="{{ route('circulation', ['month' => $prevMonth, 'year' => $prevYear]) }}"
                                class="btn-nav-month"
                                title="Bulan Sebelumnya"
                            >
                                &#8249;
                            </a>

                            {{-- Indikator Nama Bulan & Tahun Aktif --}}
                            <span class="month-display-label">
                                {{ $monthLabel }}
                            </span>

                            {{-- Tombol Bulan Berikutnya (>) --}}
                            <a
                                href="{{ route('circulation', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                                class="btn-nav-month"
                                title="Bulan Berikutnya"
                            >
                                &#8250;
                            </a>

                        </div>

                        {{-- Tombol Shortcut "Bulan Ini" jika sedang melihat bulan lampau/depan --}}
                        @if(!$isCurrentMonth)
                            <a
                                href="{{ route('circulation') }}"
                                class="btn-today-badge"
                                title="Kembali ke Bulan Berjalan"
                            >
                                Bulan Ini
                            </a>
                        @endif

                    </div>


                    {{-- Kolom Pencarian Cepat di Tabel --}}
                    <div class="borrowing-search-wrap">

                        <input
                            type="text"
                            id="borrowingSearch"
                            placeholder="Cari di tabel..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                {{-- =================================================
                     TABLE PEMINJAMAN
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
                                            @if(!$loop->last)
                                                <br>
                                            @endif
                                        @empty
                                            -
                                        @endforelse
                                    </td>


                                    {{-- TANGGAL PINJAM --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d/m/Y') }}
                                    </td>


                                    {{-- TANGGAL PENGEMBALIAN --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($borrowing->due_at)->format('d/m/Y') }}
                                    </td>


                                    {{-- STATUS --}}
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


                                    {{-- AKSI --}}
                                    <td>
                                        @if($borrowing->status !== 'dikembalikan')
                                            <form
                                                action="{{ route('circulation.return', $borrowing) }}"
                                                method="POST"
                                                class="return-form"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn-secondary"
                                                >
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
                                        class="empty-state"
                                    >
                                        Belum ada transaksi peminjaman pada periode {{ $monthLabel }}.
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
     JAVASCRIPT LIVE SEARCH
========================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('borrowingSearch');
    const rows = document.querySelectorAll('#borrowingTable tbody tr.borrowing-row');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

});
</script>

@endsection