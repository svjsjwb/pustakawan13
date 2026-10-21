@extends('emails.layout')

@section('content')
@php
  $book = $reservation->book;
  $bookTitle = $book?->judul_buku ?? $book?->title ?? 'Buku Perpustakaan';
  $author = $book?->penulis ?? '-';
  $reserveDate = $reservation->reserved_at ? \Carbon\Carbon::parse($reservation->reserved_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
  $expireDate = $reservation->expires_at ? \Carbon\Carbon::parse($reservation->expires_at)->translatedFormat('d F Y') : '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#e0f2fe;font-size:32px;margin-bottom:14px;border:2px solid #bae6fd;">
    🔖
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#0369a1;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Reservasi Berhasil Diajukan</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $user->name }}</strong>, reservasi buku Anda telah berhasil kami catat.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#0284c7;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📑 Detail Reservasi</span>
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
      <td style="padding:6px 0;color:#64748b;">Tanggal Reservasi:</td>
      <td style="padding:6px 0;color:#334155;">{{ $reserveDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Berlaku Hingga:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $expireDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ⏳ Menunggu Persetujuan
        </span>
      </td>
    </tr>
  </table>
</div>

<p style="color:#64748b;font-size:14px;line-height:1.6;margin-bottom:26px;">
  Anda akan menerima email pemberitahuan otomatis begitu petugas memverifikasi ketersediaan buku reservasi ini.
</p>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.reservations') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#0284c7,#0ea5e9);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(14,165,233,0.25);">
    Lihat Reservasi Saya &rarr;
  </a>
</div>
@endsection
