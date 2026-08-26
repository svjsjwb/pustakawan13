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
                Pilih anggota, buku, dan tanggal reservasi.
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
                        Daftar buku yang telah direservasi.
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
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reservations as $reservation)

                            <tr class="reservation-row">

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
                                    colspan="5"
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

    const searchInput = document.getElementById('reservationSearch');
    const rows = document.querySelectorAll('#reservationTable tbody tr.reservation-row');

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