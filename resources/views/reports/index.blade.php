@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
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

                     */ elseif (
                        str_contains($jenis, 'Koleksi')
                    ) {
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









                const isLateReport = reportType === 'late';

                const doc =

                    new jsPDF({

                        orientation: isLateReport ? 'landscape' : 'portrait',

                        unit: 'mm',

                        format: 'a4'

                    });

                const pageWidth = doc.internal.pageSize.getWidth();









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

                    pageWidth,

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

                    pageWidth - 28,

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

                    pageWidth - 28,

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

                    const columns = isLateReport ?
                        [
                            'no',
                            'member_name',
                            'judul_buku',
                            'due_at',
                            'status',
                            'extension_date',
                            'new_due_at',
                            'returned_at',
                            'keterangan'
                        ] :
                        [
                            'no',
                            'member_name',
                            'judul_buku',
                            'borrowed_at',
                            'due_at',
                            'status'
                        ];

                    const headers = isLateReport ?
                        [
                            'NO',
                            'NAMA',
                            'JUDUL BUKU',
                            'TENGGAT PENGEMBALIAN',
                            'STATUS',
                            'TGL PERPANJANGAN',
                            'TENGGAT PENGEMBALIAN BARU',
                            'TGL PENGEMBALIAN',
                            'KETERANGAN'
                        ] :
                        [
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

                    const rows = [];

                    tableData.forEach((row, index) => {
                        if (!isLateReport) {
                            rows.push(columns.map(column => {
                                if (column === 'no' && (row[column] === undefined || row[column] ===
                                        null)) {
                                    return index + 1;
                                }

                                if (column === 'status') {
                                    const status = String(row[column] ?? '-').toLowerCase();
                                    return statusMap[status] ?? row[column] ?? '-';
                                }

                                return row[column] ?? '-';
                            }));
                            return;
                        }

                        const extensionDates = String(row.extension_date ?? '-')
                            .split(/<br\s*\/?>(?:\s*)|\n/)
                            .map(value => value.trim())
                            .filter(Boolean);

                        const newDueDates = String(row.new_due_at ?? '-')
                            .split(/<br\s*\/?>(?:\s*)|\n/)
                            .map(value => value.trim())
                            .filter(Boolean);

                        const extensionCount = Math.max(
                            extensionDates.length,
                            newDueDates.length,
                            1
                        );

                        for (
                            let extensionIndex = 0; extensionIndex < extensionCount; extensionIndex++
                        ) {
                            const isFirstExtensionRow = extensionIndex === 0;
                            const rowSpan = extensionCount;

                            const baseCell = (content, extra = {}) => ({
                                content: content ?? '-',
                                ...(extensionCount > 1 ? {
                                    rowSpan
                                } : {}),
                                ...extra
                            });

                            const extensionCell = extensionDates[extensionIndex] ?? '-';
                            const newDueCell = newDueDates[extensionIndex] ?? '-';

                            if (isFirstExtensionRow) {
                                // BARIS PERTAMA:
                                // Semua kolom ditampilkan.
                                rows.push([
                                    baseCell(row.no ?? index + 1),
                                    baseCell(row.member_name ?? '-'),
                                    baseCell(row.judul_buku ?? '-'),
                                    baseCell(row.due_at ?? '-'),

                                    baseCell(
                                        statusMap[
                                            String(row.status ?? '-').toLowerCase()
                                        ] ?? row.status ?? '-'
                                    ),

                                    extensionCell,
                                    newDueCell,

                                    baseCell(row.returned_at ?? '-'),
                                    baseCell(row.keterangan ?? '-')
                                ]);
                            } else {
                                // BARIS PERPANJANGAN BERIKUTNYA:
                                // Kolom yang sudah rowSpan TIDAK dibuat lagi.
                                // Hanya kolom PERPANJANG yang diisi.
                                rows.push([
                                    extensionCell,
                                    newDueCell
                                ]);
                            }
                        }
                    });

                    doc.autoTable({
                        startY: isLateReport ? 71 : 72,

                        head: isLateReport ?
                            [
                                [{
                                        content: 'NO',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'NAMA',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'JUDUL BUKU',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'TENGGAT PENGEMBALIAN',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'STATUS',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'PERPANJANG',
                                        colSpan: 2
                                    },
                                    {
                                        content: 'TGL PENGEMBALIAN',
                                        rowSpan: 2
                                    },
                                    {
                                        content: 'KETERANGAN',
                                        rowSpan: 2
                                    }
                                ],
                                [
                                    'TGL PERPANJANGAN',
                                    'TENGGAT PENGEMBALIAN BARU'
                                ]
                            ] :
                            [headers],

                        body: rows,

                        theme: 'grid',

                        styles: {
                            fontSize: isLateReport ? 7.5 : 8,
                            cellPadding: isLateReport ? 2.5 : 3,
                            valign: 'middle',
                            overflow: 'linebreak'
                        },

                        headStyles: {
                            fontStyle: 'bold',
                            halign: 'center',
                            valign: 'middle'
                        },

                        columnStyles: isLateReport ?
                            {
                                0: {
                                    cellWidth: 10,
                                    halign: 'center'
                                },
                                1: {
                                    cellWidth: 32
                                },
                                2: {
                                    cellWidth: 40
                                },
                                3: {
                                    cellWidth: 34
                                },
                                4: {
                                    cellWidth: 28
                                },
                                5: {
                                    cellWidth: 30
                                },
                                6: {
                                    cellWidth: 38
                                },
                                7: {
                                    cellWidth: 30
                                },
                                8: {
                                    cellWidth: 35
                                }
                            } :
                            {
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

                        tableWidth: isLateReport ? 277 : 'wrap',

                        margin: {
                            left: isLateReport ? 10 : 14,
                            right: isLateReport ? 10 : 14
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

                        pageWidth - 14,

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
