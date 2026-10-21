@extends('emails.layout')

@section('content')
@php
  $book = $borrowing->details->first()?->book;
  $bookTitle = $book?->judul_buku ?? 'Koleksi Perpustakaan';
  $author = $book?->penulis ?? '-';
  $borrowDate = $borrowing->borrowed_at ? \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
  $dueDate = $borrowing->due_at ? \Carbon\Carbon::parse($borrowing->due_at)->translatedFormat('l, d F Y') : '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#ecfdf5;font-size:32px;margin-bottom:14px;border:2px solid #a7f3d0;">
    🎉
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#065f46;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Peminjaman Buku Disetujui!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Kabar gembira untuk <strong>{{ $user->name }}</strong>! Pengajuan peminjaman buku Anda telah disetujui.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#059669;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📚 Detail Buku & Pengambilan</span>
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
        <span style="display:inline-block;background:#d1fae5;color:#065f46;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ✓ Disetujui (Siap Diambil)
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Pinjam:</td>
      <td style="padding:6px 0;color:#334155;">{{ $borrowDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Batas Pengembalian:</td>
      <td style="padding:6px 0;color:#dc2626;font-weight:800;font-size:15px;">{{ $dueDate }}</td>
    </tr>
  </table>
</div>

<div style="background:#eff9f9;border-left:4px solid #278889;border-radius:8px;padding:16px 20px;margin-bottom:26px;">
  <p style="margin:0;color:#1e5e60;font-size:13px;line-height:1.6;">
    📍 <strong>Lokasi Pengambilan:</strong> Silakan tunjukkan nama atau email Anda ke petugas di <strong>Meja Layanan Sirkulasi Perpustakaan</strong> untuk mengambil buku fisik.
  </p>
</div>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.loans') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#059669,#10b981);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(16,185,129,0.25);">
    Buka Rincian Peminjaman &rarr;
  </a>
</div>
@endsection
