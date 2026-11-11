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

        @if (session('error'))
            <div class="alert-error" style="display: flex; align-items: center; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; flex-shrink: 0; color: #dc2626;">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <span>{{ session('error') }}</span>
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
                                                <button type="button" class="btn-edit"
                                                    title="Edit Anggota" aria-label="Edit Anggota"
                                                    data-member-id="{{ $member->id }}"
                                                    data-update-url="{{ route('members.update', $member->id) }}"
                                                    data-name="{{ $member->name }}"
                                                    data-email="{{ $member->email }}"
                                                    data-division="{{ $member->division }}"
                                                    data-phone="{{ $member->phone }}">

                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                    </svg>

                                                </button>


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
        class="member-create-modal-overlay{{ $errors->any() && old('member_form_action') !== 'edit' ? ' show' : '' }}"
        aria-hidden="{{ $errors->any() && old('member_form_action') !== 'edit' ? 'false' : 'true' }}">
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

            @if ($errors->any() && old('member_form_action') !== 'edit')
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

    <div
        id="memberEditModal"
        class="member-create-modal-overlay{{ $errors->any() && old('member_form_action') === 'edit' ? ' show' : '' }}"
        aria-hidden="{{ $errors->any() && old('member_form_action') === 'edit' ? 'false' : 'true' }}">
        <div
            class="member-create-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="memberEditModalTitle"
            aria-describedby="memberEditModalDescription">
            <header class="member-create-modal-header">
                <div>
                    <h2 id="memberEditModalTitle">Edit Anggota</h2>
                    <p id="memberEditModalDescription">Perbarui data anggota perpustakaan.</p>
                </div>

                <button
                    type="button"
                    class="member-create-modal-close"
                    onclick="closeMemberEditModal()"
                    aria-label="Tutup form edit anggota">
                    &times;
                </button>
            </header>

            @if ($errors->any() && old('member_form_action') === 'edit')
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
                <form
                    id="memberEditForm"
                    action="{{ $errors->any() && old('member_form_action') === 'edit' ? route('members.update', old('member_id')) : '#' }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="member_form_action" value="edit">
                    <input id="editMemberId" type="hidden" name="member_id" value="{{ old('member_id') }}">

                    <div class="member-create-form-section">
                        <div class="member-create-section-title">
                            <span>1</span>
                            <h3>Informasi Pribadi</h3>
                        </div>

                        <div class="form-group">
                            <label for="editMemberName">Nama <span>*</span></label>
                            <input
                                type="text"
                                id="editMemberName"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Masukkan nama anggota"
                                autocomplete="name"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="editMemberDivision">Divisi <span>*</span></label>
                            <select id="editMemberDivision" name="division" required>
                                <option value="">-- Pilih Divisi --</option>
                                <option value="Center Of Excellence" @selected(old('division') === 'Center Of Excellence')>Center Of Excellence</option>
                                <option value="Digital Business" @selected(old('division') === 'Digital Business')>Digital Business</option>
                                <option value="E-Publishing" @selected(old('division') === 'E-Publishing')>E-Publishing</option>
                                <option value="Finance" @selected(old('division') === 'Finance')>Finance</option>
                                <option value="General Trading" @selected(old('division') === 'General Trading')>General Trading</option>
                                <option value="HR &amp; GA" @selected(old('division') === 'HR & GA')>HR &amp; GA</option>
                                <option value="HSE" @selected(old('division') === 'HSE')>HSE</option>
                                <option value="IQA" @selected(old('division') === 'IQA')>IQA</option>
                                <option value="IT" @selected(old('division') === 'IT')>IT</option>
                                <option value="Marketing" @selected(old('division') === 'Marketing')>Marketing</option>
                                <option value="MTIS Perpuskita dan Tisera" @selected(old('division') === 'MTIS Perpuskita dan Tisera')>MTIS Perpuskita dan Tisera</option>
                                <option value="MTIS Planning and Development" @selected(old('division') === 'MTIS Planning and Development')>MTIS Planning and Development</option>
                                <option value="People Development Center" @selected(old('division') === 'People Development Center')>People Development Center</option>
                                <option value="Production" @selected(old('division') === 'Production')>Production</option>
                                <option value="School Book Sales" @selected(old('division') === 'School Book Sales')>School Book Sales</option>
                                <option value="School Book Publishing" @selected(old('division') === 'School Book Publishing')>School Book Publishing</option>
                                <option value="SCM" @selected(old('division') === 'SCM')>SCM</option>
                                <option value="TAX" @selected(old('division') === 'TAX')>TAX</option>
                            </select>
                        </div>
                    </div>

                    <div class="member-create-form-section">
                        <div class="member-create-section-title">
                            <span>2</span>
                            <h3>Kontak</h3>
                        </div>

                        <div class="form-group">
                            <label for="editMemberEmail">Email</label>
                            <input
                                type="email"
                                id="editMemberEmail"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Contoh: nama@email.com"
                                autocomplete="email">
                        </div>

                        <div class="form-group">
                            <label for="editMemberPhone">No. Telepon <span>*</span></label>
                            <input
                                type="text"
                                id="editMemberPhone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Contoh: 081234567890"
                                autocomplete="tel"
                                required>
                        </div>
                    </div>

                    <div class="member-create-modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeMemberEditModal()">Batal</button>
                        <button type="submit" class="btn-save">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" />
                                <path d="M17 21v-8H7v8" />
                                <path d="M7 3v5h8" />
                            </svg>
                            Simpan Perubahan
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
            const editModal = document.getElementById('memberEditModal');
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

            document.querySelectorAll('.btn-edit[data-update-url]').forEach(function(button) {
                button.addEventListener('click', function() {
                    openMemberEditModal(button);
                });
            });

            if (editModal) {
                editModal.addEventListener('click', function(event) {
                    if (event.target === editModal) {
                        closeMemberEditModal();
                    }
                });
            }

            if (editModal && editModal.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
                document.getElementById('editMemberName')?.focus();
            }

            @if ($errors->any() && old('member_form_action') === 'edit')
                document.getElementById('editMemberId').value = @json(old('member_id'));
            @endif

            function filterMemberRows() {
                if (!table) {
                    return;
                }

                const keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const rows = table.querySelectorAll('tbody tr:not(.empty-search-row)');
                let visibleCount = 0;

                rows.forEach(function(row) {
                    if (row.querySelector('.empty-data')) {
                        return;
                    }

                    const rowText = (row.textContent || row.innerText).toLowerCase();
                    const statusBadge = row.querySelector('.status-active, .status-inactive');
                    const matchesSearch = !keyword || rowText.includes(keyword);
                    const matchesStatus = !selectedStatus || (
                        selectedStatus === 'aktif'
                            ? statusBadge && statusBadge.classList.contains('status-active')
                            : statusBadge && statusBadge.classList.contains('status-inactive')
                    );

                    const isMatch = matchesSearch && matchesStatus;
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) visibleCount++;
                });

                // Tampilkan pesan kosong jika tidak ada yang cocok
                let noResultRow = table.querySelector('.empty-search-row');
                if (visibleCount === 0 && keyword) {
                    if (!noResultRow) {
                        const tbody = table.querySelector('tbody');
                        if (tbody) {
                            noResultRow = document.createElement('tr');
                            noResultRow.className = 'empty-search-row';
                            noResultRow.innerHTML = '<td colspan="7" style="text-align: center; padding: 32px; color: #94a3b8; font-weight: 500;">Tidak ada data anggota yang sesuai dengan kata kunci "' + keyword + '".</td>';
                            tbody.appendChild(noResultRow);
                        }
                    } else {
                        noResultRow.style.display = '';
                        const td = noResultRow.querySelector('td');
                        if (td) td.textContent = 'Tidak ada data anggota yang sesuai dengan kata kunci "' + keyword + '".';
                    }
                } else if (noResultRow) {
                    noResultRow.style.display = 'none';
                }
            }

            if (searchInput && table) {
                searchInput.addEventListener('input', filterMemberRows);
                searchInput.addEventListener('keyup', filterMemberRows);
                searchInput.addEventListener('search', filterMemberRows);
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
                        const editModal = document.getElementById('memberEditModal');
                        if (editModal && editModal.classList.contains('show')) {
                            closeMemberEditModal();
                        } else if (createModal && createModal.classList.contains('show')) {
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

        function openMemberEditModal(button) {
            const modal = document.getElementById('memberEditModal');
            const form = document.getElementById('memberEditForm');
            if (!modal || !form || !button) {
                return;
            }

            form.action = button.dataset.updateUrl;
            document.getElementById('editMemberId').value = button.dataset.memberId || '';
            document.getElementById('editMemberName').value = button.dataset.name || '';
            document.getElementById('editMemberEmail').value = button.dataset.email || '';
            document.getElementById('editMemberDivision').value = button.dataset.division || '';
            document.getElementById('editMemberPhone').value = button.dataset.phone || '';
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            document.getElementById('editMemberName').focus();
        }

        function closeMemberEditModal() {
            const modal = document.getElementById('memberEditModal');
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