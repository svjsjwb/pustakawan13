@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#e6f7f7;font-size:30px;margin-bottom:14px;border:2px solid #b7e4e4;">
    👋
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#132a40;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Selamat Datang, {{ $user->name }}!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Akun pendaftaran Anda di <strong>{{ config('app.name', 'Perpustakaan Tiga Serangkai') }}</strong> berhasil dibuat.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#278889;text-transform:uppercase;letter-spacing:1px;font-weight:700;">📋 Data Registrasi Akun</span>
  </div>
  <table role="presentation" class="info-table" style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Nama Lengkap:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $user->name }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Email Terdaftar:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $user->email }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status Awal:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ⏳ Menunggu Persetujuan Admin
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Registrasi:</td>
      <td style="padding:6px 0;color:#334155;font-weight:600;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
    </tr>
  </table>
</div>

<div style="background:#eff9f9;border-left:4px solid #278889;border-radius:8px;padding:16px 20px;margin-bottom:26px;">
  <p style="margin:0;color:#1e5e60;font-size:13px;line-height:1.6;">
    💡 <strong>Informasi Persetujuan:</strong> Petugas perpustakaan akan memverifikasi akun Anda. Setelah disetujui, Anda akan menerima email konfirmasi dan dapat langsung meminjam buku fisik, digital, serta reservasi ruang baca.
  </p>
</div>

<div style="text-align:center;margin-bottom:24px;">
  <a href="{{ route('login') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#217475,#278889);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(39,136,137,0.25);">
    Masuk ke Portal Perpustakaan &rarr;
  </a>
</div>
@endsection
