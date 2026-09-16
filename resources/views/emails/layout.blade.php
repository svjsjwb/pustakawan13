{{-- Reusable email header --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailTitle ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;line-height:1.7;">
<div style="max-width:600px;margin:32px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

  {{-- Header --}}
  <div style="background:linear-gradient(135deg,#1e40af 0%,#1d4ed8 100%);padding:32px 28px;text-align:center;">
    <div style="font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">
      ?? Perpustakaan Tiga Serangkai
    </div>
    <div style="color:#bfdbfe;font-size:13px;margin-top:4px;">Sistem Informasi Perpustakaan</div>
  </div>

  {{-- Body --}}
  <div style="padding:32px 28px;">
    @yield('content')
  </div>

  {{-- Footer --}}
  <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 28px;text-align:center;">
    <p style="margin:0;font-size:12px;color:#94a3b8;">
      Email ini dikirim otomatis oleh sistem. Jangan membalas email ini.<br>
      &copy; {{ date('Y') }} <strong>Perpustakaan Tiga Serangkai</strong>. Semua hak dilindungi.
    </p>
  </div>

</div>
</body>
</html>
