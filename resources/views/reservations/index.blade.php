@extends('layouts.app')

@section('title', 'Reservasi Buku')

@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/reservations.css') }}"
    >

    {{-- Flatpickr CSS --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css"
    >


    <style>
        @keyframes highlightPulse {
            0% { background-color: #ecfdf5; transform: scale(1.005); }
            50% { background-color: #d1fae5; }
            100% { background-color: transparent; transform: scale(1); }
        }
        .row-pulse {
            animation: highlightPulse 2.5s ease;
        }

        /* =====================================================
           FILTER BAR
        ====================================================== */

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


        /* =====================================================
           DATE RANGE FORM
        ====================================================== */

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

            z-index: 2;

        }


        .date-range-input {

            margin-left: 0;

            padding: 7px 12px 7px 38px;

            font-size: 12px;

            border: 1px solid #cbd5e1;

            border-radius: 6px;

            background-color: #fff;

            color: #1e293b;

            min-width: 230px;

            cursor: pointer;

            transition:
                border-color .15s ease-in-out,
                box-shadow .15s ease-in-out;

        }


        .date-range-input:focus {

            border-color: #287b7b;

            outline: 0;

            box-shadow:
                0 0 0 2px rgba(
                    40,
                    123,
                    123,
                    .15
                );

        }


        /* =====================================================
           FILTER BUTTON
        ====================================================== */

        .btn-filter-submit {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 4px;

            padding: 7px 14px;

            font-size: 12px;

            font-weight: 600;

            color: #fff;

            background-color: #287b7b;

            border: 1px solid #287b7b;

            border-radius: 6px;

            cursor: pointer;

            transition:
                background-color .15s ease,
                transform .15s ease;

        }


        .btn-filter-submit:hover {

            background-color: #216969;

            border-color: #216969;

            transform:
                translateY(-1px);

        }


        /* =====================================================
           RESET BUTTON
        ====================================================== */

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

            transition:
                all .15s ease;

        }


        .btn-filter-reset:hover {

            background-color: #f1f5f9;

            color: #334155;

            border-color: #94a3b8;

        }


        /* =====================================================
           ACTIVE FILTER BADGE
        ====================================================== */

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


        /* =====================================================
           SEARCH
        ====================================================== */

        .reservation-search-wrapper {

            flex-grow: 1;

            max-width: 250px;

            margin-right: 8px;

        }


        .reservation-search-wrapper input {

            width: 100%;

            box-sizing: border-box;

            padding: 7px 12px;

            font-size: 12px;

            border: 1px solid #cbd5e1;

            border-radius: 6px;

            outline: none;

            transition:
                border-color .15s ease,
                box-shadow .15s ease;

        }


        .reservation-search-wrapper input:focus {

            border-color: #287b7b;

            box-shadow:
                0 0 0 2px rgba(
                    40,
                    123,
                    123,
                    .10
                );

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 700px) {

            .reservation-filter-bar {

                align-items: stretch;

                flex-direction: column;

            }


            .date-range-form {

                width: 100%;

            }


            .date-range-wrapper {

                width: 100%;

            }


            .date-range-input {

                width: 100%;

                min-width: 0;

                box-sizing: border-box;

            }


            .reservation-search-wrapper {

                width: 100%;

                max-width: none;

                margin-right: 0;

            }


            .reservation-search-wrapper input {

                width: 100%;

            }

        }

    </style>

@endpush


@section('content')

<section id="page-reservations">


    {{-- =====================================================
         ALERT SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="reservation-alert success">

            {{ session('success') }}

        </div>

    @endif


    {{-- =====================================================
         ALERT ERROR
    ====================================================== --}}

    @if(session('error'))

        <div class="reservation-alert error">

            {{ session('error') }}

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="reservation-alert error">

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
         MAIN LAYOUT
    ====================================================== --}}

    <div class="reservation-layout">


        {{-- =================================================
             FORM RESERVASI
        ================================================== --}}

        <div class="reservation-form-card">

            <h3>
                Reservasi Buku
            </h3>

            <p>
                Pilih anggota, buku, dan tanggal reservasi.
            </p>


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
                                @selected(
                                    old('member_id') == $member->id
                                )
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
                                @selected(
                                    old('book_id') == $book->id
                                )
                                @disabled(
                                    $book->available_stock < 1
                                )
                            >

                                {{ $book->title }}

                                @if($book->available_stock < 1)

                                    — Stok Habis

                                @else

                                    — Stok
                                    {{ $book->available_stock }}

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
                            value="{{ old(
                                'reserved_at',
                                $selectedDate
                            ) }}"
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
                            min="{{ old(
                                'reserved_at',
                                $selectedDate
                            ) }}"
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


                    @if(
                        request('start_date')
                        &&
                        request('end_date')
                    )

                        <div>

                            <span class="filter-badge-active">

                                📅 Filter:

                                {{ \Carbon\Carbon::parse(
                                    request('start_date')
                                )->format('d/m/Y') }}

                                -

                                {{ \Carbon\Carbon::parse(
                                    request('end_date')
                                )->format('d/m/Y') }}

                            </span>

                        </div>

                    @endif

                </div>

            </div>


            {{-- =============================================
                 FILTER BAR
            ============================================== --}}

            <div class="reservation-filter-bar">


                {{-- =============================================
                     DATE RANGE
                ============================================== --}}

                <form
                    action="{{ route('reservations.index') }}"
                    method="GET"
                    class="date-range-form"
                    id="dateRangeFilterForm"
                >


                    <div class="date-range-wrapper">

                        <span class="date-range-icon">
                            📅
                        </span>


                        <input
                            type="text"
                            id="dateRangePicker"
                            class="date-range-input"
                            placeholder="Pilih rentang tanggal..."
                            readonly
                        >


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


                    {{-- APPLY --}}

                    <button
                        type="submit"
                        class="btn-filter-submit"
                    >

                        Terapkan

                    </button>


                    {{-- RESET --}}

                    @if(
                        request('start_date')
                        ||
                        request('end_date')
                    )

                        <a
                            href="{{ route('reservations.index') }}"
                            class="btn-filter-reset"
                            title="Reset Filter"
                        >

                            Reset

                        </a>

                    @endif

                </form>


                {{-- =============================================
                     LIVE SEARCH
                ============================================== --}}

                <div class="reservation-search-wrapper">

                    <input
                        type="text"
                        id="reservationSearch"
                        placeholder="Cari di tabel..."
                        autocomplete="off"
                    >

                </div>

            </div>


            {{-- =================================================
                 TABLE
            ================================================== --}}

            <div class="reservation-table-wrap">

                <table
                    class="reservation-table"
                    id="reservationTable"
                >

                    <thead>

                        <tr>

                            <th>
                                ANGGOTA
                            </th>

                            <th>
                                BUKU
                            </th>

                            <th>
                                RESERVASI
                            </th>

                            <th>
                                BERLAKU SAMPAI
                            </th>

                            <th>
                                STATUS
                            </th>

                            <th>
                                AKSI
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @forelse(
                            $reservations
                            as $reservation
                        )

                            <tr class="reservation-row" data-reservation-id="{{ $reservation->id }}">


                                {{-- =========================
                                     ANGGOTA
                                ========================== --}}

                                <td>

                                    <strong>{{ $reservation->member->name ?? $reservation->user->name ?? '-' }}</strong>
                                    @if($reservation->user_id && !$reservation->member_id)
                                        <div style="font-size: 11px; color: #0f766e; display: inline-flex; align-items: center; gap: 4px; margin-top: 3px; background: #f0fdfa; padding: 2px 6px; border-radius: 4px; border: 1px solid #ccfbf1;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                            Online (User)
                                        </div>
                                    @endif

                                </td>


                                {{-- =========================
                                     BUKU
                                ========================== --}}

                                <td>

                                    {{ $reservation->book->title ?? $reservation->book->judul_buku ?? '-' }}

                                </td>


                                {{-- =========================
                                     RESERVASI
                                ========================== --}}

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $reservation->reserved_at
                                    )->format('d/m/Y') }}

                                </td>


                                {{-- =========================
                                     BERLAKU SAMPAI
                                ========================== --}}

                                <td>

                                    @if(
                                        $reservation->expires_at
                                    )

                                        {{ \Carbon\Carbon::parse(
                                            $reservation->expires_at
                                        )->format('d/m/Y') }}

                                    @else

                                        <span style="color: #64748b; font-size: 12px;">Menunggu Approval</span>

                                    @endif

                                </td>


                                {{-- =========================
                                     STATUS
                                ========================== --}}

                                <td>

                                    @if(
                                        $reservation->status ===
                                        'menunggu'
                                    )

                                        <span
                                            class="reservation-status waiting"
                                        >
                                            Menunggu
                                        </span>


                                    @elseif(
                                        $reservation->status ===
                                        'disetujui'
                                    )

                                        <span
                                            class="reservation-status approved"
                                        >
                                            Disetujui
                                        </span>


                                    @elseif(
                                        $reservation->status ===
                                        'ditolak'
                                    )

                                        <span
                                            class="reservation-status rejected"
                                        >
                                            Ditolak
                                        </span>


                                    @elseif(
                                        $reservation->status ===
                                        'dibatalkan'
                                    )

                                        <span
                                            class="reservation-status cancelled"
                                        >
                                            Dibatalkan
                                        </span>


                                    @elseif(
                                        $reservation->status ===
                                        'selesai'
                                    )

                                        <span
                                            class="reservation-status finished"
                                        >
                                            Selesai
                                        </span>


                                    @else

                                        <span
                                            class="reservation-status"
                                        >

                                            {{ ucfirst(
                                                $reservation->status
                                            ) }}

                                        </span>

                                    @endif

                                </td>


                                {{-- =========================
                                     AKSI
                                ========================== --}}

                                <td>

                                    <div class="reservation-actions">


                                        {{-- =================================
                                             SETUJUI
                                        ================================== --}}

                                        @if(
                                            $reservation->status ===
                                            'menunggu'
                                        )

                                            <form
                                                action="{{ route(
                                                    'reservations.updateStatus',
                                                    $reservation
                                                ) }}"
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

                                        @endif


                                        {{-- =================================
                                             LOCATOR
                                        ================================== --}}

                                        @if(
                                            $reservation->book_copy_id
                                            &&
                                            $reservation->bookCopy?->shelf_id
                                            &&
                                            in_array(
                                                $reservation->status,
                                                [
                                                    'menunggu',
                                                    'disetujui'
                                                ]
                                            )
                                        )

                                            <a
                                                href="{{ route(
                                                    'reservations.locator',
                                                    $reservation
                                                ) }}"
                                                class="btn btn-primary"
                                            >

                                                📍 Temukan Buku

                                            </a>

                                        @endif


                                        {{-- =================================
                                             TOLAK
                                        ================================== --}}

                                        @if(
                                            $reservation->status ===
                                            'menunggu'
                                        )

                                            <form
                                                action="{{ route(
                                                    'reservations.updateStatus',
                                                    $reservation
                                                ) }}"
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

                                        @endif


                                        {{-- =================================
                                             EMPTY ACTION
                                        ================================== --}}

                                        @if(
                                            $reservation->status !==
                                            'menunggu'
                                            &&
                                            !(
                                                $reservation->book_copy_id
                                                &&
                                                $reservation->bookCopy?->shelf_id
                                                &&
                                                in_array(
                                                    $reservation->status,
                                                    [
                                                        'menunggu',
                                                        'disetujui'
                                                    ]
                                                )
                                            )
                                        )

                                            <span class="action-done">
                                                —
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

                                    @if(
                                        request('start_date')
                                        &&
                                        request('end_date')
                                    )

                                        Tidak ada data reservasi pada
                                        rentang tanggal

                                        {{
                                            \Carbon\Carbon::parse(
                                                request('start_date')
                                            )->format('d/m/Y')
                                        }}

                                        s/d

                                        {{
                                            \Carbon\Carbon::parse(
                                                request('end_date')
                                            )->format('d/m/Y')
                                        }}.

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

    {{-- =====================================================
         FLATPICKR
    ====================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =================================================
                   FLATPICKR RANGE
                ================================================= */

                const startDateVal =
                    @json(request('start_date'));

                const endDateVal =
                    @json(request('end_date'));


                let defaultDateRange = [];


                if (
                    startDateVal
                    &&
                    endDateVal
                ) {

                    defaultDateRange = [
                        startDateVal,
                        endDateVal
                    ];

                } else if (
                    startDateVal
                ) {

                    defaultDateRange = [
                        startDateVal
                    ];

                }


                const datePicker =
                    document.getElementById(
                        'dateRangePicker'
                    );


                if (datePicker) {

                    flatpickr(
                        datePicker,
                        {

                            mode: 'range',

                            dateFormat: 'Y-m-d',

                            altInput: true,

                            altFormat: 'j M Y',

                            locale:
                                typeof flatpickr.l10ns.id !==
                                'undefined'
                                    ? flatpickr.l10ns.id
                                    : 'default',

                            defaultDate:
                                defaultDateRange,

                            allowInput: false,


                            onChange:
                                function (
                                    selectedDates,
                                    dateStr,
                                    instance
                                ) {

                                    const startInput =
                                        document.getElementById(
                                            'startDateInput'
                                        );


                                    const endInput =
                                        document.getElementById(
                                            'endDateInput'
                                        );


                                    if (
                                        selectedDates.length === 2
                                    ) {

                                        startInput.value =
                                            instance.formatDate(
                                                selectedDates[0],
                                                'Y-m-d'
                                            );


                                        endInput.value =
                                            instance.formatDate(
                                                selectedDates[1],
                                                'Y-m-d'
                                            );

                                    } else if (
                                        selectedDates.length === 1
                                    ) {

                                        startInput.value =
                                            instance.formatDate(
                                                selectedDates[0],
                                                'Y-m-d'
                                            );


                                        endInput.value = '';

                                    } else {

                                        startInput.value = '';

                                        endInput.value = '';

                                    }

                                }

                        }
                    );

                }


                /* =================================================
                   LIVE SEARCH
                ================================================= */

                const searchInput =
                    document.getElementById(
                        'reservationSearch'
                    );


                const rows =
                    document.querySelectorAll(
                        '#reservationTable tbody tr.reservation-row'
                    );


                if (!searchInput) {

                    return;

                }


                searchInput.addEventListener(
                    'input',
                    function () {

                        const keyword =
                            this.value
                                .toLowerCase()
                                .trim();


                        rows.forEach(
                            function (row) {

                                const text =
                                    row.textContent
                                        .toLowerCase();


                                row.style.display =
                                    text.includes(keyword)
                                        ? ''
                                        : 'none';

                            }
                        );

                    }
                );

                /* =================================================
                   REAL-TIME SYNCHRONIZATION (AC-1)
                ================================================= */
                if (window.PustakawanRealtime) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const tbody = document.querySelector('#reservationTable tbody');

                    function escapeHtml(str) {
                        if (!str) return '';
                        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                    }

                    // 1. Reservasi Baru Masuk Live
                    PustakawanRealtime.on('reservation.created', function (data) {
                        if (!tbody) return;

                        // Cegah duplikasi baris
                        if (tbody.querySelector(`tr[data-reservation-id="${data.id}"]`)) return;

                        // Hilangkan pesan kosong jika ada
                        const emptyRow = tbody.querySelector('.reservation-empty');
                        if (emptyRow) emptyRow.remove();

                        const tr = document.createElement('tr');
                        tr.className = 'reservation-row row-pulse';
                        tr.setAttribute('data-reservation-id', data.id);

                        const onlineBadge = data.is_online_user
                            ? `<div style="font-size: 11px; color: #0f766e; display: inline-flex; align-items: center; gap: 4px; margin-top: 3px; background: #f0fdfa; padding: 2px 6px; border-radius: 4px; border: 1px solid #ccfbf1;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>Online (User)</div>`
                            : '';

                        const expiresText = data.expires_at ? escapeHtml(data.expires_at) : '<span style="color: #64748b; font-size: 12px;">Menunggu Approval</span>';

                        tr.innerHTML = `
                            <td>
                                <strong>${escapeHtml(data.member_name)}</strong>
                                ${onlineBadge}
                            </td>
                            <td>${escapeHtml(data.book_title)}</td>
                            <td>${escapeHtml(data.reserved_at_formatted || data.reserved_at)}</td>
                            <td>${expiresText}</td>
                            <td>
                                <span class="reservation-status waiting">Menunggu</span>
                            </td>
                            <td>
                                <div class="reservation-actions">
                                    <form action="/reservations/${data.id}/status" method="POST">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <input type="hidden" name="status" value="disetujui">
                                        <button type="submit" class="btn-approve">Setujui</button>
                                    </form>
                                    <form action="/reservations/${data.id}/status" method="POST">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn-reject" onclick="return confirm('Tolak reservasi ini?')">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        `;

                        tbody.prepend(tr);

                        // Notifikasi toast live
                        PustakawanRealtime.toast(
                            `Buku "${escapeHtml(data.book_title)}" oleh ${escapeHtml(data.member_name)}`,
                            'info',
                            '🔔 Reservasi Baru Masuk'
                        );
                    });

                    // 2. Status Reservasi Disetujui / Diperbarui Live
                    PustakawanRealtime.on('reservation.approved', function (data) {
                        const row = tbody ? tbody.querySelector(`tr[data-reservation-id="${data.id}"]`) : null;
                        if (row) {
                            const statusTd = row.children[4];
                            if (statusTd) {
                                statusTd.innerHTML = '<span class="reservation-status approved">Disetujui</span>';
                            }
                            const actionsDiv = row.querySelector('.reservation-actions');
                            if (actionsDiv) {
                                const approveForm = actionsDiv.querySelector('button.btn-approve')?.closest('form');
                                if (approveForm) approveForm.remove();
                            }
                            row.classList.add('row-pulse');
                        }
                    });

                    PustakawanRealtime.on('reservation.updated', function (data) {
                        const row = tbody ? tbody.querySelector(`tr[data-reservation-id="${data.id}"]`) : null;
                        if (row) {
                            const statusTd = row.children[4];
                            if (statusTd) {
                                let badgeClass = 'waiting';
                                if (data.status === 'ditolak') badgeClass = 'rejected';
                                else if (data.status === 'dibatalkan') badgeClass = 'cancelled';
                                else if (data.status === 'selesai') badgeClass = 'finished';
                                statusTd.innerHTML = `<span class="reservation-status ${badgeClass}">${escapeHtml(data.status_label || data.status)}</span>`;
                            }
                            row.classList.add('row-pulse');
                        }
                    });
                }

            }
        );

    </script>
@endpush