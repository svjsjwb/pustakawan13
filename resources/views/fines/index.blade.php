@extends('layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<section class="page" id="page-fines">
    <div class="grid stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;" id="fines-stats"></div>
    <div class="filters">
        <input type="text" id="fines-search" placeholder="Cari anggota…" oninput="renderFines()">
        <select id="fines-filter" onchange="renderFines()">
            <option value="">Semua</option>
            <option value="belum">Belum Dibayar</option>
            <option value="lunas">Lunas</option>
        </select>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Terlambat</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="fines-table-body"></tbody>
            </table>
        </div>
    </div>
</section>
@endsection