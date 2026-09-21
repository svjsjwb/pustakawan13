@extends('layouts.user')
@section('title', 'Peminjaman Buku – Perpustakaan Digital')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-loans.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
@php
    $activeCount = $borrowings->count();
    $nearDueCount = $borrowings->filter(fn ($item) => ($item->days_remaining ?? 99) >= 0 && ($item->days_remaining ?? 99) <= 3)->count();
    $overdueCount = $borrowings->filter(fn ($item) => ($item->days_remaining ?? 0) < 0)->count();
@endphp

<section class="loans-hero">
    <div class="loans-hero-pattern"></div>
    <div class="loans-hero-copy">
        <h1>Daftar Peminjaman Buku</h1>
        <p>Kelola buku yang sedang Anda pinjam, perpanjang peminjaman, dan pantau waktu pengembalian dengan mudah.</p>
        <div class="loans-hero-stats">
            <div><span>Total Buku Dipinjam</span><strong>{{ $activeCount }}</strong><small>buku aktif</small></div>
        </div>
    </div>
    <div class="loans-book-stack" aria-hidden="true"><span class="loan-book loan-book-back"></span><span class="loan-book loan-book-mid"></span><span class="loan-book loan-book-front"><i></i></span><span class="loan-book-page"></span></div>
</section>

@if(!$member)
    <div class="ul-card"><div class="ul-empty-state"><h3>Data anggota tidak ditemukan</h3><p>Akun Anda belum terdaftar sebagai anggota perpustakaan.</p></div></div>
@else
    <div class="loans-stat-grid">
        <div class="loans-stat-card is-green"><span class="loans-stat-icon">↻</span><strong data-stat="active">{{ $activeCount }}</strong><small>Sedang Dipinjam</small></div>
        <div class="loans-stat-card is-yellow"><span class="loans-stat-icon">◷</span><strong data-stat="near">{{ $nearDueCount }}</strong><small>Mendekati Batas Pengembalian</small></div>
        <div class="loans-stat-card is-red"><span class="loans-stat-icon">!</span><strong data-stat="overdue">{{ $overdueCount }}</strong><small>Terlambat</small></div>
        <div class="loans-stat-card is-slate"><span class="loans-stat-icon">✓</span><strong>{{ $completedCount }}</strong><small>Selesai</small></div>
    </div>

    <div class="loans-layout">
        <main class="loans-main">
            <div class="loans-section-heading">
                <div><span class="loans-section-eyebrow">AKTIVITAS TERKINI</span><h2>Buku yang Sedang Dipinjam</h2><p>Berikut adalah daftar buku yang sedang Anda pinjam saat ini.</p></div>
                <label class="loans-search"><span>⌕</span><input type="search" id="borrowingSearch" placeholder="Cari judul buku, penulis, atau ISBN..." aria-label="Cari peminjaman"></label>
            </div>

            @if($borrowings->isEmpty())
                <div class="ul-card"><div class="ul-empty-state"><h3>Tidak ada peminjaman aktif</h3><p>Jelajahi katalog untuk menemukan buku yang menarik.</p><a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-primary">Jelajahi Katalog</a></div></div>
            @else
                <div class="loans-list" id="borrowingList">
                    @foreach($borrowings as $borrowing)
                        @php
                            $days = $borrowing->days_remaining ?? 0;
                            $overdue = $days < 0;
                            $nearDue = !$overdue && $days <= 3;
                            $color = $overdue ? 'var(--danger)' : ($nearDue ? 'var(--brass)' : 'var(--success)');
                            $bg = $overdue ? 'var(--danger-soft)' : ($nearDue ? 'var(--brass-soft)' : 'var(--success-soft)');
                            $book = $borrowing->details->first()?->book;
                            $loanTitles = strtolower($borrowing->details->map(fn($d) => $d->book?->title)->filter()->implode('|||'));
                            $searchText = strtolower(($book?->title ?? '') . ' ' . ($book?->author ?? '') . ' ' . ($book?->isbn ?? ''));
                            $totalDays = $borrowing->borrowed_at && $borrowing->due_at ? max(1, $borrowing->borrowed_at->diffInDays($borrowing->due_at)) : 14;
                            $elapsed = $borrowing->borrowed_at ? min($totalDays, max(0, $borrowing->borrowed_at->diffInDays(now()))) : 0;
                            $progress = $overdue ? 100 : max(8, round(($elapsed / $totalDays) * 100));
                            $canExtend = !$overdue && !$borrowing->has_reservation_queue && $borrowing->extension_status !== 'menunggu';
                            $disabledTitle = $borrowing->has_reservation_queue
                                ? 'Perpanjangan tidak tersedia karena ada antrean reservasi.'
                                : ($overdue ? 'Perpanjangan tidak tersedia karena buku sudah terlambat.' : 'Perpanjangan tidak tersedia saat ini.');
                        @endphp
                        <article class="loan-card" data-search="{{ $searchText }}" data-titles="{{ $loanTitles }}" data-borrowing-id="{{ $borrowing->id }}">
                            <div class="loan-card-accent" style="background:{{ $color }}"></div>
                            <div class="loan-card-content">
                                <div class="loan-card-head"><div><p class="loan-meta-label">NO. PEMINJAMAN</p><p class="loan-number">#{{ str_pad($borrowing->id, 6, '0', STR_PAD_LEFT) }}</p></div><div><span class="loan-status" style="background:{{ $bg }};color:{{ $color }}">{{ $overdue ? 'Terlambat' : ($nearDue ? 'Mendekati Batas Pengembalian' : 'Sedang Dipinjam') }}</span></div></div>
                                <div class="loan-dates"><div><p class="loan-meta-label">TANGGAL PINJAM</p><p class="loan-date-value">{{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }}</p></div><div class="loan-date-divider"></div><div><p class="loan-meta-label">TANGGAL JATUH TEMPO</p><p class="loan-date-value due-date" style="color:{{ $color }}">{{ $borrowing->due_at?->format('d M Y') ?? '-' }}</p></div></div>
                                <div class="loan-book-row">
                                    @if($book?->cover)<img src="{{ asset('storage/'.$book->cover) }}" class="loan-cover" alt="{{ $book->title }}">@else<div class="loan-cover loan-cover-fallback">{{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}</div>@endif
                                    <div class="loan-book-info"><p class="loan-book-title">{{ $book?->title ?? 'Buku' }}</p><p class="loan-book-author">{{ $book?->author ?? '-' }}</p><span class="loan-category">{{ $book?->category?->name ?? 'Umum' }}</span></div>
                                </div>
                                <div class="loan-progress-wrap"><div class="loan-progress-label"><span>Progress pengembalian</span><strong class="days-remaining">{{ $overdue ? 'Terlambat ' . abs($days) . ' hari' : max(0, $days) . ' hari tersisa' }}</strong></div><div class="loan-progress"><span class="progress-fill {{ $overdue ? 'is-danger' : ($nearDue ? 'is-warning' : 'is-safe') }}" style="width:{{ $progress }}%"></span></div></div>
                                <div class="loan-card-actions"><span class="loan-extension-note">{{ $borrowing->has_reservation_queue ? 'Tidak tersedia: ada antrean reservasi.' : '' }}</span><button type="button" class="loans-action-btn" data-borrowing-id="{{ $borrowing->id }}" data-borrowing-title="{{ $book?->title ?? 'Buku' }}" data-borrowing-borrowed="{{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }}" data-borrowing-due="{{ $borrowing->due_at?->format('d M Y') ?? '-' }}" data-borrowing-due-iso="{{ $borrowing->due_at?->format('Y-m-d') ?? '' }}" data-extend-action="{{ route('borrowings.extend', $borrowing->id) }}" {{ $canExtend ? '' : 'disabled title="' . $disabledTitle . '"' }} onclick="openBorrowingExtend(this)">↻ Perpanjang</button></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </main>

        <aside class="loans-sidebar"><div class="loans-side-panel"><div class="loans-side-heading"><span>✦</span><h3>Aksi Cepat</h3></div><button type="button" class="loans-side-action loans-side-button" onclick="openQuickBorrowingExtend(event)">↻ <span>Perpanjang Peminjaman</span></button><a href="{{ route('history') }}" class="loans-side-action">◷ <span>Lihat Semua Peminjaman</span></a></div><div class="loans-side-panel loans-important"><div class="loans-side-heading"><span>ⓘ</span><h3>Informasi Penting</h3></div><ul><li>Maksimal peminjaman 5 buku per anggota</li><li>Lama peminjaman 14 hari</li><li>Dapat diperpanjang jika tidak ada antrian</li></ul></div></aside>
    </div>
@endif

{{-- Modal Pilih Buku untuk Quick Action --}}
<div id="borrowingPickerModal" class="borrowing-modal" aria-hidden="true"><div class="borrowing-modal-card"><h3>Pilih Buku untuk Diperpanjang</h3><p>Pilih salah satu buku yang masih memenuhi syarat perpanjangan.</p><div id="borrowingPickerList" class="borrowing-picker-list"></div><div><button type="button" class="btn-cancel" style="padding: 9px 15px; border-radius: 9px; border: 0;" onclick="closeBorrowingPicker()">Batal</button></div></div></div>
<div id="borrowingInfoModal" class="borrowing-modal" aria-hidden="true"><div class="borrowing-modal-card borrowing-info-card"><h3>Tidak Ada Buku yang Dapat Diperpanjang</h3><p>Semua buku saat ini memiliki antrean reservasi atau tidak memenuhi syarat perpanjangan.</p><div><button type="button" class="btn-cancel" style="padding: 9px 15px; border-radius: 9px; border: 0;" onclick="closeBorrowingInfo()">Tutup</button></div></div></div>

{{-- Modern Extend Modal (Flatpickr Datepicker) --}}
<div id="borrowingExtendModal" class="borrowing-modal" aria-hidden="true">
    <div class="borrowing-modal-card">
        <div class="borrowing-modal-head">
            <div>
                <h3>Perpanjang Peminjaman</h3>
                <p>Pilih tanggal pengembalian baru untuk masa pinjam buku ini.</p>
            </div>
            <button type="button" class="borrowing-modal-close" onclick="closeBorrowingExtend()" aria-label="Tutup">✕</button>
        </div>

        <dl class="borrowing-modal-details">
            <dt>Judul Buku</dt>
            <dd id="borrowingModalBook" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">-</dd>
            <dt>Tanggal Pinjam</dt>
            <dd id="borrowingModalBorrowed">-</dd>
            <dt>Jatuh Tempo Saat Ini</dt>
            <dd id="borrowingModalDue" class="highlight">-</dd>
            <dt>Perpanjangan Maksimal</dt>
            <dd>14 Hari</dd>
        </dl>

        <form id="borrowingExtendForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div class="borrowing-form-group">
                <label for="borrowingDateInput">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Tanggal Pengembalian Baru</span>
                </label>
                <div class="borrowing-date-input-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input type="text" id="borrowingDateInput" name="new_due_date" class="borrowing-date-input" placeholder="Pilih tanggal di kalender..." readonly required>
                </div>
                <span class="borrowing-date-hint" id="borrowingDateHint">Pilih tanggal baru yang diizinkan</span>
            </div>

            {{-- Real-time Preview Box --}}
            <div id="borrowingPreviewBox" class="borrowing-preview-box" style="display: none;">
                <div class="borrowing-preview-item">
                    <span class="borrowing-preview-label">Durasi Tambahan</span>
                    <strong class="borrowing-preview-val" id="previewExtraDays" style="color: #0f766e;">-</strong>
                </div>
                <div class="borrowing-preview-divider"></div>
                <div class="borrowing-preview-item">
                    <span class="borrowing-preview-label">Tanggal Baru</span>
                    <strong class="borrowing-preview-val" id="previewNewDate">-</strong>
                </div>
            </div>

            <div class="borrowing-modal-actions">
                <button type="button" class="btn-cancel" onclick="closeBorrowingExtend()">Batal</button>
                <button type="submit" class="btn-submit" id="btnSubmitExtend" disabled>Konfirmasi Perpanjangan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
const borrowingSearch = document.getElementById('borrowingSearch');
borrowingSearch?.addEventListener('input', function () {
    const value = this.value.toLowerCase().trim();
    let visibleCount = 0;
    const cards = document.querySelectorAll('#borrowingList .loan-card');
    cards.forEach(card => {
        if (value === '') {
            card.hidden = false;
            visibleCount++;
            return;
        }
        const titles = (card.dataset.titles || '').split('|||').map(s => s.trim().toLowerCase());
        const match = titles.some(t => t.startsWith(value));
        card.hidden = !match;
        if (match) visibleCount++;
    });

    let emptyEl = document.getElementById('borrowingSearchEmpty');
    const borrowingList = document.getElementById('borrowingList');
    if (!emptyEl && borrowingList) {
        emptyEl = document.createElement('div');
        emptyEl.id = 'borrowingSearchEmpty';
        emptyEl.className = 'ul-card';
        emptyEl.innerHTML = '<div class="ul-empty-state"><p style="margin:0;color:var(--muted);font-weight:600;">Buku tidak ditemukan</p></div>';
        borrowingList.appendChild(emptyEl);
    }
    if (emptyEl) {
        emptyEl.hidden = (visibleCount > 0 || value === '');
    }
});

let activeBorrowingCard = null;
let borrowingFlatpickrInstance = null;
let currentDueTimestamp = null;

function openBorrowingExtend(button) {
    if (button.disabled) return;
    activeBorrowingCard = button.closest('.loan-card');
    const form = document.getElementById('borrowingExtendForm');
    form.action = button.dataset.extendAction;
    
    document.getElementById('borrowingModalBook').textContent = button.dataset.borrowingTitle || '-';
    document.getElementById('borrowingModalBook').title = button.dataset.borrowingTitle || '';
    document.getElementById('borrowingModalBorrowed').textContent = button.dataset.borrowingBorrowed || '-';
    document.getElementById('borrowingModalDue').textContent = button.dataset.borrowingDue || '-';

    // Parse base due date (YYYY-MM-DD)
    let dueBaseDate;
    if (button.dataset.borrowingDueIso) {
        dueBaseDate = new Date(button.dataset.borrowingDueIso + 'T00:00:00');
    } else {
        dueBaseDate = new Date();
    }
    currentDueTimestamp = dueBaseDate.getTime();

    // Min = due_at + 1 day, Max = due_at + 14 days
    const minDate = new Date(dueBaseDate);
    minDate.setDate(minDate.getDate() + 1);

    const maxDate = new Date(dueBaseDate);
    maxDate.setDate(maxDate.getDate() + 14);

    // Reset UI state
    const previewBox = document.getElementById('borrowingPreviewBox');
    const btnSubmit = document.getElementById('btnSubmitExtend');
    const dateInput = document.getElementById('borrowingDateInput');
    
    previewBox.style.display = 'none';
    btnSubmit.disabled = true;
    dateInput.value = '';

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const fmtMin = `${String(minDate.getDate()).padStart(2, '0')} ${months[minDate.getMonth()]} ${minDate.getFullYear()}`;
    const fmtMax = `${String(maxDate.getDate()).padStart(2, '0')} ${months[maxDate.getMonth()]} ${maxDate.getFullYear()}`;
    document.getElementById('borrowingDateHint').textContent = `Kalender hanya memilih antara ${fmtMin} s.d. ${fmtMax}`;

    // Initialize or recreate Flatpickr
    if (borrowingFlatpickrInstance) {
        borrowingFlatpickrInstance.destroy();
    }

    borrowingFlatpickrInstance = flatpickr('#borrowingDateInput', {
        locale: 'id',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        altInputClass: 'borrowing-date-input',
        minDate: minDate,
        maxDate: maxDate,
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length > 0) {
                const selected = selectedDates[0];
                const diffTime = selected.getTime() - currentDueTimestamp;
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays >= 1 && diffDays <= 14) {
                    const selDay = String(selected.getDate()).padStart(2, '0');
                    const selMonth = months[selected.getMonth()];
                    const selYear = selected.getFullYear();
                    const formattedDate = `${selDay} ${selMonth} ${selYear}`;

                    document.getElementById('previewExtraDays').textContent = diffDays + ' Hari';
                    document.getElementById('previewNewDate').textContent = formattedDate;
                    previewBox.style.display = 'flex';
                    btnSubmit.disabled = false;
                } else {
                    previewBox.style.display = 'none';
                    btnSubmit.disabled = true;
                }
            } else {
                previewBox.style.display = 'none';
                btnSubmit.disabled = true;
            }
        }
    });

    showBorrowingModal('borrowingExtendModal');
}

function openQuickBorrowingExtend(event) {
    event.preventDefault();
    const eligible = Array.from(document.querySelectorAll('#borrowingList .loans-action-btn')).filter(button => !button.disabled);
    const list = document.getElementById('borrowingPickerList');
    list.replaceChildren();
    if (!eligible.length) {
        showBorrowingModal('borrowingInfoModal');
        return;
    }
    eligible.forEach(function (button) {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'borrowing-picker-item';
        item.dataset.borrowingId = button.dataset.borrowingId;
        item.innerHTML = '<strong></strong><span>Jatuh tempo: ' + button.dataset.borrowingDue + '</span>';
        item.querySelector('strong').textContent = button.dataset.borrowingTitle;
        item.addEventListener('click', function () {
            closeBorrowingPicker();
            openBorrowingExtend(button);
        });
        list.appendChild(item);
    });
    showBorrowingModal('borrowingPickerModal');
}

function showBorrowingModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeBorrowingPicker() {
    closeBorrowingModal('borrowingPickerModal');
}

function closeBorrowingInfo() {
    closeBorrowingModal('borrowingInfoModal');
}

function closeBorrowingModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
}

function closeBorrowingExtend() {
    closeBorrowingModal('borrowingExtendModal');
    if (borrowingFlatpickrInstance) {
        borrowingFlatpickrInstance.clear();
    }
}

// Close on outside click
document.querySelectorAll('.borrowing-modal').forEach(modal => {
    modal.addEventListener('click', function (e) {
        if (e.target === this) {
            closeBorrowingModal(this.id);
        }
    });
});

// Close on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.borrowing-modal').forEach(modal => {
            if (modal.getAttribute('aria-hidden') === 'false') {
                closeBorrowingModal(modal.id);
            }
        });
    }
});

// Submit perpanjangan AJAX
document.getElementById('borrowingExtendForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const button = document.getElementById('btnSubmitExtend');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Memproses...';

    const dateVal = document.getElementById('borrowingDateInput').value;

    try {
        const response = await fetch(this.action, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ new_due_date: dateVal })
        });

        const data = await response.json();

        if (!response.ok) {
            window.showToast?.(data.message || 'Buku tidak dapat diperpanjang', 'warning');
            button.disabled = false;
            button.textContent = originalText;
            return;
        }

        if (activeBorrowingCard) {
            activeBorrowingCard.querySelector('.due-date').textContent = data.due_at;
            activeBorrowingCard.querySelector('.days-remaining').textContent = data.days_remaining + ' hari tersisa';
            activeBorrowingCard.querySelector('.progress-fill').style.width = Math.min(100, Math.max(8, 100 - (data.days_remaining / 14 * 100))) + '%';
            
            const btn = activeBorrowingCard.querySelector('.loans-action-btn');
            btn.disabled = true;
            btn.title = 'Perpanjangan sudah digunakan';
        }

        document.querySelector('[data-stat="near"]').textContent = data.near_due_count ?? 0;
        document.querySelector('[data-stat="overdue"]').textContent = data.overdue_count ?? 0;

        window.showToast?.(data.message || 'Peminjaman berhasil diperpanjang', 'success');
        closeBorrowingExtend();
    } catch (err) {
        window.showToast?.('Terjadi kesalahan saat memproses perpanjangan: ' + err.message, 'error');
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
});
</script>
@endsection
