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
  <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:32px 24px;text-align:center;">
    <div style="display:inline-block;padding:6px 18px;background:rgba(255,255,255,0.18);border-radius:9999px;color:#ffffff;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:16px;">
      📚 Perpustakaan Tiga Serangkai
    </div>
    <div style="text-align:center;">
      @php
        $logoPath = public_path('images/logo-tiga-serangkai.png');
        $logoSrc = (isset($message) && is_object($message) && method_exists($message, 'embed') && file_exists($logoPath)) 
          ? $message->embed($logoPath) 
          : asset('images/logo-tiga-serangkai.png');
      @endphp
      <div style="display:inline-block;background:#ffffff;padding:10px 18px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <img src="{{ $logoSrc }}" alt="Logo Tiga Serangkai" style="height:56px;max-width:200px;width:auto;display:block;margin:0 auto;border:0;outline:none;text-decoration:none;">
      </div>
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
