@extends('layouts.app')

@section('title', 'Keanggotaan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush


@section('content')

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

    </section>


    {{-- =====================================================
         TOP ACTION
    ====================================================== --}}

    <div class="member-top-action">

        <a
            href="{{ route('members.create') }}"
            class="btn-add-top"
        >
            + Tambah Anggota
        </a>

    </div>


    {{-- =====================================================
         SUCCESS ALERT
    ====================================================== --}}

    @if(session('success'))

        <div class="alert-success">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >

                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />

                <polyline points="22 4 12 14.01 9 11.01" />

            </svg>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         MEMBER CARD
    ====================================================== --}}

    <div class="member-card">

        {{-- =================================================
             TOOLBAR
        ================================================== --}}

        <div class="member-toolbar">

            <div>

                <h3>
                    Daftar Anggota
                </h3>

                <p>
                    Data anggota yang terdaftar di perpustakaan.
                </p>

            </div>


            {{-- SEARCH --}}

            <div class="member-search">

                <input
                    type="text"
                    id="memberSearch"
                    placeholder="Cari anggota..."
                    autocomplete="off"
                >

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

                                @if($member->status === 'aktif')

                                    <span class="status-active">
                                        Aktif
                                    </span>

                                @else

                                    <span class="status-inactive">
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td>

                                <div class="action-buttons">

                                    {{-- EDIT --}}

                                    <a
                                        href="{{ route('members.edit', $member->id) }}"
                                        class="btn-edit"
                                        title="Edit Anggota"
                                    >
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}

                                    <form
                                        action="{{ route('members.destroy', $member->id) }}"
                                        method="POST"
                                        class="delete-member-form"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn-delete"
                                            title="Hapus Anggota"
                                            onclick="openMemberDeleteModal(this)"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty-data"
                            >
                                Belum ada data anggota.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL KONFIRMASI HAPUS ANGGOTA
========================================================= --}}

<div
    id="memberDeleteModal"
    class="member-delete-modal-overlay"
    aria-hidden="true"
>

    <div
        class="member-delete-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="memberDeleteModalTitle"
    >

        {{-- ICON --}}

        <div class="member-delete-modal-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >

                <polyline points="3 6 5 6 21 6" />

                <path
                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"
                />

                <path d="M10 11v6" />

                <path d="M14 11v6" />

                <path
                    d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"
                />

            </svg>

        </div>


        {{-- LABEL --}}

        <div class="member-delete-modal-label">
            KONFIRMASI
        </div>


        {{-- TITLE --}}

        <h3
            id="memberDeleteModalTitle"
            class="member-delete-modal-title"
        >
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

            <button
                type="button"
                class="member-delete-modal-cancel"
                onclick="closeMemberDeleteModal()"
            >
                Batal
            </button>


            <button
                type="button"
                class="member-delete-modal-confirm"
                onclick="confirmMemberDelete()"
            >
                Ya, Hapus
            </button>

        </div>

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | SEARCH ANGGOTA
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('memberSearch');

    const table =
        document.getElementById('memberTable');


    if (searchInput && table) {

        searchInput.addEventListener(
            'input',
            function () {

                const keyword =
                    this.value
                        .toLowerCase()
                        .trim();


                const rows =
                    table.querySelectorAll(
                        'tbody tr'
                    );


                rows.forEach(function (row) {

                    /*
                    | Jangan sembunyikan
                    | baris empty state.
                    */

                    if (
                        row.querySelector('.empty-data')
                    ) {
                        return;
                    }


                    const rowText =
                        row.innerText
                            .toLowerCase();


                    row.style.display =
                        rowText.includes(keyword)
                            ? ''
                            : 'none';

                });

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ESC UNTUK TUTUP MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                closeMemberDeleteModal();

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
            function (event) {

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
        row
            ? row.querySelector(
                'td:nth-child(2) strong'
            )
            : null;


    const memberName =
        nameElement
            ? nameElement.textContent.trim()
            : 'Anggota';


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

@endsection