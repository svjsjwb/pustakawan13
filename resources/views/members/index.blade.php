@extends('layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<section class="page" id="page-members">
    <div class="filters">
        <input type="text" id="members-search" placeholder="Cari nama, email, No. Anggota…" oninput="renderMembers()">
        <select id="members-filter-status" onchange="renderMembers()">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select>
        <span style="margin-left:auto;font-size:12px;color:var(--muted);" id="members-count"></span>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Kontak</th>
                        <th>Tipe</th>
                        <th>Dipinjam</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="members-table-body"></tbody>
            </table>
        </div>
    </div>
</section>
@endsection