<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Status Perpanjangan Peminjaman</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;line-height:1.7;">
@php
  $isRejected = $status === 'ditolak';
  $headerBg   = $isRejected ? 'linear-gradient(135deg,#991b1b,#dc2626)' : 'linear-gradient(135deg,#166534,#15803d)';
  $accentColor = $isRejected ? '#dc2626' : '#15803d';
  $bgColor     = $isRejected ? '#fef2f2' : '#f0fdf4';
  $borderColor = $isRejected ? '#ef4444' : '#22c55e';
  $badgeBg     = $isRejected ? '#fee2e2' : '#dcfce7';
  $badgeColor  = $isRejected ? '#991b1b' : '#166534';
  $icon        = $isRejected ? '?' : '?';
  $statusLabel = $isRejected ? 'Ditolak' : ($status === 'mandiri' ? 'Berhasil (Mandiri)' : 'Disetujui');
@endphp
<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <div style="background:{{ $headerBg }};padding:32px 28px;text-align:center;">
    <div style="font-size:24px;font-weight:800;color:#fff;">?? Perpustakaan Tiga Serangkai</div>
    <div style="color:rgba(255,255,255,.7);font-size:13px;margin-top:4px;">Sistem Informasi Perpustakaan</div>
  </div>
  <div style="padding:32px 28px;">
    <h2 style="margin:0 0 8px;font-size:20px;color:{{ $accentColor }};">{{ $icon }} Perpanjangan Peminjaman {{ $isRejected ? 'Ditolak' : 'Berhasil' }}</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:14px;">{{ now()->format('d M Y, H:i') }} WIB</p>
    <p>Halo <strong>{{ $user->name }}</strong>,</p>
    @if($isRejected)
    <p>Mohon maaf, permintaan perpanjangan peminjaman buku Anda <strong>tidak dapat disetujui</strong>.</p>
    @else
    <p>Perpanjangan peminjaman buku Anda telah <strong>berhasil</strong>. Tanggal pengembalian telah diperbarui.</p>
    @endif
    @php $book = $borrowing->details->first()?->book; @endphp
    <div style="background:{{ $bgColor }};border-left:4px solid {{ $borderColor }};border-radius:8px;padding:18px 20px;margin:20px 0;">
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <tr><td style="color:#64748b;padding:4px 0;width:150px;">Judul Buku</td><td style="font-weight:600;">{{ $book?->judul_buku ?? 'Buku' }}</td></tr>
        <tr><td style="color:#64748b;padding:4px 0;">Penulis</td><td>{{ $book?->penulis ?? '-' }}</td></tr>
        @if(!$isRejected)
        <tr><td style="color:#64748b;padding:4px 0;">Batas Kembali Baru</td><td><strong style="color:{{ $accentColor }};">{{ $borrowing->due_at?->format('d M Y') ?? '-' }}</strong></td></tr>
        @endif
        <tr><td style="color:#64748b;padding:4px 0;">Status</td><td><span style="background:{{ $badgeBg }};color:{{ $badgeColor }};padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">{{ $statusLabel }}</span></td></tr>
        @if($notes)
        <tr><td style="color:#64748b;padding:4px 0;vertical-align:top;">Catatan</td><td>{{ $notes }}</td></tr>
        @endif
      </table>
    </div>
    @if($isRejected)
    <p>Pastikan buku dikembalikan sebelum tanggal jatuh tempo yang berlaku.</p>
    @else
    <p>Pastikan buku dikembalikan tepat waktu sebelum <strong>{{ $borrowing->due_at?->format('d M Y') }}</strong>.</p>
    @endif
    <div style="text-align:center;margin:28px 0;">
      <a href="{{ config('app.url') }}/user/loans" style="background:#1d4ed8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:14px;display:inline-block;">Lihat Peminjaman Saya</a>
    </div>
    <p style="margin-bottom:0;">Terima kasih,<br><strong>Tim Perpustakaan Tiga Serangkai</strong></p>
  </div>
  <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 28px;text-align:center;">
    <p style="margin:0;font-size:12px;color:#94a3b8;">Email ini dikirim otomatis. Jangan membalas email ini.<br>&copy; {{ date('Y') }} Perpustakaan Tiga Serangkai.</p>
  </div>
</div>
</body></html>
