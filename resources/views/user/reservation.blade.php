@extends('layouts.user')
@section('title', 'Reservasi – Perpustakaan Tiga Serangkai')
@section('page-title', 'Reservasi')

@section('content')

<div class="ul-page-header">
    <div>
        <p class="ul-page-kicker">KOLEKSI SAYA</p>
        <h1 class="ul-page-h1">📅 Reservasi Buku</h1>
    </div>
    <a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-primary">+ Reservasi Buku Baru</a>
</div>

@if(!$member)
<div class="ul-card">
    <div class="ul-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>Data anggota tidak ditemukan</h3>
        <p>Akun Anda belum terdaftar sebagai anggota perpustakaan. Silakan hubungi petugas.</p>
    </div>
</div>

@elseif($reservations->isEmpty())
<div class="ul-card">
    <div class="ul-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <h3>Tidak ada reservasi</h3>
        <p>Anda belum pernah mereservasi buku. Jelajahi katalog untuk mereservasi buku yang sedang habis.</p>
        <a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-primary">Jelajahi Katalog</a>
    </div>
</div>

@else
<div class="ul-card" style="overflow:hidden">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:var(--paper);border-bottom:1px solid var(--line)">
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted);white-space:nowrap">NO</th>
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted)">BUKU</th>
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted);white-space:nowrap">TANGGAL RESERVASI</th>
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted);white-space:nowrap">KADALUARSA</th>
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted)">ANTRIAN</th>
                <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;color:var(--muted)">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $res)
            <tr style="border-bottom:1px solid var(--line-soft)" onmouseover="this.style.background='#fbfaf6'" onmouseout="this.style.background=''">
                <td style="padding:14px 16px;color:var(--muted-soft);font-weight:600">{{ $loop->iteration }}</td>
                <td style="padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:12px">
                        @if($res->book?->cover)
                        <img src="{{ asset('storage/'.$res->book->cover) }}" style="width:36px;height:52px;object-fit:cover;border-radius:4px;flex-shrink:0" alt="">
                        @else
                        <div style="width:36px;height:52px;border-radius:4px;background:linear-gradient(135deg,#1d5c59,#287879);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:rgba(255,255,255,.6);flex-shrink:0">
                            {{ strtoupper(substr($res->book?->title ?? 'B', 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p style="margin:0 0 2px;font-weight:700;color:var(--ink)">{{ $res->book?->title ?? '—' }}</p>
                            <p style="margin:0;font-size:11.5px;color:var(--muted)">{{ $res->book?->author ?? '—' }}</p>
                        </div>
                    </div>
                </td>
                <td style="padding:14px 16px;color:var(--ink-soft);white-space:nowrap">{{ $res->reserved_at?->format('d M Y') ?? '-' }}</td>
                <td style="padding:14px 16px;color:var(--ink-soft);white-space:nowrap">{{ $res->expires_at?->format('d M Y') ?? '-' }}</td>
                <td style="padding:14px 16px">
                    @if($res->seat_number)
                    <span style="font-size:13px;font-weight:700;color:var(--primary)">#{{ $res->seat_number }}</span>
                    @else
                    <span style="color:var(--muted-soft)">—</span>
                    @endif
                </td>
                <td style="padding:14px 16px">
                    @php
                        $badgeClass = match($res->status) {
                            'pending'     => 'ul-badge-warning',
                            'disetujui'   => 'ul-badge-info',
                            'selesai'     => 'ul-badge-success',
                            'dibatalkan'  => 'ul-badge-danger',
                            default       => 'ul-badge-neutral',
                        };
                        $label = match($res->status) {
                            'pending'     => '⏳ Menunggu',
                            'disetujui'   => '✓ Disetujui',
                            'selesai'     => '✔ Selesai',
                            'dibatalkan'  => '✕ Dibatalkan',
                            default       => ucfirst($res->status),
                        };
                    @endphp
                    <span class="ul-badge {{ $badgeClass }}">{{ $label }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($reservations->hasPages())
<div style="display:flex;justify-content:center;margin-top:20px">
    {{ $reservations->links('partials.pagination') }}
</div>
@endif

@endif

@endsection
