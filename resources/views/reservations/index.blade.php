@extends('layouts.app')

@section('title', 'Reservasi Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reservations.css') }}">
    <!-- Flatpickr CSS untuk Kalender Rentang Tanggal Interaktif -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <style>
        .reservation-filter-bar {
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

        .date-range-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .date-range-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .date-range-icon {
            position: absolute;
            left: 15px;
            color: #64748b;
            font-size: 14px;
            pointer-events: none;
        }

        .date-range-input {
            margin-left: 8px;
            padding: 7px 12px 7px 32px;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #fff;
            color: #1e293b;
            min-width: 230px;
            cursor: pointer;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .date-range-input:focus {
            border-color: #287b7b;
            outline: 0;
            box-shadow: 0 0 0 2px rgba(40, 123, 123, 0.15);
        }

        .btn-filter-submit {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            background-color: #287b7b;
            border: 1px solid #287b7b;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-filter-submit:hover {
            background-color: #287b7b;
            border-color: #287b7b;
        }

        .btn-filter-reset {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            background-color: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-filter-reset:hover {
            background-color: #f1f5f9;
            color: #334155;
            border-color: #94a3b8;
        }

        .filter-badge-active {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            padding: 3px 8px;
            background-color: #e0f2fe;
            color: #0369a1;
            border-radius: 4px;
            margin-top: 4px;
            font-weight: 500;
        }

        .reservation-search-wrapper {
            flex-grow: 1;
            max-width: 250px;
            margin-right: 8px;
        }

        .reservation-search-wrapper input {
            width: 100%;
            
            padding: 7px 12px;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
    </style>
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
                 HEADER & FILTER BAR RENTANG TANGGAL
            ============================================== --}}

            <div class="reservation-list-top">

                <div>

                    <h3>
                        Daftar Reservasi
                    </h3>

                    <span class="reservation-list-subtitle">
                        Daftar buku yang telah direservasi.
                    </span>

                    @if(request('start_date') && request('end_date'))
                        <div>
                            <span class="filter-badge-active">
                                📅 Filter: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif

                </div>

            </div>

            {{-- =============================================
                 FILTER KALENDER RENTANG TANGGAL (DATE RANGE PICKER)
            ============================================== --}}
            <div class="reservation-filter-bar">

                <form
                    action="{{ route('reservations.index') }}"
                    method="GET"
                    class="date-range-form"
                    id="dateRangeFilterForm"
                >
                    {{-- Input Kalender Pemilih Rentang Tanggal Interaktif --}}
                    <div class="date-range-wrapper">
                        <span class="date-range-icon">📅</span>
                        <input
                            type="text"
                            id="dateRangePicker"
                            class="date-range-input"
                            placeholder="Pilih rentang tanggal..."
                            readonly
                        >
                        {{-- Hidden Input untuk dikirim ke Controller --}}
                        <input
                            type="hidden"
                            name="start_date"
                            id="startDateInput"
                            value="{{ request('start_date') }}"
                        >
                        <input
                            type="hidden"
                            name="end_date"
                            id="endDateInput"
                            value="{{ request('end_date') }}"
                        >
                    </div>

                    {{-- Tombol Filter --}}
                    <button type="submit" class="btn-filter-submit">
                        Terapkan
                    </button>

                    {{-- Tombol Reset Filter --}}
                    @if(request('start_date') || request('end_date'))
                        <a
                            href="{{ route('reservations.index') }}"
                            class="btn-filter-reset"
                            title="Reset Filter"
                        >
                            Reset
                        </a>
                    @endif
                </form>

                {{-- Live Search Input --}}
                <div class="reservation-search-wrapper">
                    <input
                        type="text"
                        id="reservationSearch"
                        placeholder="Cari di tabel..."
                    >
                </div>

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
                                    @if(request('start_date') && request('end_date'))
                                        Tidak ada data reservasi pada rentang tanggal {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}.
                                    @else
                                        Belum ada reservasi.
                                    @endif
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>

@endsection

@push('scripts')
<!-- Flatpickr JS & Bahasa Indonesia -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * =========================================================
     * INISIALISASI FLATPICKR MODE RANGE (KALENDER RENTANG BEBAS)
     * =========================================================
     */
    const startDateVal = "{{ request('start_date') }}";
    const endDateVal = "{{ request('end_date') }}";

    let defaultDateRange = [];
    if (startDateVal && endDateVal) {
        defaultDateRange = [startDateVal, endDateVal];
    } else if (startDateVal) {
        defaultDateRange = [startDateVal];
    }

    const picker = flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j M Y",
        locale: typeof flatpickr.l10ns.id !== "undefined" ? flatpickr.l10ns.id : "default",
        defaultDate: defaultDateRange,
        allowInput: false,
        onChange: function (selectedDates, dateStr, instance) {
            const startInput = document.getElementById('startDateInput');
            const endInput = document.getElementById('endDateInput');

            if (selectedDates.length === 2) {
                startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                endInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
            } else if (selectedDates.length === 1) {
                startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                endInput.value = "";
            } else {
                startInput.value = "";
                endInput.value = "";
            }
        }
    });

    /*
     * =========================================================
     * LIVE SEARCH FILTER PADA TABEL
     * =========================================================
     */
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
@endpush