<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Reservasi Berhasil</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;line-height:1.7;">
<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
  <div style="background:linear-gradient(135deg,#1e40af,#1d4ed8);padding:32px 24px;text-align:center;">
    <div style="display:inline-block;padding:6px 18px;background:rgba(255,255,255,0.18);border-radius:9999px;color:#ffffff;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:14px;">
      📚 Perpustakaan Tiga Serangkai
    </div>
    <div style="text-align:center;">
      @php
        $logoPath = public_path('images/logo-tiga-serangkai.png');
        $logoSrc = (isset($message) && is_object($message) && method_exists($message, 'embed') && file_exists($logoPath)) ? $message->embed($logoPath) : asset('images/logo-tiga-serangkai.png');
      @endphp
      <div style="display:inline-block;background:#ffffff;padding:10px 18px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <img src="{{ $logoSrc }}" alt="Logo Tiga Serangkai" style="height:56px;max-width:200px;width:auto;display:block;margin:0 auto;border:0;outline:none;text-decoration:none;">
      </div>
    </div>
  </div>
  <div style="padding:32px 28px;">
    <h2 style="margin:0 0 8px;font-size:20px;color:#1e40af;">📖 Reservasi Buku Berhasil Diajukan</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:14px;">{{ now()->format('d M Y, H:i') }} WIB</p>
    <p>Halo <strong>{{ $user->name }}</strong>,</p>
    <p>Reservasi buku Anda telah berhasil diajukan dan sedang menunggu konfirmasi dari petugas perpustakaan.</p>
    <div style="background:#eff6ff;border-left:4px solid #3b82f6;border-radius:8px;padding:18px 20px;margin:20px 0;">
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <tr><td style="color:#64748b;padding:4px 0;width:150px;">Judul Buku</td><td style="font-weight:600;">{{ $reservation->book?->judul_buku ?? 'Buku' }}</td></tr>
        <tr><td style="color:#64748b;padding:4px 0;">Penulis</td><td>{{ $reservation->book?->penulis ?? '-' }}</td></tr>
        <tr><td style="color:#64748b;padding:4px 0;">Tanggal Reservasi</td><td>{{ \Carbon\Carbon::parse($reservation->reserved_at)->format('d M Y') }}</td></tr>
        <tr><td style="color:#64748b;padding:4px 0;">Berlaku Hingga</td><td><strong>{{ \Carbon\Carbon::parse($reservation->expires_at)->format('d M Y') }}</strong></td></tr>
        <tr><td style="color:#64748b;padding:4px 0;">Status</td><td><span style="background:#fef9c3;color:#854d0e;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Menunggu Persetujuan</span></td></tr>
      </table>
    </div>
    <p>Anda akan mendapat notifikasi kembali ketika reservasi Anda disetujui atau ditolak oleh petugas perpustakaan.</p>
    <div style="text-align:center;margin:28px 0;">
      <a href="{{ config('app.url') }}/user/reservations" style="background:#1d4ed8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:14px;display:inline-block;">Lihat Reservasi Saya</a>
    </div>
    <p style="margin-bottom:0;">Terima kasih,<br><strong>Tim Perpustakaan Tiga Serangkai</strong></p>
  </div>
  <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 28px;text-align:center;">
    <p style="margin:0;font-size:12px;color:#94a3b8;">Email ini dikirim otomatis. Jangan membalas email ini.<br>&copy; {{ date('Y') }} Perpustakaan Tiga Serangkai.</p>
  </div>
</div>
</body></html>
