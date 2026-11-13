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
    background: rgba(15, 118, 110, .12);
    color: #0f766e;
    border: 1px solid rgba(15, 118, 110, .32);
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 10px;
}
[data-theme="dark"] .rsv-detail-container .rsv-category-pill {
    background: rgba(57, 143, 136, .24);
    color: #b7f0e9;
    border-color: rgba(166, 232, 223, .48);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-menunggu {
    background: rgba(245, 158, 11, .16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, .42);
    box-shadow: 0 2px 8px rgba(245, 158, 11, .16);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-menunggu .status-badge-icon {
    background: rgba(245, 158, 11, .24);
    color: #fbbf24;
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-disetujui {
    background: rgba(22, 163, 74, .18);
    color: #4ade80;
    border-color: rgba(74, 222, 128, .44);
    box-shadow: 0 2px 8px rgba(22, 163, 74, .18);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-disetujui .status-badge-icon {
    background: rgba(74, 222, 128, .22);
    color: #4ade80;
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-siap_diambil {
    background: rgba(45, 212, 191, .18);
    color: #5eead4;
    border-color: rgba(94, 234, 212, .44);
    box-shadow: 0 2px 8px rgba(45, 212, 191, .18);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-siap_diambil .status-badge-icon {
    background: rgba(45, 212, 191, .22);
    color: #5eead4;
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-ditolak {
    background: rgba(239, 68, 68, .2);
    color: #ff6b72;
    border-color: rgba(255, 107, 114, .52);
    box-shadow: 0 2px 8px rgba(239, 68, 68, .2);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-ditolak .status-badge-icon {
    background: rgba(255, 107, 114, .24);
    color: #ff6b72;
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-dikembalikan,
[data-theme="dark"] .rsv-detail-container .status-badge.status-selesai {
    background: rgba(56, 189, 248, .18);
    color: #7dd3fc;
    border-color: rgba(125, 211, 252, .44);
    box-shadow: 0 2px 8px rgba(56, 189, 248, .16);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-dikembalikan .status-badge-icon,
[data-theme="dark"] .rsv-detail-container .status-badge.status-selesai .status-badge-icon {
    background: rgba(56, 189, 248, .22);
    color: #7dd3fc;
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-dipinjam {
    background: rgba(129, 140, 248, .18);
    color: #c4b5fd;
    border-color: rgba(196, 181, 253, .4);
    box-shadow: 0 2px 8px rgba(129, 140, 248, .16);
}
[data-theme="dark"] .rsv-detail-container .status-badge.status-dipinjam .status-badge-icon {
    background: rgba(196, 181, 253, .2);
    color: #c4b5fd;
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
[data-theme="dark"] .rsv-detail-container .rsv-btn-cancel-reservation {
    border-color: rgba(248, 113, 113, .58);
    background: rgba(220, 38, 38, .14);
    color: #fca5a5;
}
[data-theme="dark"] .rsv-detail-container .rsv-btn-cancel-reservation:hover,
[data-theme="dark"] .rsv-detail-container .rsv-btn-cancel-reservation:focus-visible {
    border-color: #f87171;
    background: rgba(220, 38, 38, .24);
    color: #fecaca;
}
.rsv-cancelled-badge { border-color: rgba(185, 28, 28, .2) !important; color: #991b1b !important; background: rgba(220, 38, 38, .08) !important; }
[data-theme="dark"] .rsv-detail-container .status-badge.rsv-cancelled-badge {
    border-color: rgba(255, 107, 114, .62) !important;
    color: #ff787f !important;
    background: rgba(239, 68, 68, .24) !important;
    box-shadow: 0 2px 10px rgba(239, 68, 68, .24) !important;
}
[data-theme="dark"] .rsv-detail-container .status-badge.rsv-cancelled-badge .status-badge-icon {
    color: #ff787f !important;
    background: rgba(255, 107, 114, .24) !important;
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
.rsv-cancel-success.is-visible { display: block; }
.rsv-cancel-success.is-pending {
    border-color: rgba(245, 158, 11, .38);
    color: #92400e;
    background: rgba(245, 158, 11, .1);
}
[data-theme="dark"] .rsv-cancel-success {
    border-color: rgba(74, 222, 128, .42);
    color: #bbf7d0;
    background: rgba(22, 163, 74, .18);
}
[data-theme="dark"] .rsv-cancel-success.is-pending {
    border-color: rgba(251, 191, 36, .44);
    color: #fef3c7;
    background: rgba(245, 158, 11, .16);
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
[data-theme="dark"] .rsv-cancel-dialog-icon {
    border-color: rgba(248, 113, 113, .5);
    color: #f87171;
    background: rgba(220, 38, 38, .2);
}
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
.rsv-section-icon {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
    color: var(--primary);
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
    width: 100%;
    margin-right: auto;
    margin-left: auto;
}
.rsv-tracking-timeline.is-cancelled-before-approval-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 calc(37.5% + 6px), var(--border) calc(37.5% + 6px));
}
[data-theme="dark"] .rsv-tracking-timeline.is-cancelled-before-approval-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 calc(37.5% + 6px), #484d53 calc(37.5% + 6px));
}
.rsv-tracking-timeline.is-cancelled-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 calc(62.5% + 6px), var(--border) calc(62.5% + 6px));
}
.rsv-tracking-timeline.is-cancellation-rejected-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 calc(62.5% + 6px), var(--border) calc(62.5% + 6px));
}
.rsv-tracking-timeline.is-cancellation-rejected-at-pickup-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 87.5%, var(--border) 87.5%);
}
.rsv-tracking-step.is-cancelled .rsv-step-node {
    background: #dc2626;
    border-color: #dc2626;
    color: #fff;
    box-shadow: 0 0 0 6px rgba(220, 38, 38, .2);
}
.rsv-tracking-step.is-cancelled .rsv-step-label {
    color: #b91c1c;
    font-weight: 800;
}
[data-theme="dark"] .rsv-tracking-step.is-cancelled .rsv-step-label { color: #ff6b72; }
[data-theme="dark"] .rsv-tracking-step.is-cancelled .rsv-step-desc { color: #f0fdfa; }
[data-theme="dark"] .rsv-tracking-timeline.is-cancelled-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 calc(62.5% + 6px), #484d53 calc(62.5% + 6px));
}
[data-theme="dark"] .rsv-tracking-timeline.is-cancellation-rejected-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 calc(62.5% + 6px), #484d53 calc(62.5% + 6px));
}
[data-theme="dark"] .rsv-tracking-timeline.is-cancellation-rejected-at-pickup-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 87.5%, #484d53 87.5%);
}
.rsv-tracking-timeline.is-cancellation-pending-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 calc(62.5% + 6px), var(--border) calc(62.5% + 6px));
}
.rsv-tracking-timeline.is-cancellation-pending-at-pickup .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #16a34a 0 87.5%, var(--border) 87.5%);
}
.rsv-tracking-step.is-cancellation-pending .rsv-step-node {
    background: #d97706;
    border-color: #d97706;
    color: #fff;
    box-shadow: 0 0 0 6px rgba(217, 119, 6, .2);
}
.rsv-tracking-step.is-cancellation-pending .rsv-step-label {
    color: #d97706;
    font-weight: 800;
}
.rsv-tracking-step.is-cancellation-pending .rsv-step-desc {
    color: var(--text-muted);
}
[data-theme="dark"] .rsv-tracking-timeline.is-cancellation-pending-flow .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 calc(62.5% + 6px), #484d53 calc(62.5% + 6px));
}
[data-theme="dark"] .rsv-tracking-timeline.is-cancellation-pending-at-pickup .rsv-tracking-bar-bg {
    background: linear-gradient(90deg, #22c55e 0 87.5%, #484d53 87.5%);
}
[data-theme="dark"] .rsv-tracking-step.is-cancellation-pending .rsv-step-label { color: #fbbf24; }
[data-theme="dark"] .rsv-tracking-step.is-cancellation-pending .rsv-step-desc { color: var(--text-muted); }

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
    color: var(--text);
    font-weight: 700;
}
.rsv-tracking-step.is-rejected .rsv-step-desc {
    color: #dc2626;
    font-weight: 800;
}
.rsv-rejected-inline-icon {
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-right: 3px;
    vertical-align: -2px;
}
[data-theme="dark"] .rsv-tracking-step.is-rejected .rsv-step-desc { color: #ff4d55; }
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
.rsv-info-icon {
    width: 14px;
    height: 14px;
    flex: 0 0 14px;
    color: var(--primary);
}
.rsv-info-icon-location {
    color: #dc2626;
}
[data-theme="dark"] .rsv-info-icon-location {
    color: #f87171;
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
[data-theme="dark"] .rsv-admin-alert.alert-danger {
    background: rgba(239, 68, 68, .12);
    border-color: rgba(248, 113, 113, .36);
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
[data-theme="dark"] .rsv-admin-alert.alert-danger .rsv-admin-alert-title {
    color: #fecaca;
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
        'ditolak', 'dibatalkan' => 'status-ditolak',
        'selesai'      => 'status-dikembalikan',
        default        => 'status-dipinjam',
    };
    $cancellationRequestPending = $reservation->cancellation_requested_at !== null;
    $cancellationRequestRejected = $cancellationRejectionNotification !== null
        && !$cancellationRequestPending
        && in_array($status, ['disetujui', 'siap_diambil'], true);
    if ($cancellationRequestPending) {
        $statusLabel = 'Menunggu Persetujuan Pembatalan';
        $badgeClass = 'status-menunggu';
    }
    $reservationCode = 'RSV-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT);
    $tanggalPengajuan = $reservation->reserved_at
        ? \Carbon\Carbon::parse($reservation->reserved_at)->format('d F Y')
        : $reservation->created_at->format('d F Y');
    $tanggalPersetujuan = in_array($status, ['disetujui', 'siap_diambil', 'selesai'])
        ? $reservation->updated_at->format('d F Y')
        : null;
    $canCancelReservation = in_array($status, ['menunggu', 'disetujui', 'siap_diambil'], true) && !$cancellationRequestPending;
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
                        @if($cancellationRequestPending || $status === 'menunggu')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @elseif($status === 'disetujui')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif($status === 'siap_diambil')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        @elseif(in_array($status, ['ditolak', 'dibatalkan'], true))
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
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
            <form method="POST" action="{{ route('user.reservations.cancel', $reservation->id) }}">
                @csrf
                <div class="rsv-cancel-dialog-content">
                    <div class="rsv-cancel-dialog-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/>
                        </svg>
                    </div>
                    <h2 id="rsvCancelTitle">Batalkan Reservasi?</h2>
                    <p id="rsvCancelDescription">Reservasi buku <strong>{{ $book?->title ?? 'ini' }}</strong> akan dibatalkan dan statusnya tersimpan.</p>
                    <div class="rsv-cancel-dialog-actions">
                        <button type="button" class="rsv-cancel-back" id="rsvCancelBack">Kembali</button>
                        <button type="submit" class="rsv-cancel-confirm" id="rsvCancelConfirm">Ya, Batalkan Reservasi</button>
                    </div>
                </div>
            </form>
        </dialog>
    @endif

    {{-- =========================================================================
         2. TRACKING STATUS RESERVASI (Timeline Modern)
         ========================================================================= --}}
    <div class="rsv-card rsv-section-card">
        <h2 class="rsv-section-title">
            <svg class="rsv-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
            </svg>
            <span>Tracking Status Reservasi</span>
        </h2>
        <p class="rsv-section-subtitle">
            Pantau progres tahapan permohonan hingga buku siap diambil
        </p>

        @php
            $isMenunggu = $status === 'menunggu';
            $isDisetujui = $status === 'disetujui';
            $isSiapDiambil = $status === 'siap_diambil';
            $isSelesai = $status === 'selesai';
            $isDitolak = $status === 'ditolak';
            $isDibatalkan = $status === 'dibatalkan';
            $isCancelledBeforeApproval = $isDibatalkan && !$reservation->borrowing_id;
            $isCancellationRejectedAtApproval = $cancellationRequestRejected && $isDisetujui;
            $isCancellationPendingAtPickup = $cancellationRequestPending && $isSiapDiambil;
            $doneVerifikasi = in_array($status, ['disetujui', 'siap_diambil', 'selesai', 'dibatalkan']);
            $donePersetujuan = in_array($status, ['siap_diambil', 'selesai']);
            $donePengambilan = $isSelesai;
        @endphp

        <div class="rsv-tracking-timeline {{ $cancellationRequestRejected ? 'is-cancellation-rejected-at-pickup-flow' : ($isCancelledBeforeApproval ? 'is-cancelled-before-approval-flow' : ($isDibatalkan ? 'is-cancelled-flow' : ($cancellationRequestPending ? 'is-cancellation-pending-flow' . ($isCancellationPendingAtPickup ? ' is-cancellation-pending-at-pickup' : '') : ''))) }}" data-status="{{ $status }}">
            <div class="rsv-tracking-bar-bg"></div>

            {{-- Langkah 1: Pengajuan Reservasi --}}
            <div class="rsv-tracking-step is-done">
                <div class="rsv-step-node">✓</div>
                <div class="rsv-step-label">✓ Pengajuan Reservasi</div>
                <p class="rsv-step-desc">Selesai · {{ $reservation->created_at->format('d M Y') }}</p>
            </div>

            {{-- Langkah 2: Verifikasi Perpustakaan --}}
            <div class="rsv-tracking-step {{ $isMenunggu ? 'is-active is-warning' : (($isDitolak || $isCancelledBeforeApproval) ? 'is-cancelled' : ($doneVerifikasi ? 'is-done' : 'is-upcoming')) }}">
                <div class="rsv-step-node">@if($isDitolak) ✕ @elseif($isCancelledBeforeApproval) ● @elseif($doneVerifikasi) ✓ @elseif($isMenunggu) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($isDitolak) Verifikasi Perpustakaan @elseif($isCancelledBeforeApproval) ● Verifikasi Perpustakaan @elseif($doneVerifikasi) ✓ Verifikasi Perpustakaan @elseif($isMenunggu) ● Verifikasi Perpustakaan @else ○ Verifikasi Perpustakaan @endif</div>
                <p class="rsv-step-desc">@if($isDitolak)<svg class="rsv-rejected-inline-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m9 9 6 6m0-6-6 6"></path></svg>Ditolak @elseif($isCancelledBeforeApproval) Dibatalkan oleh Anggota @elseif($doneVerifikasi) Selesai @else Menunggu Persetujuan @endif</p>
            </div>

            {{-- Langkah 3: Persetujuan Reservasi --}}
            <div class="rsv-tracking-step {{ $isCancellationRejectedAtApproval ? 'is-done' : (($isDibatalkan && !$isCancelledBeforeApproval) ? 'is-cancelled' : ($cancellationRequestPending && !$isCancellationPendingAtPickup ? 'is-cancellation-pending' : ($isDisetujui ? 'is-active' : ($donePersetujuan ? 'is-done' : 'is-upcoming')))) }}">
                <div class="rsv-step-node">@if($isCancellationRejectedAtApproval) &#10003; @elseif(($isDibatalkan && !$isCancelledBeforeApproval) || ($cancellationRequestPending && !$isCancellationPendingAtPickup)) ● @elseif($donePersetujuan) ✓ @elseif($isDisetujui) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($isCancellationRejectedAtApproval) &#10003; Persetujuan Reservasi @elseif($cancellationRequestPending && !$isCancellationPendingAtPickup) Persetujuan Pembatalan @elseif($isDibatalkan) ○ Persetujuan Reservasi @elseif($donePersetujuan) ✓ Persetujuan Reservasi @elseif($isDisetujui) ● Persetujuan Reservasi @else ○ Persetujuan Reservasi @endif</div>
                <p class="rsv-step-desc">@if($isCancellationRejectedAtApproval) Disetujui @elseif($cancellationRequestPending && !$isCancellationPendingAtPickup) Menunggu Persetujuan Admin @elseif($isDibatalkan) Dibatalkan oleh Anggota @elseif($donePersetujuan) Selesai @elseif($isDisetujui) Sedang Diproses @else Belum Selesai @endif</p>
            </div>

            {{-- Langkah 4: Pengambilan Buku --}}
            <div class="rsv-tracking-step {{ $isCancellationPendingAtPickup ? 'is-cancellation-pending' : ($isSelesai ? 'is-done' : (($isSiapDiambil || $isCancellationRejectedAtApproval) ? 'is-active' : 'is-upcoming')) }}">
                <div class="rsv-step-node">@if($isCancellationPendingAtPickup) ● @elseif($donePengambilan) ✓ @elseif($isSiapDiambil || $isCancellationRejectedAtApproval) ● @else ○ @endif</div>
                <div class="rsv-step-label">@if($donePengambilan) ✓ Pengambilan Buku @elseif($isSiapDiambil || $isCancellationRejectedAtApproval) ● Pengambilan Buku @else ○ Pengambilan Buku @endif</div>
                <p class="rsv-step-desc">@if($isCancellationPendingAtPickup) Menunggu Persetujuan Pembatalan @elseif($isDibatalkan) Dibatalkan @elseif($donePengambilan) Selesai @elseif($isSiapDiambil || $isCancellationRejectedAtApproval) Menunggu Pengambilan @else Menunggu @endif</p>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         3. INFORMASI RESERVASI (ID, Tgl Pengajuan, Tgl Persetujuan, Lokasi, Catatan Admin)
         ========================================================================= --}}
    <div class="rsv-card rsv-section-card">
        <h2 class="rsv-section-title">
            <svg class="rsv-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <path d="M9 8h6M9 12h6M9 16h4"></path>
            </svg>
            <span>Informasi Reservasi</span>
        </h2>
        <p class="rsv-section-subtitle">
            Rincian data pengajuan permohonan koleksi perpustakaan
        </p>

        <div class="rsv-info-grid">
            {{-- ID Reservasi --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <svg class="rsv-info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <circle cx="8" cy="11" r="2"></circle>
                        <path d="M6 16c.5-1.3 1.2-2 2-2s1.5.7 2 2M13 10h5M13 14h5"></path>
                    </svg>
                    ID Reservasi
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
                    {{ $cancellationRequestPending ? 'Menunggu Persetujuan Admin' : ($status === 'dibatalkan' ? 'Dibatalkan oleh Anggota' : ($tanggalPersetujuan ?? 'Belum disetujui')) }}
                </div>
            </div>

            {{-- Lokasi Pengambilan --}}
            <div class="rsv-info-box">
                <div class="rsv-info-label">
                    <svg class="rsv-info-icon rsv-info-icon-location" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg>
                    Lokasi Pengambilan
                </div>
                <div class="rsv-info-val">
                    Meja Layanan Sirkulasi, Lantai 1
                </div>
            </div>
        </div>

        @if($status === 'siap_diambil')
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

    @if($cancellationRequestRejected)
        <div class="rsv-admin-alert alert-danger" role="status" aria-live="polite">
            <div class="rsv-admin-alert-icon" aria-hidden="true">!</div>
            <div>
                <div class="rsv-admin-alert-title">Permintaan Pembatalan Ditolak oleh Admin</div>
                <p class="rsv-admin-alert-desc">{{ $cancellationRejectionNotification->message }}</p>
            </div>
        </div>
    @endif

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
    </script>
    @endpush
@endif

@endsection
