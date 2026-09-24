@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@push('styles')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <link rel="stylesheet" href="{{ asset('css/report.css') }}">

    <style>

        /* =========================================================

                   FILTER BAR

                ========================================================== */

        .report-filter-box {

            background: #ffffff;

            border-radius: 12px;

            border: 1px solid #d8e5e5;

            padding: 18px 24px;

            margin-bottom: 24px;

            box-shadow: 0 4px 16px rgba(40, 123, 123, 0.05);

            display: flex;

            flex-wrap: wrap;

            align-items: center;

            justify-content: space-between;

            gap: 16px;

        }









        .filter-form-group {

            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 12px;

            flex: 1;

        }









        /* =========================================================

                   DATE PICKER

                ========================================================== */

        .date-picker-input-wrapper {

            position: relative;

            display: inline-flex;

            align-items: center;

            min-width: 290px;

        }









        .date-picker-input-wrapper .calendar-icon {

            position: absolute;

            left: 14px;

            font-size: 16px;

            color: #287b7b;

            pointer-events: none;

            z-index: 2;

        }









        .date-range-flatpickr {

            width: 100%;

            height: 42px;

            padding: 0 16px 0 42px;

            font-size: 13px;

            font-weight: 600;

            color: #172f2f;

            background: #f7fbfb !important;

            border: 1.5px solid #c9dede;

            border-radius: 8px;

            cursor: pointer;

            transition: all 0.2s ease;

        }









        .date-range-flatpickr:hover,

        .date-range-flatpickr:focus {

            border-color: #287b7b;

            background: #ffffff !important;

            box-shadow:

                0 0 0 3px rgba(40, 123, 123, 0.12);

            outline: none;

        }









        /* =========================================================

                   BUTTON

                ========================================================== */

        .btn-filter-apply {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            height: 42px;

            padding: 0 20px;

            background: #287b7b;

            color: #ffffff;

            font-size: 13px;

            font-weight: 700;

            border: none;

            border-radius: 8px;

            cursor: pointer;

            transition: all 0.2s ease;

            text-decoration: none;

            box-shadow:

                0 4px 12px rgba(40, 123, 123, 0.18);

        }









        .btn-filter-apply:hover {

            background: #206363;

            transform: translateY(-1px);

            box-shadow:

                0 6px 16px rgba(40, 123, 123, 0.25);

            color: #ffffff;

        }









        .btn-filter-reset {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            height: 42px;

            padding: 0 16px;

            background: #f1f6f6;

            color: #486161;

            font-size: 13px;

            font-weight: 600;

            border: 1px solid #d0e0e0;

            border-radius: 8px;

            cursor: pointer;

            transition: all 0.2s ease;

            text-decoration: none;

        }









        .btn-filter-reset:hover {

            background: #e3eded;

            color: #172f2f;

            border-color: #bad3d3;

        }









        /* =========================================================

                   PERIOD

                ========================================================== */

        .filter-period-badge {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            background: #eef7f7;

            border: 1px solid #cce5e5;

            color: #206363;

            font-size: 12px;

            font-weight: 700;

            padding: 6px 14px;

            border-radius: 20px;

        }









        /* =========================================================

                   FLATPICKR

                ========================================================== */

        .flatpickr-calendar {

            border-radius: 12px !important;

            box-shadow:

                0 10px 30px rgba(40, 123, 123, 0.18) !important;

            border: 1px solid #d5e6e6 !important;

            font-family: inherit !important;

        }









        .flatpickr-day.selected,

        .flatpickr-day.startRange,

        .flatpickr-day.endRange {

            background: #287b7b !important;

            border-color: #287b7b !important;

            color: #ffffff !important;

        }









        .flatpickr-day.inRange {

            background: #d8eded !important;

            border-color: #d8eded !important;

            color: #174242 !important;

        }









        /* =========================================================

                   PDF BUTTON

                ========================================================== */

        .report-download-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            min-width: 105px;

            height: 38px;

            padding: 0 16px;

            border: none;

            border-radius: 8px;

            background: #287b7b;

            color: #ffffff;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition:

                **background** 0.2s ease,

                transform 0.2s ease,

                box-shadow 0.2s ease;

        }









        .report-download-btn:hover {

            background: #206363;

            transform: translateY(-1px);

            box-shadow:

                0 5px 14px rgba(40, 123, 123, 0.22);

        }









        .report-download-btn:active {

            transform: translateY(0);

        }









        /* =========================================================

                   KOLEKSI SUMMARY

                ========================================================== */

        .collection-summary {

            display: grid;

            grid-template-columns:

                repeat(4, minmax(0, 1fr));

            gap: 10px;

            margin-top: 16px;

        }









        .collection-summary-item {

            padding: 10px 11px;

            background: #f7fbfb;

            border: 1px solid #e0eceb;

            border-radius: 9px;

        }









        .collection-summary-item span {

            display: block;

            margin-bottom: 4px;

            color: #718091;

            font-size: 9px;

            font-weight: 700;

        }









        .collection-summary-item strong {

            color: #173b52;

            font-size: 15px;

        }









        /* =========================================================

                   DETAIL COLLECTION TABLE

                ========================================================== */

        .collection-detail-wrapper {

            margin-top: 22px;

            overflow-x: auto;

            border: 1px solid #e2e8f0;

            border-radius: 10px;

        }









        .collection-detail-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 700px;

        }









        .collection-detail-table th {

            padding: 11px 12px;

            background: #f7fbfb;

            border-bottom: 1px solid #e2e8f0;

            color: #52616d;

            font-size: 10px;

            font-weight: 800;

            text-align: left;

            white-space: nowrap;

        }









        .collection-detail-table td {

            padding: 11px 12px;

            border-bottom: 1px solid #edf1f2;

            color: #52616d;

            font-size: 11px;

            vertical-align: top;

        }









        .collection-detail-table tr:last-child td {

            border-bottom: none;

        }









        .collection-type-badge {

            display: inline-flex;

            padding: 4px 8px;

            border-radius: 20px;

            background: #eef7f7;

            color: #287b7b;

            font-size: 9px;

            font-weight: 800;

            white-space: nowrap;

        }









        .collection-empty {

            padding: 25px !important;

            color: #8a98a3 !important;

            text-align: center;

        }









        @media (max-width: 800px) {

            .collection-summary {

                grid-template-columns:

                    repeat(2, minmax(0, 1fr));

            }

        }









        @media (max-width: 520px) {

            .date-picker-input-wrapper {

                width: 100%;

                min-width: 0;

            }

            .filter-form-group {

                width: 100%;

            }

            .btn-filter-apply,

            .btn-filter-reset {

                flex: 1;

            }

            .collection-summary {

                grid-template-columns: 1fr;

            }

        }

    </style>

@endpush









@section('content')

    <section class="report-page">

        {{-- =========================================================

         HERO

    ========================================================== --}}

        <div class="report-hero">

            <div class="report-hero-content">

                <div class="report-label">

                    <span class="label-dot"></span>

                    Pusat Laporan

                </div>









                <h1>

                    Laporan Perpustakaan

                </h1>









                <p>

                    Pantau seluruh aktivitas perpustakaan melalui

                    ringkasan sirkulasi peminjaman, keterlambatan,

                    koleksi buku, dan keanggotaan aktif dalam

                    rentang tanggal fleksibel.

                </p>

            </div>

        </div>









        {{-- =========================================================

         SUCCESS

    ========================================================== --}}

        @if (session('success'))

            <div class="report-success">

                {{ session('success') }}

            </div>

        @endif









        {{-- =========================================================

         FILTER

    ========================================================== --}}

        <div class="report-filter-box">

            <form method="GET" action="{{ route('reports.index') }}" id="dateRangeFilterForm" class="filter-form-group">

                <div class="date-picker-input-wrapper">

                    <span class="calendar-icon">

                        📅

                    </span>









                    <input type="text" id="date_range_picker" class="date-range-flatpickr"

                        placeholder="Pilih rentang tanggal..." readonly>

                </div>









                <input type="hidden" name="start_date" id="start_date"

                    value="{{ $startDateInput ?? $startDate->format('Y-m-d') }}">









                <input type="hidden" name="end_date" id="end_date"

                    value="{{ $endDateInput ?? $endDate->format('Y-m-d') }}">









                <button type="submit" class="btn-filter-apply">

                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"

                        stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">

                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>

                    </svg>

                    Terapkan Filter

                </button>









                <a href="{{ route('reports.index') }}" class="btn-filter-reset">

                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"

                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                        <polyline points="1 4 1 10 7 10"></polyline>

                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>

                    </svg>

                    Reset Filter

                </a>

            </form>









            <div class="filter-period-badge">

                <span>

                    🗓️ Periode:

                    <strong>

                        {{ $reportMonthLabel }}

                    </strong>

                </span>

            </div>

        </div>









        {{-- =========================================================

         SECTION TITLE

    ========================================================== --}}

        <div class="report-section-title">

            <div>

                <span>

                    Ringkasan Aktivitas

                </span>

                <h2>

                    Laporan Terbaru

                </h2>

            </div>

        </div>









        {{-- =========================================================

         REPORT GRID

    ========================================================== --}}

        <div class="report-grid">

            @forelse($reports as $report)

                @php

                    $jenis = $report['jenis'];

                    /*

                                         * PEMINJAMAN

                     */

                    if (str_contains($jenis, 'Peminjaman')) {

                        $type = 'borrow';

                        $category = 'Sirkulasi';

                        $icon = '📖';

                        $valNum = $borrowedBooks ?? 0;

                        $badge = number_format($valNum, 0, ',', '.') . ' Dipinjam';

                        $badgeClass = 'positive';

                        $value = number_format($valNum, 0, ',', '.');

                        $valueLabel = 'peminjaman';

                        $description = 'Rekap transaksi peminjaman dan sirkulasi buku selama periode yang dipilih.';

                        $bars = $borrowChartBars ?? [];

                        $labels = $borrowChartLabels ?? [];

                    }

                    /*

                                         * KETERLAMBATAN

                     */ elseif (

                        str_contains($jenis, 'Keterlambatan')

                    ) {

                        $type = 'late';

                        $category = 'Monitoring';

                        $icon = '⏰';

                        $valNum = $lateBorrowings ?? 0;

                        $badge = number_format($valNum, 0, ',', '.') . ' Kasus';

                        $badgeClass = $valNum > 0 ? 'warning' : 'neutral';

                        $value = number_format($valNum, 0, ',', '.');

                        $valueLabel = 'kasus aktif';

                        $description = 'Daftar aktivitas buku yang terlambat dikembalikan pada periode yang dipilih.';

                        $bars = $lateChartBars ?? [];

                        $labels = $lateChartLabels ?? [];

                    }

                    /*

                                         * KOLEKSI

                     */ elseif (str_contains($jenis, 'Koleksi')) {

                        $type = 'collection';

                        $category = 'Koleksi';

                        $icon = '📚';

                        $valNum = $totalCollectionChanges ?? 0;

                        $badge = number_format($valNum, 0, ',', '.') . ' Perubahan';

                        $badgeClass = 'neutral';

                        $value = number_format($valNum, 0, ',', '.');

                        $valueLabel = 'perubahan koleksi';

                        $description = 'Rekap buku ditambahkan, buku ditarik, eksemplar dihapus, dan kondisi rusak.';

                        $bars = $collectionChartBars ?? [];

                        $labels = $collectionChartLabels ?? [];

                    }

                    /*

                                         * ANGGOTA

                     */ else {

                        $type = 'member';

                        $category = 'Keanggotaan';

                        $icon = '👥';

                        $valNum = $activeMembers ?? 0;

                        $badge = number_format($valNum, 0, ',', '.') . ' Aktif';

                        $badgeClass = 'positive';

                        $value = number_format($valNum, 0, ',', '.');

                        $valueLabel = 'anggota aktif';

                        $description = 'Rekap anggota aktif yang tercatat dalam periode laporan yang dipilih.';

                        $bars = $memberChartBars ?? [];

                        $labels = $memberChartLabels ?? [];

                    }

                @endphp









                {{-- =====================================================

                 CARD

            ====================================================== --}}

                <article class="report-card" onclick="openReportDetail(this)" data-report="{{ $report['jenis'] }}"

                    data-type="{{ $type }}" data-value="{{ $value }}" data-value-label="{{ $valueLabel }}"

                    data-period="{{ $periodLabel }}" data-labels="{{ implode('|', $labels) }}"

                    data-bars="{{ implode('|', $bars) }}">

                    {{-- TOP --}}

                    <div class="report-card-top">

                        <div class="report-card-icon {{ $type }}">

                            {{ $icon }}

                        </div>









                        <div class="report-card-info">

                            <span class="report-card-category">

                                {{ $category }}

                            </span>









                            <h3>

                                {{ $report['jenis'] }}

                            </h3>

                        </div>









                        <span class="report-badge {{ $badgeClass }}">

                            {{ $badge }}

                        </span>

                    </div>









                    {{-- DESCRIPTION --}}

                    <p class="report-description">

                        {{ $description }}

                    </p>









                    {{-- CHART --}}

                    <div class="report-chart-wrapper">

                        <div class="chart-value">

                            <strong>

                                {{ $value }}

                            </strong>

                            <span>

                                {{ $valueLabel }}

                            </span>

                        </div>









                        <div class="report-chart">

                            <div class="chart-line"></div>

                            <div class="chart-line"></div>

                            <div class="chart-line"></div>









                            <div class="chart-bars">

                                @foreach ($bars as $index => $bar)

                                    <div class="chart-column {{ $index === count($bars) - 1 ? 'active' : '' }}">

                                        <div class="chart-bar" style="height: {{ $bar }}%;"></div>









                                        <span>

                                            {{ $labels[$index] ?? '' }}

                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    </div>









                    {{-- KOLEKSI SUMMARY --}}

                    @if ($type === 'collection')

                        <div class="collection-summary" onclick="event.stopPropagation()">

                            <div class="collection-summary-item">

                                <span>

                                    DITAMBAHKAN

                                </span>

                                <strong>

                                    {{ $collectionAddedCount ?? 0 }}

                                </strong>

                            </div>









                            <div class="collection-summary-item">

                                <span>

                                    DITARIK

                                </span>

                                <strong>

                                    {{ $collectionWithdrawnCount ?? 0 }}

                                </strong>

                            </div>









                            <div class="collection-summary-item">

                                <span>

                                    EKSEMPLAR DIHAPUS

                                </span>

                                <strong>

                                    {{ $collectionDeletedCopyCount ?? 0 }}

                                </strong>

                            </div>









                            <div class="collection-summary-item">

                                <span>

                                    RUSAK

                                </span>

                                <strong>

                                    {{ $collectionDamagedCount ?? 0 }}

                                </strong>

                            </div>

                        </div>

                    @endif









                    {{-- FOOTER --}}

                    <div class="report-card-footer" onclick="event.stopPropagation()">

                        <span class="report-updated">

                            ● Periode:

                            {{ $periodLabel }}

                        </span>









                        <div class="report-actions" onclick="event.stopPropagation()">

                            <button type="button" class="report-download-btn" onclick="downloadReport(this)">

                                ↓

                                Unduh PDF

                            </button>

                        </div>

                    </div>

                </article>

            @empty

                <div class="report-empty">

                    <strong>

                        Belum ada laporan

                    </strong>

                    <span>

                        Klik "Tambah Laporan" untuk membuat laporan baru.

                    </span>

                </div>

            @endforelse

        </div>

    </section>









    {{-- =============================================================

     DETAIL MODAL

============================================================= --}}

    <div id="reportDetailModal" class="report-modal" onclick="closeReportDetail(event)">

        <div class="report-modal-content" onclick="event.stopPropagation()">

            {{-- HEADER --}}

            <div class="report-modal-header">

                <div>

                    <span class="report-modal-label">

                        DETAIL LAPORAN

                    </span>









                    <h2 id="modalReportTitle">

                        Laporan

                    </h2>

                </div>









                <button type="button" class="report-modal-close" onclick="closeReportDetail()">

                    ×

                </button>

            </div>









            {{-- SUMMARY --}}

            <div class="report-modal-summary">

                <div class="report-modal-value">

                    <strong id="modalReportValue">

                        0

                    </strong>

                    <span id="modalReportValueLabel">

                        data

                    </span>

                </div>









                <div class="report-modal-period" id="modalReportPeriod">

                    Periode:

                    {{ $periodLabel }}

                </div>

            </div>









            {{-- COLLECTION DETAIL --}}

            <div id="collectionDetailSection" style="display: none;">

                <div class="collection-detail-wrapper">

                    <table class="collection-detail-table">

                        <thead>

                            <tr>

                                <th>

                                    Tanggal

                                </th>

                                <th>

                                    Jenis Perubahan

                                </th>

                                <th>

                                    Judul Buku

                                </th>

                                <th>

                                    Eksemplar / Jumlah

                                </th>

                                <th>

                                    Alasan / Keterangan

                                </th>

                            </tr>

                        </thead>









                        <tbody id="collectionDetailBody">

                            @forelse ($collectionExportData ?? []

                                    as $item)

                                <tr>

                                    <td>

                                        {{ $item['tanggal'] }}

                                    </td>









                                    <td>

                                        <span class="collection-type-badge">

                                            {{ $item['jenis_perubahan'] }}

                                        </span>

                                    </td>









                                    <td>

                                        {{ $item['judul_buku'] }}

                                    </td>









                                    <td>

                                        {{ $item['eksemplar_jumlah'] }}

                                    </td>









                                    <td>

                                        {{ $item['alasan_keterangan'] }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="collection-empty">

                                        Tidak ada perubahan koleksi pada periode yang dipilih.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>









            {{-- NORMAL CHART --}}

            <div id="normalDetailChart" class="report-detail-chart">

                <div class="detail-chart-lines">

                    <span></span>

                    <span></span>

                    <span></span>

                    <span></span>

                </div>









                <div id="modalChartBars" class="detail-chart-bars"></div>

            </div>









            {{-- FOOTER --}}

            <div class="report-modal-footer">

                <span>

                    Data laporan berdasarkan periode

                    <strong>

                        {{ $periodLabel }}

                    </strong>.

                </span>









                <button type="button" onclick="closeReportDetail()">

                    Tutup

                </button>

            </div>

        </div>

    </div>









    {{-- =============================================================

     DATA PEMINJAMAN

============================================================= --}}

    <script

    id="borrowingsReportData"

    type="application/json"

>

{!! json_encode($borrowingsExportData ?? []) !!}

</script>

    {{-- =============================================================

     DATA KETERLAMBATAN

    ============================================================= --}}

    <script

        id="lateBorrowingsReportData"

        type="application/json"

    >

{!! json_encode($lateBorrowingsExportData ?? []) !!}

</script>

    {{-- =============================================================

     DATA ANGGOTA AKTIF

     ============================================================= --}}

    <script

        id="activeMembersReportData"

        type="application/json"

    >

{!! json_encode($activeMembersExportData ?? []) !!}

</script>













    {{-- =============================================================

     DATA KOLEKSI

============================================================= --}}

    <script

    id="collectionReportData"

    type="application/json"

>

{!! json_encode($collectionExportData ?? []) !!}

</script>









@endsection









@push('scripts')

    {{-- FLATPICKR --}}

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>









    {{-- DETAIL MODAL LAMA --}}

    <script src="{{ asset('js/openReportDetail.js') }}"></script>









    {{-- JSPDF --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>









    {{-- SWEETALERT --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>









    <script>

        /*

                |--------------------------------------------------------------------------

                | DATE PICKER

                |--------------------------------------------------------------------------

                */

        document.addEventListener(

            'DOMContentLoaded',

            function() {

                const startDateInput =

                    document.getElementById(

                        'start_date'

                    );









                const endDateInput =

                    document.getElementById(

                        'end_date'

                    );









                const rangePicker =

                    document.getElementById(

                        'date_range_picker'

                    );









                if (

                    !startDateInput ||

                    !endDateInput ||

                    !rangePicker

                ) {

                    return;

                }









                const currentStart =

                    startDateInput.value;









                const currentEnd =

                    endDateInput.value;









                flatpickr(

                    rangePicker, {

                        mode: 'range',

                        dateFormat: 'Y-m-d',

                        altInput: true,

                        altFormat: 'j M Y',

                        locale: 'id',

                        defaultDate: [

                            currentStart,

                            currentEnd

                        ],

                        showMonths: 2,









                        onChange: function(

                            selectedDates,

                            dateStr,

                            instance

                        ) {

                            if (

                                selectedDates.length === 2

                            ) {

                                const start =

                                    instance.formatDate(

                                        selectedDates[0],

                                        'Y-m-d'

                                    );









                                const end =

                                    instance.formatDate(

                                        selectedDates[1],

                                        'Y-m-d'

                                    );









                                startDateInput.value =

                                    start;









                                endDateInput.value =

                                    end;

                            } else if (

                                selectedDates.length === 1

                            ) {

                                const start =

                                    instance.formatDate(

                                        selectedDates[0],

                                        'Y-m-d'

                                    );









                                startDateInput.value =

                                    start;









                                endDateInput.value =

                                    start;

                            }

                        }

                    }

                );

            }

        );









        /*

        |--------------------------------------------------------------------------

        | DOWNLOAD PDF

        |--------------------------------------------------------------------------

        */

        function downloadReport(button) {

            const card =

                button.closest(

                    '.report-card'

                );









            if (!card) {

                return;

            }









            const reportName =

                card.dataset.report ||

                'Laporan Perpustakaan';









            const reportType =

                card.dataset.type ||

                'normal';









            const period =

                card.dataset.period ||

                'Periode Terpilih';









            const statValue =

                card.dataset.value ||

                '0';









            const statLabel =

                card.dataset.valueLabel ||

                'data';









            /*

             * ========================================================

             * DATA

             * ========================================================

             */

            let tableData = [];









            if (

                reportType === 'collection'

            ) {

                const collectionScript =

                    document.getElementById(

                        'collectionReportData'

                    );

                if (collectionScript) {

                    try {

                        tableData =

                            JSON.parse(

                                collectionScript.textContent

                            );

                    } catch (error) {

                        console.error(

                            'Gagal membaca data koleksi:',

                            error

                        );

                        tableData = [];

                    }

                }

            } else if (

                reportType === 'member'

            ) {

                const memberScript =

                    document.getElementById(

                        'activeMembersReportData'

                    );

                if (memberScript) {

                    try {

                        tableData =

                            JSON.parse(

                                memberScript.textContent

                            );

                    } catch (error) {

                        console.error(

                            'Gagal membaca data anggota aktif:',

                            error

                        );

                        tableData = [];

                    }

                }

            } else if (

                reportType === 'late'

            ) {

                const dataScript =

                    document.getElementById(

                        'lateBorrowingsReportData'

                    );

                if (dataScript) {

                    try {

                        tableData =

                            JSON.parse(

                                dataScript.textContent

                            );

                    } catch (error) {

                        console.error(

                            'Gagal membaca data keterlambatan:',

                            error

                        );

                        tableData = [];

                    }

                }

            } else {

                const dataScript =

                    document.getElementById(

                        'borrowingsReportData'

                    );

                if (dataScript) {

                    try {

                        tableData =

                            JSON.parse(

                                dataScript.textContent

                            );

                    } catch (error) {

                        console.error(

                            'Gagal membaca data laporan:',

                            error

                        );

                        tableData = [];

                    }

                }

            }

            /*

                         * ========================================================

                         * TANGGAL

                         * ========================================================

                         */

            const startInput =

                document.getElementById(

                    'start_date'

                );









            const endInput =

                document.getElementById(

                    'end_date'

                );









            const startDate =

                startInput?.value ||

                'start';









            const endDate =

                endInput?.value ||

                'end';









            /*

             * ========================================================

             * FILE NAME

             * ========================================================

             */

            const safeName =

                reportName

                .replace(

                    /[^a-zA-Z0-9_-]/g,

                    '_'

                );









            const fileName =

                `${safeName}_${startDate}_sd_${endDate}.pdf`;









            /*

             * ========================================================

             * CEK JSPDF

             * ========================================================

             */

            if (

                !window.jspdf ||

                !window.jspdf.jsPDF

            ) {

                Swal.fire({

                    icon: 'error',

                    title: 'PDF Tidak Siap',

                    text: 'Library PDF belum berhasil dimuat. Silakan refresh halaman.',

                    confirmButtonColor: '#287b7b'

                });

                return;

            }









            /*

             * ========================================================

             * BUAT PDF

             * ========================================================

             */

            try {

                const {

                    jsPDF

                } =

                window.jspdf;









                const doc =

                    new jsPDF({

                        orientation: 'portrait',

                        unit: 'mm',

                        format: 'a4'

                    });









                /*

                 * ====================================================

                 * HEADER

                 * ====================================================

                 */

                doc.setFillColor(

                    40,

                    123,

                    123

                );









                doc.rect(

                    0,

                    0,

                    210,

                    24,

                    'F'

                );









                doc.setTextColor(

                    255,

                    255,

                    255

                );









                doc.setFont(

                    'helvetica',

                    'bold'

                );









                doc.setFontSize(

                    14

                );









                doc.text(

                    'PERPUSTAKAAN TIGA SERANGKAI',

                    14,

                    11

                );









                doc.setFont(

                    'helvetica',

                    'normal'

                );









                doc.setFontSize(

                    9

                );









                doc.text(

                    'Sistem Informasi Manajemen Laporan Perpustakaan',

                    14,

                    18

                );









                /*

                 * ====================================================

                 * JUDUL

                 * ====================================================

                 */

                doc.setTextColor(

                    23,

                    47,

                    47

                );









                doc.setFont(

                    'helvetica',

                    'bold'

                );









                doc.setFontSize(

                    16

                );









                doc.text(

                    reportName.toUpperCase(),

                    14,

                    35

                );









                /*

                 * ====================================================

                 * PERIODE

                 * ====================================================

                 */

                doc.setFont(

                    'helvetica',

                    'normal'

                );









                doc.setFontSize(

                    10

                );









                doc.setTextColor(

                    80,

                    99,

                    99

                );









                doc.text(

                    `Periode: ${period}`,

                    14,

                    42

                );









                /*

                 * ====================================================

                 * TANGGAL UNDUH

                 * ====================================================

                 */

                const downloadDate =

                    new Date()

                    .toLocaleDateString(

                        'id-ID', {

                            day: '2-digit',

                            month: 'long',

                            year: 'numeric'

                        }

                    );









                doc.text(

                    `Tanggal Unduh: ${downloadDate}`,

                    14,

                    47

                );









                /*

                 * ====================================================

                 * SUMMARY

                 * ====================================================

                 */

                doc.setFillColor(

                    243,

                    248,

                    248

                );









                doc.roundedRect(

                    14,

                    52,

                    182,

                    14,

                    3,

                    3,

                    'F'

                );









                doc.setDrawColor(

                    210,

                    230,

                    230

                );









                doc.roundedRect(

                    14,

                    52,

                    182,

                    14,

                    3,

                    3,

                    'S'

                );









                doc.setTextColor(

                    23,

                    47,

                    47

                );









                doc.setFont(

                    'helvetica',

                    'bold'

                );









                doc.setFontSize(

                    12

                );









                doc.text(

                    String(statValue),

                    20,

                    61

                );









                doc.setFont(

                    'helvetica',

                    'normal'

                );









                doc.setFontSize(

                    9

                );









                doc.text(

                    String(statLabel),

                    40,

                    61

                );









                /*

                 * ====================================================

                 * TABEL KOLEKSI

                 * ====================================================

                 */

                if (

                    reportType === 'collection' &&

                    Array.isArray(tableData) &&

                    tableData.length > 0 &&

                    typeof doc.autoTable === 'function'

                ) {

                    const rows =

                        tableData.map(

                            (row, index) => [

                                index + 1,

                                row.tanggal ?? '-',

                                row.jenis_perubahan ?? '-',

                                row.judul_buku ?? '-',

                                row.eksemplar_jumlah ?? '-',

                                row.alasan_keterangan ?? '-'

                            ]

                        );









                    doc.autoTable({

                        startY: 72,

                        head: [

                            [

                                'NO',

                                'TANGGAL',

                                'JENIS PERUBAHAN',

                                'JUDUL BUKU',

                                'EKSEMPLAR / JUMLAH',

                                'ALASAN / KETERANGAN'

                            ]

                        ],

                        body: rows,

                        theme: 'grid',

                        styles: {

                            fontSize: 7.5,

                            cellPadding: 2.5

                        },

                        headStyles: {

                            fontStyle: 'bold'

                        },

                        columnStyles: {

                            0: {

                                cellWidth: 10,

                                halign: 'center'

                            },

                            1: {

                                cellWidth: 25

                            },

                            2: {

                                cellWidth: 32

                            },

                            3: {

                                cellWidth: 42

                            },

                            4: {

                                cellWidth: 30

                            },

                            5: {

                                cellWidth: 43

                            }

                        },

                        margin: {

                            left: 14,

                            right: 14

                        }

                    });

                }









                /*

                 * ====================================================

                 * TABEL ANGGOTA AKTIF

                 * ====================================================

                 */

                else if (



                    reportType === 'member' &&

                    Array.isArray(tableData) &&

                    tableData.length > 0 &&

                    typeof doc.autoTable === 'function'

                ) {

                    const rows =

                        tableData.map(row => [

                            row.no ?? '-',

                            row.member_name ?? '-',

                            row.activity ?? '-',

                            row.judul_buku ?? '-',

                            row.date ?? '-',

                            row.status ?? '-'

                        ]);

                    doc.autoTable({

                        startY: 72,

                        head: [

                            [

                                'NO',

                                'NAMA ANGGOTA',

                                'AKTIVITAS',

                                'JUDUL BUKU',

                                'TANGGAL',

                                'STATUS'

                            ]

                        ],

                        body: rows,

                        theme: 'grid',

                        styles: {

                            fontSize: 7.5,

                            cellPadding: 2.5

                        },

                        headStyles: {

                            fontStyle: 'bold'

                        },

                        columnStyles: {

                            0: {

                                cellWidth: 10

                            },

                            1: {

                                cellWidth: 45

                            },

                            2: {

                                cellWidth: 30

                            },

                            3: {

                                cellWidth: 48

                            },

                            4: {

                                cellWidth: 27

                            },

                            5: {

                                cellWidth: 27

                            }

                        },

                        margin: {

                            left: 14,

                            right: 14

                        }

                    });

                }

                                /*
                 * ====================================================
                 * TABEL PEMINJAMAN & KETERLAMBATAN
                 * ====================================================
                 */
                else if (
                    Array.isArray(tableData) &&
                    tableData.length > 0 &&
                    typeof doc.autoTable === 'function'
                ) {

                    /*
                     * Tetap menggunakan data dari borrowingsReportData.
                     * Hanya kolom yang ditampilkan di PDF yang ditentukan
                     * secara eksplisit agar tidak mengambil semua key
                     * dari object secara otomatis.
                     */

                    const isLateReport =
                        reportType === 'late';

                    const columns = isLateReport
                        ? [
                            'no',
                            'member_name',
                            'judul_buku',
                            'borrowed_at',
                            'due_at',
                            'status',
                            'keterangan'
                        ]
                        : [
                            'no',
                            'member_name',
                            'judul_buku',
                            'borrowed_at',
                            'due_at',
                            'status'
                        ];

                    const headers = isLateReport
                        ? [
                            'NO',
                            'NAMA ANGGOTA',
                            'JUDUL BUKU',
                            'TANGGAL PINJAM',
                            'BATAS PENGEMBALIAN',
                            'STATUS',
                            'KETERANGAN'
                        ]
                        : [
                            'NO',
                            'NAMA ANGGOTA',
                            'JUDUL BUKU',
                            'TANGGAL PINJAM',
                            'BATAS PENGEMBALIAN',
                            'STATUS'
                        ];

                    const statusMap = {
                        'dipinjam': 'Sedang Dipinjam',
                        'diperpanjang': 'Diperpanjang',
                        'dikembalikan': 'Dikembalikan',
                        'terlambat': 'Terlambat',
                        'selesai': 'Selesai',
                        'aktif': 'Aktif',
                        'nonaktif': 'Tidak Aktif',
                        'pending': 'Menunggu',
                        'disetujui': 'Disetujui',
                        'ditolak': 'Ditolak',
                        'dibatalkan': 'Dibatalkan'
                    };

                    const rows =
                        tableData.map(
                            (row, index) => {

                                return columns.map(
                                    column => {

                                        if (
                                            column === 'no' &&
                                            (
                                                row[column] === undefined ||
                                                row[column] === null
                                            )
                                        ) {
                                            return index + 1;
                                        }

                                        if (
                                            column === 'status'
                                        ) {
                                            const status =
                                                String(
                                                    row[column] ?? '-'
                                                ).toLowerCase();

                                            return statusMap[status] ??
                                                row[column] ??
                                                '-';
                                        }

                                        return row[column] ?? '-';
                                    }
                                );
                            }
                        );

                    doc.autoTable({
                        startY: isLateReport ? 71 : 72,

                        head: [
                            headers
                        ],

                        body: rows,

                        theme: 'grid',

                        styles: {
                            fontSize: 8,
                            cellPadding: 3,
                            valign: 'middle',
                            overflow: 'linebreak'
                        },

                        headStyles: {
                            fontStyle: 'bold',
                            halign: 'center'
                        },

                        columnStyles: isLateReport
                            ? {
                                0: {
                                    cellWidth: 12,
                                    halign: 'center'
                                },

                                1: {
                                    cellWidth: 30
                                },

                                2: {
                                    cellWidth: 40
                                },

                                3: {
                                    cellWidth: 23
                                },

                                4: {
                                    cellWidth: 27
                                },

                                5: {
                                    cellWidth: 23
                                },

                                6: {
                                    cellWidth: 27
                                }
                            }
                            : {
                                0: {
                                    cellWidth: 10,
                                    halign: 'center'
                                },

                                1: {
                                    cellWidth: 40
                                },

                                2: {
                                    cellWidth: 45
                                },

                                3: {
                                    cellWidth: 28
                                },

                                4: {
                                    cellWidth: 32
                                },

                                5: {
                                    cellWidth: 27
                                }
                            },

                        tableWidth: isLateReport ? 182 : 'wrap',

                        margin: {
                            left: 14,
                            right: 14
                        }
                    });
                }

/*

                 * ====================================================

                 * EMPTY

                 * ====================================================

                 */

                else {

                    doc.setTextColor(

                        80,

                        99,

                        99

                    );









                    doc.setFont(

                        'helvetica',

                        'normal'

                    );









                    doc.setFontSize(

                        10

                    );









                    doc.text(

                        'Tidak ada data detail untuk periode yang dipilih.',

                        14,

                        78

                    );

                }









                /*

                 * ====================================================

                 * FOOTER

                 * ====================================================

                 */

                const pageCount =

                    doc.internal.getNumberOfPages();









                for (

                    let page = 1; page <= pageCount; page++

                ) {

                    doc.setPage(

                        page

                    );









                    const pageHeight =

                        doc.internal

                        .pageSize

                        .height;









                    doc.setFontSize(

                        8

                    );









                    doc.setTextColor(

                        120,

                        120,

                        120

                    );









                    doc.text(

                        `Halaman ${page} dari ${pageCount}`,

                        14,

                        pageHeight - 10

                    );









                    doc.text(

                        'Perpustakaan Tiga Serangkai',

                        196,

                        pageHeight - 10, {

                            align: 'right'

                        }

                    );

                }









                /*

                 * ====================================================

                 * SAVE

                 * ====================================================

                 */

                doc.save(

                    fileName

                );









                Swal.fire({

                    icon: 'success',

                    title: 'Unduh Selesai!',

                    text: 'Laporan PDF berhasil diunduh.',

                    confirmButtonText: 'Tutup',

                    confirmButtonColor: '#287b7b',

                    timer: 3000,

                    timerProgressBar: true

                });









            } catch (error) {

                console.error(

                    'Gagal membuat PDF:',

                    error

                );









                Swal.fire({

                    icon: 'error',

                    title: 'Gagal Membuat PDF',

                    text: 'Laporan PDF gagal dibuat. Silakan coba lagi.',

                    confirmButtonColor: '#287b7b'

                });

            }

        }









        /*

        |--------------------------------------------------------------------------

        | DETAIL KOLEKSI

        |--------------------------------------------------------------------------

        |

        | Saat card koleksi diklik:

        | - chart disembunyikan

        | - tabel koleksi ditampilkan

        |

        */

        document.addEventListener(

            'DOMContentLoaded',

            function() {

                const cards =

                    document.querySelectorAll(

                        '.report-card'

                    );









                cards.forEach(

                    function(card) {

                        card.addEventListener(

                            'click',

                            function() {

                                if (

                                    card.dataset.type !==

                                    'collection'

                                ) {

                                    return;

                                }









                                setTimeout(

                                    function() {

                                        const collectionSection =

                                            document.getElementById(

                                                'collectionDetailSection'

                                            );









                                        const normalChart =

                                            document.getElementById(

                                                'normalDetailChart'

                                            );









                                        if (

                                            collectionSection

                                        ) {

                                            collectionSection.style.display =

                                                'block';

                                        }









                                        if (

                                            normalChart

                                        ) {

                                            normalChart.style.display =

                                                'none';

                                        }

                                    },

                                    50

                                );

                            }

                        );

                    }

                );









                /*

                 * Reset saat modal ditutup.

                 */

                const modal =

                    document.getElementById(

                        'reportDetailModal'

                    );









                if (modal) {

                    const observer =

                        new MutationObserver(

                            function() {

                                if (

                                    !modal.classList.contains(

                                        'show'

                                    )

                                ) {

                                    const collectionSection =

                                        document.getElementById(

                                            'collectionDetailSection'

                                        );









                                    const normalChart =

                                        document.getElementById(

                                            'normalDetailChart'

                                        );









                                    if (

                                        collectionSection

                                    ) {

                                        collectionSection.style.display =

                                            'none';

                                    }









                                    if (

                                        normalChart

                                    ) {

                                        normalChart.style.display =

                                            '';

                                    }

                                }

                            }

                        );









                    observer.observe(

                        modal, {

                            attributes: true,

                            attributeFilter: [

                                'class'

                            ]

                        }

                    );

                }

            }

        );

    </script>

@endpush