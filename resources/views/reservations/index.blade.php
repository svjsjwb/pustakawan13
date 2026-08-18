@extends('layouts.app')

@section('title', 'Reservasi Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reservations.css') }}">
@endpush

@section('content')

<section id="page-reservations">

    {{-- =====================================================
         ALERT
    ====================================================== --}}

    @if(session('success'))
        <div class="reservation-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="reservation-alert error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="reservation-alert error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =====================================================
         LAYOUT
    ====================================================== --}}

    <div class="reservation-layout">

        {{-- =================================================
             FORM RESERVASI
        ================================================== --}}

        <div class="reservation-form-card">

            <h3>Reservasi Buku</h3>

            <p>
                Pilih anggota, buku, tanggal, serta tempat duduk
                yang tersedia.
            </p>


            {{-- =============================================
                 FORM
            ============================================== --}}

            <form
                action="{{ route('reservations.store') }}"
                method="POST"
                id="reservationForm"
            >

                @csrf


                {{-- =========================================
                     ANGGOTA
                ========================================== --}}

                <div class="reservation-field">

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
                                {{ old('member_id') == $member->id ? 'selected' : '' }}
                            >
                                {{ $member->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- =========================================
                     BUKU
                ========================================== --}}

                <div class="reservation-field">

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
                                {{ old('book_id') == $book->id ? 'selected' : '' }}
                                {{ $book->available_stock < 1 ? 'disabled' : '' }}
                            >

                                {{ $book->title }}

                                @if($book->available_stock < 1)
                                    — Stok Habis
                                @else
                                    — Stok {{ $book->available_stock }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- =========================================
                     TANGGAL
                ========================================== --}}

                <div class="reservation-date-row">

                    <div class="reservation-field">

                        <label for="reserved_at">
                            Tanggal Reservasi
                        </label>

                        <input
                            type="date"
                            name="reserved_at"
                            id="reserved_at"
                            value="{{ old('reserved_at', $selectedDate) }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                        >

                    </div>


                    <div class="reservation-field">

                        <label for="expires_at">
                            Tanggal Berakhir
                        </label>

                        <input
                            type="date"
                            name="expires_at"
                            id="expires_at"
                            value="{{ old('expires_at') }}"
                            min="{{ old('reserved_at', $selectedDate) }}"
                        >

                    </div>

                </div>


                {{-- =========================================
                     PILIH TEMPAT DUDUK
                ========================================== --}}

                <div class="seat-selection-section">

                    <div class="seat-selection-title">

                        <label>
                            Pilih Tempat Duduk
                        </label>

                        <span>
                            Pilih satu kursi yang masih tersedia.
                        </span>

                    </div>


                    <div class="seat-info-box">

                        <div class="seat-info-icon">
                            i
                        </div>

                        <div>
                            Kursi berwarna merah sudah dipesan.
                            Kursi hijau merupakan kursi yang dipilih.
                        </div>

                    </div>


                    {{-- =====================================
                         DENAH KURSI
                    ====================================== --}}

                    <div class="seat-map-wrapper">

                        <div class="seat-map-title">

                            Denah Tempat Duduk

                            <span>
                                — {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                            </span>

                        </div>


                        <div class="seat-map">


                            {{-- =================================
                                 MEJA A
                            ================================== --}}

                            <div class="seat-table-group">

                                <div class="table-name">
                                    MEJA A
                                </div>

                                <div class="table-seat-layout">

                                    <div class="seat-row">

                                        @for($i = 1; $i <= 4; $i++)

                                            @php
                                                $seat = 'A' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja A"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>


                                    <div class="table-middle">
                                        <span>MEJA A</span>
                                    </div>


                                    <div class="seat-row">

                                        @for($i = 5; $i <= 8; $i++)

                                            @php
                                                $seat = 'A' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja A"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>

                                </div>

                            </div>


                            {{-- =================================
                                 MEJA B
                            ================================== --}}

                            <div class="seat-table-group">

                                <div class="table-name">
                                    MEJA B
                                </div>

                                <div class="table-seat-layout">

                                    <div class="seat-row">

                                        @for($i = 1; $i <= 4; $i++)

                                            @php
                                                $seat = 'B' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja B"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>


                                    <div class="table-middle">
                                        <span>MEJA B</span>
                                    </div>


                                    <div class="seat-row">

                                        @for($i = 5; $i <= 8; $i++)

                                            @php
                                                $seat = 'B' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja B"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>

                                </div>

                            </div>


                            {{-- =================================
                                 MEJA C
                            ================================== --}}

                            <div class="seat-table-group">

                                <div class="table-name">
                                    MEJA C
                                </div>

                                <div class="table-seat-layout">

                                    <div class="seat-row">

                                        @for($i = 1; $i <= 4; $i++)

                                            @php
                                                $seat = 'C' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja C"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>


                                    <div class="table-middle">
                                        <span>MEJA C</span>
                                    </div>


                                    <div class="seat-row">

                                        @for($i = 5; $i <= 8; $i++)

                                            @php
                                                $seat = 'C' . $i;
                                                $isBooked = in_array($seat, $bookedSeats);
                                            @endphp

                                            <button
                                                type="button"
                                                class="seat {{ $isBooked ? 'booked' : '' }}"
                                                data-seat="{{ $seat }}"
                                                data-table="Meja C"
                                                {{ $isBooked ? 'disabled' : '' }}
                                            >
                                                <span>
                                                    {{ $seat }}
                                                </span>
                                            </button>

                                        @endfor

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =====================================
                             LEGEND
                        ====================================== --}}

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

                                Sudah Dipesan

                            </div>

                        </div>


                        {{-- =====================================
                             HASIL PILIHAN
                        ====================================== --}}

                        <div
                            class="seat-selected-result"
                            id="seatSelectedResult"
                            style="display: none;"
                        >

                            <div>

                                Meja:

                                <strong id="selectedTable">
                                    -
                                </strong>

                                &nbsp;&nbsp;|&nbsp;&nbsp;

                                Kursi:

                                <strong id="selectedSeat">
                                    -
                                </strong>

                            </div>


                            <button
                                type="button"
                                class="reset-seat-button"
                                id="resetSeatButton"
                            >
                                Batalkan Pilihan
                            </button>

                        </div>


                        {{-- =====================================
                             HIDDEN INPUT
                        ====================================== --}}

                        <input
                            type="hidden"
                            name="seat_number"
                            id="seat_number"
                            value="{{ old('seat_number') }}"
                            required
                        >

                    </div>

                </div>


                {{-- =========================================
                     SUBMIT
                ========================================== --}}

                <button
                    type="submit"
                    class="reservation-submit"
                >
                    Simpan Reservasi
                </button>

            </form>

        </div>


        {{-- =================================================
             DAFTAR RESERVASI
        ================================================== --}}

        <div class="reservation-list-card">


            {{-- =============================================
                 HEADER
            ============================================== --}}

            <div class="reservation-list-top">

                <div>

                    <h3>
                        Daftar Reservasi
                    </h3>

                    <span class="reservation-list-subtitle">
                        Daftar peminjaman dan tempat duduk yang telah dipesan.
                    </span>

                </div>


                <input
                    type="text"
                    id="reservationSearch"
                    placeholder="Cari reservasi..."
                >

            </div>


            {{-- =============================================
                 TABLE
            ============================================== --}}

            <div class="reservation-table-wrap">

                <table
                    class="reservation-table"
                    id="reservationTable"
                >

                    <thead>

                        <tr>

                            <th>
                                Anggota
                            </th>

                            <th>
                                Buku
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Tempat Duduk
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reservations as $reservation)

                            @php

                                $seat = $reservation->seat_number;

                                $tableName = null;

                                if ($seat) {

                                    if (str_starts_with($seat, 'A')) {
                                        $tableName = 'Meja A';
                                    } elseif (str_starts_with($seat, 'B')) {
                                        $tableName = 'Meja B';
                                    } elseif (str_starts_with($seat, 'C')) {
                                        $tableName = 'Meja C';
                                    }

                                }

                            @endphp


                            <tr>

                                {{-- ANGGOTA --}}

                                <td>

                                    {{ $reservation->member->name ?? '-' }}

                                </td>


                                {{-- BUKU --}}

                                <td>

                                    {{ $reservation->book->title ?? '-' }}

                                </td>


                                {{-- TANGGAL --}}

                                <td>

                                    {{ \Carbon\Carbon::parse($reservation->reserved_at)->format('d/m/Y') }}

                                </td>


                                {{-- TEMPAT DUDUK --}}

                                <td>

                                    @if($reservation->seat_number)

                                        <div class="reservation-seat-info">

                                            <span class="seat-table-badge">

                                                {{ $tableName ?? 'Meja' }}

                                            </span>


                                            <span class="seat-number-badge">

                                                Kursi {{ $reservation->seat_number }}

                                            </span>

                                        </div>

                                    @else

                                        <span class="no-seat">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}

                                <td>

                                    @if($reservation->status === 'menunggu')

                                        <span class="reservation-status waiting">
                                            Menunggu
                                        </span>

                                    @elseif($reservation->status === 'disetujui')

                                        <span class="reservation-status approved">
                                            Disetujui
                                        </span>

                                    @elseif($reservation->status === 'ditolak')

                                        <span class="reservation-status rejected">
                                            Ditolak
                                        </span>

                                    @elseif($reservation->status === 'dibatalkan')

                                        <span class="reservation-status cancelled">
                                            Dibatalkan
                                        </span>

                                    @elseif($reservation->status === 'selesai')

                                        <span class="reservation-status finished">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="reservation-status">
                                            {{ ucfirst($reservation->status) }}
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}

                                <td>

                                    <div class="reservation-actions">


                                        @if($reservation->status === 'menunggu')

                                            {{-- SETUJUI --}}

                                            <form
                                                action="{{ route('reservations.updateStatus', $reservation) }}"
                                                method="POST"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="disetujui"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-approve"
                                                >
                                                    Setujui
                                                </button>

                                            </form>


                                            {{-- TOLAK --}}

                                            <form
                                                action="{{ route('reservations.updateStatus', $reservation) }}"
                                                method="POST"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="ditolak"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-reject"
                                                >
                                                    Tolak
                                                </button>

                                            </form>

                                        @else

                                            <span class="action-done">
                                                Sudah diproses
                                            </span>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="reservation-empty"
                                >
                                    Belum ada reservasi.
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
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('circulationForm');
    const seatInput = document.getElementById('seat_number');

    const selectedResult =
        document.getElementById('seatSelectedResult');

    const selectedSeat =
        document.getElementById('selectedSeat');

    const selectedTable =
        document.getElementById('selectedTable');

    const resetSeatButton =
        document.getElementById('resetSeatButton');


    /*
    |--------------------------------------------------------------------------
    | PASTIKAN INPUT ADA
    |--------------------------------------------------------------------------
    */

    console.log('Seat input:', seatInput);


    /*
    |--------------------------------------------------------------------------
    | KLIK KURSI
    |--------------------------------------------------------------------------
    */

    const seats = document.querySelectorAll('.seat');

    console.log('Jumlah kursi:', seats.length);


    seats.forEach(function (seat) {

        seat.addEventListener('click', function () {

            if (seat.disabled) {
                return;
            }


            /*
            | Hapus pilihan sebelumnya
            */

            document
                .querySelectorAll('.seat.selected')
                .forEach(function (item) {

                    item.classList.remove('selected');

                });


            /*
            | Tandai kursi
            */

            seat.classList.add('selected');


            /*
            | Ambil data kursi
            */

            const seatNumber =
                seat.dataset.seat;

            const tableName =
                seat.dataset.table;


            /*
            | MASUKKAN KE INPUT
            */

            seatInput.value = seatNumber;


            /*
            | Tampilkan hasil
            */

            selectedSeat.textContent =
                seatNumber;

            selectedTable.textContent =
                tableName;

            selectedResult.style.display =
                'flex';


            /*
            | DEBUG
            */

            console.log(
                'Kursi dipilih:',
                seatNumber
            );

            console.log(
                'Nilai input seat_number:',
                seatInput.value
            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    if (resetSeatButton) {

        resetSeatButton.addEventListener(
            'click',
            function () {

                document
                    .querySelectorAll('.seat.selected')
                    .forEach(function (seat) {

                        seat.classList.remove('selected');

                    });


                seatInput.value = '';

                selectedSeat.textContent = '-';

                selectedTable.textContent = '-';

                selectedResult.style.display = 'none';

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (event) {

        console.log(
            '=============================='
        );

        console.log(
            'SEBELUM SUBMIT'
        );

        console.log(
            'seat_number =',
            seatInput.value
        );

        console.log(
            '=============================='
        );


        if (!seatInput.value) {

            event.preventDefault();

            alert(
                'Silakan pilih tempat duduk terlebih dahulu.'
            );

            return false;
        }

    });

});

</script>

@endsection