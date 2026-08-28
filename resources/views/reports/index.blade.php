@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@push('styles')
    {{-- Flatpickr CSS & Theme --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    
    <style>
        /* Modern Filter Bar Styles */
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
            box-shadow: 0 4px 12px rgba(40, 123, 123, 0.18);
        }

        .btn-filter-apply:hover {
            background: #206363;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(40, 123, 123, 0.25);
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

        /* Flatpickr Customization to match theme */
        .flatpickr-calendar {
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(40, 123, 123, 0.18) !important;
            border: 1px solid #d5e6e6 !important;
            font-family: inherit !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #287b7b !important;
            border-color: #287b7b !important;
            color: #fff !important;
        }
        .flatpickr-day.inRange {
            background: #d8eded !important;
            border-color: #d8eded !important;
            color: #174242 !important;
        }
    </style>
@endpush

@section('content')

<section class="report-page">

    {{-- =========================================================
         HERO SECTION
    ========================================================== --}}
    <div class="report-hero">
        <div class="report-hero-content">
            <div class="report-label">
                <span class="label-dot"></span>
                Pusat Laporan
            </div>
            <h1>Laporan Perpustakaan</h1>
            <p>
                Pantau seluruh aktivitas perpustakaan melalui ringkasan sirkulasi peminjaman, keterlambatan, koleksi buku, dan keanggotaan aktif dalam rentang tanggal fleksibel.
            </p>
        </div>

        <div class="report-hero-action">
            <a href="{{ route('reports.create') }}" class="report-add-btn">
                <span class="add-icon">+</span>
                <span>Tambah Laporan</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="report-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- =========================================================
         FILTER KALENDER RENTANG TANGGAL (DATE RANGE PICKER)
    ========================================================== --}}
    <div class="report-filter-box">
        <form method="GET" action="{{ route('reports.index') }}" id="dateRangeFilterForm" class="filter-form-group">
            <div class="date-picker-input-wrapper">
                <span class="calendar-icon">📅</span>
                <input type="text" 
                       id="date_range_picker" 
                       class="date-range-flatpickr" 
                       placeholder="Pilih rentang tanggal (klik 2 tanggal)..." 
                       readonly>
            </div>

            {{-- Hidden input parameters untuk dikirimkan via GET ke Controller --}}
            <input type="hidden" name="start_date" id="start_date" value="{{ $startDateInput ?? $startDate->format('Y-m-d') }}">
            <input type="hidden" name="end_date" id="end_date" value="{{ $endDateInput ?? $endDate->format('Y-m-d') }}">

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

        <div class="filter-period-badge">
            <span>🗓️ Periode: <strong>{{ $periodLabel }}</strong></span>
        </div>
    </div>

    {{-- =========================================================
         SECTION TITLE & KARTU GRAFIK KATEGORI LAPORAN
    ========================================================== --}}
    <div class="report-section-title">
        <div>
            <span>Ringkasan Aktivitas</span>
            <h2>Laporan Terbaru</h2>
        </div>
    </div>

    <div class="report-grid">
        @forelse($reports as $report)
            @php
                $jenis = $report['jenis'];

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
                    $bars = $lateChartBars ?? [];
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
                    $description = 'Rekap buku yang tercatat dalam periode laporan yang dipilih.';
                    $bars = $collectionChartBars ?? [];
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
                    $bars = $memberChartBars ?? [];
                    $labels = $memberChartLabels ?? [];
                }
            @endphp

            <article class="report-card"
                     onclick="openReportDetail(this)"
                     data-report="{{ $report['jenis'] }}"
                     data-value="{{ $value }}"
                     data-value-label="{{ $valueLabel }}"
                     data-period="{{ $periodLabel }}"
                     data-labels="{{ implode('|', $labels) }}"
                     data-bars="{{ implode('|', $bars) }}">

                <div class="report-card-top">
                    <div class="report-card-icon {{ $type }}">
                        {{ $icon }}
                    </div>

                    <div class="report-card-info">
                        <span class="report-card-category">{{ $category }}</span>
                        <h3>{{ $report['jenis'] }}</h3>
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
                            @foreach($bars as $index => $bar)
                                <div class="chart-column {{ $index === count($bars) - 1 ? 'active' : '' }}">
                                    <div class="chart-bar" style="height: {{ $bar }}%;"></div>
                                    <span>{{ $labels[$index] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="report-card-footer" onclick="event.stopPropagation()">
                    <span class="report-updated">
                        ● Periode: {{ $periodLabel }}
                    </span>

                    <div class="report-actions" onclick="event.stopPropagation()">
                        <a href="{{ route('reports.edit', $report['id']) }}" class="report-detail-btn">
                            Edit
                        </a>

                        <select class="report-format-select" aria-label="Pilih format laporan" onclick="event.stopPropagation()">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>

                        <div class="report-download" onclick="event.stopPropagation()">
                            <button type="button" class="report-download-btn" onclick="downloadReport(this)">
                                Unduh
                            </button>
                        </div>
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

{{-- =========================================================
     MODAL DETAIL LAPORAN
========================================================== --}}
<div id="reportDetailModal" class="report-modal" onclick="closeReportDetail(event)">
    <div class="report-modal-content" onclick="event.stopPropagation()">
        <div class="report-modal-header">
            <div>
                <span class="report-modal-label">DETAIL LAPORAN</span>
                <h2 id="modalReportTitle">Laporan</h2>
            </div>
            <button type="button" class="report-modal-close" onclick="closeReportDetail()">×</button>
        </div>

        <div class="report-modal-summary">
            <div class="report-modal-value">
                <strong id="modalReportValue">0</strong>
                <span id="modalReportValueLabel">data</span>
            </div>

            <div class="report-modal-period" id="modalReportPeriod">
                Periode: {{ $periodLabel }}
            </div>
        </div>

        <div class="report-detail-chart">
            <div class="detail-chart-lines">
                <span></span><span></span><span></span><span></span>
            </div>
            <div id="modalChartBars" class="detail-chart-bars"></div>
        </div>

        <div class="report-modal-footer">
            <span>Data laporan berdasarkan periode <strong>{{ $periodLabel }}</strong>.</span>
            <button type="button" onclick="closeReportDetail()">Tutup</button>
        </div>
    </div>
</div>

{{-- Data JSON Transaksi Peminjaman untuk Ekspor PDF/Excel --}}
<script id="borrowingsReportData" type="application/json">
    {!! json_encode($borrowingsExportData ?? []) !!}
</script>

@endsection

@push('scripts')
    {{-- Flatpickr Core & Bahasa Indonesia --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="{{ asset('js/openReportDetail.js') }}"></script>

    {{-- jsPDF & jsPDF-AutoTable untuk Download PDF Otomatis --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    {{-- SweetAlert2 untuk Notifikasi Modern --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const rangePicker = document.getElementById('date_range_picker');

            const currentStart = startDateInput.value;
            const currentEnd = endDateInput.value;

            // Inisialisasi Flatpickr Range Mode yang Bebas & Fleksibel
            const fp = flatpickr(rangePicker, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                locale: 'id',
                defaultDate: [currentStart, currentEnd],
                showMonths: 2, // Tampilkan 2 bulan untuk kenyamanan memilih rentang antar-bulan
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                        const end = instance.formatDate(selectedDates[1], 'Y-m-d');
                        startDateInput.value = start;
                        endDateInput.value = end;
                    } else if (selectedDates.length === 1) {
                        const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                        startDateInput.value = start;
                        endDateInput.value = start;
                    }
                }
            });
        });

        // =========================================================
        // HANDLER UNDUH LAPORAN (OTOMATIS DOWNLOAD PDF & NOTIFIKASI)
        // =========================================================
        function downloadReport(button) {
            const card = button.closest('.report-card');
            const formatSelect = card.querySelector('.report-format-select');
            const format = formatSelect ? formatSelect.value : 'pdf';
            const reportName = card.dataset.report || 'Laporan Perpustakaan';
            const period = card.dataset.period || 'Periode Terpilih';
            const statValue = card.dataset.value || '0';
            const statLabel = card.dataset.valueLabel || 'data';

            // Ambil data tabel JSON
            let tableData = [];
            const dataScript = document.getElementById('borrowingsReportData');
            if (dataScript) {
                try {
                    tableData = JSON.parse(dataScript.textContent);
                } catch(e) {
                    console.error("Gagal parse data laporan", e);
                }
            }

            // Sanitasi nama file
            const sanitizedTitle = reportName.replace(/[^a-zA-Z0-9_-]/g, '_');
            const startVal = document.getElementById('start_date').value || 'start';
            const endVal = document.getElementById('end_date').value || 'end';
            const fileName = `${sanitizedTitle}_${startVal}_sd_${endVal}.${format === 'excel' ? 'csv' : 'pdf'}`;

            if (format === 'excel') {
                // Ekspor ke CSV / Excel
                exportToCSV(tableData, fileName);
                showDownloadSuccessAlert(fileName);
                return;
            }

            // EKSPOR PDF MENGGUNAKAN JSPDF & AUTOTABLE
            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                // Header Banner
                doc.setFillColor(40, 123, 123); // #287B7B
                doc.rect(0, 0, 210, 24, 'F');

                doc.setTextColor(255, 255, 255);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(14);
                doc.text('PERPUSTAKAAN TIGA SERANGKAI', 14, 11);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text('Sistem Informasi Manajemen Laporan Perpustakaan', 14, 18);

                // Judul Laporan & Periode
                doc.setTextColor(23, 47, 47);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(16);
                doc.text(reportName.toUpperCase(), 14, 35);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.setTextColor(80, 99, 99);
                doc.text(`Periode: ${period}`, 14, 42);
                doc.text(`Tanggal Unduh: ${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`, 14, 47);

                // Kotak Ringkasan Statistik
                doc.setFillColor(243, 248, 248);
                doc.roundedRect(14, 52, 182, 14, 3, 3, 'F');
                doc.setDrawColor(210, 230, 230);
                doc.roundedRect(14, 52, 182, 14, 3, 3, 'D');

                doc.setTextColor(40, 123, 123);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(`TOTAL REKAPITULASI: ${statValue} ${statLabel.toUpperCase()}`, 19, 61);

                // Tabel Data Transaksi
                const headers = [['No', 'Nama Anggota', 'Kode', 'Buku yang Dipinjam', 'Tgl Pinjam', 'Jatuh Tempo', 'Status']];
                const rows = tableData.map(item => [
                    item.no,
                    item.member_name,
                    item.member_code,
                    item.books,
                    item.borrowed_at,
                    item.due_at,
                    item.status
                ]);

                doc.autoTable({
                    head: headers,
                    body: rows.length > 0 ? rows : [['-', 'Tidak ada data transaksi pada periode ini', '-', '-', '-', '-', '-']],
                    startY: 71,
                    theme: 'striped',
                    headStyles: {
                        fillColor: [40, 123, 123],
                        textColor: [255, 255, 255],
                        fontStyle: 'bold',
                        fontSize: 9,
                        halign: 'left'
                    },
                    bodyStyles: {
                        fontSize: 8.5,
                        textColor: [40, 50, 50]
                    },
                    columnStyles: {
                        0: { cellWidth: 10, halign: 'center' },
                        1: { cellWidth: 36 },
                        2: { cellWidth: 20 },
                        3: { cellWidth: 54 },
                        4: { cellWidth: 22 },
                        5: { cellWidth: 22 },
                        6: { cellWidth: 18, halign: 'center' }
                    },
                    styles: {
                        cellPadding: 3,
                        valign: 'middle'
                    },
                    alternateRowStyles: {
                        fillColor: [247, 251, 251]
                    },
                    didDrawPage: function (data) {
                        // Footer Dokumen
                        const pageCount = doc.internal.getNumberOfPages();
                        doc.setFontSize(8);
                        doc.setTextColor(150);
                        doc.text('Dokumen ini dicetak otomatis oleh Sistem Perpustakaan Tiga Serangkai.', 14, 290);
                        doc.text(`Halaman ${data.pageNumber} dari ${pageCount}`, 196, 290, { align: 'right' });
                    }
                });

                // Otomatis Unduh PDF ke Device
                doc.save(fileName);

                // Tampilkan Notifikasi Sukses
                showDownloadSuccessAlert(fileName);

            } catch (err) {
                console.error("Error generating PDF:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh',
                    text: 'Terjadi kesalahan saat membuat file PDF. Silakan coba kembali.',
                    confirmButtonColor: '#287b7b'
                });
            }
        }

        // Fungsi Download Format CSV / Excel
        function exportToCSV(data, fileName) {
            if (!data || data.length === 0) {
                data = [{ No: '-', Anggota: 'Tidak ada data', Kode: '-', Buku: '-', 'Tgl Pinjam': '-', 'Jatuh Tempo': '-', Status: '-' }];
            }

            const header = ['No', 'Nama Anggota', 'Kode Anggota', 'Buku Dipinjam', 'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status'];
            const csvRows = [
                header.join(','),
                ...data.map(item => [
                    item.no,
                    `"${item.member_name.replace(/"/g, '""')}"`,
                    `"${item.member_code}"`,
                    `"${item.books.replace(/"/g, '""')}"`,
                    `"${item.borrowed_at}"`,
                    `"${item.due_at}"`,
                    `"${item.returned_at}"`,
                    `"${item.status}"`
                ].join(','))
            ];

            const blob = new Blob(["\uFEFF" + csvRows.join("\n")], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", fileName);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Tampilkan Alert Sukses Mengunduh
        function showDownloadSuccessAlert(fileName) {
            Swal.fire({
                icon: 'success',
                title: 'Unduh Selesai!',
                html: `File <strong>${fileName}</strong> telah berhasil diunduh dan tersimpan di perangkat Anda.`,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#287b7b',
                timer: 4000,
                timerProgressBar: true
            });
        }
    </script>
@endpush