@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:#eff6ff;font-size:28px;margin-bottom:12px;">
    👋
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">Selamat Datang, {{ $user->name }}!</h2>
  <p style="margin:0;color:#64748b;font-size:15px;">Akun Anda di <strong>{{ config('app.name', 'Perpustakaan') }}</strong> berhasil dibuat.</p>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:24px;">
  <h3 style="margin:0 0 12px 0;font-size:14px;color:#475569;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Detail Akun Anda</h3>
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:35%;">Nama:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $user->name }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Email Terdaftar:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ $user->email }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal Registrasi:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ now()->translatedFormat('d F Y') }}</td>
    </tr>
  </table>
</div>

<p style="color:#475569;font-size:14px;line-height:1.7;">
  Email ini akan digunakan untuk mengirim informasi reservasi, persetujuan, tanda terima pengembalian buku, event perpustakaan, serta pengingat jatuh tempo secara otomatis.
</p>

<div style="text-align:center;margin-top:28px;margin-bottom:16px;">
  <a href="{{ route('login') }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(37,99,235,0.2);">
    Masuk ke Akun Anda &rarr;
  </a>
</div>
@endsection
