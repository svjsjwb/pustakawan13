@extends('layouts.user')
@section('title', 'Reservasi Buku – Perpustakaan Digital')
@section('page-title', 'Reservasi Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/premium-dropdown.css') }}">
@endpush

@section('content')

{{-- Page Header (Senada Beranda) --}}
<div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 28px;">
    <div>
        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; background: var(--primary-light); border: 1px solid rgba(40, 123, 120, 0.25); border-radius: 9999px; font-size: 11px; font-weight: 800; color: var(--primary); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px;">
            <span>📅</span> LAYANAN RESERVASI BUKU
        </div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text); margin: 0 0 6px; font-family: 'Poppins', sans-serif; letter-spacing: -0.02em;">
            Daftar Reservasi Buku
        </h1>
        <p style="font-size: 14.5px; color: var(--text-muted); margin: 0;">
            Pantau status permohonan reservasi koleksi buku Anda secara real-time.
        </p>
    </div>

    <a href="{{ route('user.catalog') }}" class="eg-btn-block" style="width: auto; padding: 12px 24px; font-size: 13.5px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
        <span>Jelajahi Katalog Buku</span>
    </a>
</div>

{{-- 3 Stat Cards di Atas: Reservasi Aktif, Menunggu Persetujuan, Siap Diambil --}}
<div class="eg-stats-grid">
    {{-- Card 1: Reservasi Aktif --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon">
            📚
        </div>
        <div class="eg-stat-info">
            <div class="num">{{ $statusCounts['aktif'] ?? 0 }}</div>
            <div class="label">Reservasi Aktif</div>
        </div>
    </div>

    {{-- Card 2: Menunggu Persetujuan --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
            ⏳
        </div>
        <div class="eg-stat-info">
            <div class="num">{{ $statusCounts['menunggu'] ?? 0 }}</div>
            <div class="label">Menunggu Persetujuan</div>
        </div>
    </div>

    {{-- Card 3: Siap Diambil --}}
    <div class="eg-stat-card">
        <div class="eg-stat-icon" style="background: rgba(18, 63, 61, 0.14); color: #123f3d;">
            📦
        </div>
        <div class="eg-stat-info">
            <div class="num">{{ $statusCounts['siap_diambil'] ?? 0 }}</div>
            <div class="label">Siap Diambil</div>
        </div>
    </div>
</div>

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
        <div class="pd-select-wrapper" data-filter-key="status" data-form-id="reservationFilterForm">
            <button type="button" class="pd-trigger {{ request('status') ? 'has-value' : '' }}" aria-haspopup="listbox" aria-expanded="false">
                <span class="pd-trigger-icon">
                    @if(request('status') === 'menunggu')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @elseif(request('status') === 'disetujui')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @elseif(request('status') === 'siap_diambil')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    @elseif(request('status') === 'ditolak')
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
                <div class="pd-item {{ request('status') === 'siap_diambil' ? 'is-selected' : '' }}" role="option" data-value="siap_diambil" data-label="Siap Diambil">
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
            </div>
        </div>

        <button type="submit" class="eg-btn-block" style="width: auto; height: 42px; padding: 0 20px;">
            Cari
        </button>

        @if(request('search') || request('status'))
            <a href="{{ route('user.reservations') }}" style="color: #dc2626; font-size: 13px; font-weight: 700; text-decoration: none;">
                × Reset Filter
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
        if (!trigger) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            wrapper.classList.contains('is-open') ? closeAll() : openWrap_(wrapper);
        });
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                var value = item.dataset.value;
                // Update hidden input
                var hi = document.getElementById(hiddenInputId);
                if (hi) hi.value = value;
                closeAll();
                // Submit the form
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
        <h3 class="eg-empty-title">Belum ada reservasi buku.</h3>
        <p class="eg-empty-desc">
            Anda belum memiliki riwayat pengajuan reservasi buku. Silakan jelajahi koleksi buku di katalog dan ajukan reservasi.
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
                        'ditolak'      => 'status-ditolak',
                        default        => 'status-menunggu',
                    };
                    $statusLabel = match($status) {
                        'menunggu'     => 'Menunggu',
                        'disetujui'    => 'Disetujui',
                        'siap_diambil' => 'Siap Diambil',
                        'ditolak'      => 'Ditolak',
                        default        => ucfirst($res->status),
                    };
                @endphp
                <tr>
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
                                    ✍️ {{ $book?->author ?? 'Penulis -' }}
                                </p>
                                @if($book?->category)
                                    <span style="font-size: 11px; padding: 2px 8px; background: var(--primary-light); color: var(--primary); border-radius: 9999px; font-weight: 700;">
                                        {{ $book->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- 2. TANGGAL RESERVASI --}}
                    <td>
                        <div style="font-weight: 600; color: var(--text);">
                            📅 {{ $res->reserved_at ? \Carbon\Carbon::parse($res->reserved_at)->format('d M Y') : $res->created_at->format('d M Y') }}
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
                            @if($status === 'menunggu')
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
                            @elseif($status === 'ditolak')
                                <span class="status-badge-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </span>
                            @endif
                            <span>{{ $statusLabel }}</span>
                        </span>
                        @if($res->rejection_reason && $status === 'ditolak')
                            <div style="font-size: 11px; color: #dc2626; margin-top: 4px;" title="{{ $res->rejection_reason }}">
                                Ket: {{ Str::limit($res->rejection_reason, 24) }}
                            </div>
                        @endif
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

    {{-- Pagination --}}
    @if($reservations->hasPages())
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            {{ $reservations->links('partials.user-catalog-pagination') }}
        </div>
    @endif
@endif

@endsection
