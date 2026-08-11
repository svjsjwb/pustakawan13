@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

<section class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <span>Ringkasan Perpustakaan</span>
        <h1>Dashboard</h1>
    </div>


    {{-- STATISTIK --}}
    <div class="dashboard-stats">

        {{-- TOTAL BUKU --}}
        <div class="stat-card">
            <div class="stat-icon"></div>

            <div class="stat-title">
                TOTAL KOLEKSI BUKU
            </div>

            <div class="stat-value">
                {{ number_format($totalBooks, 0, ',', '.') }}
            </div>

            <div class="stat-change">
                Koleksi buku
            </div>
        </div>


        {{-- SEDANG DIPINJAM --}}
        <div class="stat-card">
            <div class="stat-icon"></div>

            <div class="stat-title">
                SEDANG DIPINJAM
            </div>

            <div class="stat-value">
                {{ number_format($borrowedBooks, 0, ',', '.') }}
            </div>

            <div class="stat-change">
                Buku sedang dipinjam
            </div>
        </div>


        {{-- ANGGOTA AKTIF --}}
        <div class="stat-card">
            <div class="stat-icon"></div>

            <div class="stat-title">
                ANGGOTA AKTIF
            </div>

            <div class="stat-value">
                {{ number_format($activeMembers, 0, ',', '.') }}
            </div>

            <div class="stat-change">
                Anggota terdaftar aktif
            </div>
        </div>


        {{-- KETERLAMBATAN --}}
        <div class="stat-card">
            <div class="stat-icon"></div>

            <div class="stat-title">
                KETERLAMBATAN
            </div>

            <div class="stat-value">
                {{ number_format($lateBorrowings, 0, ',', '.') }}
            </div>

            <div class="stat-change">
                Peminjaman melewati jatuh tempo
            </div>
        </div>

    </div>


    {{-- RESERVASI / AKTIVITAS --}}
    <div class="dashboard-section">

        <h2>Reservasi</h2>


        <div class="dashboard-table">

            {{-- HEADER --}}
            <div class="dashboard-table-head">
                <div>Waktu</div>
                <div>Anggota</div>
                <div>Buku</div>
                <div>Aktivitas</div>
                <div>Status</div>
            </div>


            {{-- DATA --}}
            @forelse($reservations as $reservation)

            <div class="dashboard-table-row">

                {{-- WAKTU --}}
                <div>
                    {{ $reservation->created_at->format('H.i') }}
                </div>


                {{-- ANGGOTA --}}
                <div>
                    {{ $reservation->member->name }}
                </div>


                {{-- BUKU --}}
                <div>
                    {{ $reservation->book->title }}
                </div>


                {{-- AKTIVITAS --}}
                <div>
                    Reservasi
                </div>


                {{-- STATUS --}}
                <div>

                    @if($reservation->display_status === 'menunggu')

                    <span class="dashboard-status borrowed">
                        Menunggu
                    </span>


                    @elseif($reservation->display_status === 'disetujui')

                    <span class="dashboard-status returned">
                        Disetujui
                    </span>


                    @elseif($reservation->display_status === 'dipinjam')

                    <span class="dashboard-status borrowed">
                        Dipinjam
                    </span>


                    @elseif($reservation->display_status === 'selesai')

                    <span class="dashboard-status returned">
                        Selesai
                    </span>


                    @elseif($reservation->display_status === 'ditolak')

                    <span class="dashboard-status late">
                        Ditolak
                    </span>


                    @elseif($reservation->display_status === 'dibatalkan')

                    <span class="dashboard-status late">
                        Dibatalkan
                    </span>


                    @elseif(str_starts_with($reservation->display_status, 'terlambat'))

                    <span class="dashboard-status late">
                        {{ ucfirst($reservation->display_status) }}
                    </span>


                    @endif

                </div>

            </div>

            @empty

            <div class="dashboard-table-row">

                <div style="grid-column: 1 / -1; text-align:center;">
                    Belum ada reservasi.
                </div>

            </div>

            @endforelse

        </div>


        <div class="dashboard-see-all">
            <a href="{{ route('reservations') }}">
                Lihat semua...
            </a>
        </div>

    </div>

</section>

@endsection