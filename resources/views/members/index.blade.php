@extends('layouts.app')

@section('title', 'Keanggotaan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush

@section('content')

<div class="member-page">

    <div class="page-header">
        <div>
            <h2>Keanggotaan</h2>
            <p>Kelola data anggota perpustakaan.</p>
        </div>

        <a href="{{ route('members.create') }}" class="btn-add-top">
            + Tambah Anggota
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="member-card">

        <div class="member-toolbar">

            <div>
                <h3>Daftar Anggota</h3>
                <p>Data anggota yang terdaftar di perpustakaan.</p>
            </div>

            <div class="member-search">
                <input
                    type="text"
                    id="memberSearch"
                    placeholder="Cari anggota..."
                    autocomplete="off"
                >
            </div>

        </div>

        <div class="table-wrap">

            <table id="memberTable">

                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NO. ANGGOTA</th>
                        <th>NAMA</th>
                        <th>NIS/NIP</th>
                        <th>KELAS</th>
                        <th>NO. TELEPON</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($members as $member)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $member->member_number }}
                            </td>

                            <td>
                                <strong>{{ $member->name }}</strong>
                            </td>

                            <td>
                                {{ $member->nis_nip ?? '-' }}
                            </td>

                            <td>
                                {{ $member->class ?? '-' }}
                            </td>

                            <td>
                                {{ $member->phone ?? '-' }}
                            </td>

                            <td>
                                @if($member->status === 'Aktif')
                                    <span class="status-badge active">
                                        Aktif
                                    </span>
                                @else
                                    <span class="status-badge inactive">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="{{ route('members.edit', $member) }}"
                                        class="btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('members.destroy', $member) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus anggota ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-delete"
                                        >
                                            Hapus
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="empty-state">
                                Belum ada anggota yang terdaftar.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
document.getElementById('memberSearch').addEventListener('keyup', function () {

    const keyword = this.value.toLowerCase();

    document.querySelectorAll('#memberTable tbody tr').forEach(function (row) {

        row.style.display =
            row.innerText.toLowerCase().includes(keyword)
                ? ''
                : 'none';

    });

});
</script>

@endsection