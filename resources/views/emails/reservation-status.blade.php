@extends('emails.layout')

@section('content')
@php
  $isApproved = ($status === 'disetujui');
  $userName = $reservation->user?->name ?? $reservation->member?->name ?? 'Anggota';
  $bookTitle = $reservation->book?->title ?? 'Buku Perpustakaan';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:{{ $isApproved ? '#f0fdf4' : '#fef2f2' }};font-size:28px;margin-bottom:12px;">
    {{ $isApproved ? '✅' : '❌' }}
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">
    {{ $isApproved ? 'Reservasi Disetujui' : 'Reservasi Ditolak' }}
  </h2>
  <p style="margin:0;color:#64748b;font-size:15px;">
    {{ $isApproved ? 'Pengajuan reservasi buku Anda telah disetujui.' : 'Pengajuan reservasi buku Anda belum dapat disetujui.' }}
  </p>
</div>

<p style="color:#334155;font-size:15px;">
  Halo <strong>{{ $userName }}</strong>, berikut adalah rincian status reservasi buku Anda:
</p>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:35%;">Judul Buku:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $bookTitle }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;padding:3px 10px;background:{{ $isApproved ? '#dcfce7' : '#fee2e2' }};color:{{ $isApproved ? '#15803d' : '#b91c1c' }};border-radius:9999px;font-size:12px;font-weight:600;text-transform:capitalize;">
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
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $reservation->seat_number }}</td>
    </tr>
    @endif
    @else
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alasan Penolakan:</td>
      <td style="padding:6px 0;color:#b91c1c;font-weight:600;">
        {{ $reservation->rejection_reason ?: 'Tidak ada alasan khusus yang dicantumkan.' }}
      </td>
    </tr>
    @endif
  </table>
</div>

@if($isApproved)
<p style="color:#64748b;font-size:13px;line-height:1.6;">
  Silakan kunjungi meja layanan perpustakaan untuk pengambilan buku atau konfirmasi kursi Anda.
</p>
@endif

<div style="text-align:center;margin-top:28px;margin-bottom:16px;">
  <a href="{{ route('user.reservations') }}" style="display:inline-block;background:{{ $isApproved ? '#16a34a' : '#2563eb' }};color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
    Lihat Daftar Reservasi &rarr;
  </a>
</div>
@endsection
