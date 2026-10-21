@extends('emails.layout')

@section('content')
@php
  $book = $borrowing->details->first()?->book;
  $bookTitle = $book?->judul_buku ?? 'Koleksi Perpustakaan';
  $author = $book?->penulis ?? '-';
  $borrowDate = $borrowing->borrowed_at ? \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
  $dueDate = $borrowing->due_at ? \Carbon\Carbon::parse($borrowing->due_at)->translatedFormat('d F Y') : '-';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#e0f2fe;font-size:32px;margin-bottom:14px;border:2px solid #bae6fd;">
    📚
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#0369a1;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Pengajuan Peminjaman Berhasil</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $user->name }}</strong>, permohonan pinjam buku Anda telah kami terima.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#0284c7;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📖 Rincian Buku yang Diajukan</span>
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
      <td style="padding:6px 0;color:#64748b;">Tanggal Pengajuan:</td>
      <td style="padding:6px 0;color:#334155;">{{ $borrowDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Estimasi Tenggat Kembali:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $dueDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status Saat Ini:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ⏳ Menunggu Konfirmasi Petugas
        </span>
      </td>
    </tr>
  </table>
</div>

<p style="color:#64748b;font-size:14px;line-height:1.6;margin-bottom:26px;">
  Petugas perpustakaan sedang memproses ketersediaan fisik buku Anda. Begitu disetujui, Anda akan menerima email pemberitahuan untuk mengambil buku di meja sirkulasi.
</p>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.loans') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#0284c7,#0ea5e9);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(14,165,233,0.25);">
    Pantau Status Pinjaman &rarr;
  </a>
</div>
@endsection
