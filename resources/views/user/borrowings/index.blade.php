@extends('layouts.user')
@section('title', 'Peminjaman Buku – Perpustakaan Digital')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-loans.css') }}">
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
        <div class="loans-kicker"><span>📚</span> PEMINJAMAN BUKU</div>
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
        <div class="loans-stat-card is-yellow"><span class="loans-stat-icon">◷</span><strong data-stat="near">{{ $nearDueCount }}</strong><small>Akan Jatuh Tempo</small></div>
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
                            $searchText = strtolower(($book?->title ?? '') . ' ' . ($book?->author ?? '') . ' ' . ($book?->isbn ?? ''));
                            $totalDays = $borrowing->borrowed_at && $borrowing->due_at ? max(1, $borrowing->borrowed_at->diffInDays($borrowing->due_at)) : 14;
                            $elapsed = $borrowing->borrowed_at ? min($totalDays, max(0, $borrowing->borrowed_at->diffInDays(now()))) : 0;
                            $progress = $overdue ? 100 : max(8, round(($elapsed / $totalDays) * 100));
                            $canExtend = !$overdue && !$borrowing->has_reservation_queue && $borrowing->extension_status !== 'menunggu';
                            $disabledTitle = $borrowing->has_reservation_queue
                                ? 'Perpanjangan tidak tersedia karena ada antrean reservasi.'
                                : ($overdue ? 'Perpanjangan tidak tersedia karena buku sudah terlambat.' : 'Perpanjangan tidak tersedia saat ini.');
                        @endphp
                        <article class="loan-card" data-search="{{ $searchText }}" data-borrowing-id="{{ $borrowing->id }}">
                            <div class="loan-card-accent" style="background:{{ $color }}"></div>
                            <div class="loan-card-content">
                                <div class="loan-card-head"><div><p class="loan-meta-label">NO. PEMINJAMAN</p><p class="loan-number">#{{ str_pad($borrowing->id, 6, '0', STR_PAD_LEFT) }}</p></div><div><span class="loan-status" style="background:{{ $bg }};color:{{ $color }}">{{ $overdue ? '⚠️ Terlambat' : ($nearDue ? '⏰ Akan Jatuh Tempo' : '✓ Sedang Dipinjam') }}</span></div></div>
                                <div class="loan-dates"><div><p class="loan-meta-label">TANGGAL PINJAM</p><p class="loan-date-value">{{ $borrowing->borrowed_at?->format('d M Y') ?? '-' }}</p></div><div class="loan-date-divider"></div><div><p class="loan-meta-label">TANGGAL JATUH TEMPO</p><p class="loan-date-value due-date" style="color:{{ $color }}">{{ $borrowing->due_at?->format('d M Y') ?? '-' }}</p></div></div>
                                <div class="loan-book-row">
                                    @if($book?->cover)<img src="{{ asset('storage/'.$book->cover) }}" class="loan-cover" alt="{{ $book->title }}">@else<div class="loan-cover loan-cover-fallback">{{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}</div>@endif
                                    <div class="loan-book-info"><p class="loan-book-title">{{ $book?->title ?? 'Buku' }}</p><p class="loan-book-author">{{ $book?->author ?? '-' }}</p><span class="loan-category">{{ $book?->category?->name ?? 'Umum' }}</span></div>
                                </div>
                                <div class="loan-progress-wrap"><div class="loan-progress-label"><span>Progress pengembalian</span><strong class="days-remaining">{{ $overdue ? 'Terlambat ' . abs($days) . ' hari' : max(0, $days) . ' hari tersisa' }}</strong></div><div class="loan-progress"><span class="progress-fill {{ $overdue ? 'is-danger' : ($nearDue ? 'is-warning' : 'is-safe') }}" style="width:{{ $progress }}%"></span></div></div>
                                <div class="loan-card-actions"><span class="loan-extension-note">{{ $borrowing->has_reservation_queue ? 'Tidak tersedia: ada antrean reservasi.' : '' }}</span><button type="button" class="loans-action-btn" data-borrowing-id="{{ $borrowing->id }}" data-borrowing-title="{{ $book?->title ?? 'Buku' }}" data-borrowing-borrowed="{{ $borrowing->borrowed_at?->format('d M Y') }}" data-borrowing-due="{{ $borrowing->due_at?->format('d M Y') }}" data-extend-action="{{ route('borrowings.extend', $borrowing->id) }}" {{ $canExtend ? '' : 'disabled title="' . $disabledTitle . '"' }} onclick="openBorrowingExtend(this)">↻ Perpanjang</button></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if($completedBorrowings->isNotEmpty())
                <section class="loans-completed-section"><div class="loans-section-heading"><div><span class="loans-section-eyebrow">RIWAYAT PEMINJAMAN</span><h2>Riwayat Peminjaman</h2><p>Buku yang sudah dikembalikan dari koleksi Anda.</p></div></div><div class="loans-completed-list">
                    @foreach($completedBorrowings as $completed)
                        @php $book = $completed->details->first()?->book; @endphp
                        <article class="loan-completed-card">@if($book?->cover)<img src="{{ asset('storage/'.$book->cover) }}" class="loan-cover" alt="{{ $book->title }}">@else<div class="loan-cover loan-cover-fallback">{{ strtoupper(substr($book?->title ?? 'B', 0, 1)) }}</div>@endif<div class="loan-book-info"><p class="loan-book-title">{{ $book?->title ?? 'Buku' }}</p><p class="loan-book-author">{{ $book?->author ?? '-' }}</p><span class="loan-category loan-category-completed">Selesai dikembalikan</span></div><div class="loan-completed-progress"><span>Progress pengembalian</span><strong>Sudah dikembalikan</strong><div><i></i></div></div><a href="{{ route('history') }}" class="loans-action-btn">👁 Lihat Detail</a></article>
                    @endforeach
                </div></section>
            @endif
        </main>

        <aside class="loans-sidebar"><div class="loans-side-panel"><div class="loans-side-heading"><span>✦</span><h3>Aksi Cepat</h3></div><button type="button" class="loans-side-action loans-side-button" onclick="openQuickBorrowingExtend(event)">↻ <span>Perpanjang Peminjaman</span></button><a href="{{ route('history') }}" class="loans-side-action">◷ <span>Lihat Semua Peminjaman</span></a></div><div class="loans-side-panel loans-important"><div class="loans-side-heading"><span>ⓘ</span><h3>Informasi Penting</h3></div><ul><li>Maksimal peminjaman 3 buku per anggota</li><li>Lama peminjaman 14 hari</li><li>Dapat diperpanjang jika tidak ada antrian</li></ul></div></aside>
    </div>
@endif

<div id="borrowingPickerModal" class="borrowing-modal" aria-hidden="true"><div class="borrowing-modal-card"><h3>Pilih Buku untuk Diperpanjang</h3><p>Pilih salah satu buku yang masih memenuhi syarat perpanjangan.</p><div id="borrowingPickerList" class="borrowing-picker-list"></div><div><button type="button" onclick="closeBorrowingPicker()">Batal</button></div></div></div>
<div id="borrowingInfoModal" class="borrowing-modal" aria-hidden="true"><div class="borrowing-modal-card borrowing-info-card"><h3>Tidak Ada Buku yang Dapat Diperpanjang</h3><p>Semua buku saat ini memiliki antrean reservasi atau tidak memenuhi syarat perpanjangan.</p><div><button type="button" onclick="closeBorrowingInfo()">Tutup</button></div></div></div>
<div id="borrowingExtendModal" class="borrowing-modal" aria-hidden="true"><div class="borrowing-modal-card borrowing-calendar-card"><h3>Perpanjang Peminjaman</h3><p>Pilih tanggal pengembalian baru untuk buku ini.</p><dl class="borrowing-modal-details"><dt>Judul Buku</dt><dd id="borrowingModalBook"></dd><dt>Jatuh Tempo Saat Ini</dt><dd id="borrowingModalDue"></dd></dl><div class="borrowing-date-picker"><label for="borrowingReturnDate">📅 Pilih Tanggal Pengembalian Baru</label><input type="date" id="borrowingReturnDate" name="return_date" aria-label="Pilih Tanggal Pengembalian Baru"><small>Maksimal perpanjangan hingga 14 hari dari tanggal jatuh tempo saat ini.</small></div><dl class="borrowing-date-summary"><dt>Jatuh Tempo Saat Ini</dt><dd id="summaryCurrentDue">-</dd><dt>Tanggal Baru</dt><dd id="summaryNewDue">-</dd><dt>Tambahan Durasi</dt><dd id="summaryExtraDays">-</dd></dl><form id="borrowingExtendForm" method="POST">@csrf @method('PATCH')<div><button type="button" onclick="closeBorrowingExtend()">Batal</button><button type="submit" disabled>Perpanjang Sekarang</button></div></form></div></div>

<script>
const borrowingSearch = document.getElementById('borrowingSearch');
borrowingSearch?.addEventListener('input', function () { const value = this.value.toLowerCase().trim(); document.querySelectorAll('#borrowingList .loan-card').forEach(card => { card.hidden = value && !card.dataset.search.includes(value); }); });
let activeBorrowingCard = null;
function openBorrowingExtend(button) { if (button.disabled) return; activeBorrowingCard = button.closest('.loan-card'); const dateInput = document.getElementById('borrowingReturnDate'); const currentDue = parseDateInput(button.dataset.borrowingDue); dateInput.min = dateValue(currentDue); dateInput.max = dateValue(addDays(currentDue, 14)); dateInput.value = ''; document.getElementById('borrowingExtendForm').action = button.dataset.extendAction; document.getElementById('borrowingModalBook').textContent = button.dataset.borrowingTitle; document.getElementById('borrowingModalDue').textContent = button.dataset.borrowingDue; document.getElementById('summaryCurrentDue').textContent = button.dataset.borrowingDue; document.getElementById('summaryNewDue').textContent = '-'; document.getElementById('summaryExtraDays').textContent = '-'; document.getElementById('borrowingExtendForm').querySelector('button[type="submit"]').disabled = true; showBorrowingModal('borrowingExtendModal'); }
function openQuickBorrowingExtend(event) { event.preventDefault(); const eligible = Array.from(document.querySelectorAll('#borrowingList .loans-action-btn')).filter(button => !button.disabled); const list = document.getElementById('borrowingPickerList'); list.replaceChildren(); if (!eligible.length) { showBorrowingModal('borrowingInfoModal'); return; } eligible.forEach(function (button) { const item = document.createElement('button'); item.type = 'button'; item.className = 'borrowing-picker-item'; item.dataset.borrowingId = button.dataset.borrowingId; item.innerHTML = '<strong></strong><span>Jatuh tempo: ' + button.dataset.borrowingDue + '</span>'; item.querySelector('strong').textContent = button.dataset.borrowingTitle; item.addEventListener('click', function () { closeBorrowingPicker(); openBorrowingExtend(button); }); list.appendChild(item); }); showBorrowingModal('borrowingPickerModal'); }
function showBorrowingModal(id) { const modal = document.getElementById(id); modal.style.display = 'flex'; modal.setAttribute('aria-hidden', 'false'); }
function closeBorrowingPicker() { closeBorrowingModal('borrowingPickerModal'); }
function closeBorrowingInfo() { closeBorrowingModal('borrowingInfoModal'); }
function closeBorrowingModal(id) { const modal = document.getElementById(id); modal.style.display = 'none'; modal.setAttribute('aria-hidden', 'true'); }
function closeBorrowingExtend() { const modal = document.getElementById('borrowingExtendModal'); modal.style.display = 'none'; modal.setAttribute('aria-hidden', 'true'); }
function parseDateInput(label) { const parts = label.split(' '); const months = {Jan:0,Feb:1,Mar:2,Apr:3,Mei:4,May:4,Jun:5,Jul:6,Agt:7,Aug:7,Sep:8,Oct:9,Okt:9,Nov:10,Des:11,Dec:11}; return new Date(Number(parts[2]), months[parts[1]] ?? 0, Number(parts[0])); }
function addDays(date, days) { const result = new Date(date); result.setDate(result.getDate() + days); return result; }
function dateValue(date) { return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`; }
function prettyDate(value) { return new Date(`${value}T00:00:00`).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}); }
const returnDate = document.getElementById('borrowingReturnDate');
returnDate?.addEventListener('change', function () { const current = document.getElementById('summaryCurrentDue').textContent; const currentDate = parseDateInput(current); const selected = new Date(`${this.value}T00:00:00`); const days = Math.round((selected - currentDate) / 86400000); document.getElementById('summaryNewDue').textContent = this.value ? prettyDate(this.value) : '-'; document.getElementById('summaryExtraDays').textContent = this.value ? `+${days} Hari` : '-'; document.querySelector('#borrowingExtendForm button[type="submit"]').disabled = !this.value || days < 0 || days > 14; });
document.getElementById('borrowingExtendForm')?.addEventListener('submit', async function (event) { event.preventDefault(); const button = this.querySelector('button[type="submit"]'); button.disabled = true; button.textContent = 'Memproses...'; const response = await fetch(this.action, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ return_date: returnDate.value }) }); const data = await response.json(); if (!response.ok) { button.disabled = false; button.textContent = 'Perpanjang Sekarang'; window.showToast?.(data.message || 'Buku tidak dapat diperpanjang', 'warning'); return; } const card = activeBorrowingCard; card.querySelector('.due-date').textContent = data.due_at; card.querySelector('.days-remaining').textContent = data.days_remaining + ' hari tersisa'; card.querySelector('.progress-fill').style.width = Math.min(100, Math.max(8, 100 - (data.days_remaining / 14 * 100))) + '%'; card.querySelector('.loans-action-btn').disabled = true; card.querySelector('.loans-action-btn').title = 'Perpanjangan sudah digunakan'; document.querySelector('[data-stat="near"]').textContent = data.near_due_count; document.querySelector('[data-stat="overdue"]').textContent = data.overdue_count; window.showToast?.('Peminjaman berhasil diperpanjang', 'success'); closeBorrowingExtend(); button.textContent = 'Perpanjang Sekarang'; });
</script>
@endsection
