@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#ecfdf5;font-size:32px;margin-bottom:14px;border:2px solid #a7f3d0;">
    ✅
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#065f46;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Pendaftaran Akun Disetujui!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $user->name }}</strong>, akun perpustakaan Anda telah diverifikasi dan disetujui oleh Administrator.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px;margin-bottom:24px;">
  <div style="margin-bottom:14px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
    <span style="font-size:12px;color:#059669;text-transform:uppercase;letter-spacing:1px;font-weight:700;">👤 Informasi Akun Aktif</span>
  </div>
  <table role="presentation" class="info-table" style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:38%;">Nama Anggota:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $user->name }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Email Terdaftar:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $user->email }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Status Keanggotaan:</td>
      <td style="padding:6px 0;">
        <span style="display:inline-block;background:#d1fae5;color:#065f46;font-size:12px;font-weight:700;padding:3px 10px;border-radius:9999px;">
          ✓ Aktif (Member Penuh)
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Persetujuan:</td>
      <td style="padding:6px 0;color:#334155;font-weight:600;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
    </tr>
  </table>
</div>

<div style="background:#f0fdf4;border-left:4px solid #10b981;border-radius:8px;padding:16px 20px;margin-bottom:26px;">
  <p style="margin:0;color:#065f46;font-size:13px;line-height:1.6;">
    🎉 <strong>Anda Sekarang Dapat Menggunakan Semua Fitur:</strong> Silakan login untuk mulai meminjam buku favorit Anda, mengajukan reservasi, serta memanfaatkan seluruh fasilitas perpustakaan.
  </p>
</div>

<div style="text-align:center;margin-bottom:24px;">
  <a href="{{ route('login') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#059669,#10b981);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(16,185,129,0.25);">
    Login & Mulai Pinjam Buku &rarr;
  </a>
</div>
@endsection
