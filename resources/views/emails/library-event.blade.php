@extends('emails.layout')

@section('content')
@php
  $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#eff6ff;font-size:32px;margin-bottom:14px;border:2px solid #bfdbfe;">
    📢
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#1e40af;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Pengumuman & Acara Perpustakaan</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Perpustakaan Tiga Serangkai mengundang Anda untuk bergabung dalam kegiatan berikut:</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#2563eb;text-transform:uppercase;letter-spacing:1px;font-weight:700;">🗓️ Detail Acara</span>
  </div>
  <table role="presentation" class="info-table" style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Nama Kegiatan:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:800;font-size:15px;">{{ $event->name }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Pelaksanaan:</td>
      <td style="padding:6px 0;color:#1d4ed8;font-weight:700;">{{ $eventDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Lokasi Kegiatan:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $event->location }}</td>
    </tr>
  </table>
</div>

<div style="background:#eff9f9;border-left:4px solid #278889;border-radius:8px;padding:18px 20px;margin-bottom:26px;">
  <h4 style="margin:0 0 8px 0;color:#134e4a;font-size:14px;font-weight:700;">Deskripsi & Informasi Lengkap:</h4>
  <p style="margin:0;color:#1e5e60;font-size:14px;line-height:1.7;white-space:pre-line;">{{ $event->description }}</p>
</div>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('dashboard') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#1e40af,#2563eb);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(37,99,235,0.25);">
    Kunjungi Perpustakaan &rarr;
  </a>
</div>
@endsection
