@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:#fef3c7;font-size:28px;margin-bottom:12px;">
    ⏰
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">Pengingat Jatuh Tempo (H-1)</h2>
  <p style="margin:0;color:#64748b;font-size:15px;">Buku pinjaman Anda akan jatuh tempo besok hari.</p>
</div>

@php
  $userName = $borrowing->user?->name ?? $borrowing->member?->name ?? 'Anggota';
  $bookTitle = $borrowing->details->first()?->book?->title ?? $borrowing->book?->title ?? 'Buku Perpustakaan';
  $author = $borrowing->details->first()?->book?->penulis ?? $borrowing->book?->penulis ?? '-';
  $dueDate = $borrowing->due_at ? \Carbon\Carbon::parse($borrowing->due_at)->translatedFormat('l, d F Y') : 'Besok';
@endphp

<p style="color:#334155;font-size:15px;">
  Halo <strong>{{ $userName }}</strong>, masa peminjaman untuk buku koleksi berikut akan berakhir besok. Mohon segera mengembalikan buku ke perpustakaan untuk menghindari denda keterlambatan.
</p>

<div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:12px;padding:20px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:35%;">Judul Buku:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $bookTitle }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Penulis:</td>
      <td style="padding:6px 0;color:#334155;">{{ $author }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Batas Waktu:</td>
      <td style="padding:6px 0;color:#b45309;font-weight:700;">{{ $dueDate }}</td>
    </tr>
  </table>
</div>

<p style="color:#64748b;font-size:13px;line-height:1.6;">
  <em>Catatan:</em> Jika Anda masih membutuhkan buku ini dan belum mencapai batas perpanjangan, Anda dapat mengajukan perpanjangan melalui halaman pinjaman akun Anda.
</p>

<div style="text-align:center;margin-top:28px;margin-bottom:16px;">
  <a href="{{ route('user.loans') }}" style="display:inline-block;background:#f59e0b;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(245,158,11,0.2);">
    Periksa Peminjaman Saya &rarr;
  </a>
</div>
@endsection
