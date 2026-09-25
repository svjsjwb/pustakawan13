@extends('layouts.user')
@section('title', 'Peminjaman Buku – Perpustakaan Tiga Serangkai')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-loans.css') }}">
{{-- Flatpickr date picker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ── Modern Extend Modal ─────────────────────────────────────────── */
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

.flatpickr-months .flatpickr-month {
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%) !important;
    border-radius: 16px 16px 0 0 !important;
}

.flatpickr-current-month, .flatpickr-current-month .numInputWrapper span,
.flatpickr-prev-month svg, .flatpickr-next-month svg {
    color: #fff !important;
    fill: #fff !important;
}

.flatpickr-weekday { color: #0f766e !important; font-weight: 700 !important; }

.flatpickr-day.flatpickr-disabled,
.flatpickr-day.flatpickr-disabled:hover {
    color: #cbd5e1 !important;
    background: transparent !important;
    text-decoration: line-through;
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
        <div class="loans-kicker"><span>📚</span> PEMINJAMAN BUKU</div>
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
                        onclick="openExtendModal(
                            '{{ route('user.loans.extend', $borrowing->id) }}',
                            {{ json_encode($loanTitle) }},
                            '{{ $borrowing->due_at?->format('Y-m-d') }}',
                            '{{ $borrowing->due_at?->format('d M Y') }}',
                            '{{ $borrowing->borrowed_at?->format('d M Y') }}'
                        )"
                        class="loans-action-btn">
                        ⏳ Ajukan Perpanjangan
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
        <a href="{{ $borrowings->first() ? route('user.loans') . '#loan-' . $borrowings->first()->id : route('user.catalog') }}" class="loans-side-action">↻ <span>Perpanjang Peminjaman</span></a>
        <a href="{{ route('user.history') }}" class="loans-side-action">◷ <span>Lihat Semua Peminjaman</span></a>
    </div>
    <div class="loans-side-panel loans-important">
        <div class="loans-side-heading"><span>ⓘ</span><h3>Informasi Penting</h3></div>
        <ul>
            <li>Maksimal peminjaman 5 buku per anggota</li>
            <li>Lama peminjaman 14 hari</li>
            <li>Dapat diperpanjang jika tidak ada antrian</li>
            <li>Denda keterlambatan Rp1.000 per hari per buku</li>
        </ul>
    </div>
</aside>
</div>
@endif

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
            <button type="button" class="ext-modal-close" onclick="closeExtendModal()" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
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
                    <div class="ext-info-box-label">Perpanjangan Maksimal</div>
                    <div class="ext-info-box-value">14 Hari dari jatuh tempo</div>
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
let EXT_MAX_DUE_ISO     = null;
let EXT_PICKER          = null;

const MAX_EXTENSION_DAYS = 14;

function openExtendModal(actionUrl, bookTitle, dueDateISO, dueDateFormatted, borrowedAtFormatted) {
    // Hitung batas
    const currentDue = new Date(dueDateISO);
    const minDate    = new Date(currentDue);
    minDate.setDate(minDate.getDate() + 1);

    const maxDate = new Date(currentDue);
    maxDate.setDate(maxDate.getDate() + MAX_EXTENSION_DAYS);

    EXT_CURRENT_DUE_ISO = dueDateISO;
    EXT_MAX_DUE_ISO     = toISODate(maxDate);

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
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        minDate: toISODate(minDate),
        maxDate: EXT_MAX_DUE_ISO,
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            onDateSelected(dateStr);
        }
    });

    // Buka overlay
    const overlay = document.getElementById('extendModalOverlay');
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

    if (diffDays > MAX_EXTENSION_DAYS) {
        showError(`Perpanjangan melebihi batas maksimum ${MAX_EXTENSION_DAYS} hari.`);
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

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('extendModalOverlay').classList.contains('open')) {
        closeExtendModal();
    }
});
</script>
@endpush

@endsection
