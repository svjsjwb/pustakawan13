<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP – Perpustakaan Tiga Serangkai</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .auth-card { width:min(440px,calc(100vw - 40px)); }
        .hint { color:#6b7280; font-size:14px; line-height:1.5; margin-bottom:22px; }
        .otp-input { text-align:center; font-size:28px; letter-spacing:10px; font-weight:700; }
        .alert-error,.alert-success { margin-bottom:16px; padding:12px 14px; border-radius:10px; font-size:14px; }
        .alert-error { background:#fee2e2; color:#991b1b; }
        .alert-success { background:#dcfce7; color:#166534; }
        .secondary { margin-top:14px; text-align:center; }
        .secondary button,.back { border:0; background:none; color:inherit; text-decoration:underline; cursor:pointer; font-size:14px; }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-background"></div>
    <div class="login-panel">
        <div class="login-card auth-card">
            <h1>VERIFIKASI OTP</h1>
            <p class="hint">Kode OTP telah dikirim ke <strong>{{ $email }}</strong>. Kode berlaku selama 5 menit.</p>

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.otp.verify') }}">
                @csrf
                <div class="form-group">
                    <label for="otp">Kode OTP</label>
                    <input class="otp-input" id="otp" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="000000" required autofocus>
                </div>
                <button type="submit">VERIFIKASI</button>
            </form>

            <div class="secondary">
                <form method="POST" action="{{ route('password.otp.resend') }}">
                    @csrf
                    <button type="submit">Kirim ulang OTP</button>
                </form>
                <a class="back" href="{{ route('password.request') }}">Gunakan email lain</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
