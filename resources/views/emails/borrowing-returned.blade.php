@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:#f0fdf4;font-size:28px;margin-bottom:12px;">
    ✅
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">Buku Berhasil Dikembalikan!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;">Terima kasih telah mengembalikan koleksi buku perpustakaan.</p>
</div>

@php
  $bookTitle = $borrowing->details->first()?->book?->title ?? $borrowing->book?->title ?? 'Buku Perpustakaan';
  $bookAuthor = $borrowing->details->first()?->book?->penulis ?? $borrowing->book?->penulis ?? '-';
  $borrowedDate = $borrowing->borrowed_at ? \Carbon\Carbon::parse($borrowing->borrowed_at)->translatedFormat('d M Y') : '-';
  $returnedDate = $borrowing->returned_at ? \Carbon\Carbon::parse($borrowing->returned_at)->translatedFormat('d M Y') : now()->translatedFormat('d M Y');
  $dueDate = $borrowing->due_at ? \Carbon\Carbon::parse($borrowing->due_at)->translatedFormat('d M Y') : '-';
@endphp

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:24px;">
  <h3 style="margin:0 0 14px 0;font-size:14px;color:#475569;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Rincian Pengembalian</h3>
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Judul Buku:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $bookTitle }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Penulis:</td>
      <td style="padding:6px 0;color:#334155;">{{ $bookAuthor }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Pinjam:</td>
      <td style="padding:6px 0;color:#334155;">{{ $borrowedDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Batas Waktu (Due):</td>
      <td style="padding:6px 0;color:#334155;">{{ $dueDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Kembali:</td>
      <td style="padding:6px 0;color:#16a34a;font-weight:700;">{{ $returnedDate }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;padding:3px 10px;background:#dcfce7;color:#15803d;border-radius:9999px;font-size:12px;font-weight:600;">
          Dikembalikan
        </span>
      </td>
    </tr>
  </table>
</div>

<div style="text-align:center;margin-top:28px;">
  <a href="{{ route('user.loans') }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
    Lihat Riwayat Peminjaman &rarr;
  </a>
</div>
@endsection
