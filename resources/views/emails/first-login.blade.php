@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:#ecfdf5;font-size:28px;margin-bottom:12px;">
    🎉
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">Selamat Datang, {{ $user->name }}!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;">Ini adalah login pertama kali Anda ke sistem perpustakaan.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:24px;">
  <h3 style="margin:0 0 12px 0;font-size:14px;color:#475569;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Informasi Sesi Login</h3>
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:35%;">Waktu Login:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ now()->translatedFormat('l, d F Y H:i') }} WIB</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alamat Email:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $user->email }}</td>
    </tr>
    @if(!empty($ipAddress))
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alamat IP:</td>
      <td style="padding:6px 0;color:#0f172a;font-family:monospace;">{{ $ipAddress }}</td>
    </tr>
    @endif
  </table>
</div>

<div style="margin-bottom:28px;">
  <h4 style="margin:0 0 12px 0;color:#0f172a;font-size:15px;font-weight:600;">Apa yang bisa Anda lakukan sekarang?</h4>
  <ul style="margin:0;padding-left:20px;color:#475569;font-size:14px;line-height:1.8;">
    <li><strong>Jelajahi Katalog:</strong> Temukan ribuan judul buku fisik dan digital.</li>
    <li><strong>Peminjaman Online:</strong> Ajukan peminjaman buku langsung dari gadget Anda.</li>
    <li><strong>Reservasi Kursi & Buku:</strong> Pesan tempat dan buku favorit sebelum kehabisan.</li>
    <li><strong>Pantau Riwayat:</strong> Dapatkan pengingat otomatis sebelum batas pengembalian.</li>
  </ul>
</div>

<div style="text-align:center;margin-bottom:24px;">
  <a href="{{ route('user.home') }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
    Mulai Jelajahi Perpustakaan &rarr;
  </a>
</div>

<p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;">
  Jika aktivitas login ini bukan dilakukan oleh Anda, segera hubungi admin perpustakaan untuk mengamankan akun Anda.
</p>
@endsection
