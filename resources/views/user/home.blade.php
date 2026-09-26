@extends('layouts.user')

@section('title', 'Beranda')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endpush

@section('content')

<div class="user-home">

    {{-- =====================================================
         HERO
    ====================================================== --}}

    <section class="user-hero motion-section">

        <div class="user-hero-content motion-fade-up">

            <span class="user-hero-label">
                PERPUSTAKAAN TIGA SERANGKAI
            </span>

            <h1>
                Temukan buku<br>
                berikutnya.
            </h1>

            <p>
                Jelajahi berbagai koleksi buku pilihan dan temukan bacaan yang sesuai dengan minat serta kebutuhan Anda.
            </p>

            <div class="user-search-wrap">
                <div class="user-search">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <circle
                        cx="11"
                        cy="11"
                        r="8">
                    </circle>

                    <line
                        x1="21"
                        y1="21"
                        x2="16.65"
                        y2="16.65">
                    </line>

                </svg>

                    <input
                    type="text"
                    id="user-book-search"
                    placeholder="Cari judul, penulis, atau kategori..."
                    autocomplete="off">

                </div>

                <div class="user-search-results" id="user-search-results" role="listbox" aria-label="Hasil pencarian buku"></div>

            </div>

        </div>


        {{-- =================================================
             HERO 3D COVERFLOW CAROUSEL
        ================================================== --}}

        @if($popularBooks->isNotEmpty())

        <div class="user-hero-coverflow-wrap" id="heroCoverflowWrap">
            <div class="user-hero-coverflow" id="heroCoverflow">
                <div class="user-coverflow-stage" id="coverflowStage">
                    @foreach($popularBooks->take(8) as $index => $book)
                        <div class="user-coverflow-card"
                             data-index="{{ $index }}"
                             data-book-id="{{ $book->id }}"
                             role="group"
                             aria-label="Buku {{ $book->title }}"
                             data-title="{{ $book->title }}"
                             data-author="{{ $book->author ?? '-' }}"
                             data-category="{{ $book->category->name ?? '-' }}"
                             data-publisher="{{ $book->publisher ?? '-' }}"
                             data-year="{{ $book->publication_year ?? '-' }}"
                             data-isbn="{{ $book->isbn ?? '-' }}"
                             data-stock="{{ $book->available_stock ?? 0 }}"
                             data-description="{{ Str::limit($book->description ?? 'Deskripsi buku belum tersedia.', 150, '...') }}"
                             data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}">

                            <div class="coverflow-card-inner">
                                <div class="coverflow-book" aria-hidden="true">
                                    <div class="coverflow-book-back"></div>
                                    <div class="coverflow-book-pages"></div>
                                    <div class="coverflow-book-spine"></div>

                                    <div class="coverflow-book-front">
                                        <span class="coverflow-card-badge">
                                            {{ $book->main_category ?? $book->category->name ?? 'Koleksi' }}
                                        </span>

                                        <div class="coverflow-poster">
                                            @if($book->cover)
                                                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" loading="lazy">
                                            @else
                                                <div class="coverflow-fallback-cover">
                                                    <div class="fallback-spine"></div>
                                                    <div class="fallback-content">
                                                        <div class="fallback-emblem">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="coverflow-glare"></div>
                                        </div>

                                        <div class="coverflow-card-info">
                                            <h4 class="coverflow-title" title="{{ $book->title }}">{{ $book->title }}</h4>
                                            <p class="coverflow-author">{{ $book->author ?? 'Pustaka' }}</p>
                                        </div>

                                        <div class="coverflow-reflection"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Controls: Arrows & Pagination Indicator --}}
            <div class="user-coverflow-controls">
                <button type="button" class="coverflow-nav-btn prev" id="coverflowPrev" aria-label="Buku sebelumnya" title="Sebelumnya">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="coverflow-pagination" id="coverflowPagination" role="tablist" aria-label="Indikator halaman buku"></div>
                <button type="button" class="coverflow-nav-btn next" id="coverflowNext" aria-label="Buku selanjutnya" title="Selanjutnya">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        @endif

    </section>


    {{-- =====================================================
         4 WIDGET DASHBOARD USER (TERINTEGRASI DENGAN ADMIN)
    ====================================================== --}}
    <section class="user-dashboard-widgets" style="margin: 32px 0 40px; padding: 0 24px;">

        <div style="margin-bottom: 18px;">
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #287879; text-transform: uppercase;">Aktivitas Terbaru</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">

            {{-- 1. Peminjaman Aktif --}}
            <a href="{{ route('user.loans') }}" class="user-dashboard-stat-card" style="text-decoration: none; display: block; background: #ffffff; border: 1px solid #e2eeee; border-radius: 14px; padding: 18px 20px; box-shadow: 0 4px 14px rgba(27, 42, 58, 0.04);">
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: #648282; text-transform: uppercase;">Peminjaman Aktif</span>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e3d3d; margin: 10px 0 2px;">
                    {{ $activeLoansCount }}
                </div>
                <div style="font-size: 12px; color: #759292;">
                    {{ $activeLoansCount > 0 ? 'Buku sedang Anda pinjam' : 'Tidak ada buku dipinjam' }}
                </div>
            </a>

            {{-- 2. Reservasi Aktif --}}
            <a href="{{ route('user.reservations') }}" class="user-dashboard-stat-card" style="text-decoration: none; display: block; background: #ffffff; border: 1px solid #e2eeee; border-radius: 14px; padding: 18px 20px; box-shadow: 0 4px 14px rgba(27, 42, 58, 0.04);">
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: #648282; text-transform: uppercase;">Reservasi Aktif</span>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e3d3d; margin: 10px 0 2px;">
                    {{ $activeReservesCount }}
                </div>
                <div style="font-size: 12px; color: #759292;">
                    {{ $activeReservesCount > 0 ? 'Reservasi sedang berjalan' : 'Belum ada reservasi' }}
                </div>
            </a>

            {{-- 3. Riwayat Peminjaman --}}
            <a href="{{ route('user.history') }}" class="user-dashboard-stat-card" style="text-decoration: none; display: block; background: #ffffff; border: 1px solid #e2eeee; border-radius: 14px; padding: 18px 20px; box-shadow: 0 4px 14px rgba(27, 42, 58, 0.04);">
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: #648282; text-transform: uppercase;">Riwayat Selesai</span>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e3d3d; margin: 10px 0 2px;">
                    {{ $historyCount }}
                </div>
                <div style="font-size: 12px; color: #759292;">
                    Total buku selesai dibaca
                </div>
            </a>

            {{-- 4. Status Permintaan Terakhir --}}
            <div class="user-dashboard-stat-card" style="background: #ffffff; border: 1px solid #e2eeee; border-radius: 14px; padding: 18px 20px; box-shadow: 0 4px 14px rgba(27, 42, 58, 0.04); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 11px; font-weight: 700; color: #648282; text-transform: uppercase;">Status Permintaan Terakhir</span>
                        @if($latestRequest)
                            @php
                                $badgeBg = match($latestRequest['status_raw'] ?? '') {
                                    'menunggu'     => '#fef5e8',
                                    'dipinjam', 'disetujui' => '#e8f8f0',
                                    'ditolak'      => '#fdeeed',
                                    default        => '#f0f4f4',
                                };
                                $badgeColor = match($latestRequest['status_raw'] ?? '') {
                                    'menunggu'     => '#b86200',
                                    'dipinjam', 'disetujui' => '#0b8247',
                                    'ditolak'      => '#c22c24',
                                    default        => '#497171',
                                };
                            @endphp
                            <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">
                                {{ $latestRequest['status'] }}
                            </span>
                        @endif
                    </div>

                    @if($latestRequest)
                        <div style="font-size: 13px; font-weight: 700; color: #1e3d3d; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 4px;">
                            {{ $latestRequest['title'] }}
                        </div>
                        <div style="font-size: 11.5px; color: #759292; margin-top: 2px;">
                            {{ $latestRequest['type'] }} • {{ $latestRequest['date']?->diffForHumans() }}
                        </div>
                        @if(!empty($latestRequest['notes']))
                            <div style="font-size: 11px; color: #c22c24; background: #fff5f5; padding: 4px 8px; border-radius: 6px; margin-top: 6px;">
                                Catatan: {{ Str::limit($latestRequest['notes'], 45) }}
                            </div>
                        @endif
                    @else
                        <div style="font-size: 13px; color: #8ba2a2; font-weight: 500; margin-top: 8px;">
                            Belum ada aktivitas permintaan.
                        </div>
                    @endif
                </div>

                @if($latestRequest && !empty($latestRequest['link']))
                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #f2f6f6;">
                        <a href="{{ $latestRequest['link'] }}" style="font-size: 11.5px; font-weight: 600; color: #287879; text-decoration: none;">
                            Lihat Detail Permintaan →
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </section>



    {{-- =====================================================
         POPULAR
    ====================================================== --}}

    <section class="user-section user-popular-section">

        <div class="user-section-header motion-title">

            <div>

                <span class="user-section-kicker">
                    REKOMENDASI BUKU
                </span>

                <h2>
                    Rekomendasi Untuk Anda
                </h2>

            </div>

        </div>


        <div class="user-popular-carousel-wrap">

            <button type="button" class="popular-carousel-nav popular-carousel-prev" id="popularCarouselPrev" aria-label="Buku sebelumnya" title="Sebelumnya">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <div class="user-book-carousel-viewport" data-book-carousel>
            <div class="user-book-row user-popular-row" data-book-carousel-track>

            @foreach($recommendations as $book)

            <article
                class="user-book-card motion-book-card popular-book-card"
                style="--motion-delay: {{ $loop->index * 70 }}ms"
                data-book-card
                data-title="{{ strtolower($book->title) }}"
                data-author="{{ strtolower($book->author ?? '') }}"
                data-category="{{ strtolower($book->category->name ?? '') }}">

                <div class="popular-card-inner">
                    <button
                        type="button"
                        class="user-book-cover user-book-open"

                        data-title="{{ $book->title }}"
                        data-author="{{ $book->author ?? '-' }}"
                        data-category="{{ $book->category->name ?? '-' }}"
                        data-publisher="{{ $book->publisher ?? '-' }}"
                        data-year="{{ $book->publication_year ?? '-' }}"
                        data-isbn="{{ $book->isbn ?? '-' }}"
                        data-stock="{{ $book->available_stock ?? 0 }}"
                        data-description="{{ Str::limit($book->description ?? 'Deskripsi buku belum tersedia.', 150, '...') }}"
                        data-cover="{{ $book->cover ? asset('storage/' . $book->cover) : '' }}">

                        <span class="user-cover-category">
                            {{ $book->main_category ?? $book->category->name ?? 'Koleksi' }}
                        </span>

                        @if($book->cover)

                        <img
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="{{ $book->title }}">

                        @else

                        <div class="user-cover-placeholder">
                            <span class="user-cover-placeholder-mark" aria-hidden="true"></span>
                            <span class="user-cover-placeholder-title">{{ $book->title }}</span>
                        </div>

                        @endif

                    </button>


                    <div class="user-book-info">

                        <span class="user-book-category">
                            {{ $book->category->name ?? 'Koleksi' }}
                        </span>

                        <h3>
                            {{ $book->title }}
                        </h3>

                        <p>
                            {{ $book->author ?? 'Penulis tidak diketahui' }}
                        </p>

                    </div>
                </div>

            </article>

            @endforeach

            </div>
            </div>

            <button type="button" class="popular-carousel-nav popular-carousel-next" id="popularCarouselNext" aria-label="Buku selanjutnya" title="Selanjutnya">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

        </div>

    </section>

</div>


{{-- =========================================================
     PENGUMUMAN (ANNOUNCEMENT) POPUP
     Hanya muncul jika ada aktivitas MANUAL yang belum dibaca.
========================================================= --}}

@if(isset($unreadAnnouncement) && $unreadAnnouncement)

<div
    class="announcement-modal"
    id="announcementModal"
    aria-hidden="false"
    data-announcement-id="{{ $unreadAnnouncement->id }}">

    <div class="announcement-modal-overlay"></div>

    <div class="announcement-modal-box">

        <div class="announcement-modal-header">
            📢 Pengumuman
        </div>

        <div class="announcement-modal-body">

            <h2>
                {{ $unreadAnnouncement->title }}
            </h2>

            <p>
                {{ $unreadAnnouncement->description ?? '' }}
            </p>

        </div>

        <button
            type="button"
            class="announcement-close-btn"
            id="announcementCloseBtn">

            Tutup

        </button>

    </div>

</div>

@endif


{{-- =========================================================
     BOOK DETAIL MODAL
========================================================== --}}
</parameter>

<div
    id="userBookModal"
    class="user-book-modal"
    aria-hidden="true">

    <div
        class="user-book-modal-overlay"
        id="userBookModalOverlay">
    </div>


    <div class="user-book-modal-content">

        {{-- =================================================
             3D BOOK
        ================================================== --}}

        <div class="user-book-3d-section">

            <div class="user-book-3d-hint">
                🖱️ Geser untuk memutar buku
            </div>


            <div
                class="book-3d-stage"
                id="book3dStage">

                <div
                    class="book-3d"
                    id="book3d">

                    {{-- FRONT --}}

                    <div
                        class="book-face book-front"
                        id="book3dFront">

                        <div
                            class="book-cover-fallback"
                            id="book3dFallback">

                            <span id="book3dFallbackTitle">
                                Buku
                            </span>

                        </div>


                        <img
                            id="book3dCover"
                            src=""
                            alt="Cover buku">

                    </div>


                    {{-- BACK --}}

                    <div class="book-face book-back">

                        <span id="book3dBackTitle">
                            Perpustakaan Tiga Serangkai
                        </span>

                    </div>


                    {{-- SPINE --}}

                    <div class="book-face book-left">
                    </div>


                    {{-- PAGE EDGE --}}

                    <div class="book-face book-right">
                    </div>


                    {{-- TOP --}}

                    <div class="book-face book-top">
                    </div>


                    {{-- BOTTOM --}}

                    <div class="book-face book-bottom">
                    </div>

                </div>

            </div>


            <div class="book-3d-controls">

                <button
                    type="button"
                    id="book3dReset">

                    ↻ Reset

                </button>

                <span>
                    Drag untuk memutar
                </span>

            </div>

        </div>


        {{-- =================================================
             DETAIL
        ================================================== --}}

        <div class="user-book-detail">

            <div class="user-book-category">

                <span id="modalBookCategory">
                    KATEGORI
                </span>

            </div>


            <h2 id="modalBookTitle">
                Judul Buku
            </h2>


            <p
                class="user-book-author"
                id="modalBookAuthor">

                Penulis

            </p>


            <div class="user-book-info-grid">

                <div class="user-book-info-box">

                    <span>
                        STOK
                    </span>

                    <strong id="modalBookStock">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        PENERBIT
                    </span>

                    <strong id="modalBookPublisher">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        TAHUN
                    </span>

                    <strong id="modalBookYear">
                        -
                    </strong>

                </div>


                <div class="user-book-info-box">

                    <span>
                        ISBN
                    </span>

                    <strong id="modalBookIsbn">
                        -
                    </strong>

                </div>

            </div>


            <div class="user-book-synopsis">

                <span>
                    SINOPSIS
                </span>

                <p id="modalBookDescription">
                    Deskripsi buku belum tersedia.
                </p>

            </div>


            <div class="user-book-actions">

                <a
                    href="{{ route('catalog') }}"
                    class="user-book-catalog-btn"
                    id="userBookCatalogButton">

                    Lihat di Katalog →

                </a>

                <button type="button" class="user-book-home-btn" id="userBookHomeButton">
                    <span aria-hidden="true">←</span>
                    <span>Kembali ke Beranda</span>
                </button>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

@push('scripts')

<script
    src="{{ asset('js/user-home.js') }}">
</script>

<script
    src="{{ asset('js/userBook3d.js') }}">
</script>

<script>
    (function () {

        const announcementModal =
            document.getElementById('announcementModal');

        if (!announcementModal) {
            return;
        }

        const closeBtn =
            document.getElementById('announcementCloseBtn');

        const activityId =
            announcementModal.getAttribute('data-announcement-id');

        const markAsReadUrl =
            '{{ route('activities.markAsRead', ['activity' => '__ID__']) }}'
            .replace('__ID__', activityId);


        function closeAnnouncement() {

            // Simpan status "sudah dibaca" ke database
            // agar popup tidak muncul lagi pada refresh berikutnya.
            fetch(markAsReadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        )?.getAttribute('content') ?? '',
                    'Content-Type': 'application/json'
                }
            }).catch(function () {
                // Abaikan error jaringan agar popup tetap bisa ditutup.
            });

            announcementModal.classList.add('hidden');
            announcementModal.setAttribute('aria-hidden', 'true');
        }


        if (closeBtn) {
            closeBtn.addEventListener('click', closeAnnouncement);
        }


        // Tutup juga saat klik overlay
        const overlay = announcementModal.querySelector(
            '.announcement-modal-overlay'
        );

        if (overlay) {
            overlay.addEventListener('click', closeAnnouncement);
        }

    })();
</script>

@endpush

@endsection