@extends('layouts.app')

@section('title', 'Semua Aktivitas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
@endpush

@section('content')

    <section class="page activities-page">

        {{-- KEMBALI KE DASHBOARD --}}

        <div class="activities-dashboard-back">

            <a href="{{ route('dashboard') }}" class="activities-back">

                <span class="activities-back-icon" aria-hidden="true">←</span>

                <span>Kembali ke Dashboard</span>

            </a>

        </div>

        {{-- HERO --}}

        <div class="activities-hero">

            <div class="activities-hero-row">

                <div class="activities-hero-content">

                    <div class="activities-hero-label">

                        <span class="activities-hero-dot"></span>

                        Pusat Aktivitas Perpustakaan

                    </div>

                    <h1>Semua Aktivitas</h1>

                    <p>

                        Kelola seluruh aktivitas dan announcement

                        yang dibuat melalui dashboard perpustakaan.

                    </p>

                </div>

                <button type="button" class="activities-add-btn" id="btnAddActivity">

                    <span class="activities-add-icon">+</span>

                    Tambah Aktivitas

                </button>

            </div>

        </div>

        {{-- SEARCH + FILTER --}}

        <div class="activities-tools">

            <div class="activities-search">

                <span class="activities-search-icon" aria-hidden="true"></span>

                <input type="search" id="activitySearch" placeholder="Cari aktivitas..." autocomplete="off">

            </div>

            <div class="activities-filter-group">

                {{-- SEMUA JENIS --}}

                <div class="activities-filter-dropdown" id="typeFilterDropdown">

                    <button type="button" class="activities-filter-btn" id="activities-type-btn" aria-expanded="false"
                        aria-haspopup="true">
                        <span>Semua Jenis</span>

                        <span class="filter-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </span>
                    </button>

                    <div class="activities-filter-menu">

                        <div class="activities-filter-title">

                            Pilih Jenis

                        </div>

                        <button type="button" class="activities-filter-option active" data-type-filter="all">

                            Semua Jenis

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="book">

                            Buku Baru

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="borrowing">

                            Peminjaman

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="reservation">

                            Reservasi

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="return">

                            Pengembalian

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="member">

                            Anggota Baru

                        </button>

                        <button type="button" class="activities-filter-option" data-type-filter="manual">

                            Pengumuman

                        </button>

                    </div>

                </div>

                {{-- PILIH WAKTU --}}

                <div class="activities-filter-dropdown" id="timeFilterDropdown">

                    <button type="button" class="activities-filter-btn" id="activities-time-btn" aria-expanded="false"
                        aria-haspopup="true">
                        <span>Pilih Waktu</span>

                        <span class="filter-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </span>
                    </button>

                    <div class="activities-filter-menu">

                        <div class="activities-filter-title">

                            Pilih Waktu

                        </div>

                        <button type="button" class="activities-filter-option active" data-time-filter="all">

                            Semua Waktu

                        </button>

                        <button type="button" class="activities-filter-option" data-time-filter="today">

                            Hari Ini

                        </button>

                        <button type="button" class="activities-filter-option" data-time-filter="7days">

                            7 Hari Terakhir

                        </button>

                        <button type="button" class="activities-filter-option" data-time-filter="month">

                            Bulan Ini

                        </button>

                    </div>

                </div>

                {{-- DIPIN PALING KANAN --}}

                <button type="button" class="activities-pinned-btn" id="pinnedFilterButton">

                    Dipin ({{ $pinnedActivities }})

                </button>

            </div>

        </div>

        {{-- ACTIVITIES --}}

        <div id="activitiesResults">

            @php

                $groupedActivities = $activities->groupBy(function ($activity) {
                    if (empty($activity['created_at'])) {
                        return 'lainnya';
                    }

                    $date = $activity['created_at']->copy()->startOfDay();

                    $today = now()->startOfDay();

                    if ($date->equalTo($today)) {
                        return 'hari_ini';
                    }

                    if ($date->greaterThanOrEqualTo($today->copy()->subDays(6))) {
                        return 'tujuh_hari';
                    }

                    if ($date->month === $today->month && $date->year === $today->year) {
                        return 'bulan_ini';
                    }

                    return 'lainnya';
                });

                $groupTitles = [
                    'hari_ini' => 'HARI INI',

                    'tujuh_hari' => '7 HARI TERAKHIR',

                    'bulan_ini' => 'BULAN INI',

                    'lainnya' => 'LAINNYA',
                ];

            @endphp

            @forelse ($groupTitles as $groupKey => $groupTitle)

                @if ($groupedActivities->has($groupKey) && $groupedActivities[$groupKey]->count())
                    <section class="activities-day-group" data-group="{{ $groupKey }}">

                        <h2 class="activities-day-title">

                            {{ $groupTitle }}

                        </h2>

                        <div class="activities-day-card">

                            @foreach ($groupedActivities[$groupKey] as $activity)
                                <div class="activities-item" data-activity-item data-type="{{ $activity['type'] }}"
                                    data-pinned="{{ !empty($activity['pinned_at']) ? '1' : '0' }}"
                                    data-created-at="{{ !empty($activity['created_at']) ? $activity['created_at']->timestamp : 0 }}"
                                    data-search="{{ strtolower(($activity['title'] ?? '') . ' ' . ($activity['description'] ?? '')) }}">

                                    <div class="activities-icon">

                                        {{ $activity['icon'] }}

                                    </div>

                                    <div class="activities-info">

                                        <div class="activities-title-row">

                                            <strong>

                                                {{ $activity['title'] }}

                                                @if (!empty($activity['description']) && $activity['type'] !== 'manual')
                                                    <span class="activity-book">

                                                        — {{ $activity['description'] }}

                                                    </span>
                                                @endif

                                            </strong>

                                        </div>

                                        @if ($activity['type'] === 'manual')
                                            <p>

                                                {{ $activity['description'] ?: '-' }}

                                            </p>
                                        @elseif (!empty($activity['description']))
                                            <p>

                                                {{ $activity['description'] }}

                                            </p>
                                        @endif

                                        <div class="activities-meta">

                                            <span class="activities-meta-time">

                                                ◷

                                                {{ !empty($activity['created_at']) ? $activity['created_at']->locale('id')->diffForHumans() : '-' }}

                                            </span>

                                            @if (!empty($activity['pinned_at']))
                                                <span class="activities-pin">

                                                    📌 Dipin

                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    @if ($activity['type'] === 'manual' && !empty($activity['id']))
                                        <div class="activities-menu-wrapper">

                                            <button type="button" class="activities-menu-btn"
                                                aria-label="Opsi aktivitas">

                                                <span></span>

                                                <span></span>

                                                <span></span>

                                            </button>

                                            <div class="activities-menu">

                                                <button type="button" class="activities-menu-item"
                                                    onclick="openEditActivity(

                                                    {{ $activity['id'] }},

                                                    @js($activity['title']),

                                                    @js($activity['description'])

                                                )">

                                                    <span>✎</span>

                                                    Edit

                                                </button>

                                                <form method="POST"
                                                    action="{{ route('activities.pin', $activity['id']) }}">

                                                    @csrf

                                                    @method('PATCH')

                                                    <button type="submit" class="activities-menu-item">

                                                        <span>📌</span>

                                                        {{ !empty($activity['pinned_at']) ? 'Lepas Pin' : 'Pin' }}

                                                    </button>

                                                </form>

                                                <form method="POST"
                                                    action="{{ route('activities.destroy', $activity['id']) }}"
                                                    onsubmit="return confirm('Hapus aktivitas ini?')">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button type="submit" class="activities-menu-item danger">

                                                        <span>🗑</span>

                                                        Hapus

                                                    </button>

                                                </form>

                                            </div>

                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                    </section>
                @endif

            @endforeach

            <div class="activities-empty" id="activitiesNoResults" style="display:none;">

                Tidak ada aktivitas yang sesuai dengan pencarian atau filter.

            </div>

        </div>

        {{-- PAGINATION --}}

        @if ($activities->total() > 0)
            <div class="activities-pagination">

                <div class="activities-pagination-nav">

                    @if ($activities->onFirstPage())
                        <span class="activities-page-btn disabled">
                            « Prev
                        </span>
                    @else
                        <a href="{{ $activities->previousPageUrl() }}" class="activities-page-btn">
                            « Prev
                        </a>
                    @endif

                    @foreach ($activities->getUrlRange(max(1, $activities->currentPage() - 2), min($activities->lastPage(), $activities->currentPage() + 2)) as $page => $url)
                        @if ($page == $activities->currentPage())
                            <span class="activities-page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="activities-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($activities->hasMorePages())
                        <a href="{{ $activities->nextPageUrl() }}" class="activities-page-btn">
                            Next »
                        </a>
                    @else
                        <span class="activities-page-btn disabled">
                            Next »
                        </span>
                    @endif

                </div>

                <div class="activities-pagination-info">
                    Menampilkan
                    <strong>{{ $activities->firstItem() }}</strong>
                    -
                    <strong>{{ $activities->lastItem() }}</strong>
                    dari
                    <strong>{{ $activities->total() }}</strong>
                    aktivitas
                </div>

            </div>
        @endif

    </section>



    {{-- =====================================================

     TAMBAH / EDIT AKTIVITAS MODAL

===================================================== --}}

    <div class="activity-modal" id="activityModal" aria-hidden="true">

        <div class="activity-modal-overlay" id="activityModalOverlay"></div>

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
                        placeholder="Contoh: Perpustakaan Tutup" maxlength="255" required>

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

                /* =====================================================

                   MODAL TAMBAH

                ===================================================== */

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



                if (btnAdd) {

                    btnAdd.addEventListener(

                        'click',

                        function() {

                            formMethod.value = 'POST';

                            form.action =

                                '{{ route('activities.store') }}';

                            modalTitle.textContent =

                                'Tambah Aktivitas';

                            titleInput.value = '';

                            descInput.value = '';

                            openModal();

                            setTimeout(function() {

                                titleInput.focus();

                            }, 50);

                        }

                    );

                }



                if (btnClose) {

                    btnClose.addEventListener(

                        'click',

                        closeModal

                    );

                }



                if (btnCancel) {

                    btnCancel.addEventListener(

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

                   EDIT MODAL

                ===================================================== */

                window.openEditActivity = function(

                    id,

                    title,

                    description

                ) {

                    formMethod.value = 'PUT';

                    form.action =

                        '{{ url('/activities') }}/' + id;

                    modalTitle.textContent =

                        'Edit Aktivitas';

                    titleInput.value =

                        title || '';

                    descInput.value =

                        description || '';

                    openModal();

                    setTimeout(function() {

                        titleInput.focus();

                    }, 50);

                };



                /* =====================================================

                   ESC

                ===================================================== */

                document.addEventListener(

                    'keydown',

                    function(event) {

                        if (event.key === 'Escape') {

                            closeModal();

                            document

                                .querySelectorAll(

                                    '.activities-menu-wrapper'

                                )

                                .forEach(function(wrapper) {

                                    wrapper.classList.remove(

                                        'menu-open'

                                    );

                                });

                        }

                    }

                );



                /* =====================================================

                   THREE DOT MENU

                ===================================================== */

                document

                    .querySelectorAll('.activities-menu-wrapper')

                    .forEach(function(wrapper) {

                        const button =

                            wrapper.querySelector(

                                '.activities-menu-btn'

                            );

                        if (!button) {

                            return;

                        }

                        button.addEventListener(

                            'click',

                            function(event) {

                                event.preventDefault();

                                event.stopPropagation();

                                document

                                    .querySelectorAll(

                                        '.activities-menu-wrapper'

                                    )

                                    .forEach(function(other) {

                                        if (other !== wrapper) {

                                            other.classList.remove(

                                                'menu-open'

                                            );

                                        }

                                    });

                                wrapper.classList.toggle(

                                    'menu-open'

                                );

                            }

                        );

                    });



                document.addEventListener(

                    'click',

                    function() {

                        document

                            .querySelectorAll(

                                '.activities-menu-wrapper'

                            )

                            .forEach(function(wrapper) {

                                wrapper.classList.remove(

                                    'menu-open'

                                );

                            });

                    }

                );



                document

                    .querySelectorAll('.activities-menu')

                    .forEach(function(menu) {

                        menu.addEventListener(

                            'click',

                            function(event) {

                                event.stopPropagation();

                            }

                        );

                    });



                /* =====================================================

                   SEARCH + FILTER

                ===================================================== */

                const searchInput =

                    document.getElementById('activitySearch');

                const typeDropdown =

                    document.getElementById('typeFilterDropdown');

                const timeDropdown =

                    document.getElementById('timeFilterDropdown');

                const typeButton =
                    document.getElementById('activities-type-btn');

                const timeButton =
                    document.getElementById('activities-time-btn');

                const pinnedButton =

                    document.getElementById('pinnedFilterButton');

                const noResults =

                    document.getElementById('activitiesNoResults');

                let selectedType = 'all';

                let selectedTime = 'all';

                let pinnedOnly = false;



                function toggleDropdown(dropdown) {

                    const isOpen =

                        dropdown.classList.contains('open');

                    document

                        .querySelectorAll(

                            '.activities-filter-dropdown'

                        )

                        .forEach(function(item) {

                            item.classList.remove('open');

                        });

                    if (!isOpen) {

                        dropdown.classList.add('open');

                    }

                }



                if (typeButton) {

                    typeButton.addEventListener(

                        'click',

                        function(event) {

                            event.stopPropagation();

                            toggleDropdown(

                                typeDropdown

                            );

                        }

                    );

                }



                if (timeButton) {

                    timeButton.addEventListener(

                        'click',

                        function(event) {

                            event.stopPropagation();

                            toggleDropdown(

                                timeDropdown

                            );

                        }

                    );

                }



                document.addEventListener(

                    'click',

                    function() {

                        document

                            .querySelectorAll(

                                '.activities-filter-dropdown'

                            )

                            .forEach(function(dropdown) {

                                dropdown.classList.remove(

                                    'open'

                                );

                            });

                    }

                );



                function getStartOfToday() {

                    const now =

                        new Date();

                    return new Date(

                        now.getFullYear(),

                        now.getMonth(),

                        now.getDate()

                    );

                }



                function getItemDate(item) {

                    const timestamp =

                        Number(

                            item.dataset.createdAt

                        );

                    if (!timestamp) {

                        return null;

                    }

                    return new Date(

                        timestamp * 1000

                    );

                }



                function matchesTime(

                    itemDate

                ) {

                    if (

                        selectedTime === 'all' ||

                        !itemDate

                    ) {

                        return true;

                    }

                    const today =

                        getStartOfToday();

                    if (selectedTime === 'today') {

                        return (

                            itemDate.getFullYear() === today.getFullYear() &&

                            itemDate.getMonth() === today.getMonth() &&

                            itemDate.getDate() === today.getDate()

                        );

                    }



                    if (selectedTime === '7days') {

                        const start =

                            new Date(today);

                        start.setDate(

                            start.getDate() - 6

                        );

                        return (

                            itemDate >= start &&

                            itemDate < new Date(today.getTime() + 86400000)

                        );

                    }



                    if (selectedTime === 'month') {

                        return (

                            itemDate.getFullYear() === today.getFullYear() &&

                            itemDate.getMonth() === today.getMonth()

                        );

                    }

                    return true;

                }



                function applyFilters() {

                    const keyword =

                        (

                            searchInput

                            ?
                            searchInput.value

                            :
                            ''

                        )

                        .trim()

                        .toLowerCase();

                    let visibleCount = 0;

                    document

                        .querySelectorAll(

                            '[data-activity-item]'

                        )

                        .forEach(function(item) {

                            const text =

                                item.dataset.search || '';

                            const type =

                                item.dataset.type || '';

                            const pinned =

                                item.dataset.pinned === '1';

                            const itemDate =

                                getItemDate(item);

                            const matchesSearch =

                                !keyword ||

                                text.includes(keyword);

                            const matchesType =

                                selectedType === 'all' ||

                                type === selectedType;

                            const matchesPinned =

                                !pinnedOnly ||

                                pinned;

                            const matchesDate =

                                matchesTime(itemDate);

                            const visible =

                                matchesSearch &&

                                matchesType &&

                                matchesPinned &&

                                matchesDate;

                            item.style.display =

                                visible

                                ?
                                ''

                                :
                                'none';

                            if (visible) {

                                visibleCount++;

                            }

                        });



                    document

                        .querySelectorAll(

                            '.activities-day-group'

                        )

                        .forEach(function(group) {

                            const visibleItems =

                                group.querySelectorAll(

                                    '[data-activity-item]:not([style*="display: none"])'

                                );

                            group.style.display =

                                visibleItems.length

                                ?
                                ''

                                :
                                'none';

                        });



                    if (noResults) {

                        noResults.style.display =

                            visibleCount === 0

                            ?
                            'block'

                            :
                            'none';

                    }

                }



                document

                    .querySelectorAll(

                        '[data-type-filter]'

                    )

                    .forEach(function(option) {

                        option.addEventListener(

                            'click',

                            function(event) {

                                event.stopPropagation();

                                selectedType =

                                    option.dataset.typeFilter;

                                document

                                    .querySelectorAll(

                                        '[data-type-filter]'

                                    )

                                    .forEach(function(item) {

                                        item.classList.remove(

                                            'active'

                                        );

                                    });

                                option.classList.add(

                                    'active'

                                );

                                typeButton

                                    .querySelector('span')

                                    .textContent =

                                    option.textContent.trim();

                                typeDropdown.classList.remove(

                                    'open'

                                );

                                applyFilters();

                            }

                        );

                    });



                document

                    .querySelectorAll(

                        '[data-time-filter]'

                    )

                    .forEach(function(option) {

                        option.addEventListener(

                            'click',

                            function(event) {

                                event.stopPropagation();

                                selectedTime =

                                    option.dataset.timeFilter;

                                document

                                    .querySelectorAll(

                                        '[data-time-filter]'

                                    )

                                    .forEach(function(item) {

                                        item.classList.remove(

                                            'active'

                                        );

                                    });

                                option.classList.add(

                                    'active'

                                );

                                timeButton

                                    .querySelector('span')

                                    .textContent =

                                    option.textContent.trim();

                                timeDropdown.classList.remove(

                                    'open'

                                );

                                applyFilters();

                            }

                        );

                    });



                if (pinnedButton) {

                    pinnedButton.addEventListener(

                        'click',

                        function() {

                            pinnedOnly =

                                !pinnedOnly;

                            pinnedButton.classList.toggle(

                                'active',

                                pinnedOnly

                            );

                            applyFilters();

                        }

                    );

                }



                if (searchInput) {

                    searchInput.addEventListener(

                        'input',

                        applyFilters

                    );

                }



            })();
        </script>
    @endpush

@endsection
