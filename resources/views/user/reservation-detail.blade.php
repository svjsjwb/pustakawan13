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

.rsv-book-actions {
    display: flex;
    justify-content: flex-end;
    width: 100%;
    margin-top: 18px;
}

.rsv-btn-cancel-reservation {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 9px 15px;
    border: 1.5px solid rgba(185, 28, 28, .42);
    border-radius: 12px;
    background: transparent;
    color: #b91c1c;
    font: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}
.rsv-btn-cancel-reservation:hover,
.rsv-btn-cancel-reservation:focus-visible { outline: none; border-color: #b91c1c; background: rgba(185, 28, 28, .06); }
.rsv-btn-cancel-reservation svg { width: 17px; height: 17px; flex: 0 0 auto; }
.rsv-cancelled-badge,
.status-badge.status-ditolak {
    border-color: rgba(239, 68, 68, 0.35) !important;
    color: #b91c1c !important;
    background: rgba(239, 68, 68, 0.12) !important;
}
.rsv-cancelled-badge .status-badge-icon,
.status-badge.status-ditolak .status-badge-icon,
.rsv-cancelled-badge .status-badge-icon svg,
.status-badge.status-ditolak .status-badge-icon svg {
    color: #b91c1c !important;
    stroke: #b91c1c !important;
}
.rsv-cancel-success {
    display: none;
    margin-bottom: 18px;
    padding: 13px 16px;
    border: 1px solid rgba(22, 101, 52, .2);
    border-radius: 12px;
    color: #166534;
    background: rgba(22, 163, 74, .08);
    font-size: 13.5px;
    font-weight: 700;
}

.rsv-cancel-dialog {
    width: min(440px, calc(100vw - 32px));
    max-width: none;
    padding: 0;
    border: 1px solid var(--border);
    border-radius: 18px;
    color: var(--text);
    background: var(--surface);
    box-shadow: 0 24px 70px rgba(8, 35, 34, .24);
}
.rsv-cancel-dialog::backdrop { background: rgba(12, 35, 34, .48); backdrop-filter: blur(3px); }
.rsv-cancel-dialog-content { padding: 26px; }
.rsv-cancel-dialog-icon { display: grid; place-items: center; width: 44px; height: 44px; margin-bottom: 16px; border: 1px solid rgba(185, 28, 28, .2); border-radius: 14px; color: #b91c1c; background: rgba(220, 38, 38, .07); }
.rsv-cancel-dialog-icon svg { width: 22px; height: 22px; }
.rsv-cancel-dialog h2 { margin: 0 0 8px; color: var(--text); font: 800 18px/1.3 'Poppins', sans-serif; }
.rsv-cancel-dialog p { margin: 0; color: var(--text-muted); font-size: 13.5px; line-height: 1.55; }
.rsv-cancel-dialog-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }
.rsv-cancel-dialog-actions button { min-height: 42px; padding: 9px 14px; border-radius: 11px; font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; }
.rsv-cancel-back { border: 1px solid var(--border); color: var(--text); background: var(--surface); }
.rsv-cancel-confirm { border: 1px solid #b91c1c; color: #fff; background: #b91c1c; }
.rsv-cancel-back:hover { background: var(--primary-light); }
.rsv-cancel-confirm:hover { border-color: #991b1b; background: #991b1b; }

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
    .rsv-header-info { width: 100%; }
    .rsv-cancel-dialog-content { padding: 22px 18px; }
    .rsv-cancel-dialog-actions { flex-direction: column-reverse; }
    .rsv-cancel-dialog-actions button { width: 100%; }
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
        'menunggu'              => 'status-menunggu',
        'disetujui'             => 'status-disetujui',
        'siap_diambil'          => 'status-siap_diambil',
        'ditolak', 'dibatalkan' => 'status-ditolak rsv-cancelled-badge',
        'selesai'               => 'status-dikembalikan',
        default                 => 'status-dipinjam',
    };
    $reservationCode = 'RSV-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT);
    $tanggalPengajuan = $reservation->reserved_at
        ? \Carbon\Carbon::parse($reservation->reserved_at)->format('d F Y')
        : $reservation->created_at->format('d F Y');
    $tanggalPersetujuan = in_array($status, ['disetujui', 'siap_diambil', 'selesai'])
        ? $reservation->updated_at->format('d F Y')
        : null;
    $canCancelReservation = in_array($status, ['menunggu', 'disetujui', 'siap_diambil'], true);
@endphp

<div class="rsv-detail-container">
    <div class="rsv-cancel-success" id="rsvCancelSuccess" role="status" aria-live="polite">Reservasi berhasil dibatalkan.</div>

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
                {{ $book?->category->name ?? 'Koleksi Umum' }}
            </span>
            <h1 class="rsv-book-title">
                {{ $book?->title ?? 'Judul Buku Tidak Diketahui' }}
            </h1>
            <p class="rsv-book-author">
                <span>Penulis:</span>
                <strong>{{ $book?->author ?? 'Penulis Anonim' }}</strong>
                @if($book?->publisher)
                    <span style="opacity: 0.5;">•</span>
                    <span>Penerbit: {{ $book->publisher }}</span>
                @endif
            </p>

            <div class="rsv-header-status-row">
                {{-- Premium Status Badge --}}
                <span class="status-badge {{ $badgeClass }}" id="rsvStatusBadge" style="padding: 6px 16px 6px 10px; font-size: 13px;">
                    <span class="status-badge-icon" style="width: 22px; height: 22px;">
                        @if($status === 'menunggu')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @elseif($status === 'disetujui')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif($status === 'siap_diambil')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        @elseif($status === 'ditolak' || $status === 'dibatalkan')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                        @endif
                    </span>
                    <span id="rsvStatusLabel">{{ $statusLabel }}</span>
                </span>

                <span style="font-size: 12.5px; color: var(--text-muted); font-family: monospace;">
                    #{{ $reservationCode }}
                </span>
            </div>

            @if($canCancelReservation)
                <div class="rsv-book-actions" id="rsvBookActions">
                    <button type="button" class="rsv-btn-cancel-reservation" id="rsvCancelTrigger" aria-haspopup="dialog">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/>
                        </svg>
                        <span>Batalkan Reservasi</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if($canCancelReservation)
        <dialog class="rsv-cancel-dialog" id="rsvCancelDialog" aria-labelledby="rsvCancelTitle" aria-describedby="rsvCancelDescription">
            <div class="rsv-cancel-dialog-content">
                <div class="rsv-cancel-dialog-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/>
                    </svg>
                </div>
                <h2 id="rsvCancelTitle">Batalkan Reservasi?</h2>
                <p id="rsvCancelDescription">Reservasi buku <strong>{{ $book?->title ?? 'ini' }}</strong> akan ditandai sebagai dibatalkan pada tampilan ini.</p>
                <div class="rsv-cancel-dialog-actions">
                    <button type="button" class="rsv-cancel-back" id="rsvCancelBack">Kembali</button>
                    <button type="button" class="rsv-cancel-confirm" id="rsvCancelConfirm">Ya, Batalkan Reservasi</button>
                </div>
            </div>
        </dialog>
    @endif

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

        @php
            $isMenunggu = $status === 'menunggu';
            $isDisetujui = $status === 'disetujui';
            $isSiapDiambil = $status === 'siap_diambil';
            $isSelesai = $status === 'selesai';
            $isDibatalkan = $status === 'dibatalkan';
            $isDitolak = in_array($status, ['ditolak', 'dibatalkan'], true);
            $doneVerifikasi = in_array($status, ['disetujui', 'siap_diambil', 'selesai']);
            $donePersetujuan = in_array($status, ['siap_diambil', 'selesai']);
            $donePengambilan = $isSelesai;
        @endphp

        <div class="rsv-tracking-timeline">
            <div class="rsv-tracking-bar-bg"></div>

            {{-- Langkah 1: Pengajuan Reservasi --}}
            <div class="rsv-tracking-step is-done">
                <div class="rsv-step-node">✓</div>
                <div class="rsv-step-label">✓ Pengajuan Reservasi</div>
                <p class="rsv-step-desc">Selesai · {{ $reservation->created_at->format('d M Y') }}</p>
            </div>

            {{-- Langkah 2: Verifikasi Perpustakaan --}}
            <div class="rsv-tracking-step {{ $isMenunggu ? 'is-active is-warning' : ($isDitolak ? 'is-rejected' : ($doneVerifikasi ? 'is-done' : 'is-upcoming')) }}">
                <div class="rsv-step-node">@if($isDibatalkan || $isDitolak) ✕ @elseif($doneVerifikasi) ✓ @elseif($isMenunggu) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($isDibatalkan) ✕ Dibatalkan @elseif($isDitolak) ✕ Ditolak @elseif($doneVerifikasi) ✓ Verifikasi Perpustakaan @elseif($isMenunggu) ● Verifikasi Perpustakaan @else ○ Verifikasi Perpustakaan @endif</div>
                <p class="rsv-step-desc">@if($isDibatalkan) Dibatalkan oleh Pengguna @elseif($isDitolak) Ditolak @elseif($doneVerifikasi) Selesai @else Menunggu Persetujuan @endif</p>
            </div>

            {{-- Langkah 3: Persetujuan Reservasi --}}
            <div class="rsv-tracking-step {{ $isDisetujui ? 'is-active' : ($donePersetujuan ? 'is-done' : 'is-upcoming') }}">
                <div class="rsv-step-node">@if($donePersetujuan) ✓ @elseif($isDisetujui) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($donePersetujuan) ✓ Persetujuan Reservasi @elseif($isDisetujui) ● Persetujuan Reservasi @else ○ Persetujuan Reservasi @endif</div>
                <p class="rsv-step-desc">@if($donePersetujuan) Selesai @elseif($isDisetujui) Sedang Diproses @else Belum Selesai @endif</p>
            </div>

            {{-- Langkah 4: Pengambilan Buku --}}
            <div class="rsv-tracking-step {{ $isSelesai ? 'is-done' : ($isSiapDiambil ? 'is-active' : 'is-upcoming') }}">
                <div class="rsv-step-node">@if($donePengambilan) ✓ @elseif($isSiapDiambil) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($donePengambilan) ✓ Pengambilan Buku @elseif($isSiapDiambil) ● Pengambilan Buku @else ○ Pengambilan Buku @endif</div>
                <p class="rsv-step-desc">@if($donePengambilan) Selesai @elseif($isSiapDiambil) Menunggu Pengambilan @else Menunggu @endif</p>
            </div>
        </div>
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
                    Tanggal Pengajuan
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
         4. TOMBOL "KEMBALI KE RESERVASI" (SATU-SATUNYA TOMBOL DI PALING BAWAH)
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

@if($canCancelReservation)
    @push('scripts')
    <script>
        const cancelReservationDialog = document.getElementById('rsvCancelDialog');
        const cancelReservationTrigger = document.getElementById('rsvCancelTrigger');
        const cancelReservationBack = document.getElementById('rsvCancelBack');
        const cancelReservationConfirm = document.getElementById('rsvCancelConfirm');

        cancelReservationTrigger?.addEventListener('click', function() {
            cancelReservationDialog?.showModal();
        });
        cancelReservationBack?.addEventListener('click', function() {
            cancelReservationDialog?.close();
            cancelReservationTrigger?.focus();
        });
        cancelReservationDialog?.addEventListener('click', function(event) {
            if (event.target === cancelReservationDialog) {
                cancelReservationDialog.close();
                cancelReservationTrigger?.focus();
            }
        });
        cancelReservationConfirm?.addEventListener('click', async function() {
            cancelReservationConfirm.disabled = true;
            const originalText = cancelReservationConfirm.textContent;
            cancelReservationConfirm.textContent = 'Membatalkan...';

            try {
                const response = await fetch('{{ route('user.reservations.cancel', $reservation->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const badge = document.getElementById('rsvStatusBadge');
                    badge?.classList.remove('status-menunggu', 'status-disetujui', 'status-siap_diambil', 'status-ditolak', 'status-dikembalikan', 'status-dipinjam');
                    badge?.classList.add('status-ditolak', 'rsv-cancelled-badge');
                    const badgeIcon = badge?.querySelector('.status-badge-icon');
                    if (badgeIcon) {
                        badgeIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/></svg>';
                    }
                    const statusLabel = document.getElementById('rsvStatusLabel');
                    if (statusLabel) {
                        statusLabel.textContent = 'Dibatalkan';
                    }
                    const successAlert = document.getElementById('rsvCancelSuccess');
                    if (successAlert) {
                        successAlert.textContent = data.message || 'Reservasi berhasil dibatalkan.';
                        successAlert.style.display = 'block';
                    }
                    document.getElementById('rsvBookActions')?.remove();
                    cancelReservationDialog.close();
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    setTimeout(function() {
                        window.location.reload();
                    }, 1200);
                } else {
                    alert(data.message || 'Gagal membatalkan reservasi. Silakan coba lagi.');
                    cancelReservationConfirm.disabled = false;
                    cancelReservationConfirm.textContent = originalText;
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat membatalkan reservasi.');
                cancelReservationConfirm.disabled = false;
                cancelReservationConfirm.textContent = originalText;
            }
        });
    </script>
    @endpush
@endif

@endsection
