<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailTitle ?? config('app.name', 'Perpustakaan') }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;line-height:1.6;">
<div style="max-width:600px;margin:32px auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);border:1px solid #e2e8f0;">

  {{-- Header --}}
  <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:36px 32px;text-align:center;">
    <div style="display:inline-block;padding:8px 16px;background:rgba(255,255,255,0.15);border-radius:9999px;color:#ffffff;font-size:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:12px;">
      Sistem Informasi Perpustakaan
    </div>
    <div style="font-size:24px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;margin:0;">
      📚 {{ config('app.name', 'Perpustakaan') }}
    </div>
  </div>

  {{-- Body --}}
  <div style="padding:36px 32px;">
    @yield('content')
  </div>

  {{-- Footer --}}
  <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 32px;text-align:center;">
    <p style="margin:0 0 8px 0;font-size:12px;color:#64748b;line-height:1.5;">
      Email ini dikirim otomatis oleh sistem notifikasi perpustakaan.<br>
      Jika Anda membutuhkan bantuan, silakan hubungi bagian administrasi perpustakaan.
    </p>
    <p style="margin:0;font-size:11px;color:#94a3b8;">
      &copy; {{ date('Y') }} <strong>{{ config('app.name', 'Perpustakaan') }}</strong>. Semua hak dilindungi.
    </p>
  </div>

</div>
</body>
</html>
