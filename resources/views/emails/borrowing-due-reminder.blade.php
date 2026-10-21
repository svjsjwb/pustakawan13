@extends('emails.layout')

@section('content')
@php
  $userName = $borrowing->user?->name ?? $borrowing->member?->name ?? 'Anggota';
  $bookTitle = $borrowing->details->first()?->book?->title ?? $borrowing->book?->title ?? 'Buku Perpustakaan';
  $author = $borrowing->details->first()?->book?->penulis ?? $borrowing->book?->penulis ?? '-';
  $dueDate = $borrowing->due_at ? \Carbon\Carbon::parse($borrowing->due_at)->translatedFormat('l, d F Y') : 'Besok';
@endphp

<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#fef3c7;font-size:32px;margin-bottom:14px;border:2px solid #fde68a;">
    ⏰
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#92400e;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Pengingat Jatuh Tempo (H-1)</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $userName }}</strong>, masa peminjaman buku Anda akan berakhir besok.</p>
</div>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #fef3c7;padding-bottom:10px;">
    <span style="font-size:12px;color:#b45309;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📚 Detail Buku yang Dipinjam</span>
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
      <td style="padding:6px 0;color:#64748b;">Batas Waktu Pengembalian:</td>
      <td style="padding:6px 0;color:#dc2626;font-weight:800;font-size:15px;">{{ $dueDate }}</td>
    </tr>
  </table>
</div>

<div style="background:#eff9f9;border-left:4px solid #278889;border-radius:8px;padding:16px 20px;margin-bottom:26px;">
  <p style="margin:0;color:#1e5e60;font-size:13px;line-height:1.6;">
    💡 <strong>Ingin Memperpanjang Masa Pinjam?</strong> Jika belum mencapai batas maksimal perpanjangan, Anda dapat mengajukan perpanjangan online secara mandiri melalui halaman pinjaman akun Anda sebelum masa pinjam habis.
  </p>
</div>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.loans') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#d97706,#f59e0b);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(245,158,11,0.25);">
    Kelola Peminjaman Saya &rarr;
  </a>
</div>
@endsection
