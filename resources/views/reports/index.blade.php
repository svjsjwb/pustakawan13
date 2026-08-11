@extends('layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<section class="page" id="page-reports">
    <div class="grid stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;" id="report-stats"></div>
    <div class="two-col">
        <div>
            <div class="section-head" style="margin-top:0;">
                <h3>Buku Terpopuler Bulan Ini</h3>
            </div>
            <div class="card card-pad" id="top-books-list"></div>
        </div>
        <div>
            <div class="section-head" style="margin-top:0;">
                <h3>Ekspor Laporan</h3>
            </div>
            <div class="card card-pad">
                <p style="font-size:12.5px;color:var(--muted);margin:0 0 16px;">Unduh ringkasan aktivitas perpustakaan dalam format siap cetak.</p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <button class="btn btn-ghost" style="justify-content:flex-start;" onclick="toast('Laporan sirkulasi disiapkan (contoh).')">📄 Laporan Sirkulasi Bulanan</button>
                    <button class="btn btn-ghost" style="justify-content:flex-start;" onclick="toast('Laporan koleksi disiapkan (contoh).')">📚 Laporan Kondisi Koleksi</button>
                    <button class="btn btn-ghost" style="justify-content:flex-start;" onclick="toast('Laporan denda disiapkan (contoh).')">💳 Laporan Denda &amp; Pembayaran</button>
                    <button class="btn btn-ghost" style="justify-content:flex-start;" onclick="toast('Laporan anggota disiapkan (contoh).')">👥 Laporan Keanggotaan</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection