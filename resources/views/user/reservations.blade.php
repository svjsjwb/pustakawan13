@extends('layouts.user')

@section('title', 'Reservasi Saya - Pustakawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<style>
    /* ═══════════════════════════════════════════════════════
       RESERVASI SAYA - STYLES
    ═══════════════════════════════════════════════════════ */

    .reservations-wrapper {
        max-width: 860px;
        margin: 0 auto;
        padding: 40px 20px 80px;
    }

    /* ─── Header ─────────────────────────────────────────── */
    .reservations-header {
        margin-bottom: 32px;
    }

    .reservations-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: #0f4c4c;
        letter-spacing: -0.5px;
        margin: 0 0 6px 0;
    }

    .reservations-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* ─── Toast / Alert ──────────────────────────────────── */
    .res-toast {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        margin-bottom: 24px;
        animation: slideDown 0.3s ease;
    }

    .res-toast.success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #6ee7b7;
    }

    .res-toast.error {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Card Daftar ────────────────────────────────────── */
    .res-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .res-card {
        background: #ffffff;
        border: 1px solid #e8edf2;
        border-radius: 16px;
        padding: 20px 22px;
        display: grid;
        grid-template-columns: 64px 1fr auto;
        gap: 18px;
        align-items: center;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .res-card:hover {
        box-shadow: 0 8px 30px rgba(15, 76, 76, 0.08);
        transform: translateY(-2px);
    }

    /* Cover thumbnail */
    .res-cover {
        width: 64px;
        height: 84px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f5f9;
        flex-shrink: 0;
    }

    .res-cover-placeholder {
        width: 64px;
        height: 84px;
        border-radius: 8px;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Info section */
    .res-info {
        min-width: 0;
    }

    .res-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid;
        margin-bottom: 8px;
    }

    .res-book-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .res-book-author {
        font-size: 12.5px;
        color: #64748b;
        margin: 0 0 10px 0;
    }

    .res-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 11.5px;
        color: #94a3b8;
    }

    .res-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Actions */
    .res-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
    }

    .res-cancel-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 14px;
        border-radius: 10px;
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .res-cancel-btn:hover {
        background: #ffe4e6;
        border-color: #fda4af;
    }

    /* Empty state */
    .res-empty {
        text-align: center;
        padding: 80px 20px;
        color: #94a3b8;
    }

    .res-empty svg {
        margin-bottom: 16px;
        opacity: 0.4;
    }

    .res-empty h3 {
        font-size: 18px;
        font-weight: 700;
        color: #64748b;
        margin: 0 0 8px 0;
    }

    .res-empty p {
        font-size: 14px;
        margin: 0 0 24px 0;
    }

    .res-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 12px;
        background: #0f4c4c;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s;
    }

    .res-empty-btn:hover {
        background: #0a3737;
    }

    /* Pagination */
    .res-pagination {
        margin-top: 32px;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .res-card {
            grid-template-columns: 50px 1fr;
            grid-template-rows: auto auto;
        }

        .res-cover, .res-cover-placeholder {
            width: 50px;
            height: 66px;
        }

        .res-actions {
            grid-column: 1 / -1;
            flex-direction: row;
        }
    }
</style>
@endpush

@section('content')

<div class="reservations-wrapper">

    {{-- ─── Header ───────────────────────────────────────── --}}
    <div class="reservations-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>📋 Reservasi Saya</h1>
            <p>Daftar buku yang sedang kamu reservasi. Maksimal <strong>3 reservasi aktif</strong> sekaligus.</p>
        </div>
        <button type="button" id="btnOpenReservationModal" class="btn-create-res-top" style="display: inline-flex; align-items: center; gap: 8px; background: #0f4c4c; color: #ffffff; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(15, 76, 76, 0.25); transition: all 0.2s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Buat Reservasi
        </button>
    </div>

    {{-- ─── Toast Notifikasi ─────────────────────────────── --}}
    @if (session('reservation_success'))
        <div class="res-toast success" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('reservation_success') }}
        </div>
    @endif

    @if (session('reservation_error'))
        <div class="res-toast error" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            {{ session('reservation_error') }}
        </div>
    @endif

    {{-- ─── Daftar Reservasi ─────────────────────────────── --}}
    @if ($reservations->isEmpty())

        <div class="res-empty">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <h3>Belum ada reservasi</h3>
            <p>Kamu belum mereservasi buku apapun.<br>Pilih buku dan buat reservasi sekarang!</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="res-empty-btn btn-open-modal-empty" style="background: #0f4c4c; color: #ffffff; border: none; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Buat Reservasi Sekarang
                </button>
                <a href="{{ route('catalog') }}" class="res-empty-btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Lihat Katalog
                </a>
            </div>
        </div>

    @else

        <div class="res-list">

            @foreach ($reservations as $res)

                @php
                    $style = $res->statusStyle();
                    $book  = $res->book;
                @endphp

                <div class="res-card" data-reservation-id="{{ $res->id }}">

                    {{-- Cover --}}
                    @if ($book && $book->cover)
                        <img
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            class="res-cover"
                        >
                    @else
                        <div class="res-cover-placeholder">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0f4c4c" stroke-width="1.5">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="res-info">

                        {{-- Badge status --}}
                        <span
                            class="res-badge"
                            style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; border-color: {{ $style['border'] }};"
                        >
                            @if ($res->status === 'menunggu')
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            @elseif ($res->status === 'disetujui')
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            @else
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            @endif
                            {{ $res->statusLabel() }}
                        </span>

                        <p class="res-book-title">{{ $book->title ?? '(Buku tidak ditemukan)' }}</p>
                        <p class="res-book-author">{{ $book->author ?? '-' }}</p>

                        <div class="res-meta">
                            <span>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Direservasi: {{ \Carbon\Carbon::parse($res->reserved_at)->translatedFormat('d M Y') }}
                            </span>

                            @if ($res->status === 'disetujui')
                                <span style="color: #047857; font-weight: 700;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Buku siap diambil di perpustakaan
                                </span>
                            @endif

                            @if ($res->status === 'menunggu')
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Menunggu persetujuan admin (Senin - Jumat)
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="res-actions">

                        @if ($res->isCancellable())
                            <form
                                action="{{ route('user.reservations.cancel', $res->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin membatalkan reservasi buku ini?')"
                            >
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="res-cancel-btn">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Batalkan
                                </button>
                            </form>
                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        {{-- Pagination --}}
        @if ($reservations->hasPages())
            <div class="res-pagination">
                {{ $reservations->links() }}
            </div>
        @endif

    @endif

</div>

{{-- ─── MODAL FORM RESERVASI BUKU USER ────────────────────────── --}}
<div id="userReservationFormModal" class="user-res-modal" style="display: none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center;">
    {{-- Backdrop --}}
    <div class="user-res-modal-backdrop" id="userResModalBackdrop" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);"></div>

    {{-- Modal Card --}}
    <div class="user-res-modal-content" style="position: relative; width: 95%; max-width: 540px; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);">

        {{-- Modal Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">Form Reservasi Buku</h2>
                <p style="font-size: 12px; color: #64748b; margin: 3px 0 0;">Ajukan reservasi buku pilihanmu ke perpustakaan.</p>
            </div>
            <button type="button" id="btnCloseUserResModal" style="background: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; cursor: pointer; transition: all 0.2s;">
                ✕
            </button>
        </div>

        {{-- Modal Body --}}
        <form action="{{ route('user.reservations.store') }}" method="POST" id="userReservationForm" style="padding: 24px;">
            @csrf

            {{-- 1. Pilih Buku --}}
            <div style="margin-bottom: 18px;">
                <label for="resBookSelect" style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                    Pilih Buku yang Tersedia <span style="color: #ef4444;">*</span>
                </label>
                <select name="book_id" id="resBookSelect" required style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none; background: #ffffff;">
                    <option value="">-- Cari atau pilih buku --</option>
                    @foreach ($availableBooks as $b)
                        <option
                            value="{{ $b->id }}"
                            data-title="{{ $b->judul_buku }}"
                            data-author="{{ $b->penulis }}"
                            data-stock="{{ $b->stok }}"
                            data-cover="{{ $b->cover_image ? (str_starts_with($b->cover_image, 'http') ? $b->cover_image : asset('storage/' . $b->cover_image)) : asset('images/book-placeholder.jpg') }}"
                        >
                            {{ $b->judul_buku }} (Stok: {{ $b->stok }}) — {{ $b->penulis }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Preview Buku Terpilih --}}
            <div id="resBookPreview" style="display: none; align-items: center; gap: 14px; padding: 12px 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; margin-bottom: 18px;">
                <img id="resPreviewCover" src="" alt="Cover" style="width: 48px; height: 68px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="flex: 1; min-width: 0;">
                    <div id="resPreviewTitle" style="font-size: 13px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                    <div id="resPreviewAuthor" style="font-size: 11px; color: #64748b; margin-top: 2px;"></div>
                    <span id="resPreviewStock" style="display: inline-block; font-size: 10px; font-weight: 700; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px; margin-top: 4px;"></span>
                </div>
            </div>

            {{-- 2. Tanggal Reservasi --}}
            <div style="margin-bottom: 18px;">
                <label for="resDateInput" style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                    Tanggal Reservasi <span style="color: #ef4444;">*</span>
                </label>
                <input
                    type="date"
                    id="resDateInput"
                    name="reserved_at"
                    value="{{ date('Y-m-d') }}"
                    min="{{ date('Y-m-d') }}"
                    required
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;"
                >
                <small style="display: block; color: #64748b; font-size: 11px; margin-top: 4px;">Pilih tanggal kunjungan / pengambilan buku.</small>
            </div>

            {{-- 3. Kursi Baca (Opsional) --}}
            <div style="margin-bottom: 20px;">
                <label for="resSeatInput" style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                    Nomor Kursi Baca (Opsional)
                </label>
                <input
                    type="text"
                    id="resSeatInput"
                    name="seat_number"
                    placeholder="Contoh: A1, B3, C5"
                    maxlength="10"
                    style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;"
                >
            </div>

            {{-- Notice Status Pending --}}
            <div style="display: flex; gap: 10px; align-items: flex-start; padding: 12px; background: #fefce8; border: 1px solid #fef08a; border-radius: 10px; margin-bottom: 24px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p style="font-size: 11px; color: #854d0e; margin: 0; line-height: 1.5;">
                    Reservasi akan disubmit dengan <strong>Status: Pending (Menunggu)</strong> dan akan langsung diteruskan ke menu <strong>Kelola Reservasi</strong> Admin untuk diverifikasi.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" id="btnCancelUserResModal" style="padding: 10px 20px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 600; font-size: 13px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitUserRes" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 10px; border: none; background: #0f4c4c; color: #ffffff; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(15, 76, 76, 0.25);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Kirim Reservasi
                </button>
            </div>

        </form>

    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('userReservationFormModal');
    const btnOpenTop = document.getElementById('btnOpenReservationModal');
    const btnOpenEmpty = document.querySelector('.btn-open-modal-empty');
    const btnClose = document.getElementById('btnCloseUserResModal');
    const btnCancel = document.getElementById('btnCancelUserResModal');
    const backdrop = document.getElementById('userResModalBackdrop');
    const bookSelect = document.getElementById('resBookSelect');
    const previewBox = document.getElementById('resBookPreview');
    const previewCover = document.getElementById('resPreviewCover');
    const previewTitle = document.getElementById('resPreviewTitle');
    const previewAuthor = document.getElementById('resPreviewAuthor');
    const previewStock = document.getElementById('resPreviewStock');

    function openModal() {
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (btnOpenTop) btnOpenTop.addEventListener('click', openModal);
    if (btnOpenEmpty) btnOpenEmpty.addEventListener('click', openModal);
    if (btnClose) btnClose.addEventListener('click', closeModal);
    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });

    // Handle book change
    if (bookSelect) {
        bookSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (this.value && opt) {
                previewTitle.textContent = opt.dataset.title || '';
                previewAuthor.textContent = 'Penulis: ' + (opt.dataset.author || '-');
                previewStock.textContent = 'Stok Tersedia: ' + (opt.dataset.stock || 0);
                previewCover.src = opt.dataset.cover || '';
                previewBox.style.display = 'flex';
            } else {
                previewBox.style.display = 'none';
            }
        });
    }

    /* =================================================
       REAL-TIME DATA SYNCHRONIZATION (AC-1)
    ================================================= */
    if (window.PustakawanRealtime) {
        const currentUserId = {{ (int) auth()->id() }};

        PustakawanRealtime.on('reservation.approved', function (data) {
            // Cek apakah event ditujukan untuk user yang sedang aktif
            if (data.user_id && data.user_id !== currentUserId) {
                return;
            }

            const card = document.querySelector(`.res-card[data-reservation-id="${data.id}"]`);
            if (card) {
                // Ubah status badge langsung menjadi Dipinjam / Siap Diambil
                const badge = card.querySelector('.res-badge');
                if (badge) {
                    badge.style.background = '#ecfdf5';
                    badge.style.color = '#047857';
                    badge.style.borderColor = '#6ee7b7';
                    badge.innerHTML = `
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Dipinjam / Siap Diambil
                    `;
                }

                // Ubah teks meta menjadi siap diambil
                const meta = card.querySelector('.res-meta');
                if (meta) {
                    Array.from(meta.querySelectorAll('span')).forEach(s => {
                        if (s.textContent.includes('Menunggu')) s.remove();
                    });
                    const readySpan = document.createElement('span');
                    readySpan.style.color = '#047857';
                    readySpan.style.fontWeight = '700';
                    readySpan.innerHTML = `
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Buku siap diambil di perpustakaan
                    `;
                    meta.appendChild(readySpan);
                }

                // Hilangkan tombol batalkan
                const actions = card.querySelector('.res-actions');
                if (actions) {
                    actions.innerHTML = '';
                }

                // Efek highlight
                card.style.transition = 'all 0.5s ease';
                card.style.boxShadow = '0 0 0 3px #6ee7b7, 0 10px 25px rgba(4, 120, 87, 0.15)';
                card.style.transform = 'translateY(-3px)';
                setTimeout(() => {
                    card.style.boxShadow = '';
                    card.style.transform = '';
                }, 3500);
            }

            // Notifikasi toast live
            PustakawanRealtime.toast(
                `Reservasi buku "${data.book_title}" Anda telah disetujui! Status: Dipinjam/Siap Diambil.`,
                'success',
                '🎉 Disetujui Admin'
            );
        });
    }
});
</script>

@endsection
