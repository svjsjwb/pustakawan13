@extends('layouts.user')
@section('title', 'Peminjaman Buku – Perpustakaan Tiga Serangkai')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-loans.css') }}">
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
        <p>Kelola buku yang sedang Anda pinjam, perpanjang peminjaman, dan pantau waktu pengembalian dengan mudah.</p>
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
    <div class="loans-stat-card is-yellow"><span class="loans-stat-icon">◷</span><strong>{{ $nearDueCount }}</strong><small>Akan Jatuh Tempo</small></div>
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
        $loanSearch = strtolower($loanTitle . ' ' . ($firstBook?->author ?? '') . ' ' . ($firstBook?->isbn ?? ''));
        $totalDays = $borrowing->borrowed_at && $borrowing->due_at ? max(1, $borrowing->borrowed_at->diffInDays($borrowing->due_at)) : 14;
        $elapsedDays = $borrowing->borrowed_at ? max(0, min($totalDays, $borrowing->borrowed_at->diffInDays(now()))) : 0;
        $progress = $isOverdue ? 100 : min(100, max(8, round(($elapsedDays / $totalDays) * 100)));
    @endphp
    <article class="loan-card" id="loan-{{ $borrowing->id }}" data-loan-search="{{ $loanSearch }}">
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
                        @if($isOverdue) ⚠️ @elseif($isNearDue) ⏰ @else ✓ @endif {{ $statusLabel }}
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
                    <button type="button" onclick="openExtendModal('{{ route('user.loans.extend', $borrowing->id) }}', '{{ $loanTitle }}', '{{ $borrowing->due_at?->format('d M Y') }}')" class="loans-action-btn">
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
            <li>Maksimal peminjaman 3 buku per anggota</li>
            <li>Lama peminjaman 14 hari</li>
            <li>Dapat diperpanjang jika tidak ada antrian</li>
            <li>Denda keterlambatan Rp1.000 per hari per buku</li>
        </ul>
    </div>
</aside>
</div>
@endif

{{-- Modal Ajukan Perpanjangan --}}
<div id="extendModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; width:90%; max-width:440px; padding:24px; box-shadow:0 12px 36px rgba(0,0,0,0.2);">
        <h4 style="margin:0 0 6px; font-size:16px; font-weight:700; color:#1e3d3d;">Ajukan Perpanjangan Buku</h4>
        <p id="extendBookTitle" style="margin:0 0 16px; font-size:13px; color:#688585;"></p>
        
        <form id="extendForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#497171; margin-bottom:6px;">Durasi Tambahan:</label>
                <select name="extension_days" required style="width:100%; border:1px solid #cbdada; border-radius:8px; padding:10px; font-size:13px; font-family:inherit;">
                    <option value="3">+3 Hari</option>
                    <option value="7" selected>+7 Hari (1 Minggu)</option>
                    <option value="14">+14 Hari (2 Minggu)</option>
                </select>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#497171; margin-bottom:6px;">Alasan Perpanjangan (Opsional):</label>
                <textarea name="reason" rows="2" style="width:100%; border:1px solid #cbdada; border-radius:8px; padding:10px; font-size:13px; font-family:inherit; box-sizing:border-box;" placeholder="Contoh: Buku masih dibutuhkan untuk riset tugas..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeExtendModal()" style="background:#f0f4f4; border:none; padding:8px 16px; border-radius:6px; font-size:12.5px; font-weight:600; color:#497171; cursor:pointer;">Batal</button>
                <button type="submit" style="background:#287879; border:none; padding:8px 16px; border-radius:6px; font-size:12.5px; font-weight:600; color:#fff; cursor:pointer;">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
const loanSearch = document.getElementById('loanSearch');
loanSearch?.addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll('.loan-card').forEach(function (card) {
        card.hidden = keyword !== '' && !card.dataset.loanSearch.includes(keyword);
    });
});

function openExtendModal(actionUrl, title, currentDue) {
    const modal = document.getElementById('extendModal');
    const form = document.getElementById('extendForm');
    form.action = actionUrl;
    document.getElementById('extendBookTitle').textContent = title + ' (Jatuh tempo saat ini: ' + currentDue + ')';
    modal.style.display = 'flex';
}

function closeExtendModal() {
    document.getElementById('extendModal').style.display = 'none';
}
</script>

@endsection
