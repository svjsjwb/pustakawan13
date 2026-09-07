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
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h3 style="margin: 0;">Daftar Anggota</h3>
                    <span id="liveBadge" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; animation: pulseDot 1.5s infinite;"></span>
                        Live Auto-Sync
                    </span>
                </div>
                <p>Data anggota yang terdaftar di perpustakaan (sinkron otomatis dari user).</p>
            </div>

            <div class="member-search">
                <input
                    type="text"
                    id="memberSearch"
                    placeholder="Cari nama, divisi, telepon..."
                    autocomplete="off"
                >
            </div>

        </div>

        <div class="table-wrap">

            <table id="memberTable">

                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA</th>
                        <th>DIVISI</th>
                        <th>NO. TELEPON</th>
                        <th>TANGGAL BERGABUNG</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody id="memberTableBody">
    @forelse($members as $member)

        <tr data-id="{{ $member->id }}">
            <td>{{ $loop->iteration }}</td>

            <td>
                <strong>{{ $member->name }}</strong>
                @if($member->email)
                    <div style="font-size: 11px; color: #64748b;">{{ $member->email }}</div>
                @endif
            </td>

            <td>
                {{ $member->division ?: 'Anggota' }}
            </td>

            <td>
                {{ $member->phone ?: '-' }}
            </td>

            <td>
                {{ $member->created_at ? $member->created_at->translatedFormat('d M Y') : '-' }}
            </td>

            <td>
                @if(strtolower($member->status) === 'aktif')
                    <span class="status-active">
                        Aktif
                    </span>
                @else
                    <span class="status-inactive">
                        Nonaktif
                    </span>
                @endif
            </td>

            <td>
                <div class="action-buttons">

                    <a
                        href="{{ route('members.edit', $member->id) }}"
                        class="btn-edit"
                    >
                        Edit
                    </a>

                    <form
                        action="{{ route('members.destroy', $member->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus anggota ini?')"
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
            <td colspan="7" class="empty-data">
                Belum ada data anggota.
            </td>
        </tr>

    @endforelse
</tbody>

            </table>

        </div>

    </div>

</div>

<style>
@keyframes pulseDot {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.2); }
    100% { opacity: 1; transform: scale(1); }
}
.new-row-highlight {
    animation: highlightFade 3s ease forwards;
}
@keyframes highlightFade {
    0% { background-color: #fef08a; }
    100% { background-color: transparent; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('memberSearch');
    const tableBody = document.getElementById('memberTableBody');
    let lastKnownIds = Array.from(document.querySelectorAll('#memberTableBody tr[data-id]')).map(r => r.dataset.id);

    // Filter pencarian
    function applySearch() {
        const keyword = searchInput.value.toLowerCase();
        document.querySelectorAll('#memberTableBody tr').forEach(function (row) {
            if (row.classList.contains('empty-data')) return;
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', applySearch);
    }

    // Auto-refresh polling data anggota setiap 8 detik
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function fetchLatestMembers() {
        fetch('{{ route("members.json") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.members) return;

            const newIds = data.members.map(m => String(m.id));
            const hasNew = newIds.some(id => !lastKnownIds.includes(id));
            const countChanged = newIds.length !== lastKnownIds.length;

            if (hasNew || countChanged) {
                renderTable(data.members, lastKnownIds);
                lastKnownIds = newIds;
                applySearch();
            }
        })
        .catch(err => console.debug('Polling members silent error:', err));
    }

    function renderTable(members, previousIds) {
        if (!members.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="empty-data">Belum ada data anggota.</td></tr>';
            return;
        }

        let html = '';
        members.forEach((m, idx) => {
            const isNew = !previousIds.includes(String(m.id));
            const highlightClass = isNew ? 'new-row-highlight' : '';
            const statusClass = m.status.toLowerCase() === 'aktif' ? 'status-active' : 'status-inactive';

            html += `
            <tr data-id="${m.id}" class="${highlightClass}">
                <td>${idx + 1}</td>
                <td>
                    <strong>${escapeHtml(m.name)}</strong>
                    ${m.email && m.email !== '-' ? `<div style="font-size: 11px; color: #64748b;">${escapeHtml(m.email)}</div>` : ''}
                </td>
                <td>${escapeHtml(m.division)}</td>
                <td>${escapeHtml(m.phone)}</td>
                <td>${escapeHtml(m.joined_at)}</td>
                <td><span class="${statusClass}">${escapeHtml(m.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <a href="${m.edit_url}" class="btn-edit">Edit</a>
                        <form action="${m.delete_url}" method="POST" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-delete">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>`;
        });

        tableBody.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Polling interval
    setInterval(fetchLatestMembers, 8000);
});
</script>

@endsection