@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#e6f7f7;font-size:30px;margin-bottom:14px;border:2px solid #b7e4e4;">
    🎉
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#132a40;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Selamat Datang, {{ $user->name }}!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Aktivitas login pertama kali ke akun perpustakaan Anda berhasil terdeteksi.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#278889;text-transform:uppercase;letter-spacing:1px;font-weight:700;">🔐 Rincian Sesi Login Pertama</span>
  </div>
  <table role="presentation" class="info-table" style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Waktu Login:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ now()->translatedFormat('l, d F Y H:i') }} WIB</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Email Akun:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $user->email }}</td>
    </tr>
    @if(!empty($ipAddress))
    <tr>
      <td style="padding:6px 0;color:#64748b;">Alamat IP:</td>
      <td style="padding:6px 0;color:#334155;font-family:monospace;font-size:13px;font-weight:600;">{{ $ipAddress }}</td>
    </tr>
    @endif
  </table>
</div>

<div style="margin-bottom:26px;">
  <h4 style="margin:0 0 14px 0;color:#0f172a;font-size:15px;font-weight:700;">Layanan yang dapat Anda nikmati:</h4>
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td style="padding:6px 0;font-size:14px;color:#334155;line-height:1.6;">
        📖 <strong>Katalog Lengkap:</strong> Akses pencarian ribuan koleksi buku pendidikan, umum, fiksi & non-fiksi.
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;font-size:14px;color:#334155;line-height:1.6;">
        ⚡ <strong>Peminjaman Mudah:</strong> Ajukan pinjam buku secara online tanpa antre.
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;font-size:14px;color:#334155;line-height:1.6;">
        🔔 <strong>Notifikasi Otomatis:</strong> Dapatkan email berkala status pinjaman & pengingat H-1 tenggat kembali.
      </td>
    </tr>
  </table>
</div>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('user.home') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#217475,#278889);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(39,136,137,0.25);">
    Mulai Eksplorasi Perpustakaan &rarr;
  </a>
</div>
@endsection
