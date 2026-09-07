@extends('layouts.user')

@section('title', 'Favorit Saya - Pustakawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<style>
    .favorites-header {
        padding: 40px 0 24px 0;
    }

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 24px;
        padding-bottom: 60px;
    }

    @media (max-width: 768px) {
        .favorites-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    @media (max-width: 480px) {
        .favorites-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .fav-book-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e8edf2;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .fav-book-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(15, 76, 76, 0.1);
        border-color: #cbd5e1;
    }

    .empty-state-card {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 24px;
        padding: 60px 24px;
        text-align: center;
        max-width: 540px;
        margin: 40px auto 80px auto;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #fef2f2;
        color: #e11d48;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px auto;
    }

    .toast-alert {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        background: #0f4c4c;
        color: #ffffff;
        padding: 14px 22px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(15, 76, 76, 0.25);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        animation: toastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes toastIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>
@endpush

@section('content')

{{-- Flash Success Alert / Toast --}}
@if(session('success'))
<div class="toast-alert" id="sessionToast">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
    </svg>
    <span>{{ session('success') }}</span>
</div>
<script>
    setTimeout(() => {
        const toast = document.getElementById('sessionToast');
        if (toast) toast.remove();
    }, 4000);
</script>
@endif

<div class="user-home">

    <section class="user-section" style="min-height: 60vh;">

        {{-- HEADER FAVORIT --}}
        <div class="user-section-header motion-title favorites-header">
            <div>
                <span class="user-section-kicker" style="color: #e11d48; display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#e11d48" stroke="#e11d48" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    KOLEKSI SAYA
                </span>

                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 4px 0 0 0;">
                    Favorit Saya
                </h1>

                <p style="font-size: 14px; color: #64748b; margin-top: 4px;">
                    Daftar semua buku yang Anda simpan ke koleksi personal.
                </p>
            </div>

            <a href="{{ route('catalog') }}" style="color: #0f4c4c; font-weight: 600; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                Jelajahi Katalog →
            </a>
        </div>

        {{-- DAFTAR BUKU FAVORIT --}}
        @if($favorites->count() > 0)

        <div class="favorites-grid">
            @foreach($favorites as $fav)
            @php 
                $book = $fav->book; 
                $stockAvailable = $book ? ($book->available_stock ?? $book->stok ?? 0) : 0;
            @endphp
            @if($book)
            <article
                class="fav-book-card motion-book-card"
                style="--motion-delay: {{ $loop->index * 60 }}ms;">

                <div>
                    {{-- 1. COVER BUKU (Identik Beranda) --}}
                    <button
                        type="button"
                        class="user-book-cover user-book-open"
                        data-id="{{ $book->id }}"
                        data-title="{{ $book->title }}"
                        data-author="{{ $book->author ?? '-' }}"
                        data-category="{{ $book->category->name ?? '-' }}"
                        data-publisher="{{ $book->publisher ?? '-' }}"
                        data-year="{{ $book->publication_year ?? '-' }}"
                        data-isbn="{{ $book->isbn ?? '-' }}"
                        data-stock="{{ $stockAvailable }}"
                        data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                        data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}">

                        @if($book->cover)
                        <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}">
                        @else
                        <div class="user-cover-placeholder">
                            {{ $book->title }}
                        </div>
                        @endif
                    </button>

                    {{-- 2. INFORMASI BUKU: Kategori, Judul, Penulis, Status Ketersediaan (Identik Beranda) --}}
                    <div class="user-book-info" style="margin-top: 10px;">
                        <span class="user-book-category">
                            {{ $book->category->name ?? 'Koleksi' }}
                        </span>
                        <h3 title="{{ $book->title }}" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 4px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $book->title }}
                        </h3>
                        <p style="font-size: 13px; color: #64748b; margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $book->author ?? 'Penulis tidak diketahui' }}
                        </p>

                        {{-- Status Ketersediaan --}}
                        <div style="margin-top: 6px;">
                            @if($stockAvailable > 0)
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #6ee7b7; padding: 2px 8px; border-radius: 12px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                                    Tersedia ({{ $stockAvailable }})
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; padding: 2px 8px; border-radius: 12px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span>
                                    Sedang Dipinjam
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. OPSI AKSI YANG TERSEDIA (DIPERKETAT: HANYA 2 TOMBOL) --}}
                <div class="user-book-actions" style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px; width: 100%;">
                    
                    {{-- Aksi 1: Lihat di Katalog Saya --}}
                    <a
                        href="{{ route('catalog', ['search' => $book->title]) }}"
                        class="btn-action-catalog"
                        style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 8px 12px; border-radius: 10px; background: #f8fafc; color: #0f4c4c; font-size: 12px; font-weight: 700; text-decoration: none; border: 1.5px solid #cbd5e1; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#0f4c4c';"
                        onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Lihat di Katalog Saya
                    </a>

                    {{-- Aksi 2: Reservasi --}}
                    <form action="{{ route('user.reservations.store') }}" method="POST" style="margin: 0; width: 100%;">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button
                            type="submit"
                            class="btn-action-reserve"
                            @disabled($stockAvailable < 1)
                            style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 8px 12px; border-radius: 10px; background: {{ $stockAvailable > 0 ? '#0f4c4c' : '#94a3b8' }}; color: #ffffff; font-size: 12px; font-weight: 700; border: none; cursor: {{ $stockAvailable > 0 ? 'pointer' : 'not-allowed' }}; transition: all 0.2s; box-shadow: 0 2px 6px rgba(15, 76, 76, 0.15);"
                            onmouseover="{{ $stockAvailable > 0 ? "this.style.background='#0a3737'" : '' }}"
                            onmouseout="{{ $stockAvailable > 0 ? "this.style.background='#0f4c4c'" : '' }}">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Reservasi
                        </button>
                    </form>

                </div>

            </article>
            @endif
            @endforeach
        </div>

        @else

        {{-- EMPTY STATE --}}
        <div class="empty-state-card motion-fade-up">
            <div class="empty-state-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </div>

            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Belum ada buku favorit
            </h2>

            <p style="font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                Belum ada buku di daftar favoritmu. Yuk, jelajahi katalog buku kami dan temukan bacaan yang Anda sukai!
            </p>

            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('catalog') }}" class="user-book-catalog-btn" style="background: #0f4c4c;">
                    Jelajahi Katalog Buku
                </a>
                <a href="{{ route('user.home') }}" class="user-book-catalog-btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        @endif

    </section>

</div>

{{-- MODAL DETAIL BUKU --}}
<div class="user-book-modal" id="userBookModal" aria-hidden="true">

    <div class="user-book-modal-overlay" id="userBookModalOverlay"></div>

    <div class="user-book-modal-container">

        <button type="button" class="user-book-modal-close" id="userBookModalClose" aria-label="Tutup modal">
            ✕
        </button>

        {{-- 3D STAGE --}}
        <div class="user-book-stage" id="book3dStage">
            <span class="user-book-stage-badge">
                📖 Geser untuk memutar buku
            </span>

            <div class="user-book-3d" id="book3d">
                <div class="user-book-face user-book-front">
                    <img src="" alt="Cover Depan" id="book3dCover" class="user-book-image is-hidden">
                    <div class="user-book-fallback" id="book3dFallback">
                        <span id="book3dFallbackTitle">Buku</span>
                    </div>
                </div>
                <div class="user-book-face user-book-back">
                    <div class="user-book-back-content">
                        <h4 id="book3dBackTitle">Buku</h4>
                        <p>Koleksi Perpustakaan</p>
                    </div>
                </div>
                <div class="user-book-face user-book-spine"></div>
                <div class="user-book-face user-book-right"></div>
                <div class="user-book-face user-book-top"></div>
                <div class="user-book-face user-book-bottom"></div>
            </div>

            <button type="button" class="user-book-reset-btn" id="book3dReset">
                ↺ Reset
            </button>
        </div>

        {{-- DETAIL INFO --}}
        <div class="user-book-detail">
            <span class="user-book-detail-category" id="modalBookCategory">
                KATEGORI
            </span>

            <h2 id="modalBookTitle">
                Judul Buku
            </h2>

            <p class="user-book-detail-author" id="modalBookAuthor">
                Penulis
            </p>

            <div class="user-book-info-grid">
                <div class="user-book-info-box">
                    <span>STOK</span>
                    <strong id="modalBookStock">0</strong>
                </div>
                <div class="user-book-info-box">
                    <span>PENERBIT</span>
                    <strong id="modalBookPublisher">-</strong>
                </div>
                <div class="user-book-info-box">
                    <span>TAHUN</span>
                    <strong id="modalBookYear">-</strong>
                </div>
                <div class="user-book-info-box">
                    <span>ISBN</span>
                    <strong id="modalBookIsbn">-</strong>
                </div>
            </div>

            <div class="user-book-synopsis">
                <span>SINOPSIS</span>
                <p id="modalBookDescription">
                    Deskripsi buku belum tersedia.
                </p>
            </div>

            {{-- AKSI MODAL (DIPERKETAT: HANYA 2 TOMBOL) --}}
            <div class="user-book-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                {{-- Aksi 1: Lihat di Katalog Saya --}}
                <a href="{{ route('catalog') }}" class="user-book-catalog-btn" id="modalCatalogBtn">
                    Lihat di Katalog Saya →
                </a>

                {{-- Aksi 2: Reservasi --}}
                <form action="{{ route('user.reservations.store') }}" method="POST" id="modalReservationForm" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="book_id" id="modalReservationBookId" value="">
                    <button
                        type="submit"
                        id="btn-reservasi-modal"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 0 16px; border-radius: 10px; background: #0f4c4c; color: #ffffff; border: none; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#0a3737'"
                        onmouseout="this.style.background='#0f4c4c'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Reservasi
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="{{ asset('js/user-home.js') }}"></script>
<script src="{{ asset('js/userBook3d.js') }}"></script>
@endpush

@endsection
