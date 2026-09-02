@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

<section class="dashboard-page">

    {{-- =====================================================
         HEADER DASHBOARD
    ====================================================== --}}

    <div class="dashboard-header">

        <div class="dashboard-welcome">
            Selamat datang, Admin! <span>👋</span>
        </div>

        <h1>Dashboard</h1>

        <p>Pantau aktivitas dan statistik perpustakaan secara real-time</p>

    </div>


    {{-- =====================================================
         STATISTIK CARD
    ====================================================== --}}

    <div class="dashboard-stats">

        {{-- TOTAL BUKU --}}
        <div class="stat-card stat-books">

            <div class="stat-card-top">

                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 5.5C4 4.672 4.672 4 5.5 4H19v15H5.5A1.5 1.5 0 0 1 4 17.5v-12Z"
                            stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M8 4v15M8 17.5c0 .828-.672 1.5-1.5 1.5H19"
                            stroke="currentColor"
                            stroke-width="1.8" />
                    </svg>
                </div>

                <div class="stat-content">

                    <div class="stat-title">
                        TOTAL KOLEKSI BUKU
                    </div>

                    <div class="stat-value">
                        {{ number_format($totalBooks, 0, ',', '.') }}
                    </div>

                    <div class="stat-description">
                        Koleksi buku
                    </div>

                </div>

            </div>

            <div class="stat-wave"></div>

        </div>


        {{-- SEDANG DIPINJAM --}}
        <div class="stat-card stat-borrowed">

            <div class="stat-card-top">

                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M6 4.5A2.5 2.5 0 0 1 8.5 2H19v17H8.5A2.5 2.5 0 0 0 6 21.5v-17Z"
                            stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M6 4.5v14M10 6h5M10 9h5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round" />
                    </svg>
                </div>

                <div class="stat-content">

                    <div class="stat-title">
                        SEDANG DIPINJAM
                    </div>

                    <div class="stat-value">
                        {{ number_format($borrowedBooks, 0, ',', '.') }}
                    </div>

                    <div class="stat-description">
                        Buku sedang dipinjam
                    </div>

                </div>

            </div>

            <div class="stat-wave"></div>

        </div>


        {{-- ANGGOTA AKTIF --}}
        <div class="stat-card stat-members">

            <div class="stat-card-top">

                <div class="stat-icon">

                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round" />
                        <circle cx="10" cy="7.5" r="3.5"
                            stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M16 11a3 3 0 1 0 0-6M17 14a4 4 0 0 1 3 4v2"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round" />
                    </svg>

                </div>

                <div class="stat-content">

                    <div class="stat-title">
                        ANGGOTA AKTIF
                    </div>

                    <div class="stat-value">
                        {{ number_format($activeMembers, 0, ',', '.') }}
                    </div>

                    <div class="stat-description">
                        Anggota terdaftar aktif
                    </div>

                </div>

            </div>

            <div class="stat-wave"></div>

        </div>


        {{-- KETERLAMBATAN --}}
        <div class="stat-card stat-late">

            <div class="stat-card-top">

                <div class="stat-icon">

                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="8.5"
                            stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M12 7v5l3 2"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round" />
                    </svg>

                </div>

                <div class="stat-content">

                    <div class="stat-title">
                        KETERLAMBATAN
                    </div>

                    <div class="stat-value">
                        {{ number_format($lateBorrowings, 0, ',', '.') }}
                    </div>

                    <div class="stat-description">
                        Peminjaman melewati jatuh tempo
                    </div>

                </div>

            </div>

            <div class="stat-wave"></div>

        </div>

    </div>


    {{-- =====================================================
         DASHBOARD MIDDLE
    ====================================================== --}}

    <div class="dashboard-middle">

        {{-- =================================================
             STATISTIK PEMINJAMAN
        ================================================== --}}

        <div class="dashboard-panel borrowing-panel">

            <div class="panel-header">

                <div class="panel-title-wrapper">

                    <div class="panel-icon chart-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 19V5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round" />
                            <path d="M4 19h16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round" />
                            <path d="M7 15l3-4 3 2 5-7"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>

                    <div>
                        <h2>Statistik Peminjaman</h2>
                        <span>Bulan ini</span>
                    </div>

                </div>

            </div>


            <div class="chart-area">

                <div class="chart-y">
                    <span>{{ $max7 }}</span>
                    <span>{{ round($max7 * 0.8) }}</span>
                    <span>{{ round($max7 * 0.6) }}</span>
                    <span>{{ round($max7 * 0.4) }}</span>
                    <span>{{ round($max7 * 0.2) }}</span>
                    <span>0</span>
                </div>

                <div class="chart-content">

                    <div class="chart-grid">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="chart-bars">

                        @forelse($chart7Days as $item)
                        <div class="chart-bar-item">
                            <div class="chart-bar-tooltip">
                                {{ $item['label'] }}: {{ $item['count'] }} dipinjam
                            </div>
                            <div class="chart-bar" style="height: {{ $item['height'] }}%;"></div>
                            <span>{{ $item['label'] }}</span>
                        </div>
                        @empty
                        <div class="empty-small">
                            Belum ada data peminjaman bulan ini.
                        </div>
                        @endforelse

                    </div>

                </div>

            </div>

            <div class="chart-caption">
                Grafik aktivitas peminjaman buku (bulan ini)
            </div>

        </div>


        {{-- =================================================
             BUKU TERPOPULER
        ================================================== --}}

        <div class="dashboard-panel popular-panel">

            <div class="panel-header">

                <div class="panel-title-wrapper">

                    <div class="panel-icon book-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H19v17H7.5A2.5 2.5 0 0 0 5 21.5v-17Z"
                                stroke="currentColor"
                                stroke-width="1.8" />
                            <path d="M5 4.5v14"
                                stroke="currentColor"
                                stroke-width="1.8" />
                        </svg>
                    </div>

                    <div>
                        <h2>Buku Terpopuler</h2>
                        <span>Berdasarkan jumlah reservasi</span>
                    </div>

                </div>

            </div>


            <div class="popular-list">

                @php
                $popularBooks = $reservations
                ->filter(fn($reservation) => $reservation->book)
                ->groupBy('book_id')
                ->map(function ($items) {
                return [
                'title' => $items->first()->book->title ?? '-',
                'total' => $items->count()
                ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();
                @endphp


                @forelse($popularBooks as $index => $book)

                <div class="popular-item">

                    <div class="popular-number">
                        {{ $index + 1 }}
                    </div>

                    <div class="popular-name">
                        {{ $book['title'] }}
                    </div>

                    <div class="popular-total">
                        {{ $book['total'] }} kali
                    </div>

                </div>

                @empty

                <div class="empty-small">
                    Belum ada data buku.
                </div>

                @endforelse

            </div>


            <a href="{{ route('reservations.index') }}" class="panel-link">
                Lihat semua buku

            </a>

        </div>


        {{-- =================================================
     AKTIVITAS TERBARU
================================================== --}}

        <div class="dashboard-panel activity-panel">

            <div class="panel-header">

                <div class="panel-title-wrapper">

                    <div class="panel-icon activity-icon">

                        <svg viewBox="0 0 24 24" fill="none">

                            <path
                                d="M4 12a8 8 0 1 0 16 0"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round" />

                            <path
                                d="M12 7v5l3 2"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round" />

                        </svg>

                    </div>

                    <div>

                        <h2>
                            Aktivitas Terbaru
                        </h2>

                        <span>
                            Update aktivitas perpustakaan
                        </span>

                    </div>

                </div>

            </div>


            <div class="activity-list">

                @forelse($activities as $activity)

                <div class="activity-item">

                    {{-- ICON --}}
                    <div class="activity-item-icon">

                        {{ $activity['icon'] }}

                    </div>


                    {{-- INFORMASI --}}
                    <div class="activity-info">

                        <strong>
                            {{ $activity['title'] }}
                        </strong>

                        <span>
                            {{ $activity['description'] }}
                        </span>

                    </div>


                    {{-- WAKTU --}}
                    <time>

                        {{ $activity['created_at']
                        ? $activity['created_at']
                            ->locale('id')
                            ->diffForHumans()
                        : '-'
                    }}

                    </time>

                </div>

                @empty

                <div class="empty-small">

                    Belum ada aktivitas.

                </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- =====================================================
         RESERVASI TERBARU
    ====================================================== --}}

    <div class="dashboard-panel reservation-panel">

        <div class="reservation-header">

            <div class="panel-title-wrapper">

                <div class="panel-icon reservation-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="5" width="16" height="15" rx="2"
                            stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M8 3v4M16 3v4M4 10h16"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round" />
                    </svg>
                </div>

                <div>
                    <h2>Reservasi Terbaru</h2>
                    <span>Daftar reservasi terbaru</span>
                </div>

            </div>


            <a href="{{ route('reservations.index') }}"
                class="reservation-see-all">

                Lihat semua reservasi


            </a>

        </div>


        <div class="dashboard-table">

            <div class="dashboard-table-head">

                <div>Waktu</div>
                <div>Anggota</div>
                <div>Buku</div>
                <div>Aktivitas</div>
                <div>Status</div>

            </div>


            @forelse($reservations as $reservation)

            <div class="dashboard-table-row">

                <div>
                    {{ $reservation->created_at
                            ? $reservation->created_at->format('H.i')
                            : '-' }}
                </div>

                <div>
                    {{ $reservation->member->name ?? '-' }}
                </div>

                <div>
                    {{ $reservation->book->title ?? '-' }}
                </div>

                <div>
                    <span class="activity-label">
                        Reservasi
                    </span>
                </div>

                <div>

                    @if($reservation->display_status === 'menunggu')

                    <span class="dashboard-status waiting">
                        Menunggu
                    </span>

                    @elseif($reservation->display_status === 'disetujui')

                    <span class="dashboard-status approved">
                        Disetujui
                    </span>

                    @elseif($reservation->display_status === 'dipinjam')

                    <span class="dashboard-status borrowed">
                        Dipinjam
                    </span>

                    @elseif($reservation->display_status === 'selesai')

                    <span class="dashboard-status completed">
                        Selesai
                    </span>

                    @elseif(
                    $reservation->display_status === 'ditolak' ||
                    $reservation->display_status === 'dibatalkan'
                    )

                    <span class="dashboard-status rejected">
                        {{ ucfirst($reservation->display_status) }}
                    </span>

                    @elseif(
                    str_starts_with(
                    $reservation->display_status ?? '',
                    'terlambat'
                    )
                    )

                    <span class="dashboard-status rejected">
                        {{ ucfirst($reservation->display_status) }}
                    </span>

                    @else

                    <span class="dashboard-status">
                        {{ ucfirst($reservation->display_status ?? '-') }}
                    </span>

                    @endif

                </div>

            </div>

            @empty

            <div class="dashboard-table-row">

                <div style="grid-column:1/-1;text-align:center;">
                    Belum ada reservasi.
                </div>

            </div>

            @endforelse

        </div>

    </div>

</section>

@endsection