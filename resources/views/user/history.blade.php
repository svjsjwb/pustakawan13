@extends('layouts.user')
@section('title', 'Riwayat Peminjaman - Perpustakaan Digital')
@section('page-title', 'Riwayat Peminjaman')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.eg-history-filter-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.eg-history-filter-controls .pd-select-wrapper { flex: 0 0 auto; }
.eg-history-filter-controls .pd-trigger { min-width: 174px; }
.eg-history-filter-controls .history-period-filter .pd-trigger { min-width: 158px; }
.history-range-wrap { display: none; width: 230px; }
.history-range-wrap.is-visible { display: block; }
.history-range-input { width: 100%; height: 42px; padding: 0 12px; border: 1.5px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font: inherit; font-size: 13px; cursor: pointer; }
.history-range-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-subtle); }
.history-filter-empty[hidden] { display: none; }
.history-filter-empty { margin-top: 12px; }
.history-reset-link {
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
.history-reset-link[hidden] { display: none; }
.history-reset-link::after {
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
.history-reset-link span { display: inline-block; transition: transform .22s ease; }
.history-reset-link:hover,
.history-reset-link:focus-visible { color: #b91c1c; }
.history-reset-link:hover::after,
.history-reset-link:focus-visible::after { transform: scaleX(1); }
.history-reset-link:hover span,
.history-reset-link:focus-visible span { transform: rotate(90deg); }
.history-reset-link:focus-visible { outline: 2px solid rgba(220, 38, 38, .24); outline-offset: 3px; border-radius: 3px; }
.history-activity-option { --activity-color: #0f766e; --activity-soft: rgba(15, 118, 110, .1); --activity-border: rgba(15, 118, 110, .2); }
.history-activity-trigger.activity-all { --activity-color: #0f766e; --activity-soft: rgba(15, 118, 110, .1); --activity-border: rgba(15, 118, 110, .2); }
.history-activity-option.activity-borrowing,
.history-activity-trigger.activity-borrowing { --activity-color: #287879; --activity-soft: rgba(39, 132, 130, .12); --activity-border: rgba(39, 132, 130, .28); }
.history-activity-option.activity-reservation,
.history-activity-trigger.activity-reservation { --activity-color: #7c3aed; --activity-soft: rgba(124, 58, 237, .1); --activity-border: rgba(124, 58, 237, .24); }
.history-activity-option.activity-return,
.history-activity-trigger.activity-return { --activity-color: #15803d; --activity-soft: rgba(22, 163, 74, .1); --activity-border: rgba(22, 163, 74, .25); }
.history-activity-option.activity-extension,
.history-activity-trigger.activity-extension { --activity-color: #b45309; --activity-soft: rgba(245, 158, 11, .12); --activity-border: rgba(245, 158, 11, .28); }
.history-activity-option .pd-item-icon { background: var(--activity-soft); color: var(--activity-color); }
.history-activity-option:hover,
.history-activity-option.is-selected { background: var(--activity-soft); color: var(--activity-color); border-color: var(--activity-border); }
.history-activity-option:hover .pd-item-icon,
.history-activity-option.is-selected .pd-item-icon { background: var(--activity-soft); color: var(--activity-color); }
.history-activity-option.is-selected .pd-item-check,
.history-activity-option .pd-item-check { background: var(--activity-color); }
.history-activity-trigger.activity-selected { color: var(--activity-color); background: var(--activity-soft); border-color: var(--activity-border); box-shadow: 0 2px 10px var(--activity-border); }
.history-activity-trigger.activity-selected:hover,
.history-activity-trigger.activity-selected.is-open { color: var(--activity-color); background: var(--activity-soft); border-color: var(--activity-border); }
.history-activity-trigger.activity-selected .pd-trigger-icon,
.history-activity-trigger.activity-selected .pd-trigger-arrow { color: inherit; }
.flatpickr-calendar.history-range-calendar {
    border: 1px solid rgba(15, 118, 110, .15);
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0, 0, 0, .15);
}
.history-range-calendar .flatpickr-months .flatpickr-month {
    height: 52px;
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    border-radius: 16px 16px 0 0;
}
.history-range-calendar .flatpickr-current-month,
.history-range-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.history-range-calendar .flatpickr-current-month input.cur-year,
.history-range-calendar .flatpickr-prev-month,
.history-range-calendar .flatpickr-next-month,
.history-range-calendar .flatpickr-prev-month svg,
.history-range-calendar .flatpickr-next-month svg { color: #fff; fill: #fff; }
.history-range-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.history-range-calendar .flatpickr-current-month input.cur-year { font-weight: 700; }
.history-range-calendar .flatpickr-prev-month,
.history-range-calendar .flatpickr-next-month { display: none !important; }
.history-range-calendar .flatpickr-current-month {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    height: 100%;
    padding: 0 10px;
    box-sizing: border-box;
}
.history-range-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.history-range-calendar .flatpickr-current-month .cur-month,
.history-range-calendar .flatpickr-current-month .numInputWrapper { display: none !important; }
.history-range-calendar .ext-calendar-select { position: static; flex: 0 0 auto; }
.history-range-calendar .ext-calendar-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    height: 36px;
    padding: 0 12px;
    border: 1px solid rgba(255, 255, 255, .38);
    border-radius: 10px;
    background: rgba(255, 255, 255, .14);
    color: #fff;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
}
.history-range-calendar .ext-calendar-select-month .ext-calendar-trigger { width: 142px; }
.history-range-calendar .ext-calendar-year-control {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 36px;
    padding: 0 4px;
    border: 1px solid rgba(255, 255, 255, .3);
    border-radius: 10px;
    background: rgba(255, 255, 255, .1);
}
.history-range-calendar .ext-calendar-year-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 36px;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
}
.history-range-calendar .ext-calendar-year-nav {
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
    transition: background-color .16s ease;
}
.history-range-calendar .ext-calendar-year-nav::before {
    width: 7px;
    height: 7px;
    border-top: 2px solid currentColor;
    border-right: 2px solid currentColor;
    content: "";
}
.history-range-calendar .ext-calendar-year-nav.is-previous::before { transform: rotate(-135deg); }
.history-range-calendar .ext-calendar-year-nav.is-next::before { transform: rotate(45deg); }
.history-range-calendar .ext-calendar-year-nav:hover,
.history-range-calendar .ext-calendar-year-nav:focus-visible { outline: none; background: rgba(255, 255, 255, .2); }
.history-range-calendar .ext-calendar-trigger::after {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    content: "";
    transform: translateY(-2px) rotate(45deg);
    transition: transform .18s ease;
}
.history-range-calendar .ext-calendar-select.is-open .ext-calendar-trigger,
.history-range-calendar .ext-calendar-trigger:hover,
.history-range-calendar .ext-calendar-trigger:focus-visible {
    outline: none;
    border-color: rgba(255, 255, 255, .8);
    background: rgba(255, 255, 255, .24);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, .14);
}
.history-range-calendar .ext-calendar-select.is-open .ext-calendar-trigger::after { transform: translateY(2px) rotate(225deg); }
.history-range-calendar .ext-calendar-menu {
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
    box-shadow: 0 14px 34px rgba(8, 47, 45, .24);
    opacity: 0;
    visibility: hidden;
    transform: translateY(5px);
    transition: opacity .16s ease, transform .16s ease, visibility .16s ease;
    scrollbar-width: thin;
    scrollbar-color: #9ac9c3 transparent;
}
.history-range-calendar .ext-calendar-menu.is-visible { opacity: 1; visibility: visible; transform: translateY(0); }
.history-range-calendar .ext-calendar-menu.opens-up { transform: translateY(-5px); }
.history-range-calendar .ext-calendar-menu.opens-up.is-visible { transform: translateY(0); }
.history-range-calendar .ext-calendar-option {
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
    transition: background-color .14s ease, color .14s ease;
}
.history-range-calendar .ext-calendar-option:hover,
.history-range-calendar .ext-calendar-option:focus-visible { outline: none; background: #edf8f6; color: #0f766e; }
.history-range-calendar .ext-calendar-option.is-current { background: #dff3ef; color: #0f766e; font-weight: 700; }
.history-range-calendar .flatpickr-weekday { color: #0f766e; font-weight: 700; }
.history-range-calendar .flatpickr-day.selected,
.history-range-calendar .flatpickr-day.startRange,
.history-range-calendar .flatpickr-day.endRange,
.history-range-calendar .flatpickr-day.selected:hover { background: var(--primary); border-color: var(--primary); }
.history-range-calendar .flatpickr-day.inRange { background: #dff3ef; border-color: #dff3ef; box-shadow: -5px 0 0 #dff3ef, 5px 0 0 #dff3ef; }
.history-range-calendar .flatpickr-day:not(.flatpickr-disabled) { transition: background-color .18s ease, border-color .18s ease; }
.history-range-calendar .flatpickr-day:hover:not(.flatpickr-disabled) { background: rgba(15, 118, 110, .12); border-color: transparent; }
.history-range-calendar .flatpickr-day.flatpickr-disabled,
.history-range-calendar .flatpickr-day.flatpickr-disabled:hover { color: #cbd5e1; background: transparent; cursor: not-allowed; opacity: .58; }
@media (max-width: 900px) { .eg-history-filter-controls { width: 100%; } }
@media (max-width: 640px) {
    .eg-history-filter-controls .pd-select-wrapper,
    .eg-history-filter-controls .pd-trigger,
    .history-range-wrap { width: 100%; }
    .eg-history-filter-controls .pd-trigger { justify-content: flex-start; }
}
</style>
@endpush

@section('content')

<section class="user-page-hero">
    <div class="user-page-hero-pattern"></div>
    <div class="user-page-hero-copy">
        <h1>Riwayat Peminjaman Buku</h1>
        <p>Pantau seluruh aktivitas peminjaman dan reservasi buku Anda, mulai dari transaksi aktif, menunggu persetujuan, hingga riwayat yang telah selesai.</p>
    </div>
    <div class="user-page-hero-stats" aria-label="Ringkasan riwayat peminjaman">
        <div><span>Total Dipinjam</span><strong>{{ $stats['total_borrowed'] }}</strong><small>seluruh transaksi</small></div>
        <div><span>Total Reservasi</span><strong>{{ $stats['total_reservations'] }}</strong><small>seluruh reservasi</small></div>
        <div><span>Sedang Dipinjam</span><strong>{{ $stats['active_borrowed'] }}</strong><small>buku aktif</small></div>
        <div><span>Selesai</span><strong>{{ $stats['returned_borrowed'] }}</strong><small>sudah dikembalikan</small></div>
    </div>
</section>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('user.history') }}" class="eg-filter-bar" id="historyFilterForm">
    <div class="eg-search-input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               data-applied-value="{{ request('search') }}"
               class="eg-search-input"
               placeholder="Cari judul buku atau penulis riwayat...">
    </div>

    <div class="eg-history-filter-controls">

        @php
            $activityLabels = [
                'all' => 'Semua Aktivitas',
                'borrowing' => 'Peminjaman',
                'reservation' => 'Reservasi',
                'return' => 'Pengembalian',
                'extension' => 'Perpanjangan',
            ];
            $periodLabels = [
                'all' => 'Semua Waktu',
                'month' => 'Bulan Ini',
                '3months' => '3 Bulan Terakhir',
                '6months' => '6 Bulan Terakhir',
                'year' => 'Tahun Ini',
                'custom' => 'Pilih Rentang Tanggal',
            ];
            $activeActivity = request('activity', 'all');
            $activeActivity = isset($activityLabels[$activeActivity]) ? $activeActivity : 'all';
            $activePeriod = request('period', 'all');
            $activePeriod = isset($periodLabels[$activePeriod]) ? $activePeriod : 'all';
        @endphp
            <div class="pd-select-wrapper history-filter-dropdown" data-filter="activity">
                <button type="button" class="pd-trigger history-activity-trigger activity-selected activity-{{ $activeActivity }}" aria-haspopup="listbox" aria-expanded="false">
                    <span class="pd-trigger-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16M4 12h16M4 19h16"/><circle cx="8" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="10" cy="19" r="1"/></svg></span>
                    <span class="pd-trigger-label">{{ $activityLabels[$activeActivity] }}</span>
                    <span class="pd-trigger-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                </button>
                <input type="hidden" name="activity" value="{{ $activeActivity }}">
                <div class="pd-panel" role="listbox" aria-label="Jenis Aktivitas">
                    @foreach($activityLabels as $activityValue => $activityLabel)
                    <div class="pd-item history-activity-option activity-{{ $activityValue }} {{ $activeActivity === $activityValue ? 'is-selected' : '' }}" role="option" data-value="{{ $activityValue }}" data-label="{{ $activityLabel }}">
                        <span class="pd-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h14M5 12h14M5 19h14"/><circle cx="8" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="10" cy="19" r="1"/></svg></span>
                        <span class="pd-item-label">{{ $activityLabel }}</span>
                        <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pd-select-wrapper history-filter-dropdown history-period-filter" data-filter="period">
                <button type="button" class="pd-trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span class="pd-trigger-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <span class="pd-trigger-label">{{ $periodLabels[$activePeriod] }}</span>
                    <span class="pd-trigger-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                </button>
                <input type="hidden" name="period" value="{{ $activePeriod }}">
                <div class="pd-panel" role="listbox" aria-label="Periode Riwayat">
                    @foreach($periodLabels as $periodValue => $periodLabel)
                    <div class="pd-item {{ $activePeriod === $periodValue ? 'is-selected' : '' }}" role="option" data-value="{{ $periodValue }}" data-label="{{ $periodLabel }}">
                        <span class="pd-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                        <span class="pd-item-label">{{ $periodLabel }}</span>
                        <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                    </div>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="date_from" id="historyDateFrom" value="{{ request('date_from') }}">
            <input type="hidden" name="date_to" id="historyDateTo" value="{{ request('date_to') }}">
            <div class="history-range-wrap {{ $activePeriod === 'custom' ? 'is-visible' : '' }}">
                <input type="text" id="historyRangePicker" class="history-range-input" placeholder="Pilih rentang tanggal" readonly autocomplete="off" aria-label="Pilih rentang tanggal">
            </div>

        <button type="submit" class="eg-btn-block" style="width: auto; height: 42px; padding: 0 18px;">
            Filter
        </button>

        <a id="historyResetFilter" class="history-reset-link" href="{{ route('user.history') }}" @if(!request('search') && $activeActivity === 'all' && $activePeriod === 'all') hidden @endif>
            <span aria-hidden="true">&times;</span> Reset Filter
        </a>
    </div>
</form>

{{-- Semua Riwayat: Gabungan Peminjaman + Reservasi --}}
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 10px 0 6px;">
    <div>
        <span style="font-size: 11px; font-weight: 800; letter-spacing: .08em; color: var(--primary); text-transform: uppercase;">Semua Aktivitas</span>
        <h2 style="margin: 4px 0 0; font-size: 20px; color: var(--text);">Semua Riwayat</h2>
    </div>
    @if($activities->isNotEmpty())
        <span id="historyActivityCount" style="font-size: 13px; color: var(--text-muted);">{{ $activities->count() }} aktivitas</span>
    @endif
</div>

@if(!$member || $activities->isEmpty())
    <div class="eg-empty-state">
        <div class="eg-empty-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="14 2 14 8 20 8"></polyline>
                <path d="M4 4v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"></path>
                <line x1="8" y1="13" x2="16" y2="13"></line>
                <line x1="8" y1="17" x2="16" y2="17"></line>
            </svg>
        </div>
        <h3 class="eg-empty-title">{{ request('search') || (request('activity') && request('activity') !== 'all') || (request('period') && request('period') !== 'all') ? 'Tidak ada aktivitas ditemukan' : 'Belum ada riwayat' }}</h3>
        <p class="eg-empty-desc">
            {{ request('search') || (request('activity') && request('activity') !== 'all') || (request('period') && request('period') !== 'all')
                ? 'Tidak ditemukan riwayat yang sesuai dengan filter yang dipilih.'
                : 'Riwayat akan muncul secara otomatis setelah Anda melakukan peminjaman atau reservasi buku.' }}
        </p>
        <a href="{{ route('user.catalog') }}" class="eg-btn-block" style="width: auto; display: inline-flex; padding: 12px 28px; margin: 0 auto;">
            Jelajahi Katalog Buku
        </a>
    </div>
@else
    <div class="eg-table-container" id="historyActivityTable">
        <table class="eg-table">
            <thead>
                <tr>
                    <th style="width: 42%;">Buku</th>
                    <th style="width: 18%;">Tanggal</th>
                    <th style="width: 13%; text-align: center;">Jenis</th>
                    <th style="width: 27%; text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $item)
                @php $book = $item->book; @endphp
                <tr data-activity="{{ $item->type === 'reservation' ? 'reservation' : ($item->badge_class === 'status-selesai' ? 'return' : 'borrowing') }}" data-activity-date="{{ $item->date?->format('Y-m-d') }}">
                    {{-- Cover & Judul --}}
                    <td>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            @if($book?->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}"
                                     alt="{{ $book->title }}"
                                     style="width: 46px; height: 62px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 10px rgba(18,63,61,0.15); flex-shrink: 0;">
                            @else
                                <div style="width: 46px; height: 62px; border-radius: 8px; background: linear-gradient(145deg, #278482, #124f4e); color: #fff; font-weight: 800; font-size: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    {{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 style="font-size: 14.5px; font-weight: 700; color: var(--text); margin: 0 0 3px; line-height: 1.3;">
                                    {{ $book?->title ?? 'Judul Tidak Tersedia' }}
                                </h4>
                                <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 4px;">
                                    {{ $book?->author ?? 'Penulis Anonim' }}
                                </p>
                                @if($book?->category)
                                    <span style="font-size: 11px; padding: 2px 8px; background: var(--primary-light); color: var(--primary); border-radius: 9999px; font-weight: 700;">
                                        {{ $book->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    {{-- Tanggal --}}
                    <td>
                        <div style="font-weight: 600; color: var(--text);">{{ $item->date_label }}</div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 3px;">{{ $item->extra }}</div>
                    </td>

                    {{-- Jenis Aktivitas --}}
                    <td style="text-align: center;">
                        @if($item->type === 'borrowing')
                            <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: rgba(39,132,130,0.12); color: var(--primary); white-space: nowrap;">Peminjaman</span>
                        @else
                            <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: rgba(124,58,237,0.10); color: #7c3aed; white-space: nowrap;">Reservasi</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="text-align: right;">
                        <span class="status-badge {{ $item->badge_class }}">{{ $item->status_label }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="eg-empty-state history-filter-empty" id="historyFilterEmpty" hidden>
        <h3 class="eg-empty-title">Tidak ada aktivitas ditemukan</h3>
        <p class="eg-empty-desc">Tidak ditemukan riwayat yang sesuai dengan filter yang dipilih.</p>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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
    var historyDateFrom = document.getElementById('historyDateFrom');
    var historyDateTo = document.getElementById('historyDateTo');
    var rangeWrap = document.querySelector('.history-range-wrap');
    var rangeInput = document.getElementById('historyRangePicker');
    var rangePicker = null;
    var historySearchInput = document.querySelector('#historyFilterForm [name="search"]');
    var historyResetLink = document.getElementById('historyResetFilter');

    function syncHistoryResetLink() {
        var form = document.getElementById('historyFilterForm');
        if (!form || !historyResetLink) return;
        var search = form.querySelector('[name="search"]')?.dataset.appliedValue?.trim() || '';
        var activity = form.querySelector('[name="activity"]')?.value || 'all';
        var period = form.querySelector('[name="period"]')?.value || 'all';
        historyResetLink.hidden = !search && activity === 'all' && period === 'all';
    }

    historySearchInput?.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') event.preventDefault();
    });

    function toLocalISO(date) {
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function closeHistoryCalendarMenu(instance) {
        var controls = instance.historyCalendarControls;
        if (!controls) return;
        controls.monthControl.classList.remove('is-open');
        controls.monthTrigger.setAttribute('aria-expanded', 'false');
        controls.monthMenu.classList.remove('is-visible', 'opens-up');
    }

    function updateHistoryCalendarControls(instance) {
        var controls = instance.historyCalendarControls;
        if (!controls) return;
        controls.monthTrigger.textContent = instance.l10n.months.longhand[instance.currentMonth];
        controls.yearLabel.textContent = String(instance.currentYear);
        controls.monthMenu.replaceChildren();
        instance.l10n.months.longhand.forEach(function(month, index) {
            var option = document.createElement('button');
            option.type = 'button';
            option.className = 'ext-calendar-option' + (index === instance.currentMonth ? ' is-current' : '');
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', String(index === instance.currentMonth));
            option.textContent = month;
            option.addEventListener('click', function() {
                instance.changeMonth(index, false);
                closeHistoryCalendarMenu(instance);
            });
            controls.monthMenu.appendChild(option);
        });
    }

    function openHistoryCalendarMenu(instance) {
        var controls = instance.historyCalendarControls;
        if (!controls) return;
        closeHistoryCalendarMenu(instance);
        var trigger = controls.monthTrigger;
        var rect = trigger.getBoundingClientRect();
        var calendarRect = instance.calendarContainer.getBoundingClientRect();
        var spaceBelow = window.innerHeight - rect.bottom;
        var opensUp = spaceBelow < 270 && rect.top > spaceBelow;
        var availableSpace = opensUp ? rect.top : spaceBelow;
        var menuWidth = 230;
        controls.monthMenu.style.maxHeight = Math.max(90, Math.min(250, availableSpace - 16)) + 'px';
        controls.monthMenu.style.width = menuWidth + 'px';
        controls.monthMenu.style.left = Math.max(8, Math.min(rect.left - calendarRect.left, calendarRect.width - menuWidth - 8)) + 'px';
        controls.monthMenu.style.top = opensUp ? 'auto' : rect.bottom - calendarRect.top + 6 + 'px';
        controls.monthMenu.style.bottom = opensUp ? calendarRect.bottom - rect.top + 6 + 'px' : 'auto';
        controls.monthMenu.classList.toggle('opens-up', opensUp);
        controls.monthMenu.classList.add('is-visible');
        controls.monthControl.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        controls.monthMenu.querySelector('.is-current')?.scrollIntoView({ block: 'nearest' });
    }

    function addHistoryCalendarControls(instance) {
        var currentMonth = instance.calendarContainer.querySelector('.flatpickr-current-month');
        if (!currentMonth) return;

        var monthControl = document.createElement('div');
        monthControl.className = 'ext-calendar-select ext-calendar-select-month';
        var monthTrigger = document.createElement('button');
        monthTrigger.type = 'button';
        monthTrigger.className = 'ext-calendar-trigger';
        monthTrigger.setAttribute('aria-label', 'Pilih bulan');
        monthTrigger.setAttribute('aria-haspopup', 'listbox');
        monthTrigger.setAttribute('aria-expanded', 'false');
        var monthMenu = document.createElement('div');
        monthMenu.className = 'ext-calendar-menu';
        monthMenu.setAttribute('role', 'listbox');
        monthMenu.setAttribute('aria-label', 'Pilih bulan');
        monthTrigger.addEventListener('click', function(event) {
            event.stopPropagation();
            if (monthControl.classList.contains('is-open')) closeHistoryCalendarMenu(instance);
            else openHistoryCalendarMenu(instance);
        });
        monthControl.appendChild(monthTrigger);
        currentMonth.appendChild(monthControl);
        instance.calendarContainer.appendChild(monthMenu);

        var yearControl = document.createElement('div');
        yearControl.className = 'ext-calendar-year-control';
        var previousYearButton = document.createElement('button');
        previousYearButton.type = 'button';
        previousYearButton.className = 'ext-calendar-year-nav is-previous';
        previousYearButton.setAttribute('aria-label', 'Tahun sebelumnya');
        var yearLabel = document.createElement('span');
        yearLabel.className = 'ext-calendar-year-label';
        var nextYearButton = document.createElement('button');
        nextYearButton.type = 'button';
        nextYearButton.className = 'ext-calendar-year-nav is-next';
        nextYearButton.setAttribute('aria-label', 'Tahun berikutnya');
        yearControl.append(previousYearButton, yearLabel, nextYearButton);
        currentMonth.appendChild(yearControl);

        instance.historyCalendarControls = { monthControl: monthControl, monthTrigger: monthTrigger, monthMenu: monthMenu, yearLabel: yearLabel };
        previousYearButton.addEventListener('click', function(event) {
            event.stopPropagation();
            instance.changeYear(instance.currentYear - 1);
        });
        nextYearButton.addEventListener('click', function(event) {
            event.stopPropagation();
            instance.changeYear(instance.currentYear + 1);
        });
        instance.calendarContainer.addEventListener('click', function(event) {
            if (!event.target.closest('.ext-calendar-select, .ext-calendar-menu')) closeHistoryCalendarMenu(instance);
        });
        updateHistoryCalendarControls(instance);
    }

    if (window.flatpickr && rangeInput) {
        rangePicker = flatpickr(rangeInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            locale: (flatpickr.l10ns && flatpickr.l10ns.id) || 'default',
            disableMobile: true,
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('history-range-calendar');
                addHistoryCalendarControls(instance);
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                updateHistoryCalendarControls(instance);
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                updateHistoryCalendarControls(instance);
            },
            onChange: function(selectedDates) {
                historyDateFrom.value = selectedDates[0] ? toLocalISO(selectedDates[0]) : '';
                historyDateTo.value = selectedDates[1] ? toLocalISO(selectedDates[1]) : '';
                if (selectedDates.length === 2) applyHistoryFilters();
            }
        });

        if (historyDateFrom.value && historyDateTo.value) {
            rangePicker.setDate([historyDateFrom.value, historyDateTo.value], false);
        }
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
        var trigger     = wrapper.querySelector('.pd-trigger');
        var items       = wrapper.querySelectorAll('.pd-item');
        var formId      = wrapper.dataset.formId;
        var inputId     = wrapper.dataset.inputId;
        var filterType  = wrapper.dataset.filter;
        if (!trigger) return;
        if (filterType) {
            var currentValue = wrapper.querySelector('input[type="hidden"]')?.value || 'all';
            trigger.classList.toggle('has-value', currentValue !== 'all');
            if (filterType === 'activity') {
                trigger.classList.add('activity-selected');
                ['all', 'borrowing', 'reservation', 'return', 'extension'].forEach(function(activity) {
                    trigger.classList.remove('activity-' + activity);
                });
                trigger.classList.add('activity-' + currentValue);
            }
        }
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrapper.classList.contains('is-open') ? closeAll() : openWrap_(wrapper);
        });
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                var value = item.dataset.value;
                if (filterType) {
                    var hiddenInput = wrapper.querySelector('input[type="hidden"]');
                    var triggerLabel = trigger.querySelector('.pd-trigger-label');
                    if (hiddenInput) hiddenInput.value = value;
                    if (triggerLabel) triggerLabel.textContent = item.dataset.label;
                    items.forEach(function(option) { option.classList.toggle('is-selected', option === item); });
                    trigger.classList.toggle('has-value', value !== 'all');
                    if (filterType === 'activity') {
                        trigger.classList.add('activity-selected');
                        ['all', 'borrowing', 'reservation', 'return', 'extension'].forEach(function(activity) {
                            trigger.classList.remove('activity-' + activity);
                        });
                        trigger.classList.add('activity-' + value);
                    }
                    closeAll();

                    if (filterType === 'period' && rangeWrap) {
                        rangeWrap.classList.toggle('is-visible', value === 'custom');
                        if (value === 'custom' && rangePicker) rangePicker.open();
                    }
                    syncHistoryResetLink();
                    if (filterType !== 'period' || value !== 'custom' || !rangePicker || rangePicker.selectedDates.length === 2) {
                        applyHistoryFilters();
                    }
                    return;
                }
                var hi = inputId ? document.getElementById(inputId) : null;
                if (hi) hi.value = value;
                closeAll();
                if (formId) {
                    var form = document.getElementById(formId);
                    if (form) form.submit();
                }
            });
        });
    }
    function applyHistoryFilters() {
        var form = document.getElementById('historyFilterForm');
        var table = document.getElementById('historyActivityTable');
        if (!form || !table) return;

        var activity = form.querySelector('[name="activity"]')?.value || 'all';
        var period = form.querySelector('[name="period"]')?.value || 'all';
        var dateFromValue = historyDateFrom?.value || '';
        var dateToValue = historyDateTo?.value || '';
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var periodStart = new Date(today);

        if (period === 'month') periodStart.setDate(1);
        if (period === '3months' || period === '6months') {
            var dayOfMonth = periodStart.getDate();
            var monthsBack = period === '3months' ? 3 : 6;
            periodStart.setDate(1);
            periodStart.setMonth(periodStart.getMonth() - monthsBack);
            var lastDayOfTargetMonth = new Date(periodStart.getFullYear(), periodStart.getMonth() + 1, 0).getDate();
            periodStart.setDate(Math.min(dayOfMonth, lastDayOfTargetMonth));
        }
        if (period === 'year') periodStart.setMonth(0, 1);

        var visibleCount = 0;
        table.querySelectorAll('tbody tr').forEach(function(row) {
            var rowActivity = row.dataset.activity || '';
            var activityMatches = activity === 'all' || rowActivity === activity;
            var rowDate = row.dataset.activityDate ? new Date(row.dataset.activityDate + 'T00:00:00') : null;
            var periodMatches = true;

            if (rowDate && !Number.isNaN(rowDate.getTime())) {
                if (period === 'month') {
                    periodMatches = rowDate.getFullYear() === today.getFullYear() && rowDate.getMonth() === today.getMonth();
                } else if (period === '3months' || period === '6months' || period === 'year') {
                    periodMatches = rowDate >= periodStart && rowDate <= today;
                } else if (period === 'custom' && dateFromValue) {
                    var dateFrom = new Date(dateFromValue + 'T00:00:00');
                    var dateTo = dateToValue ? new Date(dateToValue + 'T23:59:59') : null;
                    periodMatches = rowDate >= dateFrom && (!dateTo || rowDate <= dateTo);
                }
            }

            row.hidden = !(activityMatches && periodMatches);
            if (!row.hidden) visibleCount++;
        });

        var emptyState = document.getElementById('historyFilterEmpty');
        var countLabel = document.getElementById('historyActivityCount');
        table.hidden = visibleCount === 0;
        if (emptyState) emptyState.hidden = visibleCount !== 0;
        if (countLabel) countLabel.textContent = visibleCount + ' aktivitas';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.pd-select-wrapper').forEach(initWrapper);
        syncHistoryResetLink();
        applyHistoryFilters();
    });
})();
</script>
@endpush

@endsection
