@extends('layouts.app')

@section('title', 'Sirkulasi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/circulation.css') }}">
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
                    Pilih anggota, buku, tanggal, serta tempat duduk yang tersedia.
                </p>


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
                                Jatuh Tempo
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
                         PILIH TEMPAT DUDUK
                    ================================================== --}}

                    <div class="seat-selection-section">

                        <div class="seat-selection-title">

                            <label>
                                Pilih Tempat Duduk
                            </label>

                            <span>
                                Pilih satu kursi yang masih tersedia.
                            </span>

                        </div>


                        {{-- INFO BOX --}}

                        <div class="seat-info-box">

                            <div class="seat-info-icon">
                                i
                            </div>

                            <div>
                                Kursi berwarna merah sudah digunakan.
                                Kursi hijau merupakan kursi yang dipilih.
                            </div>

                        </div>


                        {{-- =================================================
                             DENAH TEMPAT DUDUK
                        ================================================== --}}

                        <div class="seat-map-wrapper">

                            <div class="seat-map-title">
                                Denah Tempat Duduk
                            </div>


                            <div class="seat-map">


                                {{-- =========================================
                                     MEJA A
                                ========================================== --}}

                                <div class="seat-table-group">

                                    <div class="table-name">
                                        MEJA A
                                    </div>

                                    <div class="table-seat-layout">


                                        {{-- A1 - A4 --}}

                                        <div class="seat-row">

                                            @for($i = 1; $i <= 4; $i++)

                                                @php
                                                    $seat = 'A' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja A"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>


                                        {{-- MEJA A --}}

                                        <div class="table-middle">
                                            MEJA A
                                        </div>


                                        {{-- A5 - A8 --}}

                                        <div class="seat-row">

                                            @for($i = 5; $i <= 8; $i++)

                                                @php
                                                    $seat = 'A' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja A"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>

                                    </div>

                                </div>


                                {{-- =========================================
                                     MEJA B
                                ========================================== --}}

                                <div class="seat-table-group">

                                    <div class="table-name">
                                        MEJA B
                                    </div>

                                    <div class="table-seat-layout">


                                        {{-- B1 - B4 --}}

                                        <div class="seat-row">

                                            @for($i = 1; $i <= 4; $i++)

                                                @php
                                                    $seat = 'B' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja B"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>


                                        {{-- MEJA B --}}

                                        <div class="table-middle">
                                            MEJA B
                                        </div>


                                        {{-- B5 - B8 --}}

                                        <div class="seat-row">

                                            @for($i = 5; $i <= 8; $i++)

                                                @php
                                                    $seat = 'B' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja B"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>

                                    </div>

                                </div>


                                {{-- =========================================
                                     MEJA C
                                ========================================== --}}

                                <div class="seat-table-group">

                                    <div class="table-name">
                                        MEJA C
                                    </div>

                                    <div class="table-seat-layout">


                                        {{-- C1 - C4 --}}

                                        <div class="seat-row">

                                            @for($i = 1; $i <= 4; $i++)

                                                @php
                                                    $seat = 'C' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja C"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>


                                        {{-- MEJA C --}}

                                        <div class="table-middle">
                                            MEJA C
                                        </div>


                                        {{-- C5 - C8 --}}

                                        <div class="seat-row">

                                            @for($i = 5; $i <= 8; $i++)

                                                @php
                                                    $seat = 'C' . $i;

                                                    $isBooked = in_array(
                                                        $seat,
                                                        $bookedSeats ?? []
                                                    );
                                                @endphp

                                                <button
                                                    type="button"
                                                    class="seat {{ $isBooked ? 'booked' : '' }}"
                                                    data-seat="{{ $seat }}"
                                                    data-table="Meja C"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>

                                            @endfor

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                 LEGEND
                            ================================================== --}}

                            <div class="seat-legend">

                                <div class="seat-legend-item">

                                    <span class="legend-seat available"></span>

                                    Tersedia

                                </div>


                                <div class="seat-legend-item">

                                    <span class="legend-seat selected"></span>

                                    Dipilih

                                </div>


                                <div class="seat-legend-item">

                                    <span class="legend-seat booked"></span>

                                    Sudah Digunakan

                                </div>

                            </div>


                            {{-- =================================================
                                 HASIL PILIHAN
                            ================================================== --}}

                            <div
                                class="seat-selected-result"
                                id="seatSelectedResult"
                                style="display: none;"
                            >

                                <div>

                                    Meja:
                                    <strong id="selectedTable">-</strong>

                                    <span class="separator">|</span>

                                    Kursi:
                                    <strong id="selectedSeat">-</strong>

                                </div>


                                <button
                                    type="button"
                                    class="reset-seat-button"
                                    id="resetSeatButton"
                                >
                                    Batalkan Pilihan
                                </button>

                            </div>


                            {{-- =================================================
                                 INPUT YANG DIKIRIM KE CONTROLLER
                            ================================================== --}}

                            <input
                                type="hidden"
                                name="seat_number"
                                id="seat_number"
                                value="{{ old('seat_number') }}"
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
                        autocomplete="off"
                    >

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

                            <th>JATUH TEMPO</th>

                            <th>TEMPAT DUDUK</th>

                            <th>STATUS</th>

                            <th>AKSI</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($borrowings as $borrowing)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | STATUS
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


                                /*
                                |--------------------------------------------------------------------------
                                | SEAT
                                |--------------------------------------------------------------------------
                                */

                                $seatNumber = $borrowing->seat_number ?? null;


                                /*
                                |--------------------------------------------------------------------------
                                | TABLE NAME
                                |--------------------------------------------------------------------------
                                */

                                $tableName = match (true) {

                                    $seatNumber
                                    && str_starts_with(
                                        strtoupper($seatNumber),
                                        'A'
                                    )
                                        => 'Meja A',

                                    $seatNumber
                                    && str_starts_with(
                                        strtoupper($seatNumber),
                                        'B'
                                    )
                                        => 'Meja B',

                                    $seatNumber
                                    && str_starts_with(
                                        strtoupper($seatNumber),
                                        'C'
                                    )
                                        => 'Meja C',

                                    default => null,

                                };

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
                                     JATUH TEMPO
                                ========================================== --}}

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $borrowing->due_at
                                    )->format('d/m/Y') }}

                                </td>


                                {{-- =========================================
                                     TEMPAT DUDUK
                                     SAMA SEPERTI RESERVASI
                                ========================================== --}}

                                <td>

                                    @if($seatNumber)

                                        <div class="borrowing-seat">

                                            <span class="seat-table-badge">
                                                {{ $tableName ?? '-' }}
                                            </span>

                                            <span class="seat-number-badge">
                                                Kursi {{ $seatNumber }}
                                            </span>

                                        </div>

                                    @else

                                        <span class="no-seat">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- =========================================
                                     STATUS
                                ========================================== --}}

                                <td>

                                    @if($displayStatus === 'dipinjam')

                                        <span class="status-badge aktif">
                                            ● Aktif
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
                                    colspan="7"
                                    class="empty-state"
                                >
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
     TEMPAT DUDUK + SEARCH
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById('circulationForm');

    const seatInput =
        document.getElementById('seat_number');

    const selectedResult =
        document.getElementById('seatSelectedResult');

    const selectedSeat =
        document.getElementById('selectedSeat');

    const selectedTable =
        document.getElementById('selectedTable');

    const resetSeatButton =
        document.getElementById('resetSeatButton');

    const seats =
        document.querySelectorAll(
            '#page-circulation .seat:not(:disabled)'
        );


    /*
    |--------------------------------------------------------------------------
    | FUNGSI MENAMPILKAN KURSI
    |--------------------------------------------------------------------------
    */

    function showSelectedSeat(seatElement) {

        if (!seatElement) {
            return;
        }

        const seatNumber =
            seatElement.dataset.seat;

        const tableName =
            seatElement.dataset.table;


        /*
        | Hapus selected sebelumnya
        */

        document
            .querySelectorAll('#page-circulation .seat.selected')
            .forEach(function (oldSeat) {

                oldSeat.classList.remove('selected');

            });


        /*
        | Tandai kursi
        */

        seatElement.classList.add('selected');


        /*
        | Simpan nilai seat ke input
        */

        seatInput.value = seatNumber;


        /*
        | Tampilkan informasi
        */

        selectedSeat.textContent =
            seatNumber;

        selectedTable.textContent =
            tableName;


        selectedResult.style.display =
            'flex';

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK KURSI
    |--------------------------------------------------------------------------
    */

    seats.forEach(function (seat) {

        seat.addEventListener('click', function () {

            showSelectedSeat(seat);

        });

    });


    /*
    |--------------------------------------------------------------------------
    | RESET KURSI
    |--------------------------------------------------------------------------
    */

    if (resetSeatButton) {

        resetSeatButton.addEventListener(
            'click',
            function () {

                document
                    .querySelectorAll(
                        '#page-circulation .seat.selected'
                    )
                    .forEach(function (seat) {

                        seat.classList.remove(
                            'selected'
                        );

                    });


                seatInput.value = '';


                selectedSeat.textContent =
                    '-';

                selectedTable.textContent =
                    '-';


                selectedResult.style.display =
                    'none';

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI FORM
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                if (!seatInput.value) {

                    event.preventDefault();

                    alert(
                        'Silakan pilih tempat duduk terlebih dahulu.'
                    );

                    return false;

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE OLD INPUT
    |--------------------------------------------------------------------------
    */

    const oldSeat =
        seatInput.value;


    if (oldSeat) {

        const oldSeatElement =
            document.querySelector(
                '#page-circulation .seat[data-seat="' +
                oldSeat +
                '"]'
            );


        if (
            oldSeatElement &&
            !oldSeatElement.disabled
        ) {

            showSelectedSeat(
                oldSeatElement
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH DAFTAR PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById(
            'borrowingSearch'
        );

    const rows =
        document.querySelectorAll(
            '#borrowingTable tbody tr.borrowing-row'
        );


    if (searchInput) {

        searchInput.addEventListener(
            'input',
            function () {

                const keyword =
                    this.value
                        .toLowerCase()
                        .trim();


                rows.forEach(function (row) {

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

                });

            }
        );

    }

});

</script>

@endsection