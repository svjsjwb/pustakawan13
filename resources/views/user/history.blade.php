@extends('layouts.user')
@section('title', 'Riwayat Peminjaman - Perpustakaan Digital')
@section('page-title', 'Riwayat Peminjaman')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
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
               class="eg-search-input"
               placeholder="Cari judul buku atau penulis riwayat...">
    </div>

    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

        {{-- CUSTOM STATUS DROPDOWN (hanya Dikembalikan, karena riwayat = selesai) --}}
        {{-- Filter status dihapus karena riwayat sudah pasti hanya 'dikembalikan' --}}

        {{-- CUSTOM MONTH DROPDOWN --}}
        @php
            $indonesianMonths = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $activeMonth = request('month', '');
            $activeMonthLabel = $activeMonth && isset($indonesianMonths[(int)$activeMonth])
                ? $indonesianMonths[(int)$activeMonth]
                : 'Semua Bulan';
        @endphp
        <div class="pd-select-wrapper" data-form-id="historyFilterForm" data-input-id="historyMonthInput">
            <button type="button" class="pd-trigger {{ $activeMonth ? 'has-value' : '' }}" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
                <span class="pd-trigger-label">{{ $activeMonthLabel }}</span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <input type="hidden" name="month" id="historyMonthInput" value="{{ $activeMonth }}">
            <div class="pd-panel pd-scrollable" role="listbox" aria-label="Filter Bulan Riwayat">
                <div class="pd-item {{ empty($activeMonth) ? 'is-selected' : '' }}" role="option" data-value="" data-label="Semua Bulan">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <span class="pd-item-label">Semua Bulan</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                @foreach($indonesianMonths as $mNum => $mName)
                <div class="pd-item {{ (string)$activeMonth === (string)$mNum ? 'is-selected' : '' }}" role="option" data-value="{{ $mNum }}" data-label="{{ $mName }}">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <span class="pd-item-label">{{ $mName }}</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="eg-btn-block" style="width: auto; height: 42px; padding: 0 18px;">
            Filter
        </button>

        @if(request('search') || request('month'))
            <a href="{{ route('user.history') }}" style="color: #dc2626; font-size: 13px; font-weight: 700; text-decoration: none;">
                &times; Reset Filter
            </a>
        @endif
    </div>
</form>

{{-- Semua Riwayat: Gabungan Peminjaman + Reservasi --}}
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 10px 0 6px;">
    <div>
        <span style="font-size: 11px; font-weight: 800; letter-spacing: .08em; color: var(--primary); text-transform: uppercase;">Semua Aktivitas</span>
        <h2 style="margin: 4px 0 0; font-size: 20px; color: var(--text);">Semua Riwayat</h2>
    </div>
    @if($activities->isNotEmpty())
        <span style="font-size: 13px; color: var(--text-muted);">{{ $activities->count() }} aktivitas</span>
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
        <h3 class="eg-empty-title">{{ request('search') ? 'Tidak ditemukan' : 'Belum ada riwayat' }}</h3>
        <p class="eg-empty-desc">
            {{ request('search')
                ? 'Tidak ada riwayat yang sesuai dengan kata kunci pencarian Anda.'
                : 'Riwayat akan muncul secara otomatis setelah Anda melakukan peminjaman atau reservasi buku.' }}
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
                    <th style="width: 42%;">Buku</th>
                    <th style="width: 18%;">Tanggal</th>
                    <th style="width: 13%; text-align: center;">Jenis</th>
                    <th style="width: 27%; text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $item)
                @php $book = $item->book; @endphp
                <tr>
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
                        <div style="font-weight: 600; color: var(--text);">
                            {{ $item->date_label }}
                        </div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 3px;">
                            {{ $item->extra }}
                        </div>
                    </td>

                    {{-- Jenis Aktivitas --}}
                    <td style="text-align: center;">
                        @if($item->type === 'borrowing')
                            <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: rgba(39,132,130,0.12); color: var(--primary); white-space: nowrap;">
                                Peminjaman
                            </span>
                        @else
                            <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: rgba(124,58,237,0.10); color: #7c3aed; white-space: nowrap;">
                                Reservasi
                            </span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="text-align: right;">
                        <span class="status-badge {{ $item->badge_class }}">
                            {{ $item->status_label }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

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
        var trigger     = wrapper.querySelector('.pd-trigger');
        var items       = wrapper.querySelectorAll('.pd-item');
        var formId      = wrapper.dataset.formId;
        var inputId     = wrapper.dataset.inputId;
        if (!trigger) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrapper.classList.contains('is-open') ? closeAll() : openWrap_(wrapper);
        });
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                var value = item.dataset.value;
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
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.pd-select-wrapper').forEach(initWrapper);
    });
})();
</script>
@endpush

@endsection
