 @extends('layouts.user')
@section('title', 'Peminjaman Buku – Perpustakaan Tiga Serangkai')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-loans.css') }}">
{{-- Flatpickr date picker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ── Modern Extend Modal ─────────────────────────────────────────── */
.ext-picker-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9998;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(0, 0, 0, 0.48);
    backdrop-filter: blur(4px);
}

.ext-picker-overlay.open { display: flex; }

.ext-picker-card {
    display: flex;
    flex-direction: column;
    width: min(440px, 100%);
    max-height: min(560px, calc(100dvh - 32px));
    overflow: hidden;
    border: 1px solid #e0eeec;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 22px 56px rgba(18, 67, 70, 0.18);
    animation: extSlideUp 0.24s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.ext-picker-header {
    min-height: 76px;
    padding: 14px 18px;
    background: linear-gradient(110deg, #0D7F82 0%, #18A39C 100%);
    color: #fff;
}

.ext-picker-header h3 { margin: 0 0 4px; font-size: 18px; line-height: 1.25; font-weight: 750; }
.ext-picker-header p { margin: 0; color: rgba(255,255,255,.82); font-size: 13px; }

.ext-picker-list {
    display: grid;
    flex: 1 1 auto;
    gap: 10px;
    min-height: 0;
    max-height: min(360px, calc(100dvh - 220px));
    padding: 12px 10px;
    overflow-y: auto;
}

.ext-picker-book {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
    min-height: 68px;
    padding: 9px 14px;
    border: 2px solid #C7E3E0;
    border-radius: 16px;
    background: #fff;
    color: #1B4347;
    text-align: left;
    cursor: default;
    transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
}

.ext-picker-book:hover {
    transform: translateY(-1px);
    border-color: #18A39C;
    box-shadow: 0 4px 14px rgba(24, 163, 156, 0.1);
}

.ext-picker-book > span { min-width: 0; }
.ext-picker-book-title {
    display: block;
    font-size: 15px;
    line-height: 1.3;
    font-weight: 650;
}
.ext-picker-book-meta { display: block; margin-top: 3px; color: #829795; font-size: 12px; line-height: 1.3; overflow-wrap: anywhere; }
.ext-picker-book-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    min-height: 32px;
    padding: 0 12px;
    border-radius: 999px;
    background: #EEF7F6;
    color: #148A84;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}
.ext-picker-book-action {
    border: 0;
    font-family: inherit;
    cursor: pointer;
    transition: transform .22s ease, box-shadow .22s ease, background-color .22s ease, color .22s ease;
}
.ext-picker-book-action:hover {
    transform: translateY(-1px);
    background: #0D7F82;
    color: #fff;
    box-shadow: 0 4px 12px rgba(13, 127, 130, 0.22);
}
.ext-picker-book-action:focus-visible { outline: 3px solid rgba(24, 163, 156, 0.3); outline-offset: 2px; }
.ext-picker-book-arrow {
    display: inline-block;
    margin-left: 3px;
    transition: transform .22s ease;
}
.ext-picker-book-action:hover .ext-picker-book-arrow { transform: translateX(3px); }
.ext-picker-book-status.is-pending { background: #edf1f0; color: #778684; }

.ext-picker-footer {
    display: flex;
    justify-content: flex-end;
    padding: 10px 12px 12px;
    border-top: 1px solid #e6efee;
}

.ext-picker-cancel {
    padding: 11px 22px;
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    background: var(--background, #f8fafc);
    color: var(--text-muted, #64748b);
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
}

.ext-picker-cancel:hover {
    background: var(--border, #e2e8f0);
    color: var(--text, #0f172a);
}

@media (max-width: 480px) {
    .ext-picker-overlay { padding: 12px; }
    .ext-picker-card { max-height: calc(100dvh - 24px); }
    .ext-picker-header { padding: 13px 15px; }
    .ext-picker-header h3 { font-size: 17px; }
    .ext-picker-list { padding: 10px 9px; }
    .ext-picker-book { gap: 8px; padding: 9px 10px; }
    .ext-picker-book-title { font-size: 14px; }
    .ext-picker-book-meta { font-size: 11px; }
    .ext-picker-book-status { min-height: 30px; padding: 0 9px; font-size: 11px; }
}

.ext-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.52);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.ext-modal-overlay.open {
    display: flex;
    animation: extFadeIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes extFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.ext-modal-card {
    background: var(--surface, #ffffff);
    border-radius: 24px;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18), 0 8px 24px rgba(0,0,0,0.1);
    animation: extSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    overflow: hidden;
}

@keyframes extSlideUp {
    from { opacity: 0; transform: translateY(24px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Header */
.ext-modal-header {
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    padding: 22px 28px 20px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.ext-modal-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.ext-modal-header-icon {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.18);
    border: 1.5px solid rgba(255,255,255,0.25);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
}

.ext-modal-header-icon svg {
    width: 22px; height: 22px;
}

.ext-modal-header h3 {
    font-size: 17px;
    font-weight: 800;
    color: #fff;
    margin: 0 0 3px;
    font-family: 'Poppins', sans-serif;
}

.ext-modal-header p {
    font-size: 12.5px;
    color: rgba(255,255,255,0.78);
    margin: 0;
}

.ext-modal-close {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.18s ease;
    flex-shrink: 0;
}
.ext-modal-close:hover {
    background: rgba(255,255,255,0.28);
}

/* Body */
.ext-modal-body {
    padding: 24px 28px;
}

/* Info Grid */
.ext-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}

.ext-info-box {
    background: var(--background, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 11px 14px;
}

.ext-info-box-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--text-muted, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 4px;
}

.ext-info-box-value {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text, #0f172a);
    line-height: 1.3;
}

.ext-info-box.is-full {
    grid-column: 1 / -1;
}

.ext-info-box.is-warning .ext-info-box-value {
    color: #0f766e;
}

/* Date Picker Section */
.ext-datepicker-section {
    margin-bottom: 18px;
}

.ext-datepicker-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--text, #0f172a);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 7px;
}

.ext-datepicker-label svg {
    width: 16px; height: 16px;
    color: #0f766e;
}

.ext-datepicker-input-wrap {
    position: relative;
}

.ext-datepicker-input-wrap input[type="text"],
.ext-datepicker-input-wrap input[type="date"] {
    width: 100%;
    height: 48px;
    padding: 0 44px 0 16px;
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 14px;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    color: var(--text, #0f172a);
    background: var(--surface, #fff);
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.ext-datepicker-input-wrap input:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
}

.ext-datepicker-input-wrap input.is-error {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.ext-datepicker-input-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted, #94a3b8);
    pointer-events: none;
}

.ext-datepicker-input-icon svg {
    width: 18px; height: 18px;
}

/* Validation message */
.ext-validation-msg {
    display: none;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 10px;
    margin-top: 8px;
    align-items: center;
    gap: 7px;
}

.ext-validation-msg.show {
    display: flex;
}

.ext-validation-msg.is-error {
    background: rgba(220, 38, 38, 0.08);
    color: #b91c1c;
    border: 1px solid rgba(220, 38, 38, 0.2);
}

.ext-validation-msg.is-success {
    background: rgba(15, 118, 110, 0.08);
    color: #0f766e;
    border: 1px solid rgba(15, 118, 110, 0.2);
}

.ext-validation-msg svg {
    width: 14px; height: 14px; flex-shrink: 0;
}

/* Preview Card */
.ext-preview-card {
    background: rgba(15, 118, 110, 0.06);
    border: 1.5px solid rgba(15, 118, 110, 0.2);
    border-radius: 14px;
    padding: 14px 16px;
    margin-top: 14px;
    display: none;
    gap: 0;
    flex-direction: column;
}

.ext-preview-card.show {
    display: flex;
    animation: extFadeIn 0.2s ease forwards;
}

.ext-preview-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
}

.ext-preview-row:not(:last-child) {
    border-bottom: 1px solid rgba(15, 118, 110, 0.12);
}

.ext-preview-key {
    font-size: 12px;
    font-weight: 600;
    color: #0f766e;
    opacity: 0.85;
}

.ext-preview-val {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f766e;
    font-family: 'Poppins', sans-serif;
}

/* Reason Field */
.ext-reason-section {
    margin-top: 16px;
}

.ext-reason-section label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text, #0f172a);
    margin-bottom: 7px;
}

.ext-reason-section textarea {
    width: 100%;
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 13px;
    font-family: inherit;
    color: var(--text, #0f172a);
    background: var(--surface, #fff);
    resize: none;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.ext-reason-section textarea:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
}

/* Footer */
.ext-modal-footer {
    padding: 16px 28px 24px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    border-top: 1px solid var(--border, #e2e8f0);
}

.ext-btn-cancel {
    padding: 11px 22px;
    border-radius: 12px;
    background: var(--background, #f8fafc);
    border: 1.5px solid var(--border, #e2e8f0);
    color: var(--text-muted, #64748b);
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s ease;
}

.ext-btn-cancel:hover {
    background: var(--border, #e2e8f0);
    color: var(--text, #0f172a);
}

.ext-btn-submit {
    padding: 11px 24px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    border: none;
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.22s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(15, 118, 110, 0.3);
}

.ext-btn-submit:hover:not(:disabled) {
    box-shadow: 0 6px 20px rgba(15, 118, 110, 0.4);
    transform: translateY(-1px);
}

.ext-btn-submit:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Flatpickr custom styling */
.flatpickr-calendar {
    border-radius: 16px !important;
    box-shadow: 0 16px 48px rgba(0,0,0,0.15) !important;
    border: 1px solid rgba(15, 118, 110, 0.15) !important;
    z-index: 10001 !important;
}

.flatpickr-day.selected,
.flatpickr-day.selected:hover {
    background: #0f766e !important;
    border-color: #0f766e !important;
}

.flatpickr-day:hover:not(.flatpickr-disabled) {
    background: rgba(15, 118, 110, 0.12) !important;
    border-color: transparent !important;
}

.flatpickr-day:not(.flatpickr-disabled) {
    transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}

.flatpickr-months .flatpickr-month {
    position: relative !important;
    height: 52px !important;
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%) !important;
    border-radius: 16px 16px 0 0 !important;
}

.flatpickr-prev-month,
.flatpickr-next-month {
    display: none !important;
}

.flatpickr-current-month {
    position: absolute !important;
    inset: 0 !important;
    width: 100% !important;
    height: 100%;
    padding: 0 10px !important;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.flatpickr-current-month, .flatpickr-current-month .numInputWrapper span,
.flatpickr-prev-month svg, .flatpickr-next-month svg {
    color: #fff !important;
    fill: #fff !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-current-month .cur-month,
.flatpickr-current-month .numInputWrapper {
    display: none !important;
}

.ext-calendar-select {
    position: static;
    flex: 0 0 auto;
}

.ext-calendar-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    height: 36px;
    padding: 0 12px;
    border: 1px solid rgba(255, 255, 255, 0.38);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.14);
    color: #fff;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}

.ext-calendar-select-month .ext-calendar-trigger { width: 142px; }

.ext-calendar-year-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 36px;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
}

.ext-calendar-year-control {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 36px;
    padding: 0 4px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
}

.ext-calendar-year-nav {
    display: grid;
    place-items: center;
    width: 26px;
    height: 28px;
    padding: 0;
    border: 0;
    border-radius: 7px;
    background: transparent;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.16s ease, opacity 0.16s ease;
}

.ext-calendar-year-nav::before {
    content: "";
    width: 7px;
    height: 7px;
    border-top: 2px solid currentColor;
    border-right: 2px solid currentColor;
}

.ext-calendar-year-nav.is-previous::before { transform: rotate(-135deg); }
.ext-calendar-year-nav.is-next::before { transform: rotate(45deg); }

.ext-calendar-year-nav:hover:not(:disabled),
.ext-calendar-year-nav:focus-visible {
    outline: none;
    background: rgba(255, 255, 255, 0.2);
}

.ext-calendar-year-nav:disabled {
    opacity: 0.38;
    cursor: not-allowed;
}

.ext-calendar-trigger::after {
    content: "";
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: translateY(-2px) rotate(45deg);
    transition: transform 0.18s ease;
}

.ext-calendar-select.is-open .ext-calendar-trigger,
.ext-calendar-trigger:hover,
.ext-calendar-trigger:focus-visible {
    outline: none;
    border-color: rgba(255, 255, 255, 0.8);
    background: rgba(255, 255, 255, 0.24);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.14);
}

.ext-calendar-select.is-open .ext-calendar-trigger::after {
    transform: translateY(2px) rotate(225deg);
}

.ext-calendar-menu {
    position: absolute;
    z-index: 10003;
    display: flex;
    flex-direction: column;
    gap: 3px;
    max-height: 250px;
    padding: 6px;
    overflow-y: auto;
    border: 1px solid #d7e8e5;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 14px 34px rgba(8, 47, 45, 0.24);
    opacity: 0;
    visibility: hidden;
    transform: translateY(5px);
    transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s ease;
    scrollbar-width: thin;
    scrollbar-color: #9ac9c3 transparent;
}

.ext-calendar-menu.is-visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.ext-calendar-menu.opens-up {
    transform: translateY(-5px);
}

.ext-calendar-menu.opens-up.is-visible {
    transform: translateY(0);
}

.ext-calendar-option {
    width: 100%;
    min-height: 36px;
    padding: 7px 10px;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: #164743;
    font: inherit;
    font-size: 13px;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.14s ease, color 0.14s ease;
}

.ext-calendar-option:hover,
.ext-calendar-option:focus-visible {
    outline: none;
    background: #edf8f6;
    color: #0f766e;
}

.ext-calendar-option.is-current {
    background: #dff3ef;
    color: #0f766e;
    font-weight: 700;
}

.flatpickr-current-month input.cur-year {
    height: 100%;
    padding: 0;
    color: #fff !important;
    font-weight: 700;
    text-align: center;
    appearance: textfield;
    -moz-appearance: textfield;
}

.flatpickr-current-month input.cur-year::-webkit-inner-spin-button,
.flatpickr-current-month input.cur-year::-webkit-outer-spin-button,
.flatpickr-current-month .numInputWrapper span {
    display: none !important;
}

.flatpickr-weekday { color: #0f766e !important; font-weight: 700 !important; }

.flatpickr-day.flatpickr-disabled,
.flatpickr-day.flatpickr-disabled:hover {
    color: #cbd5e1 !important;
    background: transparent !important;
    text-decoration: line-through;
    cursor: not-allowed !important;
    opacity: .58;
}
</style>
@endpush

@section('content')

@php
    $activeCount = $borrowings->count();
    $nearDueCount = $borrowings->filter(fn($b) => ($b->days_remaining ?? 99) >= 0 && ($b->days_remaining ?? 99) <= 3)->count();
    $overdueCount = $borrowings->filter(fn($b) => ($b->days_remaining ?? 0) < 0)->count();
@endphp

<section class="loans-hero">
    <div class="loans-hero-pattern"></div>
    <div class="loans-hero-copy">
        <h1>Daftar Peminjaman Buku</h1>
        <p>Kelola buku yang sedang Anda pinjam, pantau batas waktu pengembalian, dan perpanjang masa peminjaman dengan mudah.</p>
        <div class="loans-hero-stats">
            <div><strong>{{ $activeCount }}</strong><span>Total Buku Dipinjam</span></div>
            <div><strong>{{ $activeCount }}</strong><span>Jumlah Buku Aktif</span></div>
        </div>
    </div>
    <div class="loans-book-stack" aria-hidden="true">
        <span class="loan-book loan-book-back"></span>
        <span class="loan-book loan-book-mid"></span>
        <span class="loan-book loan-book-front"><i></i></span>
        <span class="loan-book-page"></span>
    </div>
</section>

@if(!$member)
<div class="ul-card">
    <div class="ul-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>Data anggota tidak ditemukan</h3>
        <p>Akun Anda belum terdaftar sebagai anggota perpustakaan. Silakan hubungi petugas.</p>
    </div>
</div>
@else

<div class="loans-stat-grid">
    <div class="loans-stat-card is-green"><span class="loans-stat-icon">↻</span><strong>{{ $activeCount }}</strong><small>Sedang Dipinjam</small></div>
    <div class="loans-stat-card is-yellow"><span class="loans-stat-icon">◷</span><strong>{{ $nearDueCount }}</strong><small>Mendekati Batas Pengembalian</small></div>
    <div class="loans-stat-card is-red"><span class="loans-stat-icon">!</span><strong>{{ $overdueCount }}</strong><small>Terlambat</small></div>
    <div class="loans-stat-card is-slate"><span class="loans-stat-icon">✓</span><strong>{{ $completedCount }}</strong><small>Selesai</small></div>
</div>

<div class="loans-layout">
<main class="loans-main">
<div class="loans-section-heading">
    <div><span class="loans-section-eyebrow">AKTIVITAS TERKINI</span><h2>Buku yang Sedang Dipinjam</h2><p>Berikut adalah daftar buku yang sedang Anda pinjam saat ini.</p></div>
    <div class="loans-search"><span>⌕</span><input type="search" id="loanSearch" placeholder="Cari judul buku, penulis, atau ISBN..." aria-label="Cari peminjaman"></div>
</div>

@if($borrowings->isEmpty())
<div class="ul-card">
    <div class="ul-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
        <h3>Tidak ada peminjaman aktif</h3>
        <p>Anda tidak sedang meminjam buku. Jelajahi katalog untuk menemukan buku yang menarik.</p>
        <a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-primary">Jelajahi Katalog</a>
    </div>
</div>
@else

{{-- Borrowing Cards --}}
<div class="loans-list" id="loansList">
    @foreach($borrowings as $borrowing)
    @php
        $daysLeft = $borrowing->days_remaining ?? null;
        $isOverdue = $daysLeft !== null && $daysLeft < 0;
        $isNearDue = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 3;
        $statusColor = $isOverdue ? 'var(--danger)' : ($isNearDue ? 'var(--brass)' : 'var(--success)');
        $statusBg    = $isOverdue ? 'var(--danger-soft)' : ($isNearDue ? 'var(--brass-soft)' : 'var(--success-soft)');
        $statusLabel = $isOverdue ? 'Terlambat ' . abs($daysLeft) . ' hari' : ($isNearDue ? ($daysLeft == 0 ? 'Hari ini!' : 'Sisa ' . $daysLeft . ' hari') : 'Sisa ' . $daysLeft . ' hari');
    @endphp
    @php
        $firstBook = $borrowing->details->first()?->book;
        $loanTitle = $firstBook?->title ?? 'Buku';
        $loanTitles = strtolower($borrowing->details->map(fn($d) => $d->book?->title)->filter()->implode('|||'));
        $loanSearch = strtolower($loanTitle . ' ' . ($firstBook?->author ?? '') . ' ' . ($firstBook?->isbn ?? ''));
        $totalDays = $borrowing->borrowed_at && $borrowing->due_at ? max(1, $borrowing->borrowed_at->diffInDays($borrowing->due_at)) : 14;
        $elapsedDays = $borrowing->borrowed_at ? max(0, min($totalDays, $borrowing->borrowed_at->diffInDays(now()))) : 0;
        $progress = $isOverdue ? 100 : min(100, max(8, round(($elapsedDays / $totalDays) * 100)));
    @endphp
    <article class="loan-card" id="loan-{{ $borrowing->id }}" data-loan-search="{{ $loanSearch }}" data-titles="{{ $loanTitles }}">
        {{-- Top status bar --}}
        <div class="loan-card-accent" style="background:{{ $statusColor }}"></div>

        <div class="loan-card-content">
            {{-- Header --}}
            <div class="loan-card-head">
                <div>
                    <p class="loan-meta-label">NO. PEMINJAMAN</p>
                    <p class="loan-number">#{{ str_pad($borrowing->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span class="loan-status" style="background:{{ $statusBg }};color:{{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="loan-status loan-status-neutral">Dipinjam</span>
                </div>
            </div>

            {{-- Date info --}}
            <div class="loan-dates">
                <div>
                    <p class="loan-meta-label">TANGGAL PINJAM</p>
                    <p class="loan-date-value">{{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <div style="width:1px;background:var(--line)"></div>
                <div>
                    <p class="loan-meta-label">BATAS KEMBALI</p>
                    <p class="loan-date-value" style="color:{{ $statusColor }}">{{ $borrowing->due_at?->format('d M Y') ?? '-' }}</p>
                </div>
                @if($borrowing->returned_at)
                <div style="width:1px;background:var(--line)"></div>
                <div>
                    <p style="margin:0;font-size:9.5px;font-weight:700;letter-spacing:.08em;color:var(--muted-soft)">DIKEMBALIKAN</p>
                    <p style="margin:0;font-size:13px;font-weight:600;color:var(--ink)">{{ $borrowing->returned_at->format('d M Y') }}</p>
                </div>
                @endif
            </div>

            {{-- Books --}}
            <div class="loan-books">
                @foreach($borrowing->details as $detail)
                <div class="loan-book-row">
                    @if($detail->book?->cover)
                        <img src="{{ asset('storage/'.$detail->book->cover) }}"
                             class="loan-cover"
                             alt="{{ $detail->book->title }}">
                    @else
                        <div class="loan-cover loan-cover-fallback">
                            {{ strtoupper(substr($detail->book?->title ?? 'B', 0, 1)) }}
                        </div>
                    @endif
                    <div class="loan-book-info">
                        <p class="loan-book-title">{{ $detail->book?->title ?? '—' }}</p>
                        <p class="loan-book-author">{{ $detail->book?->author ?? '—' }}</p>
                        <span class="loan-category">{{ $detail->book?->category?->name ?? '—' }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="loan-progress-wrap">
                <div class="loan-progress-label"><span>Progress pengembalian</span><strong>{{ $isOverdue ? 'Terlambat ' . abs($daysLeft) . ' hari' : 'Sisa ' . max(0, $daysLeft) . ' hari' }}</strong></div>
                <div class="loan-progress"><span class="{{ $isOverdue ? 'is-danger' : ($isNearDue ? 'is-warning' : 'is-safe') }}" style="width:{{ $progress }}%"></span></div>
            </div>

            {{-- Action & Status Perpanjangan --}}
            <div class="loan-card-actions">
                @if($borrowing->extension_status === 'menunggu')
                    <div class="loan-extension-note is-pending">
                        <span>⏳</span> Permintaan perpanjangan diajukan hingga {{ $borrowing->extension_requested_due_at?->format('d M Y') }} (Menunggu persetujuan Admin)
                    </div>
                @elseif($borrowing->extension_status === 'disetujui')
                    <div class="loan-extension-note is-approved">
                        ✓ Perpanjangan disetujui hingga {{ $borrowing->due_at?->format('d M Y') }}
                    </div>
                @elseif($borrowing->extension_status === 'ditolak')
                    <div class="loan-extension-note is-rejected">
                        ✕ Permintaan perpanjangan ditolak {{ $borrowing->extension_admin_notes ? '('.$borrowing->extension_admin_notes.')' : '' }}
                    </div>
                @else
                    <div class="loan-extension-note"></div>
                @endif

                @if($borrowing->extension_status !== 'menunggu')
                    <button type="button"
                        class="loans-action-btn js-extension-card-button"
                        data-extension-url="{{ route('user.loans.extend', $borrowing->id) }}"
                        data-book-title="{{ $loanTitle }}"
                        data-due-date="{{ $borrowing->due_at?->format('Y-m-d') }}"
                        data-due-formatted="{{ $borrowing->due_at?->format('d M Y') }}"
                        data-borrowed-formatted="{{ $borrowing->borrowed_at?->format('d M Y') }}"
                        data-borrowed-date="{{ $borrowing->borrowed_at?->format('Y-m-d') }}">
                        ↻ Ajukan Perpanjangan
                    </button>
                @endif
            </div>

            @if($isNearDue || $isOverdue)
            <div class="loan-alert" style="background:{{ $isOverdue ? 'var(--danger-soft)' : 'var(--brass-soft)' }};color:{{ $isOverdue ? 'var(--danger)' : '#7a5420' }}">
                {{ $isOverdue ? '⚠️ Buku ini sudah melewati batas pengembalian. Segera hubungi petugas perpustakaan.' : '⏰ Batas pengembalian semakin dekat. Harap kembalikan tepat waktu.' }}
            </div>
            @endif
        </div>
    </article>
    @endforeach
</div>

@endif

@if($completedBorrowings->isNotEmpty())
<section class="loans-completed-section">
    <div class="loans-section-heading">
        <div><span class="loans-section-eyebrow">ARSIP PEMINJAMAN</span><h2>Peminjaman Selesai</h2><p>Buku yang sudah dikembalikan dan tercatat dalam riwayat Anda.</p></div>
    </div>
    <div class="loans-completed-list">
        @foreach($completedBorrowings as $completed)
            @php $completedBook = $completed->details->first()?->book; @endphp
            <article class="loan-completed-card">
                @if($completedBook?->cover)
                    <img src="{{ asset('storage/'.$completedBook->cover) }}" class="loan-cover" alt="{{ $completedBook->title }}">
                @else
                    <div class="loan-cover loan-cover-fallback">{{ strtoupper(substr($completedBook?->title ?? 'B', 0, 1)) }}</div>
                @endif
                <div class="loan-book-info">
                    <p class="loan-book-title">{{ $completedBook?->title ?? 'Buku' }}</p>
                    <p class="loan-book-author">{{ $completedBook?->author ?? '-' }}</p>
                    <span class="loan-category loan-category-completed">Selesai dikembalikan</span>
                </div>
                <div class="loan-completed-progress"><span>Progress pengembalian</span><strong>Sudah dikembalikan</strong><div><i></i></div></div>
                <a href="{{ route('user.history') }}" class="loans-action-btn">👁 Lihat Detail</a>
            </article>
        @endforeach
    </div>
</section>
@endif
</main>

<aside class="loans-sidebar">
    <div class="loans-side-panel">
        <div class="loans-side-heading"><span>✦</span><h3>Aksi Cepat</h3></div>
        @if($borrowings->isNotEmpty())
            <a href="#extensionPickerOverlay" class="loans-side-action" onclick="openExtendPicker(); return false;">↻ <span>Perpanjang Peminjaman</span></a>
        @else
            <a href="{{ route('user.catalog') }}" class="loans-side-action">↻ <span>Perpanjang Peminjaman</span></a>
        @endif
        <a href="{{ route('user.history') }}" class="loans-side-action">◷ <span>Lihat Semua Peminjaman</span></a>
    </div>
    <div class="loans-side-panel loans-important">
        <div class="loans-side-heading"><span>ⓘ</span><h3>Informasi Penting</h3></div>
        <ul>
            <li>Maksimal peminjaman 5 buku per anggota</li>
            <li>Lama peminjaman 14 hari</li>
            <li>Dapat diperpanjang jika tidak ada antrian</li>
        </ul>
    </div>
</aside>
</div>
@endif

<div class="ext-picker-overlay" id="extensionPickerOverlay" role="dialog" aria-modal="true" aria-labelledby="extPickerTitle">
    <div class="ext-picker-card">
        <div class="ext-picker-header">
            <h3 id="extPickerTitle">Pilih Buku untuk Diperpanjang</h3>
            <p>Pilih judul buku yang ingin Anda perpanjang.</p>
        </div>
        <div class="ext-picker-list">
            @forelse($borrowings as $borrowing)
                @foreach($borrowing->details as $detail)
                    @php
                        $pickerBookTitle = $detail->book?->title ?? 'Judul Tidak Diketahui';
                        $pickerPending = $borrowing->extension_status === 'menunggu';
                    @endphp
                    <div class="ext-picker-book">
                        <span>
                            <span class="ext-picker-book-title">{{ $pickerBookTitle }}</span>
                            <span class="ext-picker-book-meta">Dipinjam {{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }} · Jatuh tempo {{ $borrowing->due_at?->format('d M Y') ?? '-' }}</span>
                        </span>
                        @if($pickerPending)
                            <span class="ext-picker-book-status is-pending">Menunggu persetujuan</span>
                        @else
                            <button type="button"
                                    class="ext-picker-book-status ext-picker-book-action js-extension-picker-button"
                                    data-extension-url="{{ route('user.loans.extend', $borrowing->id) }}"
                                    data-book-title="{{ $pickerBookTitle }}"
                                    data-due-date="{{ $borrowing->due_at?->format('Y-m-d') }}"
                                    data-due-formatted="{{ $borrowing->due_at?->format('d M Y') }}"
                                    data-borrowed-formatted="{{ $borrowing->borrowed_at?->format('d M Y') }}"
                                    data-borrowed-date="{{ $borrowing->borrowed_at?->format('Y-m-d') }}">
                                <span>Pilih Buku</span><span class="ext-picker-book-arrow" aria-hidden="true">→</span>
                            </button>
                        @endif
                    </div>
                @endforeach
            @empty
                <p style="margin:8px;color:#718080;font-size:13px">Tidak ada buku yang sedang dipinjam.</p>
            @endforelse
        </div>
        <div class="ext-picker-footer">
            <button type="button" class="ext-picker-cancel" onclick="closeExtendPicker()">Batal</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL PERPANJANGAN PEMINJAMAN MODERN — DATE PICKER (Flatpickr)
     ═══════════════════════════════════════════════════════════════════ --}}
<div id="extendModalOverlay" class="ext-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="extModalTitle">
    <div class="ext-modal-card">

        {{-- Header --}}
        <div class="ext-modal-header">
            <div class="ext-modal-header-left">
                <div class="ext-modal-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h3 id="extModalTitle">Ajukan Perpanjangan Peminjaman</h3>
                    <p>Pilih tanggal pengembalian baru sesuai kebutuhan Anda</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="ext-modal-body">

            {{-- Info Grid --}}
            <div class="ext-info-grid">
                <div class="ext-info-box is-full">
                    <div class="ext-info-box-label">Judul Buku</div>
                    <div class="ext-info-box-value" id="extBookTitle">—</div>
                </div>
                <div class="ext-info-box">
                    <div class="ext-info-box-label">Tanggal Pinjam</div>
                    <div class="ext-info-box-value" id="extBorrowedAt">—</div>
                </div>
                <div class="ext-info-box">
                    <div class="ext-info-box-label">Jatuh Tempo Saat Ini</div>
                    <div class="ext-info-box-value" id="extCurrentDue">—</div>
                </div>
                <div class="ext-info-box is-full is-warning">
                    <div class="ext-info-box-label">Tanggal Pengembalian Baru</div>
                    <div class="ext-info-box-value">Pilih tanggal setelah jatuh tempo saat ini</div>
                </div>
            </div>

            {{-- Date Picker --}}
            <form id="extendForm" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="ext-datepicker-section">
                    <div class="ext-datepicker-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Tanggal Pengembalian Baru
                    </div>
                    <div class="ext-datepicker-input-wrap">
                        <input type="text"
                               id="extDatePicker"
                               name="new_due_date"
                               placeholder="Pilih tanggal..."
                               readonly
                               autocomplete="off">
                        <span class="ext-datepicker-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </span>
                    </div>

                    {{-- Validation Message --}}
                    <div class="ext-validation-msg" id="extValidationMsg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span id="extValidationText"></span>
                    </div>

                    {{-- Preview Card --}}
                    <div class="ext-preview-card" id="extPreviewCard">
                        <div class="ext-preview-row">
                            <span class="ext-preview-key">📅 Tanggal Baru</span>
                            <span class="ext-preview-val" id="extPreviewDate">—</span>
                        </div>
                        <div class="ext-preview-row">
                            <span class="ext-preview-key">⏱ Durasi Tambahan</span>
                            <span class="ext-preview-val" id="extPreviewDays">— hari</span>
                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="ext-reason-section">
                    <label for="extReason">Alasan Perpanjangan <span style="color:var(--text-muted,#94a3b8);font-weight:400">(Opsional)</span></label>
                    <textarea id="extReason" name="reason" rows="2" placeholder="Contoh: Buku masih dibutuhkan untuk riset tugas..."></textarea>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="ext-modal-footer">
            <button type="button" class="ext-btn-cancel" onclick="closeExtendModal()">Batal</button>
            <button type="button" class="ext-btn-submit" id="extBtnSubmit" disabled onclick="submitExtendForm()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Perpanjang Peminjaman
            </button>
        </div>

    </div>
</div>

@push('scripts')
{{-- Flatpickr JS --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
// ── Search Loans ──────────────────────────────────────────────────────────────
const loanSearch = document.getElementById('loanSearch');
loanSearch?.addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    let visibleCount = 0;
    const cards = document.querySelectorAll('.loan-card');
    cards.forEach(function (card) {
        if (keyword === '') {
            card.hidden = false;
            visibleCount++;
            return;
        }
        const titles = (card.dataset.titles || '').split('|||').map(s => s.trim().toLowerCase());
        const match = titles.some(t => t.startsWith(keyword));
        card.hidden = !match;
        if (match) visibleCount++;
    });

    let emptyEl = document.getElementById('loanSearchEmpty');
    const loansList = document.getElementById('loansList');
    if (!emptyEl && loansList) {
        emptyEl = document.createElement('div');
        emptyEl.id = 'loanSearchEmpty';
        emptyEl.className = 'ul-card';
        emptyEl.innerHTML = '<div class="ul-empty-state"><p style="margin:0;color:var(--text-muted);font-weight:600;">Buku tidak ditemukan</p></div>';
        loansList.appendChild(emptyEl);
    }
    if (emptyEl) {
        emptyEl.hidden = (visibleCount > 0 || keyword === '');
    }
});

// ── Extend Modal State ────────────────────────────────────────────────────────
let EXT_CURRENT_DUE_ISO = null; // e.g. "2026-09-28"
let EXT_BORROWED_AT_ISO = null;
let EXT_PICKER          = null;

function updateExtensionMonthDropdown(instance) {
    const controls = instance.extensionCalendarControls;
    if (!controls) return;

    controls.monthTrigger.textContent = instance.l10n.months.longhand[instance.currentMonth];
    controls.yearLabel.textContent = String(instance.currentYear);
    controls.previousYearButton.disabled = false;
    instance.prevMonthNav?.classList.remove('flatpickr-disabled');
    controls.monthMenu.replaceChildren();

    instance.l10n.months.longhand.forEach((month, index) => {
        controls.monthMenu.appendChild(createExtensionCalendarOption(
            month,
            index === instance.currentMonth,
            function() {
                instance.changeMonth(index, false);
                closeExtensionCalendarMenus(instance);
            }
        ));
    });
}

function createExtensionCalendarOption(label, isCurrent, onSelect) {
    const option = document.createElement('button');
    option.type = 'button';
    option.className = 'ext-calendar-option' + (isCurrent ? ' is-current' : '');
    option.setAttribute('role', 'option');
    option.setAttribute('aria-selected', String(isCurrent));
    option.textContent = label;
    option.addEventListener('click', onSelect);
    return option;
}

function closeExtensionCalendarMenus(instance) {
    const controls = instance.extensionCalendarControls;
    if (!controls) return;

    controls.monthControl.classList.remove('is-open');
    controls.monthTrigger.setAttribute('aria-expanded', 'false');
    controls.monthMenu.classList.remove('is-visible', 'opens-up');
}

function openExtensionCalendarMenu(instance, control) {
    closeExtensionCalendarMenus(instance);

    const trigger = control.querySelector('button');
    const rect = trigger.getBoundingClientRect();
    const calendarRect = instance.calendarContainer.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;
    const opensUp = spaceBelow < 270 && spaceAbove > spaceBelow;
    const availableSpace = opensUp ? spaceAbove : spaceBelow;
    const menuHeight = Math.max(90, Math.min(250, availableSpace - 16));
    const menuWidth = 190;

    control.menu.style.maxHeight = menuHeight + 'px';
    control.menu.style.width = menuWidth + 'px';
    control.menu.style.left = Math.max(8, Math.min(rect.left - calendarRect.left, calendarRect.width - menuWidth - 8)) + 'px';
    control.menu.style.top = opensUp ? 'auto' : rect.bottom - calendarRect.top + 6 + 'px';
    control.menu.style.bottom = opensUp ? calendarRect.bottom - rect.top + 6 + 'px' : 'auto';
    control.menu.classList.toggle('opens-up', opensUp);
    control.menu.classList.add('is-visible');
    control.classList.add('is-open');
    trigger.setAttribute('aria-expanded', 'true');

    control.menu.querySelector('.is-current')?.scrollIntoView({ block: 'nearest' });
}

function createExtensionCalendarControl(instance, type, label) {
    const currentMonth = instance.calendarContainer.querySelector('.flatpickr-current-month');
    if (!currentMonth) return null;

    const control = document.createElement('div');
    control.className = 'ext-calendar-select ext-calendar-select-' + type;

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'ext-calendar-trigger';
    trigger.setAttribute('aria-label', 'Pilih ' + label.toLowerCase());
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    const menu = document.createElement('div');
    menu.className = 'ext-calendar-menu';
    menu.setAttribute('role', 'listbox');
    menu.setAttribute('aria-label', 'Pilih ' + label.toLowerCase());

    trigger.addEventListener('click', function(event) {
        event.stopPropagation();
        if (control.classList.contains('is-open')) {
            closeExtensionCalendarMenus(instance);
        } else {
            openExtensionCalendarMenu(instance, control);
        }
    });

    control.append(trigger, menu);
    currentMonth.appendChild(control);
    instance.calendarContainer.appendChild(menu);
    control.menu = menu;

    return control;
}

function addExtensionCalendarDropdowns(instance) {
    const monthControl = createExtensionCalendarControl(instance, 'month', 'Bulan');
    if (!monthControl) return;

    const [dueYear, dueMonth, dueDay] = EXT_CURRENT_DUE_ISO.split('-').map(Number);
    const currentDue = new Date(dueYear, dueMonth - 1, dueDay);
    const yearControl = document.createElement('div');
    yearControl.className = 'ext-calendar-year-control';
    const previousYearButton = document.createElement('button');
    previousYearButton.type = 'button';
    previousYearButton.className = 'ext-calendar-year-nav is-previous';
    previousYearButton.setAttribute('aria-label', 'Tahun sebelumnya');
    const yearLabel = document.createElement('span');
    yearLabel.className = 'ext-calendar-year-label';
    const nextYearButton = document.createElement('button');
    nextYearButton.type = 'button';
    nextYearButton.className = 'ext-calendar-year-nav is-next';
    nextYearButton.setAttribute('aria-label', 'Tahun berikutnya');
    yearControl.append(previousYearButton, yearLabel, nextYearButton);
    instance.calendarContainer.querySelector('.flatpickr-current-month').appendChild(yearControl);

    instance.extensionCalendarControls = {
        monthControl,
        monthTrigger: monthControl.querySelector('button'),
        monthMenu: monthControl.menu,
        previousYearButton,
        yearLabel,
    };
    previousYearButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeYear(instance.currentYear - 1);
    });
    nextYearButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeYear(instance.currentYear + 1);
    });
    instance.calendarContainer.addEventListener('click', function(event) {
        if (!event.target.closest('.ext-calendar-select, .ext-calendar-menu')) {
            closeExtensionCalendarMenus(instance);
        }
    });

    instance.jumpToDate(currentDue);
    updateExtensionMonthDropdown(instance);
}

function openExtendPicker() {
    document.getElementById('extensionPickerOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeExtendPicker() {
    document.getElementById('extensionPickerOverlay')?.classList.remove('open');
    if (!document.getElementById('extendModalOverlay')?.classList.contains('open')) {
        document.body.style.overflow = '';
    }
}

function openExtendModalFromButton(button) {
    const data = button.dataset;
    openExtendModal(
        data.extensionUrl,
        data.bookTitle,
        data.dueDate,
        data.dueFormatted,
        data.borrowedFormatted,
        data.borrowedDate
    );
}

function selectExtensionBookFromButton(button) {
    closeExtendPicker();
    openExtendModalFromButton(button);
}

document.querySelectorAll('.js-extension-card-button').forEach(function(button) {
    button.addEventListener('click', function() {
        openExtendModalFromButton(button);
    });
});

document.querySelectorAll('.js-extension-picker-button:not(:disabled)').forEach(function(button) {
    button.addEventListener('click', function() {
        selectExtensionBookFromButton(button);
    });
});

function openExtendModal(actionUrl, bookTitle, dueDateISO, dueDateFormatted, borrowedAtFormatted, borrowedAtISO) {
    // Hitung batas
    const currentDue = new Date(dueDateISO);
    const minDate    = new Date(currentDue);
    minDate.setDate(minDate.getDate() + 1);

    EXT_CURRENT_DUE_ISO = dueDateISO;
    EXT_BORROWED_AT_ISO = borrowedAtISO;
    const overlay = document.getElementById('extendModalOverlay');

    // Isi info panel
    document.getElementById('extBookTitle').textContent   = bookTitle;
    document.getElementById('extBorrowedAt').textContent  = borrowedAtFormatted;
    document.getElementById('extCurrentDue').textContent  = dueDateFormatted;

    // Reset form
    document.getElementById('extendForm').action = actionUrl;
    document.getElementById('extReason').value = '';
    resetPreview();

    // Init / re-init Flatpickr
    if (EXT_PICKER) EXT_PICKER.destroy();
    EXT_PICKER = flatpickr('#extDatePicker', {
        locale: 'id',
        monthSelectorType: 'static',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        minDate: toISODate(minDate),
        disableMobile: true,
        appendTo: document.body,
        position: 'below',
        onReady: function(selectedDates, dateStr, instance) {
            addExtensionCalendarDropdowns(instance);
        },
        onYearChange: function(selectedDates, dateStr, instance) {
            updateExtensionMonthDropdown(instance);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
            updateExtensionMonthDropdown(instance);
        },
        onChange: function(selectedDates, dateStr) {
            onDateSelected(dateStr);
        }
    });

    // Buka overlay
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeExtendModal() {
    const overlay = document.getElementById('extendModalOverlay');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
    if (EXT_PICKER) { EXT_PICKER.clear(); }
    resetPreview();
}

function onDateSelected(dateStr) {
    const validationMsg  = document.getElementById('extValidationMsg');
    const validationText = document.getElementById('extValidationText');
    const previewCard    = document.getElementById('extPreviewCard');
    const btnSubmit      = document.getElementById('extBtnSubmit');
    const pickerInput    = document.getElementById('extDatePicker');

    if (!dateStr) {
        resetPreview();
        return;
    }

    const currentDue = new Date(EXT_CURRENT_DUE_ISO);
    const selected   = new Date(dateStr);
    const diffDays   = Math.round((selected - currentDue) / (1000 * 60 * 60 * 24));

    // Validasi
    if (selected <= currentDue) {
        showError('Tanggal harus lebih besar dari tanggal jatuh tempo saat ini.');
        btnSubmit.disabled = true;
        pickerInput.classList.add('is-error');
        previewCard.classList.remove('show');
        return;
    }

    // Valid — tampilkan preview
    pickerInput.classList.remove('is-error');
    validationMsg.classList.remove('show', 'is-error');
    validationMsg.classList.add('show', 'is-success');
    validationText.textContent = `Tanggal valid. Perpanjangan ${diffDays} hari dari jatuh tempo saat ini.`;

    // Update preview
    const formattedDate = selected.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    document.getElementById('extPreviewDate').textContent = formattedDate;
    document.getElementById('extPreviewDays').textContent = `+${diffDays} Hari`;
    previewCard.classList.add('show');

    // Aktifkan tombol submit
    btnSubmit.disabled = false;
}

function showError(msg) {
    const validationMsg  = document.getElementById('extValidationMsg');
    const validationText = document.getElementById('extValidationText');
    validationMsg.classList.remove('is-success');
    validationMsg.classList.add('show', 'is-error');
    validationText.textContent = msg;
    // Update icon ke X circle
    validationMsg.querySelector('svg').innerHTML = '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>';
}

function resetPreview() {
    document.getElementById('extValidationMsg').classList.remove('show', 'is-error', 'is-success');
    document.getElementById('extPreviewCard').classList.remove('show');
    document.getElementById('extBtnSubmit').disabled = true;
    document.getElementById('extDatePicker')?.classList.remove('is-error');
}

function submitExtendForm() {
    const form = document.getElementById('extendForm');
    if (!form.action || document.getElementById('extBtnSubmit').disabled) return;
    form.submit();
}

function toISODate(date) {
    return date.toISOString().split('T')[0];
}

// Close overlay on backdrop click
document.getElementById('extendModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeExtendModal();
});

document.getElementById('extensionPickerOverlay')?.addEventListener('click', function(event) {
    if (event.target === this) closeExtendPicker();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    if (document.getElementById('extendModalOverlay').classList.contains('open')) closeExtendModal();
    else if (document.getElementById('extensionPickerOverlay')?.classList.contains('open')) closeExtendPicker();
});
</script>
@endpush

@endsection
