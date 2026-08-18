@extends('layouts.app')

@section('title', 'Laporan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endpush

@section('content')

<section class="report-page">

    {{-- =====================================================
         HERO
    ====================================================== --}}

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
                Pantau aktivitas perpustakaan melalui ringkasan
                peminjaman, koleksi, keterlambatan, dan anggota aktif.
            </p>

        </div>


        <div class="report-hero-action">

            <a
                href="{{ route('reports.create') }}"
                class="report-add-btn"
            >

                <span class="add-icon">
                    +
                </span>

                <span>
                    Tambah Laporan
                </span>

            </a>

        </div>

    </div>


    {{-- =====================================================
         SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="report-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- =====================================================
         SECTION TITLE
    ====================================================== --}}

    <div class="report-section-title">

        <div>

            <span>
                Ringkasan Aktivitas
            </span>

            <h2>
                Laporan Terbaru
            </h2>

        </div>


        <span class="report-period">
            Agustus 2026
        </span>

    </div>


    {{-- =====================================================
         REPORT GRID
    ====================================================== --}}

    <div class="report-grid">

        @forelse($reports as $report)

            @php

                $jenis = $report['jenis'];

                /*
                 * Tentukan tipe visual berdasarkan jenis laporan.
                 */

                if (str_contains($jenis, 'Peminjaman')) {

                    $type = 'borrow';

                    $category = 'Sirkulasi';

                    $icon = '📖';

                    $badge = '+12,5%';

                    $badgeClass = 'positive';

                    $value = '128';

                    $valueLabel = 'peminjaman';

                    $description =
                        'Rekap transaksi peminjaman dan pengembalian buku selama bulan berjalan berdasarkan kategori dan kelas.';

                    $bars = [
                        25,
                        42,
                        58,
                        47,
                        78,
                    ];

                    $labels = [
                        'Apr',
                        'Mei',
                        'Jun',
                        'Jul',
                        'Agu',
                    ];

                    $updated = 'Diperbarui hari ini';

                } elseif (str_contains($jenis, 'Keterlambatan')) {

                    $type = 'late';

                    $category = 'Monitoring';

                    $icon = '⏰';

                    $badge = '18 Kasus';

                    $badgeClass = 'warning';

                    $value = '18';

                    $valueLabel = 'kasus aktif';

                    $description =
                        'Daftar anggota dengan buku terlambat dikembalikan, lengkap dengan durasi dan status denda.';

                    $bars = [
                        30,
                        46,
                        62,
                        42,
                        70,
                    ];

                    $labels = [
                        'Apr',
                        'Mei',
                        'Jun',
                        'Jul',
                        'Agu',
                    ];

                    $updated = 'Diperbarui hari ini';

                } elseif (str_contains($jenis, 'Koleksi')) {

                    $type = 'collection';

                    $category = 'Koleksi';

                    $icon = '📚';

                    $badge = '2.999 Buku';

                    $badgeClass = 'neutral';

                    $value = '2.999';

                    $valueLabel = 'total koleksi';

                    $description =
                        'Komposisi koleksi berdasarkan kategori, status ketersediaan, serta kondisi fisik buku.';

                    $bars = [
                        42,
                        62,
                        48,
                        76,
                        58,
                    ];

                    $labels = [
                        'Nov',
                        'Tek',
                        'Ref',
                        'Umum',
                        'Lain',
                    ];

                    $updated = 'Diperbarui minggu lalu';

                } else {

                    $type = 'member';

                    $category = 'Keanggotaan';

                    $icon = '👥';

                    $badge = 'Aktif';

                    $badgeClass = 'positive';

                    $value = '2.999';

                    $valueLabel = 'anggota aktif';

                    $description =
                        'Rekap keaktifan anggota berdasarkan frekuensi kunjungan dan aktivitas peminjaman buku.';

                    $bars = [
                        30,
                        48,
                        67,
                        58,
                        82,
                    ];

                    $labels = [
                        'Apr',
                        'Mei',
                        'Jun',
                        'Jul',
                        'Agu',
                    ];

                    $updated = 'Diperbarui 2 minggu lalu';

                }

            @endphp


            {{-- =================================================
                 CARD
            ================================================== --}}

            <article 
                    class="report-card"
                    onclick="openReportDetail(this)"
                    data-report="{{ $report['jenis'] }}"
                    data-value="{{ $value }}"
                    data-value-label="{{ $valueLabel }}"
                    data-labels="{{ implode('|', $labels) }}"
                    data-bars="{{ implode('|', $bars) }}"
                >

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


                <p class="report-description">
                    {{ $description }}
                </p>


                {{-- =================================================
                     CHART
                ================================================== --}}

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

                            @foreach($bars as $index => $bar)

                                <div class="chart-column {{ $index === count($bars) - 1 ? 'active' : '' }}">

                                    <div
                                        class="chart-bar"
                                        style="height: {{ $bar }}%;"
                                    >
                                </div>

                                    <span>
                                        {{ $labels[$index] }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     FOOTER
                ================================================== --}}

                <div class="report-card-footer" onclick="event.stopPropagation()">

                    <span class="report-updated">
                        ● {{ $updated }}
                    </span>


                    <div class="report-actions" onclick="event.stopPropagation()">

                        {{-- EDIT --}}

                        <a
                            href="{{ route('reports.edit', $report['id']) }}"
                            class="report-detail-btn"
                        >
                            Edit
                        </a>

                        {{-- FORMAT --}}

                        <select class="report-format-select"
                            aria-label="Pilih format laporan"
                            onclick="event.stopPropagation()">

                            <option value="pdf">
                                PDF
                            </option>   
                            <option value="excel">
                                Excel
                            </option>
                        </select>

                        {{-- DOWNLOAD --}}

                        <div class="report-download" onclick="event.stopPropagation()">

                            <button
                                type="button"
                                class="report-download-btn"
                                onclick="downloadReport(this)"
                            >
                                Unduh
                            </button>

                        </div>

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

    <div
    id="reportDetailModal"
    class="report-modal"
    onclick="closeReportDetail(event)"
>

    <div
        class="report-modal-content"
        onclick="event.stopPropagation()"
    >

        <div class="report-modal-header">

            <div>

                <span class="report-modal-label">
                    DETAIL LAPORAN
                </span>

                <h2 id="modalReportTitle">
                    Laporan
                </h2>

            </div>

            <button
                type="button"
                class="report-modal-close"
                onclick="closeReportDetail()"
            >
                ×
            </button>

        </div>


        <div class="report-modal-summary">

            <div class="report-modal-value">

                <strong id="modalReportValue">
                    0
                </strong>

                <span id="modalReportValueLabel">
                    data
                </span>

            </div>

            <div class="report-modal-period">
                Periode: Agustus 2026
            </div>

        </div>


        <div class="report-detail-chart">

            <div class="detail-chart-lines">

                <span></span>
                <span></span>
                <span></span>
                <span></span>

            </div>


            <div
                id="detailChartBars"
                class="detail-chart-bars"
            ></div>

        </div>


        <div class="report-modal-footer">

            <span>
                Data laporan berdasarkan periode yang dipilih.
            </span>

            <button
                type="button"
                onclick="closeReportDetail()"
            >
                Tutup
            </button>

        </div>

    </div>

</div>

<script src="{{ asset('js/openReportDetail.js') }}"></script>


@endsection