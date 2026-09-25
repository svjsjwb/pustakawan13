@extends('layouts.app')

@section('title', 'Semua Aktivitas')

@push('styles')

<style>

/* =====================================================
   PAGE
===================================================== */

.activities-page {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}


/* =====================================================
   HERO
===================================================== */

.activities-hero {
    position: relative;
    width: 100%;
    margin-bottom: 20px;
    padding: 28px 32px;
    box-sizing: border-box;
    overflow: hidden;

    background:
        linear-gradient(
            110deg,
            #eef8f8 0%,
            #ffffff 68%
        );

    border: 1px solid #d5e7e7;
    border-radius: 16px;
}

.activities-hero::after {
    content: "";
    position: absolute;

    width: 180px;
    height: 180px;

    right: -70px;
    top: -90px;

    border-radius: 50%;

    background:
        rgba(40, 123, 123, 0.06);

    pointer-events: none;
}

.activities-hero-content {
    position: relative;
    z-index: 1;
}

.activities-hero-label {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 8px;

    color: #287c7c;
    font-size: 11px;
    font-weight: 700;
}

.activities-hero-dot {
    width: 8px;
    height: 8px;

    flex: 0 0 8px;

    border-radius: 50%;
    background: #287c7c;
}

.activities-hero h1 {
    margin: 0 0 6px;

    color: #071b2c;

    font-size: 27px;
    line-height: 1.2;
    font-weight: 800;
}

.activities-hero p {
    margin: 0;

    color: #718080;

    font-size: 11px;
}


/* =====================================================
   MAIN PANEL
===================================================== */

.activities-panel {
    background: #ffffff;

    border: 1px solid #e8eeee;
    border-radius: 14px;

    box-shadow:
        0 3px 13px rgba(22, 63, 63, 0.055);

    overflow: hidden;
}


/* =====================================================
   FILTER
===================================================== */

.activities-filter {
    display: flex;
    align-items: center;
    gap: 6px;

    padding: 16px 20px 12px;

    border-bottom:
        1px solid #edf1f1;
}

.activities-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 7px 13px;

    border: 1px solid #dce8e7;
    border-radius: 8px;

    background: #ffffff;
    color: #667474;

    font-size: 10px;
    font-weight: 700;

    text-decoration: none;

    cursor: pointer;

    transition:
        background 0.15s ease,
        color 0.15s ease,
        border-color 0.15s ease;
}

.activities-filter-btn:hover {
    background: #f1f8f7;
    color: #287f80;
    border-color: #c9e1df;
}

.activities-filter-btn.active {
    background: #287b7b;
    border-color: #287b7b;
    color: #ffffff;
}


/* =====================================================
   LIST
===================================================== */

.activities-list {
    padding: 10px 20px 20px;
}


/* =====================================================
   ACTIVITY ITEM
===================================================== */

.activities-item {
    position: relative;

    display: flex;
    align-items: flex-start;

    gap: 13px;

    padding: 15px 4px;

    border-bottom:
        1px solid #edf1f1;

    /*
     * Tidak menggunakan translateX.
     * Ini sengaja supaya dropdown titik tiga
     * tidak ikut bergeser / geter saat cursor
     * masuk ke menu.
     */
    transition:
        background 0.15s ease;
}

.activities-item:last-child {
    border-bottom: none;
}

.activities-item:hover {
    background: #fbfdfd;
}


/* =====================================================
   ICON
===================================================== */

.activities-icon {
    width: 38px;
    height: 38px;

    flex: 0 0 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #e3f5ef;
    color: #20a66d;

    font-size: 15px;
}


/* =====================================================
   CONTENT
===================================================== */

.activities-info {
    min-width: 0;

    flex: 1;

    padding-top: 1px;
    padding-right: 42px;
}

.activities-title-row {
    display: flex;
    align-items: center;

    gap: 7px;

    min-width: 0;
}

.activities-info strong {
    display: block;

    min-width: 0;

    overflow: hidden;

    color: #263636;

    font-size: 12px;
    font-weight: 750;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.activities-info p {
    margin: 4px 0 7px;

    color: #758282;

    font-size: 10px;
    line-height: 1.5;
}


/* =====================================================
   META
===================================================== */

.activities-meta {
    display: flex;
    align-items: center;

    gap: 8px;

    color: #8a9696;

    font-size: 9px;
}

.activities-meta-time {
    display: inline-flex;
    align-items: center;

    gap: 4px;
}


/* =====================================================
   PIN BADGE
===================================================== */

.activities-pin {
    display: inline-flex;
    align-items: center;

    padding: 3px 8px;

    border-radius: 999px;

    background: #fff4d6;

    border: 1px solid #ead9a5;

    color: #8a6a12;

    font-size: 8px;
    font-weight: 700;

    white-space: nowrap;
}


/* =====================================================
   THREE DOT MENU
===================================================== */

.activities-menu-wrapper {
    position: absolute;

    top: 15px;
    right: 4px;

    z-index: 50;

    /*
     * Tidak boleh berubah posisi saat hover.
     */
    transform: none !important;
}

.activities-menu-btn {
    width: 29px;
    height: 29px;

    padding: 0;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 3px;

    border: 1px solid #dcebea;
    border-radius: 50%;

    background: #ffffff;
    color: #287f80;

    cursor: pointer;

    opacity: 0;
    visibility: hidden;

    transform: none !important;

    transition:
        opacity 0.15s ease,
        visibility 0.15s ease,
        background 0.15s ease,
        border-color 0.15s ease;
}

.activities-menu-btn span {
    width: 3px;
    height: 3px;

    flex: 0 0 3px;

    border-radius: 50%;

    background: currentColor;
}

.activities-item:hover .activities-menu-btn,
.activities-menu-wrapper.menu-open .activities-menu-btn {
    opacity: 1;
    visibility: visible;
}

.activities-menu-btn:hover {
    background: #eaf5f4;
    border-color: #c9e1df;
}


/* =====================================================
   DROPDOWN
===================================================== */

.activities-menu {
    position: absolute;

    top: 35px;
    right: 0;

    width: 145px;

    padding: 6px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #e2e8f0;
    border-radius: 10px;

    box-shadow:
        0 12px 30px rgba(15, 23, 42, 0.12);

    opacity: 0;
    visibility: hidden;

    transform:
        translateY(-4px) scale(0.97);

    transform-origin: top right;

    transition:
        opacity 0.15s ease,
        visibility 0.15s ease,
        transform 0.15s ease;
}

.activities-menu-wrapper.menu-open .activities-menu {
    opacity: 1;
    visibility: visible;

    transform:
        translateY(0) scale(1);
}


/* =====================================================
   DROPDOWN ITEM
===================================================== */

.activities-menu-item {
    width: 100%;

    display: flex;
    align-items: center;

    gap: 9px;

    padding: 9px 10px;

    box-sizing: border-box;

    border: none;
    border-radius: 7px;

    background: transparent;

    color: #334155;

    font-family: inherit;

    font-size: 11px;
    font-weight: 600;

    text-align: left;

    cursor: pointer;

    /*
     * Kunci posisi item supaya tidak ikut
     * bergeser ketika di-hover.
     */
    position: relative;
    left: 0;
    top: 0;

    transform: none !important;

    transition:
        background 0.15s ease,
        color 0.15s ease;
}

.activities-menu-item:hover {
    background: #f1f8f7;
    color: #287f80;

    position: relative;
    left: 0;
    top: 0;

    transform: none !important;
}

.activities-menu-item:focus,
.activities-menu-item:active {
    transform: none !important;
    left: 0;
    top: 0;
}


/* =====================================================
   FORM DALAM DROPDOWN
===================================================== */

.activities-menu form {
    width: 100%;

    margin: 0;
    padding: 0;
}

.activities-menu form button {
    width: 100%;

    margin: 0;
}

.activities-menu form button:hover,
.activities-menu form button:focus,
.activities-menu form button:active {
    transform: none !important;
}


/* =====================================================
   DELETE
===================================================== */

.activities-menu-item.danger {
    color: #dc2626;
}

.activities-menu-item.danger:hover {
    background: #fef2f2;
    color: #b91c1c;

    transform: none !important;
}


/* =====================================================
   EMPTY
===================================================== */

.activities-empty {
    padding: 45px 20px;

    color: #879393;

    font-size: 11px;

    text-align: center;
}


/* =====================================================
   PAGINATION BOX
   Dibuat seperti bagian bawah Catalog
===================================================== */

.activities-footer {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        auto
        minmax(0, 1fr);

    align-items: center;

    gap: 16px;

    margin-top: 18px;

    padding: 14px 24px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 14px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.04);
}


/* =====================================================
   PER PAGE
===================================================== */

.activities-pagination-per-page {
    display: flex;
    align-items: center;

    gap: 8px;

    justify-content: flex-start;

    color: #64748b;

    font-size: 12px;
    font-weight: 500;
}

.activities-pagination-per-page label {
    color: #64748b;

    font-size: 12px;
    font-weight: 500;

    white-space: nowrap;
}

.activities-pagination-per-page select {
    height: 32px;

    padding: 0 10px;

    border: 1px solid #e2e8f0;
    border-radius: 8px;

    background: #ffffff;
    color: #12403e;

    font-family: inherit;

    font-size: 12px;
    font-weight: 700;

    outline: none;

    cursor: pointer;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.activities-pagination-per-page select:hover,
.activities-pagination-per-page select:focus {
    border-color: #287c7c;

    box-shadow:
        0 0 0 3px rgba(40, 124, 124, 0.08);
}


/* =====================================================
   PAGINATION INFO
===================================================== */

.activities-pagination-info {
    margin: 0;

    color: #64748b;

    font-size: 12px;
    font-weight: 500;

    text-align: center;

    white-space: nowrap;
}

.activities-pagination-info strong {
    color: #12403e;

    font-weight: 700;
}


/* =====================================================
   PAGINATION NAV
===================================================== */

.activities-pagination-nav {
    display: flex;
    align-items: center;

    justify-content: flex-end;

    gap: 6px;
}

.activities-page-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 32px;
    height: 32px;

    padding: 0 10px;

    box-sizing: border-box;

    border: 1px solid #e2e8f0;
    border-radius: 8px;

    background: #ffffff;

    color: #1e293b;

    font-size: 11px;
    font-weight: 600;

    text-decoration: none;

    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.03);

    transition:
        border-color 0.2s ease,
        color 0.2s ease,
        background 0.2s ease,
        box-shadow 0.2s ease;
}

.activities-page-btn:hover {
    border-color: #287c7c;

    color: #287c7c;

    background: #e6f0ef;
}

.activities-page-btn.active {
    background: #1a5c5a;

    border-color: #1a5c5a;

    color: #ffffff;

    font-weight: 700;

    box-shadow:
        0 2px 6px rgba(26, 92, 90, 0.2);
}

.activities-page-btn.disabled {
    background: #ffffff;

    border-color: #e2e8f0;

    color: #cbd5e1;

    cursor: not-allowed;

    box-shadow: none;
}


/* =====================================================
   KEMBALI KE DASHBOARD
===================================================== */

.activities-footer-back {
    grid-column: 1 / -1;

    display: flex;

    justify-content: flex-start;

    padding-top: 2px;
}

.activities-back {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    padding: 7px 13px;

    border: 1px solid #287b7b;
    border-radius: 7px;

    background: #ffffff;

    color: #287b7b;

    font-size: 10px;
    font-weight: 700;

    text-decoration: none;

    white-space: nowrap;

    transition:
        background 0.15s ease,
        border-color 0.15s ease;
}

.activities-back:hover {
    background: #f1f8f7;
    border-color: #287b7b;
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 850px) {

    .activities-footer {
        grid-template-columns: 1fr;

        justify-items: center;

        gap: 12px;
    }

    .activities-pagination-per-page {
        justify-content: center;
    }

    .activities-pagination-info {
        order: 2;
    }

    .activities-pagination-nav {
        order: 3;

        justify-content: center;

        flex-wrap: wrap;
    }

    .activities-footer-back {
        order: 4;

        justify-content: center;
    }
}


@media (max-width: 700px) {

    .activities-page {
        max-width: 100%;
    }

    .activities-hero {
        padding: 25px;
    }

    .activities-list {
        padding-left: 13px;
        padding-right: 13px;
    }

    .activities-item {
        padding-right: 0;
    }

    .activities-menu-btn {
        opacity: 1;
        visibility: visible;
    }

    .activities-info {
        padding-right: 38px;
    }
}


/* =====================================================
   EDIT ACTIVITY MODAL
===================================================== */

.activity-modal {
    position: fixed;

    inset: 0;

    z-index: 9999;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 20px;

    box-sizing: border-box;

    opacity: 0;
    visibility: hidden;

    pointer-events: none;

    transition:
        opacity 0.2s ease,
        visibility 0.2s ease;
}

.activity-modal.open {
    opacity: 1;
    visibility: visible;

    pointer-events: auto;
}


/* =====================================================
   OVERLAY
===================================================== */

.activity-modal-overlay {
    position: absolute;

    inset: 0;

    background:
        rgba(7, 27, 44, 0.45);

    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}


/* =====================================================
   MODAL BOX
===================================================== */

.activity-modal-box {
    position: relative;

    z-index: 2;

    width: min(480px, 100%);

    padding: 22px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #e2eaea;

    border-radius: 15px;

    box-shadow:
        0 20px 50px rgba(15, 23, 42, 0.18);

    transform:
        translateY(10px) scale(0.98);

    transition:
        transform 0.2s ease;
}

.activity-modal.open .activity-modal-box {
    transform:
        translateY(0) scale(1);
}


/* =====================================================
   MODAL HEADER
===================================================== */

.activity-modal-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 20px;
}

.activity-modal-header h3 {
    margin: 0;

    color: #071b2c;

    font-size: 17px;
    font-weight: 800;
}

.activity-modal-close {
    width: 30px;
    height: 30px;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid #e2e8f0;

    border-radius: 50%;

    background: #ffffff;
    color: #64748b;

    font-size: 18px;
    line-height: 1;

    cursor: pointer;

    transition:
        background 0.15s ease,
        color 0.15s ease,
        border-color 0.15s ease;
}

.activity-modal-close:hover {
    background: #fef2f2;

    border-color: #fecaca;

    color: #dc2626;
}


/* =====================================================
   FORM
===================================================== */

.activity-form-field {
    margin-bottom: 15px;
}

.activity-form-field label {
    display: block;

    margin-bottom: 6px;

    color: #334155;

    font-size: 11px;
    font-weight: 700;
}

.activity-form-input {
    width: 100%;

    padding: 10px 11px;

    box-sizing: border-box;

    border: 1px solid #dce5e5;

    border-radius: 8px;

    background: #ffffff;
    color: #263636;

    font-family: inherit;

    font-size: 11px;

    outline: none;

    transition:
        border-color 0.15s ease,
        box-shadow 0.15s ease;
}

.activity-form-input:focus {
    border-color: #287f80;

    box-shadow:
        0 0 0 3px rgba(40, 127, 128, 0.10);
}

textarea.activity-form-input {
    min-height: 105px;

    resize: vertical;

    line-height: 1.5;
}


/* =====================================================
   MODAL ACTIONS
===================================================== */

.activity-modal-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    gap: 7px;

    margin-top: 20px;
}

.activity-btn-cancel,
.activity-btn-simpan {
    padding: 8px 15px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 10px;
    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.15s ease,
        border-color 0.15s ease,
        transform 0.15s ease;
}

.activity-btn-cancel {
    border: 1px solid #dce5e5;

    background: #ffffff;

    color: #647474;
}

.activity-btn-cancel:hover {
    background: #f7f9f9;
}

.activity-btn-simpan {
    border: 1px solid #287f80;

    background: #287f80;

    color: #ffffff;

    box-shadow:
        0 3px 8px rgba(40, 127, 128, 0.18);
}

.activity-btn-simpan:hover {
    background: #236f70;

    border-color: #236f70;

    transform: translateY(-1px);
}


/* =====================================================
   MODAL MOBILE
===================================================== */

@media (max-width: 520px) {

    .activity-modal {
        padding: 14px;
    }

    .activity-modal-box {
        padding: 18px;

        border-radius: 13px;
    }

    .activity-modal-header h3 {
        font-size: 15px;
    }
}

</style>

@endpush


@section('content')

<section class="page activities-page">


    {{-- =================================================
         HERO
    ================================================== --}}

    <div class="activities-hero">

        <div class="activities-hero-content">

            <div class="activities-hero-label">

                <span class="activities-hero-dot"></span>

                Pusat Aktivitas Perpustakaan

            </div>


            <h1>
                Semua Aktivitas
            </h1>


            <p>
                Kelola seluruh aktivitas dan announcement
                yang dibuat melalui dashboard perpustakaan.
            </p>

        </div>

    </div>



    {{-- =================================================
         MAIN PANEL
    ================================================== --}}

    <div class="activities-panel">


        {{-- =================================================
             FILTER
        ================================================== --}}

        <div class="activities-filter">

            <a
                href="{{ route('activities.index', [
                    'filter' => 'all',
                    'per_page' => $perPage,
                ]) }}"
                class="activities-filter-btn {{ $filter === 'all' ? 'active' : '' }}"
            >
                Semua ({{ $totalActivities }})
            </a>


            <a
                href="{{ route('activities.index', [
                    'filter' => 'pinned',
                    'per_page' => $perPage,
                ]) }}"
                class="activities-filter-btn {{ $filter === 'pinned' ? 'active' : '' }}"
            >
                Dipin ({{ $pinnedActivities }})
            </a>

        </div>



        {{-- =================================================
             LIST
        ================================================== --}}

        <div class="activities-list">

            @forelse($activities as $activity)

                <div
                    class="activities-item"
                    data-type="{{ $activity['type'] }}"
                    data-pinned="{{ !empty($activity['pinned_at']) ? '1' : '0' }}"
                >


                    {{-- =================================================
                         ICON
                    ================================================== --}}

                    <div class="activities-icon">

                        {{ $activity['icon'] }}

                    </div>



                    {{-- =================================================
                         CONTENT
                    ================================================== --}}

                    <div class="activities-info">

                        <div class="activities-title-row">

                            <strong>
                                {{ $activity['title'] }}
                            </strong>

                        </div>


                        <p>
                            {{ $activity['description'] ?: '-' }}
                        </p>


                        <div class="activities-meta">

                            <span class="activities-meta-time">

                                ◷

                                {{ $activity['created_at']
                                    ? $activity['created_at']->locale('id')->diffForHumans()
                                    : '-'
                                }}

                            </span>


                            {{-- PIN BADGE --}}

                            @if (!empty($activity['pinned_at']))

                                <span class="activities-pin">

                                    📌 Dipin

                                </span>

                            @endif

                        </div>

                    </div>



                    {{-- =================================================
                         MENU
                         HANYA UNTUK ANNOUNCEMENT MANUAL
                    ================================================== --}}

                    @if ($activity['type'] === 'manual')

                        <div class="activities-menu-wrapper">


                            {{-- THREE DOT --}}

                            <button
                                type="button"
                                class="activities-menu-btn"
                                aria-label="Opsi aktivitas"
                            >

                                <span></span>
                                <span></span>
                                <span></span>

                            </button>



                            {{-- DROPDOWN --}}

                            <div class="activities-menu">


                                {{-- EDIT --}}

                                <button
                                    type="button"
                                    class="activities-menu-item"
                                    onclick="openEditActivity(
                                        {{ $activity['id'] }},
                                        @js($activity['title']),
                                        @js($activity['description'])
                                    )"
                                >

                                    <span>
                                        ✎
                                    </span>

                                    Edit

                                </button>



                                {{-- PIN / LEPAS PIN --}}

                                <form
                                    method="POST"
                                    action="{{ route('activities.pin', $activity['id']) }}"
                                >

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="activities-menu-item"
                                    >

                                        <span>
                                            📌
                                        </span>

                                        {{ !empty($activity['pinned_at'])
                                            ? 'Lepas Pin'
                                            : 'Pin'
                                        }}

                                    </button>

                                </form>



                                {{-- HAPUS --}}

                                <form
                                    method="POST"
                                    action="{{ route('activities.destroy', $activity['id']) }}"
                                    onsubmit="return confirm('Hapus aktivitas ini?')"
                                >

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="activities-menu-item danger"
                                    >

                                        <span>
                                            🗑
                                        </span>

                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </div>

                    @endif

                </div>

            @empty

                <div class="activities-empty">

                    Belum ada aktivitas.

                </div>

            @endforelse

        </div>

    </div>



    {{-- =================================================
         PAGINATION
         TERPISAH SEPERTI CATALOG
    ================================================== --}}

    <div class="activities-footer">


        {{-- =================================================
             KIRI
        ================================================== --}}

        <div class="activities-pagination-per-page">

            <label for="activitiesPerPage">
                Tampilkan per halaman:
            </label>


            <select
                id="activitiesPerPage"
                onchange="changeActivitiesPerPage(this.value)"
            >

                <option
                    value="10"
                    {{ $perPage == 10 ? 'selected' : '' }}
                >
                    10
                </option>


                <option
                    value="25"
                    {{ $perPage == 25 ? 'selected' : '' }}
                >
                    25
                </option>


                <option
                    value="50"
                    {{ $perPage == 50 ? 'selected' : '' }}
                >
                    50
                </option>


                <option
                    value="100"
                    {{ $perPage == 100 ? 'selected' : '' }}
                >
                    100
                </option>

            </select>

        </div>



        {{-- =================================================
             TENGAH
        ================================================== --}}

        <div class="activities-pagination-info">

            @if ($activities->total() > 0)

                Menampilkan

                <strong>
                    {{ $activities->firstItem() }}
                </strong>

                -

                <strong>
                    {{ $activities->lastItem() }}
                </strong>

                dari

                <strong>
                    {{ $activities->total() }}
                </strong>

                aktivitas

            @else

                Tidak ada aktivitas

            @endif

        </div>



        {{-- =================================================
             KANAN
        ================================================== --}}

        <div class="activities-pagination-nav">


            {{-- PREV --}}

            @if ($activities->onFirstPage())

                <span class="activities-page-btn disabled">
                    « Prev
                </span>

            @else

                <a
                    href="{{ $activities->previousPageUrl() }}"
                    class="activities-page-btn"
                >
                    « Prev
                </a>

            @endif



            {{-- NOMOR HALAMAN --}}

            @if ($activities->lastPage() > 0)

                @foreach (
                    $activities->getUrlRange(
                        max(1, $activities->currentPage() - 2),
                        min(
                            $activities->lastPage(),
                            $activities->currentPage() + 2
                        )
                    ) as $page => $url
                )

                    @if ($page == $activities->currentPage())

                        <span class="activities-page-btn active">
                            {{ $page }}
                        </span>

                    @else

                        <a
                            href="{{ $url }}"
                            class="activities-page-btn"
                        >
                            {{ $page }}
                        </a>

                    @endif

                @endforeach

            @endif



            {{-- NEXT --}}

            @if ($activities->hasMorePages())

                <a
                    href="{{ $activities->nextPageUrl() }}"
                    class="activities-page-btn"
                >
                    Next »
                </a>

            @else

                <span class="activities-page-btn disabled">
                    Next »
                </span>

            @endif

        </div>



        {{-- =================================================
             KEMBALI
        ================================================== --}}

        <div class="activities-footer-back">

            <a
                href="{{ route('dashboard') }}"
                class="activities-back"
            >

                ← Kembali ke Dashboard

            </a>

        </div>

    </div>

</section>



{{-- =====================================================
     MODAL EDIT
====================================================== --}}

<div
    class="activity-modal"
    id="activityEditModal"
    aria-hidden="true"
>


    <div
        class="activity-modal-overlay"
        onclick="closeEditActivity()"
    ></div>


    <div class="activity-modal-box">


        {{-- HEADER --}}

        <div class="activity-modal-header">

            <h3>
                Edit Aktivitas
            </h3>


            <button
                type="button"
                class="activity-modal-close"
                onclick="closeEditActivity()"
            >
                ×
            </button>

        </div>



        {{-- FORM --}}

        <form
            id="activityEditForm"
            method="POST"
        >

            @csrf

            @method('PUT')


            <div class="activity-form-field">

                <label for="activityEditTitle">
                    Judul Aktivitas
                </label>


                <input
                    type="text"
                    name="title"
                    id="activityEditTitle"
                    class="activity-form-input"
                    maxlength="255"
                    required
                >

            </div>



            <div class="activity-form-field">

                <label for="activityEditDescription">
                    Deskripsi Aktivitas
                </label>


                <textarea
                    name="description"
                    id="activityEditDescription"
                    class="activity-form-input"
                    rows="4"
                ></textarea>

            </div>



            <div class="activity-modal-actions">

                <button
                    type="button"
                    class="activity-btn-cancel"
                    onclick="closeEditActivity()"
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="activity-btn-simpan"
                >
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>



@push('scripts')

<script>

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
            function(e) {

                e.preventDefault();

                e.stopPropagation();


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



/* =====================================================
   CLOSE MENU
===================================================== */

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



/* =====================================================
   PREVENT CLICK INSIDE MENU FROM CLOSING IT
===================================================== */

document
    .querySelectorAll('.activities-menu')
    .forEach(function(menu) {

        menu.addEventListener(
            'click',
            function(e) {

                e.stopPropagation();

            }
        );

    });



/* =====================================================
   CHANGE PER PAGE
===================================================== */

function changeActivitiesPerPage(value) {

    const url =
        new URL(
            window.location.href
        );


    url.searchParams.set(
        'per_page',
        value
    );


    url.searchParams.set(
        'page',
        '1'
    );


    window.location.href =
        url.toString();

}



/* =====================================================
   EDIT MODAL
===================================================== */

function openEditActivity(
    id,
    title,
    description
) {

    const modal =
        document.getElementById(
            'activityEditModal'
        );


    const form =
        document.getElementById(
            'activityEditForm'
        );


    const titleInput =
        document.getElementById(
            'activityEditTitle'
        );


    const descriptionInput =
        document.getElementById(
            'activityEditDescription'
        );


    form.action =
        '{{ url('/activities') }}/' + id;


    titleInput.value =
        title || '';


    descriptionInput.value =
        description || '';


    modal.classList.add(
        'open'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    titleInput.focus();

}



/* =====================================================
   CLOSE EDIT MODAL
===================================================== */

function closeEditActivity() {

    const modal =
        document.getElementById(
            'activityEditModal'
        );


    modal.classList.remove(
        'open'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );

}



/* =====================================================
   ESC
===================================================== */

document.addEventListener(
    'keydown',
    function(e) {

        if (e.key === 'Escape') {

            closeEditActivity();


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

</script>

@endpush

@endsection