@extends('layouts.user')
@section('title', 'Riwayat Peminjaman – Perpustakaan Digital')
@section('page-title', 'Riwayat Peminjaman')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
@endpush

@section('content')

{{-- Page Header (Senada Beranda) --}}
<div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 28px;">
    <div>
        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; background: var(--primary-light); border: 1px solid rgba(40, 123, 120, 0.25); border-radius: 9999px; font-size: 11px; font-weight: 800; color: var(--primary); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px;">
            <span>📋</span> ARSIP PEMINJAMAN BUKU
        </div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text); margin: 0 0 6px; font-family: 'Poppins', sans-serif; letter-spacing: -0.02em;">
            Riwayat Peminjaman Buku
        </h1>
        <p style="font-size: 14.5px; color: var(--text-muted); margin: 0;">
            Pantau catatan peminjaman, tanggal tenggat waktu, dan status buku yang pernah Anda pinjam.
        </p>
    </div>

    <button onclick="window.print()" class="eg-btn-block" style="width: auto; padding: 12px 22px; font-size: 13.5px; background: var(--surface); color: var(--text); border: 1.5px solid var(--border); box-shadow: var(--shadow-sm);">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Cetak Riwayat</span>
    </button>
</div>

{{-- 3 STAT CARDS DI ATAS: Total Dipinjam, Sedang Dipinjam, Selesai --}}
<div class="eg-stats-grid">
    {{-- Card 1: Total Dipinjam --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon">📚</div>
        <div class="eg-stat-info">
            <div class="num">{{ $stats['total_borrowed'] ?? 0 }}</div>
            <div class="label">Total Dipinjam</div>
        </div>
    </div>

    {{-- Card 2: Sedang Dipinjam --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">📖</div>
        <div class="eg-stat-info">
            <div class="num">{{ $stats['active_borrowed'] ?? 0 }}</div>
            <div class="label">Sedang Dipinjam</div>
        </div>
    </div>

    {{-- Card 3: Selesai --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon" style="background: var(--primary-light); color: var(--primary);">✓</div>
        <div class="eg-stat-info">
            <div class="num">{{ $stats['returned_borrowed'] ?? 0 }}</div>
            <div class="label">Selesai</div>
        </div>
    </div>
</div>

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

        {{-- CUSTOM STATUS DROPDOWN --}}
        <div class="pd-select-wrapper" data-form-id="historyFilterForm" data-input-id="historyStatusInput">
            <button type="button" class="pd-trigger {{ request('status') ? 'has-value' : '' }}" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon">
                    @if(request('status') === 'dipinjam')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    @elseif(request('status') === 'dikembalikan')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @elseif(request('status') === 'terlambat')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                    @endif
                </span>
                <span class="pd-trigger-label">
                    @if(request('status') === 'dipinjam') Dipinjam
                    @elseif(request('status') === 'dikembalikan') Dikembalikan
                    @elseif(request('status') === 'terlambat') Terlambat
                    @else Semua Status
                    @endif
                </span>
                <span class="pd-trigger-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <input type="hidden" name="status" id="historyStatusInput" value="{{ request('status', '') }}">
            <div class="pd-panel" role="listbox" aria-label="Filter Status Riwayat">
                <div class="pd-item {{ empty(request('status')) ? 'is-selected' : '' }}" role="option" data-value="" data-label="Semua Status">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <span class="pd-item-label">Semua Status</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item {{ request('status') === 'dipinjam' ? 'is-selected' : '' }}" role="option" data-value="dipinjam" data-label="Dipinjam">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </span>
                    <span class="pd-item-label">Dipinjam</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-green {{ request('status') === 'dikembalikan' ? 'is-selected' : '' }}" role="option" data-value="dikembalikan" data-label="Dikembalikan">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="pd-item-label">Dikembalikan</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
                <div class="pd-item status-red {{ request('status') === 'terlambat' ? 'is-selected' : '' }}" role="option" data-value="terlambat" data-label="Terlambat">
                    <span class="pd-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </span>
                    <span class="pd-item-label">Terlambat</span>
                    <span class="pd-item-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            </div>
        </div>

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

        @if(request('search') || request('status') || request('month'))
            <a href="{{ route('user.history') }}" style="color: #dc2626; font-size: 13px; font-weight: 700; text-decoration: none;">
                × Reset Filter
            </a>
        @endif
    </div>
</form>

{{-- Daftar Riwayat Buku --}}
@if(!$member || $borrowings->isEmpty())
    <div class="eg-empty-state">
        <div class="eg-empty-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="14 2 14 8 20 8"></polyline>
                <path d="M4 4v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"></path>
                <line x1="8" y1="13" x2="16" y2="13"></line>
                <line x1="8" y1="17" x2="16" y2="17"></line>
            </svg>
        </div>
        <h3 class="eg-empty-title">Belum ada riwayat peminjaman</h3>
        <p class="eg-empty-desc">
            Riwayat peminjaman buku akan tercatat secara otomatis setelah Anda meminjam buku melalui katalog perpustakaan.
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
                    <th style="width: 20%;">Tanggal Pinjam</th>
                    <th style="width: 20%;">Tanggal Kembali</th>
                    <th style="width: 15%; text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrowings as $b)
                @php
                    $detail = $b->details->first();
                    $book = $detail?->book;
                    $status = strtolower($b->status);
                    $badgeClass = match($status) {
                        'dipinjam'     => 'status-dipinjam',
                        'dikembalikan' => 'status-dikembalikan',
                        'terlambat'    => 'status-terlambat',
                        default        => 'status-dipinjam',
                    };
                    $statusLabel = match($status) {
                        'dipinjam'     => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat'    => 'Terlambat',
                        default        => ucfirst($b->status),
                    };
                @endphp
                <tr>
                    {{-- 1. COVER BUKU & JUDUL --}}
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
                                    ✍️ {{ $book?->author ?? 'Penulis Anonim' }}
                                </p>
                                @if($book?->category)
                                    <span style="font-size: 11px; padding: 2px 8px; background: var(--primary-light); color: var(--primary); border-radius: 9999px; font-weight: 700;">
                                        {{ $book->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- 2. TANGGAL PINJAM --}}
                    <td>
                        <div style="font-weight: 600; color: var(--text);">
                            📅 {{ $b->borrowed_at ? \Carbon\Carbon::parse($b->borrowed_at)->format('d M Y') : $b->created_at->format('d M Y') }}
                        </div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 3px;">
                            Batas: {{ $b->due_at ? \Carbon\Carbon::parse($b->due_at)->format('d M Y') : '-' }}
                        </div>
                    </td>

                    {{-- 3. TANGGAL KEMBALI --}}
                    <td>
                        @if($b->returned_at)
                            <div style="font-weight: 600; color: var(--primary);">
                                ✓ {{ \Carbon\Carbon::parse($b->returned_at)->format('d M Y') }}
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                Selesai
                            </div>
                        @else
                            <div style="font-weight: 600; color: var(--text-muted);">
                                Belum Dikembalikan
                            </div>
                            @if($b->due_at && now()->gt($b->due_at))
                                <div style="font-size: 11.5px; color: #dc2626; font-weight: 700; margin-top: 2px;">
                                    Lewat Tenggat
                                </div>
                            @endif
                        @endif
                    </td>

                    {{-- 4. STATUS (BADGE: Dipinjam, Dikembalikan, Terlambat) --}}
                    <td style="text-align: right;">
                        <span class="status-badge {{ $badgeClass }}">
                            @if($status === 'dipinjam')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                </span>
                            @elseif($status === 'dikembalikan')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                            @elseif($status === 'terlambat')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                </span>
                            @endif
                            <span>{{ $statusLabel }}</span>
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($borrowings->hasPages())
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            {{ $borrowings->links('partials.user-catalog-pagination') }}
        </div>
    @endif
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
