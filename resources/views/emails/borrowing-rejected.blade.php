@extends('emails.layout')

@section('content')
@php
  $book = $borrowing->details->first()?->book;
  $bookTitle = $book?->judul_buku ?? 'Koleksi Perpustakaan';
  $author = $book?->penulis ?? '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#ffe4e6;font-size:32px;margin-bottom:14px;border:2px solid #fecdd3;">
    ⚠️
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#9f1239;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Peminjaman Buku Ditolak</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $user->name }}</strong>, kami mohon maaf permohonan pinjam buku Anda belum dapat disetujui.</p>
</div>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #fed7aa;padding-bottom:10px;">
    <span style="font-size:12px;color:#9f1239;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📖 Rincian Buku</span>
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
        <span style="display:inline-block;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ✕ Ditolak
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alasan:</td>
      <td style="padding:6px 0;color:#9f1239;font-weight:600;">{{ $reason }}</td>
    </tr>
  </table>
</div>

<p style="color:#64748b;font-size:14px;line-height:1.6;margin-bottom:26px;">
  Anda dapat memilih judul buku alternatif lain di katalog kami atau mengajukan peminjaman kembali saat stok fisik telah tersedia.
</p>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.catalog') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#217475,#278889);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(39,136,137,0.25);">
    Jelajahi Katalog Buku Lain &rarr;
  </a>
</div>
@endsection
