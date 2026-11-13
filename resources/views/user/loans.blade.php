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
    width: min(500px, 100%);
    max-height: min(580px, calc(100dvh - 32px));
    overflow: hidden;
    border: 1px solid #e0eeec;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 22px 56px rgba(18, 67, 70, 0.18);
    animation: extSlideUp 0.24s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.ext-picker-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 18px 22px;
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    color: #fff;
}

.ext-picker-header h3 { margin: 0 0 4px; font-size: 17px; line-height: 1.25; font-weight: 750; color: #fff; }
.ext-picker-header p { margin: 0; color: rgba(255,255,255,.85); font-size: 12.5px; }

.ext-picker-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 0;
    max-height: min(380px, calc(100dvh - 200px));
    padding: 16px 18px;
    overflow-y: auto;
}

.ext-picker-book {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 13px 16px;
    border: 1.5px solid #d4ece8;
    border-radius: 14px;
    background: #ffffff;
    color: #173c3b;
    cursor: pointer;
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.ext-picker-book:hover {
    transform: translateY(-2px);
    border-color: #20B2AA;
    background: #f2fbfa;
    box-shadow: 0 6px 18px rgba(24, 163, 156, 0.14);
}

.ext-picker-book:focus-visible {
    outline: 2px solid #20B2AA;
    outline-offset: 2px;
}

.ext-picker-book.is-selected {
    border-color: #168F88;
    background: #e6f7f5;
    box-shadow: 0 0 0 2px rgba(22, 143, 136, 0.3);
}

.ext-picker-book-info-col {
    min-width: 0;
    flex: 1 1 auto;
}

.ext-picker-book-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}

.ext-picker-book-title {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.35;
    color: #173c3b;
}

.ext-picker-book-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
}
.ext-picker-book-badge.is-safe {
    background: #e8f8f0;
    color: #0b8247;
}
.ext-picker-book-badge.is-warning {
    background: #fff6e7;
    color: #b86200;
}

.ext-picker-book-dates-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    color: #64748b;
    flex-wrap: wrap;
}

.ext-picker-book-dates-row svg {
    vertical-align: -2px;
    color: #45B8B0;
    margin-right: 2px;
}

.ext-picker-dot {
    color: #cbd5e1;
}

.ext-picker-book-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1.5px solid #20B2AA;
    background: #f0faf9;
    color: #148A84;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}
.ext-picker-book-btn * {
    pointer-events: none;
}

.ext-picker-book:hover .ext-picker-book-btn {
    background: #168F88;
    color: #fff;
    border-color: #168F88;
    transform: translateX(3px);
}

.ext-picker-empty {
    text-align: center;
    padding: 36px 16px;
}
.ext-picker-empty-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 12px;
    border-radius: 50%;
    background: rgba(32, 178, 170, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #20B2AA;
}
.ext-picker-empty h4 {
    margin: 0 0 6px;
    font-size: 15px;
    font-weight: 700;
    color: #173c3b;
}
.ext-picker-empty p {
    margin: 0;
    font-size: 12.5px;
    color: #64748b;
    line-height: 1.5;
}

.ext-picker-footer {
    display: flex;
    justify-content: flex-end;
    padding: 12px 18px 14px;
    border-top: 1px solid #e6efee;
}

.ext-picker-cancel {
    padding: 10px 20px;
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    background: var(--background, #f8fafc);
    color: var(--text-muted, #64748b);
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
}

.ext-picker-cancel:hover {
    background: var(--border, #e2e8f0);
    color: var(--text, #0f172a);
}

.ext-modal-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #fff;
    cursor: pointer;
    transition: all 0.18s ease;
    flex-shrink: 0;
    margin-right: 2px;
}
.ext-modal-back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateX(-2px);
}

.ext-btn-back-text {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-right: auto;
    padding: 8px 10px;
    border-radius: 10px;
    background: transparent;
    border: 1px solid transparent;
    color: #0f766e;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: inherit;
    white-space: nowrap;
}
.ext-btn-back-text:hover {
    background: rgba(32, 178, 170, 0.12);
    color: #148A84;
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
    box-sizing: border-box;
    overflow: hidden;
    overscroll-behavior: contain;
    background: rgba(0, 0, 0, 0.52);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 16px;
    padding: max(12px, env(safe-area-inset-top)) max(12px, env(safe-area-inset-right)) max(12px, env(safe-area-inset-bottom)) max(12px, env(safe-area-inset-left));
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
    display: flex;
    flex-direction: column;
    background: var(--surface, #ffffff);
    border-radius: 24px;
    width: 100%;
    max-width: 520px;
    max-height: calc(100vh - 32px);
    max-height: min(90dvh, calc(100dvh - 24px));
    min-height: 0;
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
    flex-shrink: 0;
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
    flex: 1 1 auto;
    min-height: 0;
    overflow-x: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: 24px 28px;
    scrollbar-gutter: stable;
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
    padding: 10px 18px;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
    border-top: 1px solid var(--border, #e2e8f0);
}

@media (max-width: 600px) {
    .ext-modal-overlay { padding: 12px; }
    .ext-modal-card { border-radius: 18px; max-height: calc(100dvh - 24px); }
    .ext-modal-header { padding: 16px 18px; }
    .ext-modal-header-left { gap: 10px; }
    .ext-modal-header-icon { width: 38px; height: 38px; border-radius: 12px; }
    .ext-modal-header h3 { font-size: 15px; }
    .ext-modal-header p { font-size: 11.5px; }
    .ext-modal-body { padding: 16px 18px; }
    .ext-modal-footer { padding: 10px 14px; gap: 8px; }
    .ext-btn-cancel, .ext-btn-submit { padding: 8px 12px; }
}

.ext-btn-cancel {
    padding: 9px 18px;
    min-height: 38px;
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
    padding: 9px 20px;
    min-height: 38px;
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
    width: min(290px, calc(100vw - 24px)) !important;
    max-width: calc(100vw - 24px) !important;
    max-height: calc(100vh - 24px) !important;
    max-height: calc(100dvh - 24px) !important;
    overflow-y: auto;
    overscroll-behavior: contain;
    box-sizing: border-box;
    transform-origin: top left;
}

.flatpickr-innerContainer,
.flatpickr-rContainer,
.flatpickr-days,
.dayContainer { width: 100% !important; max-width: 100% !important; }
.dayContainer { min-width: 100% !important; }
.flatpickr-weekdays { height: 27px !important; }
.flatpickr-weekday { height: 27px !important; line-height: 27px !important; }
.flatpickr-day {
    flex-basis: 14.2857% !important;
    max-width: 14.2857% !important;
    height: 34px !important;
    line-height: 34px !important;
    box-sizing: border-box;
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
    height: 46px !important;
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
    gap: 8px;
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

.ext-calendar-navigation-group {
    display: flex;
    flex: 1 1 auto;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    margin-inline: auto;
}

.ext-calendar-month-control {
    display: flex;
    flex: 1 1 0;
    align-items: center;
    justify-content: space-between;
    gap: 3px;
    height: 36px;
    min-width: 0;
    padding: 0 4px;
    box-sizing: border-box;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
}

.ext-calendar-month-label {
    display: flex;
    flex: 1 1 0;
    align-items: center;
    justify-content: center;
    min-width: 0;
    height: 34px;
    box-sizing: border-box;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    line-height: 1;
    white-space: nowrap;
}

.ext-calendar-year-label {
    display: flex;
    flex: 1 1 0;
    align-items: center;
    justify-content: center;
    min-width: 0;
    height: 100%;
    box-sizing: border-box;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
}

.ext-calendar-year-control {
    display: flex;
    flex: 1 1 0;
    align-items: center;
    gap: 3px;
    height: 36px;
    min-width: 0;
    padding: 0 4px;
    box-sizing: border-box;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
}

.ext-calendar-year-nav {
    display: grid;
    flex: 0 0 22px;
    place-items: center;
    width: 22px;
    height: 26px;
    padding: 0;
    box-sizing: border-box;
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

@media (max-width: 360px) {
    .flatpickr-calendar { width: min(276px, calc(100vw - 24px)) !important; }
    .ext-calendar-navigation-group { gap: 8px; }
    .flatpickr-months .flatpickr-month { height: 44px !important; }
    .flatpickr-current-month { gap: 4px; }
    .flatpickr-weekdays, .flatpickr-weekday { height: 24px !important; line-height: 24px !important; }
    .flatpickr-day { height: 32px !important; line-height: 32px !important; }
    .ext-calendar-year-control { gap: 1px; padding: 0 2px; }
    .ext-calendar-year-nav { width: 20px; height: 24px; }
}

@media (max-height: 620px) {
    .flatpickr-months .flatpickr-month { height: 42px !important; }
    .flatpickr-weekdays, .flatpickr-weekday { height: 24px !important; line-height: 24px !important; }
    .flatpickr-day { height: 30px !important; line-height: 30px !important; }
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
    <div class="loans-stat-card is-green">
        <span class="loans-stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
            </svg>
        </span>
        <strong>{{ $activeCount }}</strong><small>Sedang Dipinjam</small>
    </div>
    <div class="loans-stat-card is-yellow">
        <span class="loans-stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </span>
        <strong>{{ $nearDueCount }}</strong><small>Mendekati Batas Pengembalian</small>
    </div>
    <div class="loans-stat-card is-red">
        <span class="loans-stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </span>
        <strong>{{ $overdueCount }}</strong><small>Terlambat</small>
    </div>
    <div class="loans-stat-card is-slate">
        <span class="loans-stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </span>
        <strong>{{ $completedCount }}</strong><small>Selesai</small>
    </div>
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
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        Ajukan Perpanjangan
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
        <div class="loans-side-heading">
            <span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </span>
            <h3>Aksi Cepat</h3>
        </div>
        @if($borrowings->isNotEmpty())
            <button type="button" class="loans-side-action loans-side-button" id="btnQuickExtend" onclick="openExtendPicker(); return false;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="loans-action-icon">
                    <polyline points="23 4 23 10 17 10"/>
                    <polyline points="1 20 1 14 7 14"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                <span>Perpanjang Peminjaman</span>
            </button>
        @else
            <a href="{{ route('user.catalog') }}" class="loans-side-action">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="loans-action-icon">
                    <polyline points="23 4 23 10 17 10"/>
                    <polyline points="1 20 1 14 7 14"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                <span>Perpanjang Peminjaman</span>
            </a>
        @endif
        <a href="{{ route('user.loans') }}" class="loans-side-action" id="btnViewAllLoans">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="loans-action-icon">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Lihat Semua Peminjaman</span>
        </a>
    </div>
    <div class="loans-side-panel loans-important">
        <div class="loans-side-heading">
            <span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <h3>Informasi Penting</h3>
        </div>
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
            <div>
                <h3 id="extPickerTitle">Pilih Buku untuk Diperpanjang</h3>
                <p>Pilih salah satu judul buku yang sedang Anda pinjam.</p>
            </div>
        </div>
        <div class="ext-picker-list">
            @php
                $eligibleLoans = $borrowings->filter(function($b) {
                    $days = $b->days_remaining ?? 0;
                    return $b->status === 'dipinjam' 
                        && $b->extension_status !== 'menunggu' 
                        && $days >= 0 
                        && empty($b->has_reservation_queue);
                });
            @endphp

            @forelse($eligibleLoans as $borrowing)
                @foreach($borrowing->details as $detail)
                    @php
                        $bookTitle = $detail->book?->title ?? 'Judul Buku';
                        $daysLeft = $borrowing->days_remaining ?? null;
                        $isNear = $daysLeft !== null && $daysLeft <= 3;
                    @endphp
                    <div class="ext-picker-book js-extension-picker-item"
                         tabindex="0"
                         role="button"
                         onclick="selectExtensionBookItem(this)"
                         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectExtensionBookItem(this);}"
                         data-extension-url="{{ route('user.loans.extend', $borrowing->id) }}"
                         data-book-title="{{ $bookTitle }}"
                         data-due-date="{{ $borrowing->due_at?->format('Y-m-d') }}"
                         data-due-formatted="{{ $borrowing->due_at?->format('d M Y') }}"
                         data-borrowed-formatted="{{ $borrowing->borrowed_at?->format('d M Y') }}"
                         data-borrowed-date="{{ $borrowing->borrowed_at?->format('Y-m-d') }}">
                        <div class="ext-picker-book-info-col">
                            <div class="ext-picker-book-title-row">
                                <span class="ext-picker-book-title">{{ $bookTitle }}</span>
                                <span class="ext-picker-book-badge {{ $isNear ? 'is-warning' : 'is-safe' }}">
                                    {{ $isNear ? ($daysLeft == 0 ? 'Jatuh tempo hari ini' : 'Mendekati batas (' . $daysLeft . ' hari lagi)') : 'Sedang Dipinjam (' . $daysLeft . ' hari lagi)' }}
                                </span>
                            </div>
                            <div class="ext-picker-book-dates-row">
                                <span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Dipinjam: <strong>{{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }}</strong>
                                </span>
                                <span class="ext-picker-dot">•</span>
                                <span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Jatuh tempo: <strong>{{ $borrowing->due_at?->format('d M Y') ?? '-' }}</strong>
                                </span>
                            </div>
                        </div>
                        <button type="button" class="ext-picker-book-btn" tabindex="-1" aria-hidden="true">
                            <span>Pilih Buku</span>
                            <span class="ext-picker-book-arrow">→</span>
                        </button>
                    </div>
                @endforeach
            @empty
                <div class="ext-picker-empty">
                    <div class="ext-picker-empty-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h4>Tidak Ada Buku yang Dapat Diperpanjang</h4>
                    <p>Hanya peminjaman aktif yang belum melewati batas pengembalian, tidak memiliki antrean reservasi, dan belum mengajukan perpanjangan yang dapat diperpanjang.</p>
                </div>
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
                <div class="ext-info-box is-full is-warning" id="extNewDueBox">
                    <div class="ext-info-box-label">Tanggal Pengembalian Baru</div>
                    <div class="ext-info-box-value" id="extNewDueValue">Pilih tanggal setelah jatuh tempo saat ini</div>
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
            <button type="button" class="ext-btn-back-text" onclick="backToExtendPicker()">← Pilih Buku Lain</button>
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

function updateExtensionCalendarHeader(instance) {
    const controls = instance.extensionCalendarControls;
    if (!controls) return;

    controls.monthLabel.textContent = instance.l10n.months.longhand[instance.currentMonth];
    controls.yearLabel.textContent = String(instance.currentYear);
}

function addExtensionCalendarNavigation(instance) {
    const [dueYear, dueMonth, dueDay] = EXT_CURRENT_DUE_ISO.split('-').map(Number);
    const currentDue = new Date(dueYear, dueMonth - 1, dueDay);
    const navigationGroup = document.createElement('div');
    navigationGroup.className = 'ext-calendar-navigation-group';

    const monthControl = document.createElement('div');
    monthControl.className = 'ext-calendar-month-control';
    const previousMonthButton = document.createElement('button');
    previousMonthButton.type = 'button';
    previousMonthButton.className = 'ext-calendar-year-nav is-previous';
    previousMonthButton.setAttribute('aria-label', 'Bulan sebelumnya');
    const monthLabel = document.createElement('span');
    monthLabel.className = 'ext-calendar-month-label';
    const nextMonthButton = document.createElement('button');
    nextMonthButton.type = 'button';
    nextMonthButton.className = 'ext-calendar-year-nav is-next';
    nextMonthButton.setAttribute('aria-label', 'Bulan berikutnya');
    monthControl.append(previousMonthButton, monthLabel, nextMonthButton);

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

    navigationGroup.append(monthControl, yearControl);
    instance.calendarContainer.querySelector('.flatpickr-current-month').appendChild(navigationGroup);
    instance.extensionCalendarControls = { monthLabel, yearLabel };

    previousMonthButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeMonth(-1);
    });
    nextMonthButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeMonth(1);
    });
    previousYearButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeYear(instance.currentYear - 1);
    });
    nextYearButton.addEventListener('click', function(event) {
        event.stopPropagation();
        instance.changeYear(instance.currentYear + 1);
    });

    instance.jumpToDate(currentDue);
    updateExtensionCalendarHeader(instance);
}

function openExtendPicker() {
    const pickerOverlay = document.getElementById('extensionPickerOverlay');
    if (pickerOverlay) {
        pickerOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeExtendPicker() {
    document.getElementById('extensionPickerOverlay')?.classList.remove('open');
    if (!document.getElementById('extendModalOverlay')?.classList.contains('open')) {
        document.body.style.overflow = '';
    }
}

function selectExtensionBookItem(element) {
    if (!element) return;
    document.querySelectorAll('.js-extension-picker-item').forEach(function(el) {
        el.classList.remove('is-selected');
    });
    element.classList.add('is-selected');

    const data = element.dataset;
    closeExtendPicker();
    openExtendModal(
        data.extensionUrl,
        data.bookTitle,
        data.dueDate,
        data.dueFormatted,
        data.borrowedFormatted,
        data.borrowedDate
    );
}

function backToExtendPicker() {
    closeExtendModal();
    openExtendPicker();
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

document.querySelectorAll('.js-extension-card-button').forEach(function(button) {
    button.addEventListener('click', function() {
        openExtendModalFromButton(button);
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
        position: 'auto',
        onReady: function(selectedDates, dateStr, instance) {
            instance._positionElement = instance.altInput || instance.input;
            addExtensionCalendarNavigation(instance);
        },
        onYearChange: function(selectedDates, dateStr, instance) {
            updateExtensionCalendarHeader(instance);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
            updateExtensionCalendarHeader(instance);
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
    if (!document.getElementById('extensionPickerOverlay')?.classList.contains('open')) {
        document.body.style.overflow = '';
    }
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
        const newDueVal = document.getElementById('extNewDueValue');
        const newDueBox = document.getElementById('extNewDueBox');
        if (newDueVal) newDueVal.textContent = 'Pilih tanggal setelah jatuh tempo saat ini';
        if (newDueBox) newDueBox.classList.remove('has-selected-date');
        return;
    }

    // Valid — tampilkan preview
    pickerInput.classList.remove('is-error');
    validationMsg.classList.remove('show', 'is-error');
    validationMsg.classList.add('show', 'is-success');
    validationText.textContent = `Tanggal valid. Perpanjangan ${diffDays} hari dari jatuh tempo saat ini.`;
    validationMsg.querySelector('svg').innerHTML = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';

    // Update preview
    const formattedDate = selected.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    document.getElementById('extPreviewDate').textContent = formattedDate;
    document.getElementById('extPreviewDays').textContent = `+${diffDays} Hari`;
    previewCard.classList.add('show');

    // Update tanggal pengembalian baru pada bagian informasi
    const newDueVal = document.getElementById('extNewDueValue');
    const newDueBox = document.getElementById('extNewDueBox');
    if (newDueVal) newDueVal.textContent = formattedDate;
    if (newDueBox) newDueBox.classList.add('has-selected-date');

    // Aktifkan tombol submit
    btnSubmit.disabled = false;
}

function showError(msg) {
    const validationMsg  = document.getElementById('extValidationMsg');
    const validationText = document.getElementById('extValidationText');
    if (!validationMsg || !validationText) return;
    validationMsg.classList.remove('is-success');
    validationMsg.classList.add('show', 'is-error');
    validationText.textContent = msg;
    // Update icon ke X circle
    validationMsg.querySelector('svg').innerHTML = '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>';
    const newDueVal = document.getElementById('extNewDueValue');
    const newDueBox = document.getElementById('extNewDueBox');
    if (newDueVal) newDueVal.textContent = 'Pilih tanggal setelah jatuh tempo saat ini';
    if (newDueBox) newDueBox.classList.remove('has-selected-date');
}

function resetPreview() {
    const validationMsg = document.getElementById('extValidationMsg');
    if (validationMsg) validationMsg.classList.remove('show', 'is-error', 'is-success');
    document.getElementById('extPreviewCard')?.classList.remove('show');
    const btnSubmit = document.getElementById('extBtnSubmit');
    if (btnSubmit) btnSubmit.disabled = true;
    document.getElementById('extDatePicker')?.classList.remove('is-error');
    const newDueVal = document.getElementById('extNewDueValue');
    const newDueBox = document.getElementById('extNewDueBox');
    if (newDueVal) newDueVal.textContent = 'Pilih tanggal setelah jatuh tempo saat ini';
    if (newDueBox) newDueBox.classList.remove('has-selected-date');
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
