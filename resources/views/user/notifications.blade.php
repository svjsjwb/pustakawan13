@extends('layouts.user')
@section('title', 'Notifikasi – Perpustakaan Tiga Serangkai')
@section('page-title', 'Notifikasi')

@push('styles')
<style>
.notif-page-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 24px 16px 48px;
}

/* Header Section */
.notif-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border, #e2e8f0);
}

.notif-header-title-group h1 {
    font-size: 26px;
    font-weight: 800;
    font-family: 'Poppins', sans-serif;
    color: var(--text, #0f172a);
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notif-header-title-group p {
    font-size: 14px;
    color: var(--text-muted, #64748b);
    margin: 0;
}

.notif-mark-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    font-size: 13.5px;
    font-weight: 700;
    color: #0f766e;
    background: rgba(15, 118, 110, 0.08);
    border: 1.5px solid rgba(15, 118, 110, 0.2);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: inherit;
}

.notif-mark-all-btn:hover {
    background: #0f766e;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(15, 118, 110, 0.25);
    transform: translateY(-1px);
}

.notif-mark-all-btn:active {
    transform: translateY(0);
}

/* Tabs */
.notif-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.notif-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-muted, #64748b);
    background: var(--surface, #ffffff);
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.notif-tab:hover {
    background: rgba(15, 118, 110, 0.05);
    color: #0f766e;
    border-color: rgba(15, 118, 110, 0.3);
}

.notif-tab.active {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    color: #ffffff;
    border-color: transparent;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.25);
}

.notif-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 7px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
    color: inherit;
}

.notif-tab.active .notif-tab-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* Card List */
.notif-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Card item */
.notif-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 18px 22px;
    background: var(--surface, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-left: 4px solid transparent;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.notif-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.notif-card.unread {
    background: rgba(15, 118, 110, 0.025);
}

.notif-card.is-read {
    opacity: 0.88;
}

/* Status colors */
/* 1. HIJAU = Disetujui */
.notif-card.status-approved {
    border-left-color: #16a34a;
}
.notif-card.status-approved .notif-icon-box {
    background: rgba(22, 163, 74, 0.12);
    color: #16a34a;
}
.notif-card.status-approved .notif-status-pill {
    background: rgba(22, 163, 74, 0.12);
    color: #16a34a;
    border: 1px solid rgba(22, 163, 74, 0.25);
}

/* 2. KUNING = Menunggu */
.notif-card.status-pending {
    border-left-color: #f59e0b;
}
.notif-card.status-pending .notif-icon-box {
    background: rgba(245, 158, 11, 0.12);
    color: #d97706;
}
.notif-card.status-pending .notif-status-pill {
    background: rgba(245, 158, 11, 0.12);
    color: #b45309;
    border: 1px solid rgba(245, 158, 11, 0.25);
}

/* 3. MERAH = Ditolak */
.notif-card.status-rejected {
    border-left-color: #dc2626;
}
.notif-card.status-rejected .notif-icon-box {
    background: rgba(220, 38, 38, 0.12);
    color: #dc2626;
}
.notif-card.status-rejected .notif-status-pill {
    background: rgba(220, 38, 38, 0.12);
    color: #dc2626;
    border: 1px solid rgba(220, 38, 38, 0.25);
}

/* 4. BIRU = Informasi / Pengumuman */
.notif-card.status-info {
    border-left-color: #2563eb;
}
.notif-card.status-info .notif-icon-box {
    background: rgba(37, 99, 235, 0.12);
    color: #2563eb;
}
.notif-card.status-info .notif-status-pill {
    background: rgba(37, 99, 235, 0.12);
    color: #1d4ed8;
    border: 1px solid rgba(37, 99, 235, 0.25);
}

/* Icon Box */
.notif-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notif-icon-box svg {
    width: 22px;
    height: 22px;
}

/* Content Area */
.notif-content {
    flex: 1;
    min-width: 0;
}

.notif-content-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}

.notif-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.notif-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text, #0f172a);
    margin: 0;
    line-height: 1.35;
}

.notif-unread-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #0f766e;
    box-shadow: 0 0 6px rgba(15, 118, 110, 0.6);
    flex-shrink: 0;
}

.notif-status-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    flex-shrink: 0;
}

.notif-card-desc {
    font-size: 13.5px;
    color: var(--text-muted, #475569);
    line-height: 1.55;
    margin: 0 0 10px;
    word-break: break-word;
}

.notif-meta-row {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 12px;
    color: var(--text-muted, #94a3b8);
}

.notif-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

/* Empty State */
.notif-empty-box {
    text-align: center;
    padding: 64px 24px;
    background: var(--surface, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 20px;
    margin-top: 12px;
}

.notif-empty-icon {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: rgba(15, 118, 110, 0.1);
    color: #0f766e;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.notif-empty-icon svg {
    width: 32px;
    height: 32px;
}

.notif-empty-box h3 {
    font-size: 18px;
    font-weight: 800;
    color: var(--text, #0f172a);
    margin: 0 0 6px;
    font-family: 'Poppins', sans-serif;
}

.notif-empty-box p {
    font-size: 14px;
    color: var(--text-muted, #64748b);
    max-width: 360px;
    margin: 0 auto;
}

/* Pagination container */
.notif-pagination {
    margin-top: 28px;
    display: flex;
    justify-content: center;
}

.notif-back-wrap {
    display: flex;
    justify-content: center;
    margin-top: 32px;
}

.notif-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border: 1px solid rgba(15, 118, 110, 0.35);
    border-radius: 9999px;
    background: #ffffff;
    color: #0f766e;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
}

.notif-back-btn span:first-child {
    transition: transform 0.25s ease;
}

.notif-back-btn:hover {
    background: #0f766e;
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(15, 118, 110, 0.2);
}

.notif-back-btn:hover span:first-child {
    transform: translateX(-3px);
}
</style>
@endpush

@section('content')
<div class="notif-page-container">
    {{-- Header --}}
    <div class="notif-page-header">
        <div class="notif-header-title-group">
            <h1>
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                Notifikasi
            </h1>
            <p>Kelola seluruh aktivitas akun Anda</p>
        </div>

        <div>
            <button type="button" class="notif-mark-all-btn" id="btnPageMarkAll" onclick="pageMarkAllAsRead()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Tandai Selesai</span>
            </button>
        </div>
    </div>

    {{-- Notification List --}}
    @if($notifications->count() > 0)
        <div class="notif-list" id="notificationsList">
            @foreach($notifications as $n)
                @php
                    $rawType = strtolower($n->type ?? '');
                    $rawTitle = strtolower($n->title ?? '');
                    $rawMsg = strtolower($n->message ?? '');

                    // Determine Status: approved, pending, rejected, info
                    if (str_contains($rawType, 'approved') || str_contains($rawTitle, 'disetujui') || str_contains($rawMsg, 'disetujui') || str_contains($rawType, 'ready') || str_contains($rawTitle, 'siap')) {
                        $statusClass = 'status-approved';
                        $statusLabel = 'Disetujui';
                    } elseif (str_contains($rawType, 'pending') || str_contains($rawType, 'waiting') || str_contains($rawTitle, 'menunggu') || str_contains($rawMsg, 'menunggu')) {
                        $statusClass = 'status-pending';
                        $statusLabel = 'Menunggu';
                    } elseif (str_contains($rawType, 'rejected') || str_contains($rawTitle, 'ditolak') || str_contains($rawMsg, 'ditolak') || str_contains($rawType, 'terlambat') || str_contains($rawTitle, 'terlambat')) {
                        $statusClass = 'status-rejected';
                        $statusLabel = 'Ditolak';
                    } else {
                        $statusClass = 'status-info';
                        $statusLabel = 'Informasi';
                    }
                @endphp

                <div class="notif-card {{ $statusClass }} {{ $n->is_read ? 'is-read' : 'unread' }}"
                     id="notifCard-{{ $n->id }}"
                     data-id="{{ $n->id }}">
                    <div class="notif-icon-box">
                        @if($statusClass === 'status-approved')
                            {{-- Check circle --}}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        @elseif($statusClass === 'status-pending')
                            {{-- Clock / Waiting --}}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        @elseif($statusClass === 'status-rejected')
                            {{-- X Circle / Alert --}}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        @else
                            {{-- Info / Announcement --}}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        @endif
                    </div>

                    <div class="notif-content">
                        <div class="notif-content-header">
                            <div class="notif-title-row">
                                <h3 class="notif-card-title">{{ $n->title }}</h3>
                                @if(!$n->is_read)
                                    <span class="notif-unread-dot" title="Notifikasi baru"></span>
                                @endif
                            </div>
                            <span class="notif-status-pill">{{ $statusLabel }}</span>
                        </div>

                        <p class="notif-card-desc">{{ $n->message }}</p>

                        <div class="notif-meta-row">
                            <span class="notif-time-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ $n->created_at ? $n->created_at->diffForHumans() : 'Baru saja' }}
                                ({{ $n->created_at ? $n->created_at->format('d M Y, H:i') : '' }})
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="notif-pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="notif-empty-box">
            <div class="notif-empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </div>
            <h3>Tidak Ada Notifikasi</h3>
            <p>
                Belum ada pemberitahuan aktivitas akun untuk Anda saat ini.
            </p>
        </div>
    @endif

    <div class="notif-back-wrap">
        <button type="button" class="notif-back-btn" onclick="history.back()">
            <span aria-hidden="true">←</span>
            <span>Kembali</span>
        </button>
    </div>
</div>

<script>
function pageMarkAllAsRead() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch('/api/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res && res.success) {
            // Update UI on page
            document.querySelectorAll('.notif-card.unread').forEach(card => {
                card.classList.remove('unread');
                card.classList.add('is-read');
                const dot = card.querySelector('.notif-unread-dot');
                if (dot) dot.remove();
            });

            // Also update navbar badge
            const badgeCountEl = document.getElementById('egNotifBadgeCount');
            if (badgeCountEl) {
                badgeCountEl.textContent = '0';
                badgeCountEl.style.display = 'none';
            }
            const headerBadgeEl = document.getElementById('egNotifHeaderBadge');
            if (headerBadgeEl) {
                headerBadgeEl.style.display = 'none';
            }

            if (window.showToast) {
                window.showToast('Notifikasi berhasil ditandai sudah dibaca', 'success');
            }
        }
    })
    .catch(() => {});
}
</script>
@endsection
