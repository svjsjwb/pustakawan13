@extends('layouts.app')

@section('title', 'Keanggotaan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush


@section('content')

    @if (session('error'))
        <div class="member-delete-error-overlay" id="memberDeleteError">
            <div class="member-delete-error-modal">

                <button type="button" class="member-delete-error-close" onclick="closeMemberDeleteError()">
                    &times;
                </button>

                <div class="member-delete-error-icon">
                    !
                </div>

                <h3>Penghapusan Anggota Gagal</h3>

                <p>
                    {{ session('error') }}
                </p>

                <button type="button" class="member-delete-error-button" onclick="closeMemberDeleteError()">
                    Mengerti
                </button>

            </div>
        </div>
    @endif

    <div class="member-page">

        {{-- =====================================================
         HERO HEADER
    ====================================================== --}}

        <section class="member-hero">

            <div class="member-hero-content">

                <div class="member-hero-label">
                    <span class="member-hero-dot"></span>
                    Pusat Perpustakaan
                </div>

                <h1>
                    Keanggotaan
                </h1>

                <p>
                    Kelola data anggota perpustakaan.
                </p>

            </div>

            <div class="member-hero-action">
                <a href="{{ route('members.create') }}" class="btn-add-top" id="openMemberCreateModal">
                    + Tambah Anggota Baru
                </a>
            </div>

        </section>


        {{-- =====================================================
         SUCCESS ALERT
    ====================================================== --}}

        @if (session('success'))
            <div class="alert-success">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />

                    <polyline points="22 4 12 14.01 9 11.01" />

                </svg>

                <span>
                    {{ session('success') }}
                </span>

            </div>
        @endif


        {{-- =====================================================
    MEMBER CONTENT CARD
====================================================== --}}

        <div class="member-card">

            <div class="member-content-card">

                {{-- =================================================
            SEARCH + FILTER STATUS
        ================================================== --}}

                <div class="member-filter-row">

                    {{-- SEARCH --}}
                    <div class="member-search">
                        <svg class="member-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input type="text" id="memberSearch" placeholder="Cari data anggota..." autocomplete="off">
                    </div>


                    {{-- FILTER STATUS --}}
                    <div class="member-status-dropdown" id="memberStatusDropdown">
                        <button type="button" class="member-status-filter" id="memberStatusFilter"
                            aria-expanded="false" aria-haspopup="listbox">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                            </svg>
                            <span id="memberStatusLabel">Semua Status</span>
                            <svg class="member-status-chevron" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="member-status-menu" id="memberStatusMenu" role="group"
                            aria-label="Filter status anggota">
                            <button type="button" class="member-status-option active" data-status=""
                                data-label="Semua Status">Semua Status</button>
                            <button type="button" class="member-status-option" data-status="aktif"
                                data-label="Aktif">Aktif</button>
                            <button type="button" class="member-status-option" data-status="nonaktif"
                                data-label="Nonaktif">Nonaktif</button>
                        </div>
                    </div>

                </div>


                {{-- =================================================
            TABLE
        ================================================== --}}

                <div class="table-wrap">

                    <table id="memberTable">

                        <thead>
                            <tr>

                                <th>
                                    NO
                                </th>

                                <th>
                                    NAMA
                                </th>

                                <th>
                                    EMAIL
                                </th>

                                <th>
                                    DIVISI
                                </th>

                                <th>
                                    NO. TELEPON
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    AKSI
                                </th>

                            </tr>
                        </thead>


                        <tbody>

                            @forelse($members as $member)
                                <tr>

                                    {{-- NO --}}
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- NAMA --}}
                                    <td>
                                        <strong>
                                            {{ $member->name }}
                                        </strong>
                                    </td>


                                    {{-- EMAIL --}}
                                    <td>
                                        {{ $member->email ?? '-' }}
                                    </td>


                                    {{-- DIVISI --}}
                                    <td>
                                        {{ $member->division ?? '-' }}
                                    </td>


                                    {{-- TELEPON --}}
                                    <td>
                                        {{ $member->phone ?? '-' }}
                                    </td>


                                    {{-- STATUS --}}
                                    <td>

                                        @if ($member->status === 'aktif')
                                            <span class="status-active">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="status-inactive">
                                                Nonaktif
                                            </span>
                                        @endif

                                    </td>


                                    {{-- AKSI --}}
                                    <td>

                                        <div class="action-buttons">

                                            @if ($member->user?->role === 'guest')
                                                {{-- SETUJUI --}}
                                                <form action="{{ route('members.approve', $member->id) }}" method="POST"
                                                    style="display:inline;">

                                                    @csrf

                                                    @method('PATCH')

                                                    <button type="submit" class="btn-approve" title="Setujui Pendaftaran">
                                                        Setuju
                                                    </button>

                                                </form>


                                                {{-- TOLAK --}}
                                                <form action="{{ route('members.reject', $member->id) }}" method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('Yakin ingin menolak pendaftaran anggota ini?')">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button type="submit" class="btn-reject" title="Tolak Pendaftaran">
                                                        Tolak
                                                    </button>

                                                </form>
                                            @else
                                                {{-- EDIT --}}
                                                <a href="{{ route('members.edit', $member->id) }}" class="btn-edit"
                                                    title="Edit Anggota" aria-label="Edit Anggota">

                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                    </svg>

                                                </a>


                                                {{-- HAPUS --}}
                                                <form action="{{ route('members.destroy', $member->id) }}" method="POST"
                                                    class="delete-member-form">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button type="button" class="btn-delete" title="Hapus Anggota"
                                                        aria-label="Hapus Anggota" onclick="openMemberDeleteModal(this)">

                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            aria-hidden="true">
                                                            <polyline points="3 6 5 6 21 6" />
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                            <path d="M10 11v6" />
                                                            <path d="M14 11v6" />
                                                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                                        </svg>

                                                    </button>

                                                </form>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="empty-data">
                                        Belum ada data anggota.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- =================================================
            PAGINATION AREA
        ================================================== --}}

                @if ($members instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                    <div class="member-pagination">

                        <div class="member-pagination-links">

                            {{ $members->onEachSide(1)->links() }}

                        </div>

                        <div class="member-pagination-info">

                            Menampilkan

                            <strong>
                                {{ $members->firstItem() ?? 0 }}
                            </strong>

                            -

                            <strong>
                                {{ $members->lastItem() ?? 0 }}
                            </strong>

                            dari

                            <strong>
                                {{ $members->total() }}
                            </strong>

                            hasil

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
     MODAL TAMBAH ANGGOTA
========================================================= --}}

    <div
        id="memberCreateModal"
        class="member-create-modal-overlay{{ $errors->any() ? ' show' : '' }}"
        aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
        <div
            class="member-create-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="memberCreateModalTitle"
            aria-describedby="memberCreateModalDescription">
            <header class="member-create-modal-header">
                <div>
                    <h2 id="memberCreateModalTitle">Tambah Anggota Baru</h2>
                    <p id="memberCreateModalDescription">
                        Lengkapi data untuk mendaftarkan anggota baru ke sistem perpustakaan.
                    </p>
                </div>

                <button
                    type="button"
                    class="member-create-modal-close"
                    onclick="closeMemberCreateModal()"
                    aria-label="Tutup form tambah anggota">
                    &times;
                </button>
            </header>

            @if ($errors->any())
                <div class="member-create-errors" role="alert">
                    <strong>Periksa kembali data yang diisi.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="member-create-form-card">
                <form action="{{ route('members.store') }}" method="POST">
                    @csrf

                    @include('members.partials.create-fields')

                    <div class="member-create-modal-actions">
                        <button
                            type="button"
                            class="btn-cancel"
                            onclick="closeMemberCreateModal()">
                            Batal
                        </button>
                        <button type="submit" class="btn-save">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- =========================================================
     MODAL KONFIRMASI HAPUS ANGGOTA
========================================================= --}}

    <div id="memberDeleteModal" class="member-delete-modal-overlay" aria-hidden="true">

        <div class="member-delete-modal" role="dialog" aria-modal="true" aria-labelledby="memberDeleteModalTitle">

            {{-- ICON --}}

            <div class="member-delete-modal-icon">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">

                    <polyline points="3 6 5 6 21 6" />

                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />

                    <path d="M10 11v6" />

                    <path d="M14 11v6" />

                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />

                </svg>

            </div>


            {{-- LABEL --}}

            <div class="member-delete-modal-label">
                KONFIRMASI
            </div>


            {{-- TITLE --}}

            <h3 id="memberDeleteModalTitle" class="member-delete-modal-title">
                Hapus Anggota
            </h3>


            {{-- DESCRIPTION --}}

            <p class="member-delete-modal-description">

                Yakin ingin menghapus anggota ini?

                <br>

                Data yang sudah dihapus tidak dapat dikembalikan.

            </p>


            {{-- MEMBER INFO --}}

            <div class="member-delete-modal-member">

                <span>
                    ANGGOTA
                </span>

                <strong id="deleteMemberName">
                    -
                </strong>

            </div>


            {{-- ACTIONS --}}

            <div class="member-delete-modal-actions">

                <button type="button" class="member-delete-modal-cancel" onclick="closeMemberDeleteModal()">
                    Batal
                </button>


                <button type="button" class="member-delete-modal-confirm" onclick="confirmMemberDelete()">
                    Ya, Hapus
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
     JAVASCRIPT
========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | SEARCH ANGGOTA
            |--------------------------------------------------------------------------
            */

            const searchInput = document.getElementById('memberSearch');
            const table = document.getElementById('memberTable');
            const statusDropdown = document.getElementById('memberStatusDropdown');
            const statusFilter = document.getElementById('memberStatusFilter');
            const statusMenu = document.getElementById('memberStatusMenu');
            const statusLabel = document.getElementById('memberStatusLabel');
            const createModal = document.getElementById('memberCreateModal');
            const openCreateButton = document.getElementById('openMemberCreateModal');
            let selectedStatus = '';

            if (openCreateButton && createModal) {
                openCreateButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    openMemberCreateModal();
                });

                createModal.addEventListener('click', function(event) {
                    if (event.target === createModal) {
                        closeMemberCreateModal();
                    }
                });
            }

            if (createModal && createModal.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
                const nameInput = createModal.querySelector('#name');
                if (nameInput) {
                    nameInput.focus();
                }
            }

            function filterMemberRows() {
                if (!table) {
                    return;
                }

                const keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(function(row) {
                    if (row.querySelector('.empty-data')) {
                        return;
                    }

                    const rowText = row.innerText.toLowerCase();
                    const statusBadge = row.querySelector('.status-active, .status-inactive');
                    const matchesSearch = rowText.includes(keyword);
                    const matchesStatus = !selectedStatus || (
                        selectedStatus === 'aktif'
                            ? statusBadge && statusBadge.classList.contains('status-active')
                            : statusBadge && statusBadge.classList.contains('status-inactive')
                    );

                    row.style.display = matchesSearch && matchesStatus ? '' : 'none';
                });
            }

            if (searchInput && table) {
                searchInput.addEventListener('input', filterMemberRows);
            }

            if (statusFilter && statusMenu && statusLabel) {
                statusFilter.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const isOpen = statusMenu.classList.toggle('is-open');
                    statusFilter.setAttribute('aria-expanded', String(isOpen));
                });

                statusMenu.addEventListener('click', function(event) {
                    const option = event.target.closest('.member-status-option');
                    if (!option) {
                        return;
                    }

                    selectedStatus = option.dataset.status || '';
                    statusLabel.textContent = option.dataset.label || 'Semua Status';
                    statusMenu.querySelectorAll('.member-status-option').forEach(function(item) {
                        item.classList.toggle('active', item === option);
                    });
                    statusMenu.classList.remove('is-open');
                    statusFilter.setAttribute('aria-expanded', 'false');
                    filterMemberRows();
                });

                document.addEventListener('click', function(event) {
                    if (statusDropdown && !statusDropdown.contains(event.target)) {
                        statusMenu.classList.remove('is-open');
                        statusFilter.setAttribute('aria-expanded', 'false');
                    }
                });
            }



            /*
            |--------------------------------------------------------------------------
            | ESC UNTUK TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (event.key === 'Escape') {
                        const createModal = document.getElementById('memberCreateModal');
                        if (createModal && createModal.classList.contains('show')) {
                            closeMemberCreateModal();
                        } else {
                            closeMemberDeleteModal();
                        }

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKDROP
            |--------------------------------------------------------------------------
            */

            const modal =
                document.getElementById(
                    'memberDeleteModal'
                );


            if (modal) {

                modal.addEventListener(
                    'click',
                    function(event) {

                        if (event.target === this) {

                            closeMemberDeleteModal();

                        }

                    }
                );

            }

        });


        /*
        |--------------------------------------------------------------------------
        | FORM DELETE TERPILIH
        |--------------------------------------------------------------------------
        */

        function openMemberCreateModal() {
            const modal = document.getElementById('memberCreateModal');
            if (!modal) {
                return;
            }

            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            const nameInput = modal.querySelector('#name');
            if (nameInput) {
                nameInput.focus();
            }
        }

        function closeMemberCreateModal() {
            const modal = document.getElementById('memberCreateModal');
            if (!modal) {
                return;
            }

            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        let memberDeleteForm = null;


        /*
        |--------------------------------------------------------------------------
        | BUKA MODAL HAPUS
        |--------------------------------------------------------------------------
        */

        function openMemberDeleteModal(button) {

            memberDeleteForm =
                button.closest(
                    '.delete-member-form'
                );


            if (!memberDeleteForm) {
                return;
            }


            const row =
                button.closest('tr');


            /*
            | Ambil nama anggota
            */

            const nameElement =
                row ?
                row.querySelector(
                    'td:nth-child(2) strong'
                ) :
                null;


            const memberName =
                nameElement ?
                nameElement.textContent.trim() :
                'Anggota';


            /*
            | Tampilkan nama di modal
            */

            const nameTarget =
                document.getElementById(
                    'deleteMemberName'
                );


            if (nameTarget) {

                nameTarget.textContent =
                    memberName;

            }


            /*
            | Buka modal
            */

            const modal =
                document.getElementById(
                    'memberDeleteModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add('show');

            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.style.overflow =
                'hidden';

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        function closeMemberDeleteModal() {

            const modal =
                document.getElementById(
                    'memberDeleteModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.remove('show');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.style.overflow =
                '';


            memberDeleteForm =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI DELETE
        |--------------------------------------------------------------------------
        */

        function confirmMemberDelete() {

            if (!memberDeleteForm) {
                return;
            }


            memberDeleteForm.submit();

        }
    </script>

    <script>
        function closeMemberDeleteError() {
            const popup = document.getElementById('memberDeleteError');

            if (popup) {
                popup.remove();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {

            const popup = document.getElementById('memberDeleteError');

            if (popup) {

                popup.addEventListener('click', function(event) {

                    if (event.target === popup) {
                        closeMemberDeleteError();
                    }

                });

            }

        });
    </script>

@endsection