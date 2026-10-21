@extends('emails.layout')

@section('content')
@php
  $isApproved = ($status === 'disetujui');
  $userName = $reservation->user?->name ?? $reservation->member?->name ?? 'Anggota';
  $bookTitle = $reservation->book?->judul_buku ?? $reservation->book?->title ?? 'Buku Perpustakaan';
  $author = $reservation->book?->penulis ?? '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:{{ $isApproved ? '#ecfdf5' : '#ffe4e6' }};font-size:32px;margin-bottom:14px;border:2px solid {{ $isApproved ? '#a7f3d0' : '#fecdd3' }};">
    {{ $isApproved ? '✅' : '❌' }}
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:{{ $isApproved ? '#065f46' : '#9f1239' }};font-size:22px;font-weight:800;letter-spacing:-0.3px;">
    {{ $isApproved ? 'Reservasi Buku Disetujui' : 'Reservasi Belum Dapat Disetujui' }}
  </h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">
    Halo <strong>{{ $userName }}</strong>, {{ $isApproved ? 'pengajuan reservasi buku Anda telah disetujui petugas perpustakaan.' : 'mohon maaf, pengajuan reservasi buku Anda saat ini belum dapat disetujui.' }}
  </p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:{{ $isApproved ? '#059669' : '#e11d48' }};text-transform:uppercase;letter-spacing:1px;font-weight:700;">📑 Detail Reservasi</span>
  </div>
  <table role="presentation" class="info-table" style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Judul Buku:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $bookTitle }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Penulis:</td>
      <td style="padding:6px 0;color:#334155;font-weight:600;">{{ $author }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;padding:3px 12px;background:{{ $isApproved ? '#d1fae5' : '#fee2e2' }};color:{{ $isApproved ? '#065f46' : '#991b1b' }};border-radius:9999px;font-size:12px;font-weight:700;text-transform:capitalize;">
          {{ $status }}
        </span>
      </td>
    </tr>
    @if($isApproved)
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Reservasi:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">
        {{ $reservation->reserved_at ? \Carbon\Carbon::parse($reservation->reserved_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
      </td>
    </tr>
    @if(!empty($reservation->seat_number))
    <tr>
      <td style="padding:6px 0;color:#64748b;">Nomor Kursi:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $reservation->seat_number }}</td>
    </tr>
    @endif
    @else
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alasan Penolakan:</td>
      <td style="padding:6px 0;color:#9f1239;font-weight:600;">
        {{ $reservation->rejection_reason ?: 'Buku sedang dipinjam atau tidak tersedia di rak.' }}
      </td>
    </tr>
    @endif
  </table>
</div>

@if($isApproved)
<div style="background:#f0fdf4;border-left:4px solid #10b981;border-radius:8px;padding:16px 20px;margin-bottom:26px;">
  <p style="margin:0;color:#065f46;font-size:13px;line-height:1.6;">
    📍 <strong>Pengambilan Buku:</strong> Buku yang direservasi telah disiapkan. Silakan ambil di perpustakaan sebelum batas masa berlaku reservasi berakhir.
  </p>
</div>
@endif

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.reservations') }}" class="btn-primary" style="display:inline-block;background:{{ $isApproved ? 'linear-gradient(135deg,#059669,#10b981)' : 'linear-gradient(135deg,#217475,#278889)' }};color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.15);">
    {{ $isApproved ? 'Lihat Tiket Reservasi' : 'Cari Buku Lain' }} &rarr;
  </a>
</div>
@endsection
