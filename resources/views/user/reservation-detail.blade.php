@extends('layouts.user')
@section('title', 'Detail Reservasi Buku – Perpustakaan Digital')
@section('page-title', 'Detail Reservasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
<style>
/* ── Detail Reservasi Container (Clean Top, No Top Button) ─────────────── */
.rsv-detail-container {
    max-width: 960px;
    margin: 0 auto;
    padding-top: 4px;
    padding-bottom: 56px;
}

/* ── Card Base ─────────────────────────────────────────────────────────── */
.rsv-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(18, 63, 61, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.22s ease;
}
.rsv-card:hover {
    box-shadow: 0 8px 30px rgba(18, 63, 61, 0.08);
}

/* ── 1. Header Card: Informasi Buku ────────────────────────────────────── */
.rsv-book-header-card {
    padding: 28px 32px;
    display: flex;
    align-items: center;
    gap: 28px;
    position: relative;
}
.rsv-book-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F766E, #14B8A6, #38BDF8);
}

.rsv-cover-wrap {
    width: 120px;
    height: 168px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(15, 118, 110, 0.16);
    flex-shrink: 0;
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.rsv-cover-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.rsv-cover-fallback {
    width: 100%;
    height: 100%;
    background: linear-gradient(145deg, #278482, #124f4e);
    color: #FFFFFF;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px;
    text-align: center;
}
.rsv-cover-fallback .letter {
    font-size: 40px;
    font-weight: 800;
    opacity: 0.45;
    font-family: 'Poppins', sans-serif;
}
.rsv-cover-fallback .text {
    font-size: 10.5px;
    font-weight: 700;
    opacity: 0.9;
    margin-top: 4px;
    line-height: 1.2;
}

.rsv-header-info {
    flex: 1;
    min-width: 0;
}
.rsv-category-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: var(--primary-light);
    color: var(--primary);
    border: 1px solid rgba(15, 118, 110, 0.22);
    border-radius: 9999px;
    font-size: 11.5px;
    font-weight: 700;
    margin-bottom: 10px;
}
.rsv-book-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--text);
    margin: 0 0 8px;
    font-family: 'Poppins', sans-serif;
    line-height: 1.3;
    letter-spacing: -0.02em;
}
.rsv-book-author {
    font-size: 14px;
    color: var(--text-muted);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.rsv-header-status-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .rsv-book-header-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 22px 20px;
        gap: 18px;
    }
    .rsv-cover-wrap {
        width: 100px;
        height: 140px;
    }
    .rsv-book-title {
        font-size: 20px;
    }
}

/* ── Section Title Standard ────────────────────────────────────────────── */
.rsv-section-card {
    padding: 26px 30px;
}
.rsv-section-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--text);
    font-family: 'Poppins', sans-serif;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rsv-section-subtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0 0 28px;
}

/* ── 2. Tracking Status Timeline ───────────────────────────────────────── */
.rsv-tracking-timeline {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    position: relative;
    gap: 16px;
    margin-bottom: 8px;
}
.rsv-tracking-timeline.is-rejected-flow {
    grid-template-columns: repeat(2, 1fr);
    max-width: 520px;
}

.rsv-tracking-bar-bg {
    position: absolute;
    top: 21px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: var(--border);
    z-index: 1;
}

.rsv-tracking-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.rsv-step-node {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: var(--surface);
    border: 2.5px solid var(--border);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    margin-bottom: 12px;
    transition: all 0.25s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Done State */
.rsv-tracking-step.is-done .rsv-step-node {
    background: #16A34A;
    border-color: #16A34A;
    color: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.18);
}

/* Active State (Prominent Glow) */
.rsv-tracking-step.is-active .rsv-step-node {
    background: #0F766E;
    border-color: #0F766E;
    color: #FFFFFF;
    box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.24);
    animation: rsvActivePulse 2s infinite ease-in-out;
}
.rsv-tracking-step.is-active.is-warning .rsv-step-node {
    background: #D97706;
    border-color: #D97706;
    color: #FFFFFF;
    box-shadow: 0 0 0 6px rgba(217, 119, 6, 0.24);
}

/* Rejected State */
.rsv-tracking-step.is-rejected .rsv-step-node {
    background: #DC2626;
    border-color: #DC2626;
    color: #FFFFFF;
    box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.24);
}

/* Upcoming State */
.rsv-tracking-step.is-upcoming .rsv-step-node {
    background: var(--surface);
    border-color: var(--border);
    color: #94A3B8;
}

@keyframes rsvActivePulse {
    0%   { box-shadow: 0 0 0 0 rgba(15, 118, 110, 0.5); }
    70%  { box-shadow: 0 0 0 10px rgba(15, 118, 110, 0); }
    100% { box-shadow: 0 0 0 0 rgba(15, 118, 110, 0); }
}

.rsv-step-label {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.rsv-tracking-step.is-active .rsv-step-label {
    color: var(--primary);
    font-weight: 800;
}
.rsv-tracking-step.is-active.is-warning .rsv-step-label {
    color: #D97706;
}
.rsv-tracking-step.is-rejected .rsv-step-label {
    color: #DC2626;
    font-weight: 800;
}
.rsv-tracking-step.is-upcoming .rsv-step-label {
    color: var(--text-muted);
}

.rsv-step-desc {
    font-size: 11.5px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.4;
}

@media (max-width: 640px) {
    .rsv-tracking-bar-bg {
        display: none;
    }
    .rsv-tracking-timeline {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .rsv-tracking-step {
        flex-direction: row;
        align-items: flex-start;
        text-align: left;
        gap: 16px;
    }
    .rsv-step-node {
        margin-bottom: 0;
    }
    .rsv-step-label {
        justify-content: flex-start;
    }
}

/* ── 3 & 4. Grid Key-Value Box ─────────────────────────────────────────── */
.rsv-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 24px;
}
@media (max-width: 640px) {
    .rsv-info-grid {
        grid-template-columns: 1fr;
    }
}

.rsv-info-box {
    background: rgba(18, 63, 61, 0.02);
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 14px 18px;
    transition: all 0.2s ease;
}
.rsv-info-box:hover {
    border-color: rgba(15, 118, 110, 0.3);
    background: var(--surface);
}
.rsv-info-label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.rsv-info-val {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text);
}
.rsv-info-val.mono {
    font-family: monospace;
    color: var(--primary);
    font-size: 15px;
}

/* Admin Alert Box */
.rsv-admin-alert {
    margin-top: 20px;
    padding: 16px 20px;
    border-radius: 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.rsv-admin-alert.alert-danger {
    background: rgba(220, 38, 38, 0.06);
    border: 1.5px solid rgba(220, 38, 38, 0.25);
}
.rsv-admin-alert.alert-info {
    background: rgba(15, 118, 110, 0.06);
    border: 1.5px solid rgba(15, 118, 110, 0.25);
}
.rsv-admin-alert-icon {
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 1px;
}
.rsv-admin-alert-title {
    font-size: 12.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin: 0 0 3px;
}
.rsv-admin-alert.alert-danger .rsv-admin-alert-title {
    color: #991B1B;
}
.rsv-admin-alert.alert-info .rsv-admin-alert-title {
    color: #0F766E;
}
.rsv-admin-alert-desc {
    font-size: 13px;
    color: var(--text);
    margin: 0;
    line-height: 1.5;
}

/* ── 5. Bottom Action: SATU-SATUNYA TOMBOL "KEMBALI KE RESERVASI" ──────── */
.rsv-actions-bar {
    margin-top: 32px;
    display: flex;
}

.rsv-btn-back-large {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    min-height: 52px;
    padding: 15px 28px;
    background: linear-gradient(135deg, #0F766E, #115E59);
    color: #FFFFFF;
    border-radius: 16px;
    font-size: 14.5px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(15, 118, 110, 0.24);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
}

.rsv-btn-back-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15, 118, 110, 0.35);
    color: #FFFFFF;
    background: linear-gradient(135deg, #115E59, #0d4a46);
}

.rsv-btn-back-large svg {
    width: 18px;
    height: 18px;
    transition: transform 0.2s ease;
}

.rsv-btn-back-large:hover svg {
    transform: translateX(-4px);
}
</style>
@endpush

@section('content')

@php
    $book = $reservation->book;
    $status = strtolower($reservation->status);
    $statusLabel = match($status) {
        'menunggu'     => 'Menunggu Persetujuan',
        'disetujui'    => 'Disetujui',
        'siap_diambil' => 'Siap Diambil',
        'ditolak'      => 'Ditolak',
        'selesai'      => 'Selesai',
        'dibatalkan'   => 'Dibatalkan',
        default        => ucfirst($status),
    };
    $badgeClass = match($status) {
        'menunggu'     => 'status-menunggu',
        'disetujui'    => 'status-disetujui',
        'siap_diambil' => 'status-siap_diambil',
        'ditolak'      => 'status-ditolak',
        'selesai'      => 'status-dikembalikan',
        default        => 'status-dipinjam',
    };
    $reservationCode = 'RSV-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT);
    $tanggalPengajuan = $reservation->reserved_at
        ? \Carbon\Carbon::parse($reservation->reserved_at)->format('d F Y')
        : $reservation->created_at->format('d F Y');
    $tanggalPersetujuan = in_array($status, ['disetujui', 'siap_diambil', 'selesai'])
        ? $reservation->updated_at->format('d F Y')
        : null;
@endphp

<div class="rsv-detail-container">

    {{-- =========================================================================
         1. INFORMASI BUKU (Cover, Judul, Penulis, Kategori, Status)
         ========================================================================= --}}
    <div class="rsv-card rsv-book-header-card">
        <div class="rsv-cover-wrap">
            @if($book?->cover)
                <img src="{{ asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
            @else
                <div class="rsv-cover-fallback">
                    <span class="letter">{{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}</span>
                    <span class="text">{{ Str::limit($book?->title ?? 'Buku', 28) }}</span>
                </div>
            @endif
        </div>

        <div class="rsv-header-info">
            <span class="rsv-category-pill">
                📚 {{ $book?->category->name ?? 'Koleksi Umum' }}
            </span>
            <h1 class="rsv-book-title">
                {{ $book?->title ?? 'Judul Buku Tidak Diketahui' }}
            </h1>
            <p class="rsv-book-author">
                <span>✍️ Penulis:</span>
                <strong>{{ $book?->author ?? 'Penulis Anonim' }}</strong>
                @if($book?->publisher)
                    <span style="opacity: 0.5;">•</span>
                    <span>Penerbit: {{ $book->publisher }}</span>
                @endif
            </p>

            <div class="rsv-header-status-row">
                {{-- Premium Status Badge --}}
                <span class="status-badge {{ $badgeClass }}" style="padding: 6px 16px 6px 10px; font-size: 13px;">
                    <span class="status-badge-icon" style="width: 22px; height: 22px;">
                        @if($status === 'menunggu')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @elseif($status === 'disetujui')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif($status === 'siap_diambil')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        @elseif($status === 'ditolak')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                        @endif
                    </span>
                    <span>{{ $statusLabel }}</span>
                </span>

                <span style="font-size: 12.5px; color: var(--text-muted); font-family: monospace;">
                    #{{ $reservationCode }}
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         2. TRACKING STATUS RESERVASI (Timeline Modern)
         ========================================================================= --}}
    <div class="rsv-card rsv-section-card">
        <h2 class="rsv-section-title">
            <span>⏱️</span> Tracking Status Reservasi
        </h2>
        <p class="rsv-section-subtitle">
            Pantau progres tahapan permohonan hingga buku siap diambil
        </p>

        @if($status === 'ditolak')
            {{-- Flow Khusus Ditolak: ✓ Menunggu -> ✕ Ditolak --}}
            <div class="rsv-tracking-timeline is-rejected-flow">
                <div class="rsv-tracking-bar-bg" style="background: linear-gradient(90deg, #16A34A 50%, #DC2626 50%);"></div>

                {{-- Step 1: Menunggu (Done) --}}
                <div class="rsv-tracking-step is-done">
                    <div class="rsv-step-node">
                        ✓
                    </div>
                    <div class="rsv-step-label">
                        ✓ Menunggu
                    </div>
                    <p class="rsv-step-desc">
                        Diajukan {{ $reservation->created_at->format('d M Y') }}
                    </p>
                </div>

                {{-- Step 2: Ditolak (Active Rejected) --}}
                <div class="rsv-tracking-step is-rejected">
                    <div class="rsv-step-node">
                        ✕
                    </div>
                    <div class="rsv-step-label">
                        ✕ Ditolak
                    </div>
                    <p class="rsv-step-desc">
                        {{ $reservation->updated_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        @else
            {{-- Flow Normal: ✓ Menunggu -> ✓ Disetujui -> ● Siap Diambil -> ○ Selesai --}}
            @php
                $isMenunggu   = ($status === 'menunggu');
                $isDisetujui  = ($status === 'disetujui');
                $isSiapDiambil = ($status === 'siap_diambil');
                $isSelesai    = ($status === 'selesai');

                $doneMenunggu    = in_array($status, ['disetujui', 'siap_diambil', 'selesai']);
                $doneDisetujui   = in_array($status, ['siap_diambil', 'selesai']);
                $doneSiapDiambil = in_array($status, ['selesai']);
            @endphp

            <div class="rsv-tracking-timeline">
                <div class="rsv-tracking-bar-bg"></div>

                {{-- Step 1: Menunggu --}}
                <div class="rsv-tracking-step {{ $isMenunggu ? 'is-active is-warning' : ($doneMenunggu ? 'is-done' : 'is-upcoming') }}">
                    <div class="rsv-step-node">
                        @if($doneMenunggu) ✓ @elseif($isMenunggu) ● @else ○ @endif
                    </div>
                    <div class="rsv-step-label">
                        @if($doneMenunggu) ✓ @elseif($isMenunggu) ● @else ○ @endif Menunggu
                    </div>
                    <p class="rsv-step-desc">
                        {{ $reservation->created_at->format('d M Y') }}
                    </p>
                </div>

                {{-- Step 2: Disetujui --}}
                <div class="rsv-tracking-step {{ $isDisetujui ? 'is-active' : ($doneDisetujui ? 'is-done' : 'is-upcoming') }}">
                    <div class="rsv-step-node">
                        @if($doneDisetujui) ✓ @elseif($isDisetujui) ● @else ○ @endif
                    </div>
                    <div class="rsv-step-label">
                        @if($doneDisetujui) ✓ @elseif($isDisetujui) ● @else ○ @endif Disetujui
                    </div>
                    <p class="rsv-step-desc">
                        @if($doneDisetujui || $isDisetujui)
                            {{ $reservation->updated_at->format('d M Y') }}
                        @else
                            Menunggu verifikasi
                        @endif
                    </p>
                </div>

                {{-- Step 3: Siap Diambil --}}
                <div class="rsv-tracking-step {{ $isSiapDiambil ? 'is-active' : ($doneSiapDiambil ? 'is-done' : 'is-upcoming') }}">
                    <div class="rsv-step-node">
                        @if($doneSiapDiambil) ✓ @elseif($isSiapDiambil) ● @else ○ @endif
                    </div>
                    <div class="rsv-step-label">
                        @if($doneSiapDiambil) ✓ @elseif($isSiapDiambil) ● @else ○ @endif Siap Diambil
                    </div>
                    <p class="rsv-step-desc">
                        @if($isSiapDiambil)
                            Batas: {{ $reservation->expires_at ? \Carbon\Carbon::parse($reservation->expires_at)->format('d M Y') : '-' }}
                        @elseif($doneSiapDiambil)
                            Telah diambil
                        @else
                            Penyiapan buku
                        @endif
                    </p>
                </div>

                {{-- Step 4: Selesai --}}
                <div class="rsv-tracking-step {{ $isSelesai ? 'is-done' : 'is-upcoming' }}">
                    <div class="rsv-step-node">
                        @if($isSelesai) ✓ @else ○ @endif
                    </div>
                    <div class="rsv-step-label">
                        @if($isSelesai) ✓ @else ○ @endif Selesai
                    </div>
                    <p class="rsv-step-desc">
                        @if($isSelesai) Selesai @else Tahap akhir @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- =========================================================================
         3. INFORMASI RESERVASI (ID, Tgl Pengajuan, Tgl Persetujuan, Lokasi, Catatan Admin)
         ========================================================================= --}}
    <div class="rsv-card rsv-section-card">
        <h2 class="rsv-section-title">
            <span>📋</span> Informasi Reservasi
        </h2>
        <p class="rsv-section-subtitle">
            Rincian data pengajuan permohonan koleksi perpustakaan
        </p>

        <div class="rsv-info-grid">
            {{-- ID Reservasi --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>🏷️</span> ID Reservasi
                </div>
                <div class="rsv-info-val mono">
                    #{{ $reservationCode }}
                </div>
            </div>

            {{-- Tanggal Pengajuan --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>📅</span> Tanggal Pengajuan
                </div>
                <div class="rsv-info-val">
                    {{ $tanggalPengajuan }}
                </div>
            </div>

            {{-- Tanggal Persetujuan (jika ada) --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>✓</span> Tanggal Persetujuan
                </div>
                <div class="rsv-info-val">
                    {{ $tanggalPersetujuan ?? 'Belum disetujui' }}
                </div>
            </div>

            {{-- Lokasi Pengambilan --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>📍</span> Lokasi Pengambilan
                </div>
                <div class="rsv-info-val">
                    Meja Layanan Sirkulasi, Lantai 1
                </div>
            </div>
        </div>

        {{-- Catatan Admin (jika ada) --}}
        @if($reservation->rejection_reason)
            <div class="rsv-admin-alert alert-danger">
                <div class="rsv-admin-alert-icon">⚠️</div>
                <div>
                    <div class="rsv-admin-alert-title">Catatan Petugas Admin:</div>
                    <p class="rsv-admin-alert-desc">
                        {{ $reservation->rejection_reason }}
                    </p>
                </div>
            </div>
        @elseif($status === 'siap_diambil')
            <div class="rsv-admin-alert alert-info">
                <div class="rsv-admin-alert-icon">ℹ️</div>
                <div>
                    <div class="rsv-admin-alert-title">Petunjuk Pengambilan:</div>
                    <p class="rsv-admin-alert-desc">
                        Buku fisik telah disiapkan di meja layanan sirkulasi. Silakan tunjukkan kode <strong>#{{ $reservationCode }}</strong> kepada petugas sebelum tanggal <strong>{{ $reservation->expires_at ? \Carbon\Carbon::parse($reservation->expires_at)->format('d F Y') : '-' }}</strong>.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- =========================================================================
         4. INFORMASI PENGGUNA (Nama, Kontak, Keanggotaan, Meja)
         ========================================================================= --}}
    <div class="rsv-card rsv-section-card">
        <h2 class="rsv-section-title">
            <span>👤</span> Informasi Pengguna
        </h2>
        <p class="rsv-section-subtitle">
            Data pemustaka yang mengajukan reservasi
        </p>

        <div class="rsv-info-grid">
            {{-- Nama Pemustaka --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>👤</span> Nama Pemustaka
                </div>
                <div class="rsv-info-val">
                    {{ $member->name ?? Auth::user()->name }}
                </div>
            </div>

            {{-- Email / Akun --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>✉️</span> Email / Akun
                </div>
                <div class="rsv-info-val">
                    {{ $member->email ?? Auth::user()->email }}
                </div>
            </div>

            {{-- Nomor Telepon --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>📞</span> Nomor Telepon
                </div>
                <div class="rsv-info-val">
                    {{ $member->phone && $member->phone !== '-' ? $member->phone : 'Tidak ada data telepon' }}
                </div>
            </div>

            {{-- Status Keanggotaan --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <span>🛡️</span> Status Keanggotaan
                </div>
                <div class="rsv-info-val" style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                    <span>{{ ucfirst($member->status ?? 'Aktif') }}</span>
                    @if($reservation->seat_number)
                        <span style="opacity: 0.5;">•</span>
                        <span style="font-size: 13px; color: var(--primary);">Meja: #{{ $reservation->seat_number }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         5. TOMBOL "KEMBALI KE RESERVASI" (SATU-SATUNYA TOMBOL DI PALING BAWAH)
         ========================================================================= --}}
    <div class="rsv-actions-bar">
        <a href="{{ route('user.reservations') }}" class="rsv-btn-back-large">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Reservasi</span>
        </a>
    </div>

</div>

@endsection
