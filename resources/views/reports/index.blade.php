@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">

    <style>
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

        .date-range-flatpickr:focus, .date-range-flatpickr:hover {
            border-color: #287b7b;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(40, 123, 123, 0.12);
            outline: none;
        }

        .btn-filter-apply, .btn-filter-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 42px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-filter-apply {
            padding: 0 20px;
            background: #287b7b;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            border: none;
            box-shadow: 0 4px 12px rgba(40, 123, 123, 0.18);
        }

        .btn-filter-apply:hover {
            background: #206363;
            transform: translateY(-1px);
            color: #ffffff;
        }

        .btn-filter-reset {
            padding: 0 16px;
            background: #f1f6f6;
            color: #486161;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #d0e0e0;
        }

        .btn-filter-reset:hover {
            background: #e3eded;
            color: #172f2f;
        }

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

        /* Modal Pop-up */
        .report-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .report-modal.active {
            display: flex;
            opacity: 1;
        }

        .report-modal-content {
            background: #ffffff;
            border-radius: 20px;
            max-width: 860px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px 32px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
            position: relative;
            transform: translateY(20px) scale(0.98);
            transition: transform 0.25s ease;
        }

        .report-modal.active .report-modal-content {
            transform: translateY(0) scale(1);
        }

        .modal-custom-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modal-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #e8f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #287b7b;
            flex-shrink: 0;
        }

        .modal-title-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modal-badge-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .modal-report-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .modal-period-pill {
            display: inline-flex;
            align-items: center;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            margin-top: 4px;
            width: fit-content;
        }

        .modal-close-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #64748b;
            transition: all 0.2s;
        }

        .modal-close-btn:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .modal-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .modal-stat-card {
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .modal-stat-card.card-1 { background: #f0fdfa; border-color: #ccfbf1; }
        .modal-stat-card.card-2 { background: #f0f9ff; border-color: #e0f2fe; }
        .modal-stat-card.card-3 { background: #faf5ff; border-color: #f3e8ff; }
        .modal-stat-card.card-4 { background: #fffbeb; border-color: #fef3c7; }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .card-1 .stat-icon { background: #ccfbf1; color: #0d9488; }
        .card-2 .stat-icon { background: #e0f2fe; color: #0284c7; }
        .card-3 .stat-icon { background: #f3e8ff; color: #9333ea; }
        .card-4 .stat-icon { background: #fef3c7; color: #d97706; }

        .stat-details {
            display: flex;
            flex-direction: column;
        }

        .stat-num {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-text {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-top: 3px;
        }

        .modal-chart-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
        }

        .modal-chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .modal-chart-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-chart-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .modal-chart-select {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            cursor: pointer;
        }

        .modal-chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .modal-custom-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 12px;
            padding: 12px 18px;
            gap: 12px;
        }

        .modal-footer-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #134e4a;
            font-weight: 500;
        }

        .modal-close-action-btn {
            background: #206363;
            color: #ffffff;
            border: none;
            padding: 8px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close-action-btn:hover {
            background: #174242;
        }

        @media (max-width: 768px) {
            .modal-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .modal-custom-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .modal-close-action-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
<section class="report-page">

    <div class="report-hero">
        <div class="report-hero-content">
            <div class="report-label">
                <span class="label-dot"></span>
                Pusat Laporan
            </div>
            <h1>Laporan Perpustakaan</h1>
            <p>
                Pantau seluruh aktivitas perpustakaan melalui ringkasan
                sirkulasi peminjaman, keterlambatan, koleksi buku, dan
                keanggotaan aktif dalam rentang tanggal fleksibel.
            </p>
        </div>

        <div class="report-hero-action">
            <a href="{{ route('reports.create') }}" class="report-add-btn">
                <span class="add-icon">+</span>
                <span>Tambah Laporan</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="report-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="report-filter-box">
        <form
            method="GET"
            action="{{ route('reports.index') }}"
            id="dateRangeFilterForm"
            class="filter-form-group"
        >
            <div class="date-picker-input-wrapper">
                <span class="calendar-icon">📅</span>
                <input
                    type="text"
                    id="date_range_picker"
                    class="date-range-flatpickr"
                    placeholder="Pilih rentang tanggal..."
                    readonly
                >
            </div>

            <input
                type="hidden"
                name="start_date"
                id="start_date"
                value="{{ $startDateInput ?? $startDate->format('Y-m-d') }}"
            >

            <input
                type="hidden"
                name="end_date"
                id="end_date"
                value="{{ $endDateInput ?? $endDate->format('Y-m-d') }}"
            >

            <button type="submit" class="btn-filter-apply">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Terapkan Filter
            </button>

            <a href="{{ route('reports.index') }}" class="btn-filter-reset" title="Kembalikan ke periode default">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                Reset Filter
            </a>
        </form>

        {{-- Badge periode kanan --}}
        <div class="filter-period-badge">
            <span>🗓️ Periode: <strong>{{ $monthlyPeriodBadge }}</strong></span>
        </div>
    </div>

    <div class="report-section-title">
        <div>
            <span>Ringkasan Aktivitas</span>
            <h2>Laporan Terbaru</h2>
        </div>
    </div>

    <div class="report-grid">
        @forelse ($reports as $report)

            @php
                $reportStart = \Carbon\Carbon::parse($report['tanggal_mulai'])
                    ->locale('id')
                    ->translatedFormat('d M Y');

                $reportEnd = \Carbon\Carbon::parse($report['tanggal_selesai'])
                    ->locale('id')
                    ->translatedFormat('d M Y');

                $reportPeriodLabel = $reportStart . ' - ' . $reportEnd;
                $jenis = $report['jenis'];

                if (str_contains($jenis, 'Peminjaman')) {
                    $type = 'borrow';
                    $category = 'Sirkulasi';
                    $icon = '📖';
                    $valNum = $totalBorrowed ?? 0;
                    $badge = number_format($valNum, 0, ',', '.') . ' Dipinjam';
                    $badgeClass = 'positive';
                    $value = number_format($valNum, 0, ',', '.');
                    $valueLabel = 'peminjaman';
                    $description = 'Rekap transaksi peminjaman dan sirkulasi buku selama periode yang dipilih.';
                    $barsRaw = $borrowChartBarsRaw ?? [];
                    $barsNorm = $borrowChartBarsNorm ?? [];
                    $labels = $borrowChartLabels ?? [];
                } elseif (str_contains($jenis, 'Keterlambatan')) {
                    $type = 'late';
                    $category = 'Monitoring';
                    $icon = '⏰';
                    $valNum = $lateBorrowings ?? 0;
                    $badge = number_format($valNum, 0, ',', '.') . ' Kasus';
                    $badgeClass = $valNum > 0 ? 'warning' : 'neutral';
                    $value = number_format($valNum, 0, ',', '.');
                    $valueLabel = 'kasus aktif';
                    $description = 'Daftar aktivitas buku yang terlambat dikembalikan pada periode yang dipilih.';
                    $barsRaw = $lateChartBarsRaw ?? [];
                    $barsNorm = $lateChartBarsNorm ?? [];
                    $labels = $lateChartLabels ?? [];
                } elseif (str_contains($jenis, 'Koleksi')) {
                    $type = 'collection';
                    $category = 'Koleksi';
                    $icon = '📚';
                    $valNum = $totalBooks ?? 0;
                    $badge = number_format($valNum, 0, ',', '.') . ' Buku';
                    $badgeClass = 'neutral';
                    $value = number_format($valNum, 0, ',', '.');
                    $valueLabel = 'total koleksi';
                    $description = 'Rekap buku yang tercatat dan ditambahkan dalam periode laporan yang dipilih.';
                    $barsRaw = $collectionChartBarsRaw ?? [];
                    $barsNorm = $collectionChartBarsNorm ?? [];
                    $labels = $collectionChartLabels ?? [];
                } else {
                    $type = 'member';
                    $category = 'Keanggotaan';
                    $icon = '👥';
                    $valNum = $activeMembers ?? 0;
                    $badge = number_format($valNum, 0, ',', '.') . ' Aktif';
                    $badgeClass = 'positive';
                    $value = number_format($valNum, 0, ',', '.');
                    $valueLabel = 'anggota aktif';
                    $description = 'Rekap anggota aktif yang tercatat dalam periode laporan yang dipilih.';
                    $barsRaw = $memberChartBarsRaw ?? [];
                    $barsNorm = $memberChartBarsNorm ?? [];
                    $labels = $memberChartLabels ?? [];
                }
            @endphp

            <article
                class="report-card"
                onclick="openReportDetail(this)"
                data-report="{{ $jenis }}"
                data-category="{{ $category }}"
                data-icon="{{ $icon }}"
                data-value="{{ $value }}"
                data-value-label="{{ $valueLabel }}"
                data-period="{{ $periodLabel }}"
                data-start-date="{{ $report['tanggal_mulai'] }}"
                data-end-date="{{ $report['tanggal_selesai'] }}"
                data-labels="{{ implode('|', $labels) }}"
                data-bars="{{ implode('|', $barsRaw) }}"
            >
                <div class="report-card-top">
                    <div class="report-card-icon {{ $type }}">
                        {{ $icon }}
                    </div>

                    <div class="report-card-info">
                        <span class="report-card-category">{{ $category }}</span>
                        <h3>{{ $jenis }}</h3>
                    </div>

                    <span class="report-badge {{ $badgeClass }}">
                        {{ $badge }}
                    </span>
                </div>

                <p class="report-description">
                    {{ $description }}
                </p>

                <div class="report-chart-wrapper">
                    <div class="chart-value">
                        <strong>{{ $value }}</strong>
                        <span>{{ $valueLabel }}</span>
                    </div>

                    <div class="report-chart">
                        <div class="chart-line"></div>
                        <div class="chart-line"></div>
                        <div class="chart-line"></div>

                        <div class="chart-bars">
                            @foreach ($barsNorm as $index => $bar)
                                <div class="chart-column {{ $index === count($barsNorm) - 1 ? 'active' : '' }}">
                                    <div class="chart-bar" style="height: {{ $bar }}%;"></div>
                                    <span>{{ $labels[$index] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="report-card-footer" onclick="event.stopPropagation()">
                    <span class="report-updated">
                        ● Periode: {{ $reportPeriodLabel }}
                    </span>

                    <div class="report-actions" onclick="event.stopPropagation()">
                        <a href="{{ route('reports.edit', $report['id']) }}" class="report-detail-btn">
                            Edit
                        </a>

                        <button type="button" class="report-download-btn" onclick="downloadReport(this)">
                            Unduh
                        </button>
                    </div>
                </div>
            </article>

        @empty
            <div class="report-empty">
                <strong>Belum ada laporan</strong>
                <span>Klik "Tambah Laporan" untuk membuat laporan baru.</span>
            </div>
        @endforelse
    </div>
</section>

{{-- MODAL DETAIL --}}
<div id="reportDetailModal" class="report-modal" onclick="closeReportDetail(event)">
    <div class="report-modal-content" onclick="event.stopPropagation()">
        <div class="modal-custom-header">
            <div class="modal-header-left">
                <div class="modal-icon-box" id="modalIcon">📖</div>
                <div class="modal-title-info">
                    <span class="modal-badge-label">DETAIL LAPORAN</span>
                    <h2 class="modal-report-title" id="modalReportTitle">Laporan</h2>
                    <span class="modal-period-pill" id="modalReportPeriod">Periode</span>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeReportDetail()" title="Tutup">✕</button>
        </div>

        <div class="modal-stats-grid">
            <div class="modal-stat-card card-1">
                <div class="stat-icon" id="statIcon1">📖</div>
                <div class="stat-details">
                    <span class="stat-num" id="statTotalVal">0</span>
                    <span class="stat-text" id="statTotalLabel">Total Data</span>
                </div>
            </div>

            <div class="modal-stat-card card-2">
                <div class="stat-icon">📅</div>
                <div class="stat-details">
                    <span class="stat-num" id="statActiveDays">0</span>
                    <span class="stat-text">Hari Aktif</span>
                </div>
            </div>

            <div class="modal-stat-card card-3">
                <div class="stat-icon">📈</div>
                <div class="stat-details">
                    <span class="stat-num" id="statPeakVal">0</span>
                    <span class="stat-text" id="statPeakLabel">Tertinggi</span>
                </div>
            </div>

            <div class="modal-stat-card card-4">
                <div class="stat-icon">⏱️</div>
                <div class="stat-details">
                    <span class="stat-num" id="statAvgVal">0,00</span>
                    <span class="stat-text">Rata-rata per Hari</span>
                </div>
            </div>
        </div>

        <div class="modal-chart-section">
            <div class="modal-chart-header">
                <div>
                    <div class="modal-chart-title">
                        <span>📊</span>
                        <span id="modalChartHeading">Grafik Laporan</span>
                    </div>
                    <div class="modal-chart-subtitle">Jumlah aktivitas berdasarkan rentang periode yang dipilih</div>
                </div>
                <select class="modal-chart-select">
                    <option>Tampilkan per rentang</option>
                </select>
            </div>

            <div class="modal-chart-container">
                <canvas id="popupComboChart"></canvas>
            </div>
        </div>

        <div class="modal-custom-footer">
            <div class="modal-footer-info">
                <span>ℹ️</span>
                <span>Data laporan berdasarkan periode <strong id="modalFooterPeriodText">{{ $periodLabel }}</strong>.</span>
            </div>
            <button type="button" class="modal-close-action-btn" onclick="closeReportDetail()">Tutup</button>
        </div>
    </div>
</div>

<script id="borrowingsReportData" type="application/json">
    {!! json_encode($borrowingsExportData ?? []) !!}
</script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let modalChartInstance = null;

        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const rangePicker = document.getElementById('date_range_picker');

            flatpickr(rangePicker, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                locale: 'id',
                defaultDate: [startDateInput.value, endDateInput.value],
                showMonths: 2,

                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        startDateInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        endDateInput.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                    } else if (selectedDates.length === 1) {
                        startDateInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        endDateInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                    }
                }
            });
        });

        function openReportDetail(card) {
            const title = card.dataset.report || 'Laporan';
            const icon = card.dataset.icon || '📖';
            const value = card.dataset.value || '0';
            const valueLabel = card.dataset.valueLabel || 'Data';
            const period = card.dataset.period || 'Periode';

            const rawLabels = card.dataset.labels ? card.dataset.labels.split('|') : [];
            const rawBars = card.dataset.bars ? card.dataset.bars.split('|').map(v => parseInt(v) || 0) : [];

            document.getElementById('modalIcon').textContent = icon;
            document.getElementById('modalReportTitle').textContent = title;
            document.getElementById('modalReportPeriod').textContent = period;
            document.getElementById('modalFooterPeriodText').textContent = period;
            document.getElementById('modalChartHeading').textContent = `Grafik ${title}`;

            document.getElementById('statIcon1').textContent = icon;
            document.getElementById('statTotalVal').textContent = value;
            document.getElementById('statTotalLabel').textContent = `Total ${valueLabel}`;

            const totalNumeric = parseInt(value.replace(/\./g, '')) || 0;
            const activeCount = rawBars.filter(b => b > 0).length || (totalNumeric > 0 ? 1 : 0);
            const peakVal = rawBars.length > 0 ? Math.max(...rawBars) : totalNumeric;
            const peakIndex = rawBars.indexOf(peakVal);
            const peakLabelDate = (peakIndex !== -1 && rawLabels[peakIndex]) ? ` (${rawLabels[peakIndex]})` : '';
            const avgVal = rawBars.length > 0 ? (totalNumeric / rawBars.length) : totalNumeric;

            document.getElementById('statActiveDays').textContent = activeCount;
            document.getElementById('statPeakVal').textContent = peakVal;
            document.getElementById('statPeakLabel').textContent = `Tertinggi${peakLabelDate}`;
            document.getElementById('statAvgVal').textContent = avgVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const modal = document.getElementById('reportDetailModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            renderModalComboChart(title, rawLabels, rawBars);
        }

        function closeReportDetail() {
            const modal = document.getElementById('reportDetailModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function renderModalComboChart(title, labels, data) {
            const ctx = document.getElementById('popupComboChart');
            if (!ctx) return;

            if (modalChartInstance) {
                modalChartInstance.destroy();
            }

            modalChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Tren ' + title.replace('Laporan ', ''),
                            data: data,
                            borderColor: '#176b6b',
                            backgroundColor: 'transparent',
                            borderWidth: 2.2,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#176b6b',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            order: 1
                        },
                        {
                            type: 'bar',
                            label: title.replace('Laporan ', ''),
                            data: data,
                            backgroundColor: '#287b7b',
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 32,
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11, weight: '600' },
                                color: '#475569',
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#64748b',
                                font: { size: 11 }
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { size: 11 } }
                        }
                    }
                }
            });
        }

        function downloadReport(button) {
            const card = button.closest('.report-card');
            const reportName = card.dataset.report || 'Laporan Perpustakaan';
            const period = card.dataset.period || 'Periode Terpilih';
            const statValue = card.dataset.value || '0';
            const statLabel = card.dataset.valueLabel || 'data';
            const startVal = card.dataset.startDate || 'start';
            const endVal = card.dataset.endDate || 'end';

            const sanitizedTitle = reportName.replace(/[^a-zA-Z0-9_-]/g, '_');
            const fileName = `${sanitizedTitle}_${startVal}_sd_${endVal}.pdf`;

            let tableData = [];
            const dataScript = document.getElementById('borrowingsReportData');
            if (dataScript) {
                try {
                    tableData = JSON.parse(dataScript.textContent);
                } catch (error) {
                    console.error('Gagal membaca data laporan:', error);
                }
            }

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

                doc.setFillColor(40, 123, 123);
                doc.rect(0, 0, 210, 24, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(14);
                doc.text('PERPUSTAKAAN TIGA SERANGKAI', 14, 11);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text('Sistem Informasi Manajemen Laporan Perpustakaan', 14, 18);

                doc.setTextColor(23, 47, 47);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(16);
                doc.text(reportName.toUpperCase(), 14, 35);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.setTextColor(80, 99, 99);
                doc.text(`Periode: ${period}`, 14, 42);
                doc.text(`Tanggal Unduh: ${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`, 14, 47);

                doc.setFillColor(243, 248, 248);
                doc.roundedRect(14, 52, 182, 14, 3, 3, 'F');
                doc.setDrawColor(210, 230, 230);
                doc.roundedRect(14, 52, 182, 14, 3, 3, 'D');

                doc.setTextColor(40, 123, 123);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(`TOTAL REKAPITULASI: ${statValue} ${statLabel.toUpperCase()}`, 19, 61);

                const headers = [['No', 'Nama Anggota', 'Kode', 'Buku yang Dipinjam', 'Tgl Pinjam', 'Jatuh Tempo', 'Status']];
                const rows = tableData.map(function (item) {
                    return [item.no, item.member_name, item.member_code, item.books, item.borrowed_at, item.due_at, item.status];
                });

                doc.autoTable({
                    head: headers,
                    body: rows.length > 0 ? rows : [['-', 'Tidak ada data transaksi pada periode ini', '-', '-', '-', '-', '-']],
                    startY: 71,
                    theme: 'striped',
                    headStyles: { fillColor: [40, 123, 123], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 9, halign: 'left' },
                    bodyStyles: { fontSize: 8.5, textColor: [40, 50, 50] },
                    columnStyles: {
                        0: { cellWidth: 10, halign: 'center' },
                        1: { cellWidth: 36 },
                        2: { cellWidth: 20 },
                        3: { cellWidth: 54 },
                        4: { cellWidth: 22 },
                        5: { cellWidth: 22 },
                        6: { cellWidth: 18, halign: 'center' }
                    },
                    styles: { cellPadding: 3, valign: 'middle' },
                    alternateRowStyles: { fillColor: [247, 251, 251] },
                    didDrawPage: function (data) {
                        const pageCount = doc.internal.getNumberOfPages();
                        doc.setFontSize(8);
                        doc.setTextColor(150);
                        doc.text('Dokumen ini dicetak otomatis oleh Sistem Perpustakaan Tiga Serangkai.', 14, 290);
                        doc.text(`Halaman ${data.pageNumber} dari ${pageCount}`, 196, 290, { align: 'right' });
                    }
                });

                doc.save(fileName);
                Swal.fire({
                    icon: 'success',
                    title: 'Unduh Selesai!',
                    html: `File <strong>${fileName}</strong> berhasil diunduh.`,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#287b7b',
                    timer: 4000,
                    timerProgressBar: true
                });
            } catch (error) {
                console.error('Error membuat PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh',
                    text: 'Terjadi kesalahan saat membuat file PDF.',
                    confirmButtonColor: '#287b7b'
                });
            }
        }
    </script>
@endpush