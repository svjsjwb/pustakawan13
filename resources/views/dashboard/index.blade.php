@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

    <section class="page dashboard-page" id="page-dashboard">


        {{-- =====================================================
         HERO DASHBOARD
    ====================================================== --}}

        <div class="dashboard-hero">

            <div class="dashboard-hero-content">

                <div class="dashboard-hero-label">

                    <span class="dashboard-hero-dot"></span>

                    Pusat Perpustakaan

                </div>


                <h1>
                    Dashboard
                </h1>


                <p>
                    Pantau aktivitas dan statistik perpustakaan
                    secara real-time.
                </p>

            </div>

        </div>


        {{-- =====================================================
         STATISTIK CARD
    ====================================================== --}}

        <div class="dashboard-stats">


            {{-- TOTAL BUKU --}}

            <div class="stat-card stat-books">

                <div class="stat-card-top">

                    <div class="stat-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            <path d="M9 7h6M9 11h6"/>

                        </svg>

                    </div>


                    <div class="stat-content">

                        <div class="stat-title">
                            TOTAL KOLEKSI BUKU
                        </div>

                        <div class="stat-value">
                            {{ number_format($totalBooks, 0, ',', '.') }}
                        </div>

                        <div class="stat-description">
                            Koleksi buku
                        </div>

                    </div>

                </div>


                <div class="stat-wave"></div>

            </div>


            {{-- SEDANG DIPINJAM --}}

            <div class="stat-card stat-borrowed">

                <div class="stat-card-top">

                    <div class="stat-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            <path d="M12 6v6l3-2 3 2V6"/>

                        </svg>

                    </div>


                    <div class="stat-content">

                        <div class="stat-title">
                            SEDANG DIPINJAM
                        </div>

                        <div class="stat-value">
                            {{ number_format($borrowedBooks, 0, ',', '.') }}
                        </div>

                        <div class="stat-description">
                            Buku sedang dipinjam
                        </div>

                    </div>

                </div>


                <div class="stat-wave"></div>

            </div>


            {{-- ANGGOTA AKTIF --}}

            <div class="stat-card stat-members">

                <div class="stat-card-top">

                    <div class="stat-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>

                        </svg>

                    </div>


                    <div class="stat-content">

                        <div class="stat-title">
                            ANGGOTA AKTIF
                        </div>

                        <div class="stat-value">
                            {{ number_format($activeMembers, 0, ',', '.') }}
                        </div>

                        <div class="stat-description">
                            Anggota terdaftar aktif
                        </div>

                    </div>

                </div>


                <div class="stat-wave"></div>

            </div>


            {{-- KETERLAMBATAN --}}

            <div class="stat-card stat-late">

                <div class="stat-card-top">

                    <div class="stat-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                            <circle cx="12" cy="12" r="9"/>
                            <polyline points="12 7 12 12 14.5 14.5"/>

                        </svg>

                    </div>


                    <div class="stat-content">

                        <div class="stat-title">
                            KETERLAMBATAN
                        </div>

                        <div class="stat-value">
                            {{ number_format($lateBorrowings, 0, ',', '.') }}
                        </div>

                        <div class="stat-description">
                            Peminjaman melewati jatuh tempo
                        </div>

                    </div>

                </div>


                <div class="stat-wave"></div>

            </div>

        </div>


        {{-- =====================================================
         DASHBOARD MIDDLE
    ====================================================== --}}

        <div class="dashboard-middle">


            {{-- =================================================
             STATISTIK PEMINJAMAN
        ================================================== --}}

            <div class="dashboard-panel borrowing-panel">

                <div class="panel-header">

                    <div class="panel-title-wrapper">

                        <div class="panel-icon chart-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                                <line x1="3" y1="20" x2="21" y2="20"/>

                            </svg>

                        </div>


                        <div>

                            <h2>
                                Statistik Peminjaman
                            </h2>

                            <span>
                                Bulan ini
                            </span>

                        </div>

                    </div>

                </div>


                <div class="chart-area">

                    <div class="chart-y">

                        <span>
                            {{ $max7 }}
                        </span>

                        <span>
                            {{ round($max7 * 0.8) }}
                        </span>

                        <span>
                            {{ round($max7 * 0.6) }}
                        </span>

                        <span>
                            {{ round($max7 * 0.4) }}
                        </span>

                        <span>
                            {{ round($max7 * 0.2) }}
                        </span>

                        <span>
                            0
                        </span>

                    </div>


                    <div class="chart-content">

                        <div class="chart-grid">

                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>

                        </div>


                        <div class="chart-bars">

                            @forelse($chart7Days as $item)
                                <div class="chart-bar-item">

                                    <div class="chart-bar-tooltip">

                                        {{ $item['label'] }}:
                                        {{ $item['count'] }}
                                        dipinjam

                                    </div>


                                    <div class="chart-bar" style="height: {{ $item['height'] }}%;">
                                    </div>


                                    <span>
                                        {{ $item['label'] }}
                                    </span>

                                </div>

                            @empty

                                <div class="empty-small">
                                    Belum ada data peminjaman bulan ini.
                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>


                <div class="chart-caption">

                    Grafik aktivitas peminjaman buku
                    (bulan ini)

                </div>

            </div>


            {{-- =================================================
             BUKU TERPOPULER
        ================================================== --}}

            <div class="dashboard-panel popular-panel">

                <div class="panel-header">

                    <div class="panel-title-wrapper">

                        <div class="panel-icon book-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                <polygon points="12 6 13.5 9 17 9.5 14.5 12 15 15.5 12 14 9 15.5 9.5 12 7 9.5 10.5 9 12 6"/>

                            </svg>

                        </div>


                        <div>

                            <h2>
                                Buku Terpopuler
                            </h2>

                            <span>
                                Berdasarkan jumlah peminjaman &amp; reservasi
                            </span>

                        </div>

                    </div>

                </div>


                <div class="popular-list">




                    @forelse($popularBooks as $index => $book)
                        <div class="popular-item">

                            <div class="popular-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="popular-name">
                                {{ $book['title'] }}
                            </div>


                            <div class="popular-total">
                                {{ $book['total'] }} kali
                            </div>

                        </div>

                    @empty

                        <div class="empty-small">
                            Belum ada data buku.
                        </div>
                    @endforelse

                </div>


                <a href="{{ route('catalog') }}" class="panel-link">

                    Lihat semua buku

                </a>

            </div>


            {{-- =================================================
             AKTIVITAS TERBARU
        ================================================== --}}

            <div class="dashboard-panel activity-panel">

                <div class="panel-header">

                    <div class="panel-title-wrapper">

                        <div class="panel-icon activity-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>

                            </svg>

                        </div>


                        <div>

                            <h2>
                                Aktivitas Terbaru
                            </h2>

                            <span>
                                Update aktivitas perpustakaan
                            </span>

                        </div>

                    </div>

                </div>


                <div class="activity-list">

                    @forelse($activities as $activity)
                        <div class="activity-item">

                            {{-- ICON --}}
                            <div class="activity-item-icon icon-{{ ($activity['type'] ?? '') === 'manual' && !empty($activity['pinned_at']) ? 'pinned' : ($activity['type'] ?? 'default') }}">
                                @if (($activity['type'] ?? '') === 'member')
                                    👥
                                @elseif (($activity['type'] ?? '') === 'book')
                                    📚
                                @elseif (($activity['type'] ?? '') === 'borrowing')
                                    📖
                                @elseif (($activity['type'] ?? '') === 'reservation')
                                    📅
                                @elseif (($activity['type'] ?? '') === 'manual')
                                    @if (!empty($activity['pinned_at']))
                                        📌
                                    @else
                                        📢
                                    @endif
                                @else
                                    •
                                @endif
                            </div>


                            {{-- INFORMASI AKTIVITAS --}}
                            <div class="activity-info">

                                <strong>
                                    {{ $activity['title'] }}
                                </strong>

                                <span>
                                    {{ $activity['description'] }}
                                </span>

                            </div>


                            {{-- WAKTU --}}
                            <time>

                                {{ $activity['created_at'] ? $activity['created_at']->locale('id')->diffForHumans() : '-' }}

                            </time>


                            {{-- MENU AKTIVITAS MANUAL --}}
                            @if (($activity['type'] ?? '') === 'manual')
                                <div class="activity-menu-wrapper">

                                    <button type="button" class="activity-menu-btn" aria-label="Opsi aktivitas"
                                        title="Opsi aktivitas">

                                        <span></span>
                                        <span></span>
                                        <span></span>

                                    </button>


                                    <div class="activity-menu">

                                        {{-- EDIT --}}
                                        <button type="button" class="activity-menu-item activity-menu-edit"
                                            data-activity-id="{{ $activity['id'] }}"
                                            data-activity-title="{{ $activity['title'] }}"
                                            data-activity-description="{{ $activity['description'] }}">

                                            <span class="activity-menu-icon">
                                                ✎
                                            </span>

                                            <span>
                                                Edit
                                            </span>

                                        </button>


                                        {{-- PIN / LEPAS PIN --}}
                                        <button type="button" class="activity-menu-item activity-menu-pin"
                                            data-activity-id="{{ $activity['id'] }}">

                                            <span class="activity-menu-icon">
                                                📌
                                            </span>

                                            <span>
                                                {{ !empty($activity['pinned_at']) ? 'Lepas Pin' : 'Pin' }}
                                            </span>

                                        </button>


                                        {{-- HAPUS --}}
                                        <button type="button" class="activity-menu-item danger activity-menu-delete"
                                            data-activity-id="{{ $activity['id'] }}">

                                            <span class="activity-menu-icon">
                                                🗑
                                            </span>

                                            <span>
                                                Hapus
                                            </span>

                                        </button>

                                    </div>

                                </div>
                            @endif

                        </div>

                    @empty

                        <div class="empty-small">
                            Belum ada aktivitas.
                        </div>
                    @endforelse

                </div>


                <div class="activity-panel-actions">

                    {{-- TAMBAH AKTIVITAS --}}
                    <a href="#" class="panel-link" id="btnAddActivity" onclick="return false;">

                        + Tambah Aktivitas

                    </a>


                    {{-- LIHAT SEMUA AKTIVITAS --}}
                    <a href="{{ route('activities.index') }}" class="panel-link activity-see-all">

                        Lihat Semua Aktivitas

                    </a>

                </div>

            </div>

        </div>


        {{-- =====================================================
         RESERVASI TERBARU
    ====================================================== --}}

        <div class="dashboard-panel reservation-panel">

            <div class="reservation-header">

                <div class="panel-title-wrapper">

                    <div class="panel-icon reservation-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">

                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <polyline points="9 16 11 18 15 14"/>

                        </svg>

                    </div>


                    <div>

                        <h2>
                            Reservasi Terbaru
                        </h2>

                        <span>
                            Daftar reservasi terbaru
                        </span>

                    </div>

                </div>


                <a href="{{ route('reservations.index') }}" class="reservation-see-all">

                    Lihat semua reservasi

                </a>

            </div>


            <div class="dashboard-table">

                <div class="dashboard-table-head">

                    <div>
                        Waktu
                    </div>

                    <div>
                        Anggota
                    </div>

                    <div>
                        Buku
                    </div>

                    <div>
                        Aktivitas
                    </div>

                    <div>
                        Status
                    </div>

                </div>


                @forelse($reservations as $reservation)
                    <div class="dashboard-table-row">

                        <div>

                            {{ $reservation->created_at ? $reservation->created_at->format('H.i') : '-' }}

                        </div>


                        <div>

                            {{ $reservation->member->name ?? '-' }}

                        </div>


                        <div>

                            {{ $reservation->book->title ?? '-' }}

                        </div>


                        <div>

                            <span class="activity-label">
                                Reservasi
                            </span>

                        </div>


                        <div>

    @if ($reservation->status === 'menunggu')

        <span class="dashboard-status waiting">
            Menunggu
        </span>

    @elseif ($reservation->status === 'disetujui')

        <span class="dashboard-status approved">
            Disetujui
        </span>

    @elseif ($reservation->status === 'selesai')

        <span class="dashboard-status completed">
            Selesai
        </span>

    @elseif ($reservation->status === 'ditolak')

        <span class="dashboard-status rejected">
            Ditolak
        </span>

    @elseif ($reservation->status === 'dibatalkan')

        <span class="dashboard-status rejected">
            Dibatalkan
        </span>

    @else

        <span class="dashboard-status">
            {{ ucfirst($reservation->status ?? '-') }}
        </span>

    @endif

</div>

                    </div>

                @empty

                    <div class="dashboard-table-row">

                        <div style="grid-column:1/-1;text-align:center;">

                            Belum ada reservasi.

                        </div>

                    </div>
                @endforelse

            </div>

        </div>

    </section>


    {{-- =========================================================
     MODAL TAMBAH / EDIT AKTIVITAS
========================================================= --}}

    <div class="activity-modal" id="activityModal" aria-hidden="true">


        <div class="activity-modal-overlay" id="activityModalOverlay">
        </div>


        <div class="activity-modal-box">


            <div class="activity-modal-header">

                <h3 id="activityModalTitle">
                    Tambah Aktivitas
                </h3>


                <button type="button" class="activity-modal-close" id="activityModalClose" aria-label="Tutup">

                    ×

                </button>

            </div>


            <form id="activityForm" method="POST" action="{{ route('activities.store') }}">

                @csrf


                <input type="hidden" name="_method" id="activityFormMethod" value="POST">


                <div class="activity-form-field">

                    <label for="activityTitle">
                        Judul Aktivitas
                    </label>


                    <input type="text" name="title" id="activityTitle" class="activity-form-input"
                        placeholder="Contoh: Perpustakaan Tutup" required maxlength="255">


                    @error('title')
                        <span class="activity-form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <div class="activity-form-field">

                    <label for="activityDescription">
                        Deskripsi Aktivitas
                    </label>


                    <textarea name="description" id="activityDescription" class="activity-form-input"
                        placeholder="Contoh: Perpustakaan akan tutup pada tanggal 5 September." rows="4"></textarea>

                </div>


                <div class="activity-modal-actions">

                    <button type="button" class="activity-btn-cancel" id="btnCancelActivity">

                        Batal

                    </button>


                    <button type="submit" class="activity-btn-simpan">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>


    @push('scripts')
        <script>
            (function() {

                const modal =
                    document.getElementById('activityModal');

                const modalTitle =
                    document.getElementById('activityModalTitle');

                const form =
                    document.getElementById('activityForm');

                const formMethod =
                    document.getElementById('activityFormMethod');

                const titleInput =
                    document.getElementById('activityTitle');

                const descInput =
                    document.getElementById('activityDescription');

                const btnAdd =
                    document.getElementById('btnAddActivity');

                const btnClose =
                    document.getElementById('activityModalClose');

                const btnCancel =
                    document.getElementById('btnCancelActivity');

                const overlay =
                    document.getElementById('activityModalOverlay');


                let currentEditId = null;


                /* =====================================================
                   MODAL
                ====================================================== */

                function openModal() {

                    modal.classList.add('open');

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                }


                function closeModal() {

                    modal.classList.remove('open');

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                }


                /* =====================================================
                   TAMBAH AKTIVITAS
                ====================================================== */

                if (btnAdd) {

                    btnAdd.addEventListener(
                        'click',
                        function(e) {

                            e.preventDefault();

                            formMethod.value = 'POST';

                            form.action =
                                '{{ route('activities.store') }}';

                            modalTitle.textContent =
                                'Tambah Aktivitas';

                            titleInput.value = '';

                            descInput.value = '';

                            titleInput.removeAttribute(
                                'readonly'
                            );

                            descInput.removeAttribute(
                                'readonly'
                            );

                            currentEditId = null;

                            openModal();

                            titleInput.focus();

                        }
                    );

                }


                /* =====================================================
                   TUTUP MODAL
                ====================================================== */

                if (btnCancel) {

                    btnCancel.addEventListener(
                        'click',
                        closeModal
                    );

                }


                if (btnClose) {

                    btnClose.addEventListener(
                        'click',
                        closeModal
                    );

                }


                if (overlay) {

                    overlay.addEventListener(
                        'click',
                        closeModal
                    );

                }


                /* =====================================================
                   MENU TITIK TIGA
                ====================================================== */

                const menuWrappers =
                    document.querySelectorAll(
                        '.activity-menu-wrapper'
                    );


                menuWrappers.forEach(
                    function(wrapper) {

                        const menuButton =
                            wrapper.querySelector(
                                '.activity-menu-btn'
                            );


                        if (!menuButton) {
                            return;
                        }


                        menuButton.addEventListener(
                            'click',
                            function(e) {

                                e.preventDefault();

                                e.stopPropagation();


                                /*
                                 * Tutup menu lain
                                 */

                                menuWrappers.forEach(
                                    function(otherWrapper) {

                                        if (
                                            otherWrapper !== wrapper
                                        ) {

                                            otherWrapper.classList.remove(
                                                'menu-open'
                                            );

                                        }

                                    }
                                );


                                /*
                                 * Toggle menu sekarang
                                 */

                                wrapper.classList.toggle(
                                    'menu-open'
                                );

                            }
                        );

                    }
                );


                /* =====================================================
                   KLIK DI LUAR MENU
                ====================================================== */

                document.addEventListener(
                    'click',
                    function() {

                        menuWrappers.forEach(
                            function(wrapper) {

                                wrapper.classList.remove(
                                    'menu-open'
                                );

                            }
                        );

                    }
                );


                /* =====================================================
                   EDIT AKTIVITAS
                ====================================================== */

                document
                    .querySelectorAll('.activity-menu-edit')
                    .forEach(
                        function(btn) {

                            btn.addEventListener(
                                'click',
                                function(e) {

                                    e.preventDefault();

                                    e.stopPropagation();


                                    const id =
                                        btn.getAttribute(
                                            'data-activity-id'
                                        );


                                    const title =
                                        btn.getAttribute(
                                            'data-activity-title'
                                        );


                                    const description =
                                        btn.getAttribute(
                                            'data-activity-description'
                                        );


                                    if (!id) {
                                        return;
                                    }


                                    /*
                                     * Tutup dropdown
                                     */

                                    const wrapper =
                                        btn.closest(
                                            '.activity-menu-wrapper'
                                        );


                                    if (wrapper) {

                                        wrapper.classList.remove(
                                            'menu-open'
                                        );

                                    }


                                    /*
                                     * Setup form edit
                                     */

                                    formMethod.value =
                                        'PUT';


                                    form.action =
                                        '{{ url('activities') }}' +
                                        '/' +
                                        id;


                                    modalTitle.textContent =
                                        'Edit Aktivitas';


                                    titleInput.value =
                                        title;


                                    descInput.value =
                                        description ?? '';


                                    titleInput.removeAttribute(
                                        'readonly'
                                    );

                                    descInput.removeAttribute(
                                        'readonly'
                                    );


                                    currentEditId = id;


                                    openModal();

                                    titleInput.focus();

                                }
                            );

                        }
                    );


                /* =====================================================
                   PIN / LEPAS PIN
                ====================================================== */

                document
                    .querySelectorAll('.activity-menu-pin')
                    .forEach(
                        function(btn) {

                            btn.addEventListener(
                                'click',
                                function(e) {

                                    e.preventDefault();

                                    e.stopPropagation();


                                    const id =
                                        btn.getAttribute(
                                            'data-activity-id'
                                        );


                                    if (!id) {
                                        return;
                                    }


                                    /*
                                     * Tutup dropdown
                                     */

                                    const wrapper =
                                        btn.closest(
                                            '.activity-menu-wrapper'
                                        );


                                    if (wrapper) {

                                        wrapper.classList.remove(
                                            'menu-open'
                                        );

                                    }


                                    /*
                                     * Form pin
                                     */

                                    const pinForm =
                                        document.createElement(
                                            'form'
                                        );


                                    pinForm.method =
                                        'POST';


                                    pinForm.action =
                                        '{{ url('activities') }}' +
                                        '/' +
                                        id +
                                        '/pin';


                                    /*
                                     * CSRF
                                     */

                                    const csrf =
                                        document.createElement(
                                            'input'
                                        );


                                    csrf.type =
                                        'hidden';

                                    csrf.name =
                                        '_token';

                                    csrf.value =
                                        document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute(
                                            'content'
                                        ) ??
                                        '';


                                    /*
                                     * PATCH
                                     */

                                    const method =
                                        document.createElement(
                                            'input'
                                        );


                                    method.type =
                                        'hidden';

                                    method.name =
                                        '_method';

                                    method.value =
                                        'PATCH';


                                    pinForm.appendChild(
                                        csrf
                                    );

                                    pinForm.appendChild(
                                        method
                                    );


                                    document.body.appendChild(
                                        pinForm
                                    );


                                    pinForm.submit();

                                }
                            );

                        }
                    );


                /* =====================================================
                   HAPUS AKTIVITAS
                ====================================================== */

                document
                    .querySelectorAll('.activity-menu-delete')
                    .forEach(
                        function(btn) {

                            btn.addEventListener(
                                'click',
                                function(e) {

                                    e.preventDefault();

                                    e.stopPropagation();


                                    const id =
                                        btn.getAttribute(
                                            'data-activity-id'
                                        );


                                    if (!id) {
                                        return;
                                    }


                                    /*
                                     * Tutup dropdown
                                     */

                                    const wrapper =
                                        btn.closest(
                                            '.activity-menu-wrapper'
                                        );


                                    if (wrapper) {

                                        wrapper.classList.remove(
                                            'menu-open'
                                        );

                                    }


                                    /*
                                     * Konfirmasi
                                     */

                                    if (
                                        !window.confirm(
                                            'Hapus aktivitas ini?'
                                        )
                                    ) {

                                        return;

                                    }


                                    /*
                                     * Form DELETE
                                     */

                                    const deleteForm =
                                        document.createElement(
                                            'form'
                                        );


                                    deleteForm.method =
                                        'POST';


                                    deleteForm.action =
                                        '{{ url('activities') }}' +
                                        '/' +
                                        id;


                                    /*
                                     * CSRF
                                     */

                                    const csrf =
                                        document.createElement(
                                            'input'
                                        );


                                    csrf.type =
                                        'hidden';

                                    csrf.name =
                                        '_token';

                                    csrf.value =
                                        document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute(
                                            'content'
                                        ) ??
                                        '';


                                    /*
                                     * DELETE
                                     */

                                    const method =
                                        document.createElement(
                                            'input'
                                        );


                                    method.type =
                                        'hidden';

                                    method.name =
                                        '_method';

                                    method.value =
                                        'DELETE';


                                    deleteForm.appendChild(
                                        csrf
                                    );

                                    deleteForm.appendChild(
                                        method
                                    );


                                    document.body.appendChild(
                                        deleteForm
                                    );


                                    deleteForm.submit();

                                }
                            );

                        }
                    );


                /* =====================================================
                   ESC = TUTUP MODAL + MENU
                ====================================================== */

                document.addEventListener(
                    'keydown',
                    function(e) {

                        if (e.key !== 'Escape') {
                            return;
                        }


                        closeModal();


                        menuWrappers.forEach(
                            function(wrapper) {

                                wrapper.classList.remove(
                                    'menu-open'
                                );

                            }
                        );

                    }
                );


            })();
        </script>
    @endpush

@endsection