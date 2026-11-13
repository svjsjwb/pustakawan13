@extends('layouts.user')
@section('title', 'Reservasi Buku – Perpustakaan Digital')
@section('page-title', 'Reservasi Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
<style>
.reservation-reset-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #dc2626;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: color .2s ease;
}
.reservation-reset-link::after {
    position: absolute;
    right: 0;
    bottom: -2px;
    left: 0;
    height: 1px;
    background: currentColor;
    content: "";
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .22s ease;
}
.reservation-reset-link span { display: inline-block; transition: transform .22s ease; }
.reservation-reset-link:hover,
.reservation-reset-link:focus-visible { color: #b91c1c; }
.reservation-reset-link:hover::after,
.reservation-reset-link:focus-visible::after { transform: scaleX(1); }
.reservation-reset-link:hover span,
.reservation-reset-link:focus-visible span { transform: rotate(90deg); }
.reservation-reset-link:focus-visible { outline: 2px solid rgba(220, 38, 38, .24); outline-offset: 3px; border-radius: 3px; }
.pd-select-wrapper[data-filter-key="status"] .pd-trigger {
    width: 190px;
    min-width: 190px;
    max-width: 190px;
    height: 50px;
    padding: 0 18px;
    box-sizing: border-box;
    transition: background-color .24s ease, border-color .24s ease, color .24s ease, box-shadow .24s ease, transform .24s ease;
}
.reservation-category-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 9999px;
    background: var(--primary-light);
    color: var(--primary);
    font-size: 11px;
    font-weight: 700;
}
[data-theme="dark"] .reservation-category-pill {
    background: rgba(57, 143, 136, .2);
    color: #a6e8df;
}
[data-theme="dark"] .status-badge.status-menunggu {
    background: rgba(245, 158, 11, .16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, .42);
}
[data-theme="dark"] .status-badge.status-menunggu .status-badge-icon { background: rgba(245, 158, 11, .24); color: #fbbf24; }
[data-theme="dark"] .status-badge.status-disetujui {
    background: rgba(22, 163, 74, .16);
    color: #4ade80;
    border-color: rgba(22, 163, 74, .42);
}
[data-theme="dark"] .status-badge.status-disetujui .status-badge-icon { background: rgba(22, 163, 74, .24); color: #4ade80; }
[data-theme="dark"] .status-badge.status-siap_diambil {
    background: rgba(15, 118, 110, .2);
    color: #5eead4;
    border-color: rgba(45, 212, 191, .42);
}
[data-theme="dark"] .status-badge.status-siap_diambil .status-badge-icon { background: rgba(45, 212, 191, .2); color: #5eead4; }
[data-theme="dark"] .status-badge.status-ditolak {
    background: rgba(220, 38, 38, .17);
    color: #f87171;
    border-color: rgba(248, 113, 113, .42);
}
[data-theme="dark"] .status-badge.status-ditolak .status-badge-icon { background: rgba(248, 113, 113, .2); color: #f87171; }
[data-theme="dark"] #reservationFilterForm .pd-trigger:not([class*="reservation-status-"]) .pd-trigger-icon {
    color: #5eead4;
}
[data-theme="dark"] #reservationFilterForm .pd-trigger-arrow {
    display: flex;
    flex: 0 0 18px;
    width: 18px;
    height: 18px;
    align-items: center;
    justify-content: center;
    color: #d1faf4;
    opacity: 1;
    visibility: visible;
}
[data-theme="dark"] #reservationFilterForm .pd-trigger-arrow svg {
    display: block;
    width: 16px;
    height: 16px;
    stroke: currentColor;
}
[data-theme="dark"] #reservationFilterForm .pd-trigger.is-open .pd-trigger-arrow { transform: rotate(180deg); }
[data-theme="dark"] #reservationFilterForm .pd-trigger.reservation-status-menunggu {
    --status-trigger-color: #fbbf24;
    --status-trigger-bg: rgba(245, 158, 11, .16);
    --status-trigger-hover-bg: rgba(245, 158, 11, .24);
    --status-trigger-border: rgba(245, 158, 11, .42);
}
[data-theme="dark"] #reservationFilterForm .pd-trigger.reservation-status-disetujui {
    --status-trigger-color: #4ade80;
    --status-trigger-bg: rgba(22, 163, 74, .16);
    --status-trigger-hover-bg: rgba(22, 163, 74, .24);
    --status-trigger-border: rgba(22, 163, 74, .42);
}
[data-theme="dark"] #reservationFilterForm .pd-trigger.reservation-status-siap {
    --status-trigger-color: #5eead4;
    --status-trigger-bg: rgba(15, 118, 110, .2);
    --status-trigger-hover-bg: rgba(45, 212, 191, .24);
    --status-trigger-border: rgba(45, 212, 191, .42);
}
[data-theme="dark"] #reservationFilterForm .pd-trigger.reservation-status-ditolak {
    --status-trigger-color: #f87171;
    --status-trigger-bg: rgba(220, 38, 38, .17);
    --status-trigger-hover-bg: rgba(248, 113, 113, .25);
    --status-trigger-border: rgba(248, 113, 113, .42);
}
[data-theme="dark"] #reservationFilterForm .pd-trigger[class*="reservation-status-"] {
    background: var(--status-trigger-bg) !important;
    border-color: var(--status-trigger-border) !important;
    color: var(--status-trigger-color) !important;
    box-shadow: 0 2px 10px var(--status-trigger-border) !important;
}
[data-theme="dark"] #reservationFilterForm .pd-trigger[class*="reservation-status-"]:hover,
[data-theme="dark"] #reservationFilterForm .pd-trigger[class*="reservation-status-"].is-open {
    background: var(--status-trigger-hover-bg) !important;
    border-color: var(--status-trigger-border) !important;
    color: var(--status-trigger-color) !important;
}
[data-theme="dark"] #reservationFilterForm .pd-item[data-value=""].is-selected {
    background: rgba(45, 212, 191, .16);
    color: #5eead4;
    border-color: rgba(45, 212, 191, .3);
}
[data-theme="dark"] #reservationFilterForm .pd-item[data-value=""].is-selected .pd-item-icon {
    background: rgba(45, 212, 191, .2);
    color: #5eead4;
}
[data-theme="dark"] #reservationFilterForm .pd-item[data-value=""].is-selected .pd-item-check {
    background: #2dd4bf;
    color: #102323;
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning:hover,
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning.is-selected {
    background: rgba(245, 158, 11, .16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, .3);
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning:hover .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning.is-selected .pd-item-icon {
    background: rgba(245, 158, 11, .2);
    color: #fbbf24;
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-warning.is-selected .pd-item-check { background: #f59e0b; color: #241a08; }
[data-theme="dark"] #reservationFilterForm .pd-item.status-green:hover,
[data-theme="dark"] #reservationFilterForm .pd-item.status-green.is-selected {
    background: rgba(22, 163, 74, .16);
    color: #4ade80;
    border-color: rgba(22, 163, 74, .3);
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-green .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-green:hover .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-green.is-selected .pd-item-icon {
    background: rgba(22, 163, 74, .2);
    color: #4ade80;
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-green.is-selected .pd-item-check { background: #22c55e; color: #102116; }
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready:hover,
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready.is-selected {
    background: rgba(45, 212, 191, .16);
    color: #5eead4;
    border-color: rgba(45, 212, 191, .3);
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready:hover .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready.is-selected .pd-item-icon {
    background: rgba(45, 212, 191, .2);
    color: #5eead4;
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-ready.is-selected .pd-item-check { background: #2dd4bf; color: #102323; }
[data-theme="dark"] #reservationFilterForm .pd-item.status-red:hover,
[data-theme="dark"] #reservationFilterForm .pd-item.status-red.is-selected {
    background: rgba(220, 38, 38, .17);
    color: #f87171;
    border-color: rgba(248, 113, 113, .3);
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-red .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-red:hover .pd-item-icon,
[data-theme="dark"] #reservationFilterForm .pd-item.status-red.is-selected .pd-item-icon {
    background: rgba(248, 113, 113, .2);
    color: #f87171;
}
[data-theme="dark"] #reservationFilterForm .pd-item.status-red.is-selected .pd-item-check { background: #ef4444; color: #fff; }
</style>
@endpush

@section('content')

<section class="user-page-hero">
    <div class="user-page-hero-pattern"></div>
    <div class="user-page-hero-copy">
        <h1>Daftar Reservasi Buku</h1>
        <p>Pantau seluruh proses reservasi buku Anda mulai dari pengajuan, persetujuan, hingga buku tersedia dan siap untuk diambil sesuai jadwal yang ditentukan.</p>
    </div>
    <div class="user-page-hero-stats" aria-label="Ringkasan reservasi">
        <div><span>Total Reservasi Aktif</span><strong id="statActiveRes">{{ $statusCounts['aktif'] ?? 0 }}</strong><small>reservasi berjalan</small></div>
        <div><span>Menunggu Persetujuan</span><strong id="statWaitingRes">{{ $statusCounts['menunggu'] ?? 0 }}</strong><small>perlu diproses</small></div>
        <div><span>Siap Diambil</span><strong id="statReadyRes">{{ $statusCounts['siap_diambil'] ?? 0 }}</strong><small>buku tersedia</small></div>
    </div>
</section>

{{-- Filter & Search Bar --}}
<form method="GET" action="{{ route('user.reservations') }}" class="eg-filter-bar" id="reservationFilterForm">
    <div class="eg-search-input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               class="eg-search-input"
               placeholder="Cari judul buku atau penulis reservasi...">
    </div>

    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">

        {{-- CUSTOM STATUS DROPDOWN --}}
        @php
            $statusTriggerClass = match(request('status')) {
                'menunggu' => 'reservation-status-menunggu',
                'disetujui' => 'reservation-status-disetujui',
                'siap_diambil' => 'reservation-status-siap',
                'ditolak' => 'reservation-status-ditolak',
                'dibatalkan' => 'reservation-status-ditolak',
                default => '',
            };
        @endphp
        <div class="pd-select-wrapper" data-filter-key="status" data-form-id="reservationFilterForm">
            <button type="button" class="pd-trigger {{ request('status') ? 'has-value' : '' }} {{ $statusTriggerClass }}" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon">
                    @if(request('status') === 'menunggu')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @elseif(request('status') === 'disetujui')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @elseif(request('status') === 'siap_diambil')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    @elseif(in_array(request('status'), ['ditolak', 'dibatalkan'], true))
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                    @endif
                </span>
                <span class="pd-trigger-label">
                    @if(request('status') === 'menunggu') Menunggu
                    @elseif(request('status') === 'disetujui') Disetujui
                    @elseif(request('status') === 'siap_diambil') Siap Diambil
                    @elseif(request('status') === 'ditolak') Ditolak
                    @elseif(request('status') === 'dibatalkan') Dibatalkan
                    @else Semua Status
                    @endif
                </span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            {{-- Hidden input to carry value when form submits --}}
            <input type="hidden" name="status" id="reservationStatusInput" value="{{ request('status', '') }}">
            <div class="pd-panel" role="listbox" aria-label="Filter Status Reservasi">
                <div class="pd-item {{ empty(request('status')) ? 'is-selected' : '' }}" role="option" data-value="" data-label="Semua Status">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <span class="pd-item-label">Semua Status</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-warning {{ request('status') === 'menunggu' ? 'is-selected' : '' }}" role="option" data-value="menunggu" data-label="Menunggu">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                    <span class="pd-item-label">Menunggu</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-green {{ request('status') === 'disetujui' ? 'is-selected' : '' }}" role="option" data-value="disetujui" data-label="Disetujui">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                    <span class="pd-item-label">Disetujui</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-ready {{ request('status') === 'siap_diambil' ? 'is-selected' : '' }}" role="option" data-value="siap_diambil" data-label="Siap Diambil">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </span>
                    <span class="pd-item-label">Siap Diambil</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-red {{ request('status') === 'ditolak' ? 'is-selected' : '' }}" role="option" data-value="ditolak" data-label="Ditolak">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                    <span class="pd-item-label">Ditolak</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-red {{ request('status') === 'dibatalkan' ? 'is-selected' : '' }}" role="option" data-value="dibatalkan" data-label="Dibatalkan">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                    <span class="pd-item-label">Dibatalkan</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            </div>
        </div>

        <button type="submit" class="eg-btn-block" style="width: auto; height: 42px; padding: 0 20px;">
            Cari
        </button>

        @if(request('search') || request('status'))
            <a href="{{ route('user.reservations') }}" class="reservation-reset-link">
                <span aria-hidden="true">&times;</span> Reset Filter
            </a>
        @endif
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    var pdBackdrop = document.getElementById('pdGlobalBackdrop');
    if (!pdBackdrop) {
        pdBackdrop = document.createElement('div');
        pdBackdrop.id = 'pdGlobalBackdrop';
        pdBackdrop.className = 'pd-backdrop';
        document.body.appendChild(pdBackdrop);
    }
    var openWrap = null;
    function closeAll() {
        document.querySelectorAll('.pd-select-wrapper.is-open').forEach(function(w) {
            w.classList.remove('is-open');
            var t = w.querySelector('.pd-trigger');
            if (t) { t.classList.remove('is-open'); t.setAttribute('aria-expanded','false'); }
        });
        pdBackdrop.classList.remove('active');
        openWrap = null;
    }
    function openWrap_(wrapper) {
        if (openWrap && openWrap !== wrapper) closeAll();
        wrapper.classList.add('is-open');
        var trigger = wrapper.querySelector('.pd-trigger');
        if (trigger) { trigger.classList.add('is-open'); trigger.setAttribute('aria-expanded','true'); }
        pdBackdrop.classList.add('active');
        openWrap = wrapper;
        var panel = wrapper.querySelector('.pd-panel');
        if (panel) {
            panel.classList.remove('align-right');
            var rect = panel.getBoundingClientRect();
            if (rect.right > window.innerWidth - 16) panel.classList.add('align-right');
        }
    }
    pdBackdrop.addEventListener('click', closeAll);
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeAll(); });
    function initWrapper(wrapper) {
        var trigger = wrapper.querySelector('.pd-trigger');
        var items   = wrapper.querySelectorAll('.pd-item');
        var formId  = wrapper.dataset.formId;
        var hiddenInputId = 'reservationStatusInput';
        var statusClasses = {
            menunggu: 'reservation-status-menunggu',
            disetujui: 'reservation-status-disetujui',
            siap_diambil: 'reservation-status-siap',
            ditolak: 'reservation-status-ditolak',
            dibatalkan: 'reservation-status-ditolak'
        };
        if (!trigger) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrapper.classList.contains('is-open') ? closeAll() : openWrap_(wrapper);
        });
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                var value = item.dataset.value;
                var triggerLabel = trigger.querySelector('.pd-trigger-label');
                var triggerIcon = trigger.querySelector('.pd-trigger-icon');
                var itemIcon = item.querySelector('.pd-item-icon svg');

                var hi = document.getElementById(hiddenInputId);
                if (hi) hi.value = value;

                trigger.classList.remove.apply(trigger.classList, Object.values(statusClasses));
                if (statusClasses[value]) trigger.classList.add(statusClasses[value]);
                trigger.classList.toggle('has-value', Boolean(value));
                if (triggerLabel) triggerLabel.textContent = item.dataset.label || 'Semua Status';
                if (triggerIcon && itemIcon) triggerIcon.replaceChildren(itemIcon.cloneNode(true));
                items.forEach(function(option) {
                    option.classList.toggle('is-selected', option === item);
                });

                closeAll();
                if (formId) {
                    var form = document.getElementById(formId);
                    if (form) window.setTimeout(function() { form.submit(); }, 260);
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.pd-select-wrapper').forEach(initWrapper);
    });
})();
</script>
@endpush

{{-- Data Table / Empty State --}}
@if(!$member || $reservations->isEmpty())
    <div class="eg-empty-state">
        <div class="eg-empty-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <h3 class="eg-empty-title">{{ request('search') ? 'Buku tidak ditemukan' : 'Belum ada reservasi buku.' }}</h3>
        <p class="eg-empty-desc">
            {{ request('search') ? 'Tidak ada reservasi buku yang sesuai dengan kata kunci pencarian Anda.' : 'Anda belum memiliki riwayat pengajuan reservasi buku. Silakan jelajahi koleksi buku di katalog dan ajukan reservasi.' }}
        </p>
        <a href="{{ route('user.catalog') }}" class="eg-btn-block" style="width: auto; display: inline-flex; padding: 12px 28px; margin: 0 auto;">
            Jelajahi Katalog Buku
        </a>
    </div>
@else
    <div class="eg-table-container">
        <table class="eg-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Buku</th>
                    <th style="width: 25%;">Tanggal Reservasi</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 15%; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $res)
                @php
                    $book = $res->book;
                    $status = strtolower($res->status);
                    $badgeClass = match($status) {
                        'menunggu'     => 'status-menunggu',
                        'disetujui'    => 'status-disetujui',
                        'siap_diambil' => 'status-siap_diambil',
                        'ditolak', 'dibatalkan' => 'status-ditolak',
                        default        => 'status-menunggu',
                    };
                    $statusLabel = match($status) {
                        'menunggu'     => 'Menunggu',
                        'disetujui'    => 'Disetujui',
                        'siap_diambil' => 'Siap Diambil',
                        'ditolak'      => 'Ditolak',
                        'dibatalkan'   => 'Dibatalkan',
                        default        => ucfirst($res->status),
                    };
                    if ($res->cancellation_requested_at) {
                        $badgeClass = 'status-menunggu';
                        $statusLabel = 'Menunggu Persetujuan Pembatalan';
                    }
                @endphp
                <tr data-res-id="{{ $res->id }}" data-status="{{ $status }}" data-cancellation-requested="{{ $res->cancellation_requested_at ? '1' : '0' }}">
                    {{-- 1. BUKU --}}
                    <td>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            @if($book?->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}"
                                     alt="{{ $book->title }}"
                                     style="width: 44px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 10px rgba(18,63,61,0.15); flex-shrink: 0;">
                            @else
                                <div style="width: 44px; height: 60px; border-radius: 8px; background: linear-gradient(145deg, #278482, #124f4e); color: #fff; font-weight: 800; font-size: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    {{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 style="font-size: 14.5px; font-weight: 700; color: var(--text); margin: 0 0 3px; line-height: 1.3;">
                                    {{ $book?->title ?? 'Judul Tidak Diketahui' }}
                                </h4>
                                <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 4px;">
                                    {{ $book?->author ?? 'Penulis -' }}
                                </p>
                                @if($book?->category)
                                    <span class="reservation-category-pill">
                                        {{ $book->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- 2. TANGGAL RESERVASI --}}
                    <td>
                        <div style="font-weight: 600; color: var(--text);">
                            {{ $res->reserved_at ? \Carbon\Carbon::parse($res->reserved_at)->format('d M Y') : $res->created_at->format('d M Y') }}
                        </div>
                        @if($res->expires_at)
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 3px;">
                                Batas Pengambilan: {{ \Carbon\Carbon::parse($res->expires_at)->format('d M Y') }}
                            </div>
                        @endif
                    </td>

                    {{-- 3. STATUS (BADGE: Menunggu, Disetujui, Siap Diambil, Ditolak) --}}
                    <td>
                        <span class="status-badge {{ $badgeClass }}">
                            @if($res->cancellation_requested_at || $status === 'menunggu')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                            @elseif($status === 'disetujui')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                            @elseif($status === 'siap_diambil')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                </span>
                            @elseif(in_array($status, ['ditolak', 'dibatalkan'], true))
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </span>
                            @endif
                            <span>{{ $statusLabel }}</span>
                        </span>
                    </td>

                    {{-- 4. AKSI: LIHAT DETAIL PERMINTAAN (HALAMAN KHUSUS DETAIL RESERVASI) --}}
                    <td style="text-align: right;">
                        <a href="{{ route('user.reservations.show', $res->id) }}"
                           class="eg-detail-link"
                           title="Lihat Detail Permintaan: {{ $book?->title ?? '' }}">
                            <span>Lihat Detail Permintaan</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif

@endsection

@push('scripts')
<script>
(function() {
    function pollUserReservations() {
        fetch("{{ route('user.reservations.statusFeed') }}")
            .then(res => res.json())
            .then(data => {
                if (!data || !data.reservations) return;

                // Update hero stats if present
                if (data.counts) {
                    const elActive = document.getElementById('statActiveRes');
                    const elWaiting = document.getElementById('statWaitingRes');
                    const elReady = document.getElementById('statReadyRes');
                    if (elActive && data.counts.active !== undefined) elActive.textContent = data.counts.active;
                    if (elWaiting && data.counts.waiting !== undefined) elWaiting.textContent = data.counts.waiting;
                    if (elReady && data.counts.ready !== undefined) elReady.textContent = data.counts.ready;
                }

                let needsReload = false;

                // Update rows
                data.reservations.forEach(item => {
                    const row = document.querySelector(`tr[data-res-id="${item.id}"]`);
                    if (row) {
                        const currentStatus = row.getAttribute('data-status');
                        const newStatus = (item.status || '').toLowerCase();
                        const cancellationPending = Boolean(item.cancellation_requested_at);
                        const currentCancellationPending = row.dataset.cancellationRequested === '1';

                        if (currentStatus !== newStatus || currentCancellationPending !== cancellationPending) {
                            row.setAttribute('data-status', newStatus);
                            row.dataset.cancellationRequested = cancellationPending ? '1' : '0';

                            // Find badge span
                            const badge = row.querySelector('.status-badge');
                            if (badge) {
                                // Clear old status classes
                                const badgeStatus = cancellationPending ? 'menunggu' : newStatus;
                                badge.className = `status-badge status-${badgeStatus}`;

                                const icons = {
                                    'menunggu': '<span class="status-badge-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>',
                                    'disetujui': '<span class="status-badge-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="20 6 9 17 4 12"/></svg></span>',
                                    'siap_diambil': '<span class="status-badge-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg></span>',
                                    'ditolak': '<span class="status-badge-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>',
                                    'dibatalkan': '<span class="status-badge-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>'
                                };

                                const iconHtml = icons[badgeStatus] || icons['menunggu'];
                                const label = item.status_label || (newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                                badge.innerHTML = `${iconHtml}<span>${label}</span>`;
                            }

                            row.querySelector('.rejection-note')?.remove();
                        }
                    } else {
                        // A new reservation exists that's not rendered on the current table page
                        needsReload = true;
                    }
                });

                if (needsReload) {
                    window.location.reload();
                }
            })
            .catch(() => {});
    }

    // Auto-poll user reservations every 6 seconds
    setInterval(pollUserReservations, 6000);
})();
</script>
@endpush
