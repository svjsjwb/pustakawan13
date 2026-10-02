<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password – Perpustakaan Tiga Serangkai</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .auth-card { width: min(440px, calc(100vw - 40px)); }
        .auth-card .back { display:inline-block; margin-top:18px; color:inherit; text-decoration:none; font-size:14px; opacity:.8; }
        .auth-card .hint { color:#6b7280; font-size:14px; line-height:1.5; margin-bottom:22px; }
        .alert-error,.alert-success { margin-bottom:16px; padding:12px 14px; border-radius:10px; font-size:14px; }
        .alert-error { background:#fee2e2; color:#991b1b; }
        .alert-success { background:#dcfce7; color:#166534; }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-background"></div>
    <div class="login-panel">
        <div class="login-card auth-card">
            <h1>RESET PASSWORD</h1>
            <p class="hint">Masukkan email akunmu. Kami akan mengirim kode OTP 6 digit untuk verifikasi.</p>

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email akun" required autocomplete="email">
                </div>
                <button type="submit">KIRIM OTP</button>
            </form>

            <a class="back" href="{{ route('login') }}">← Kembali ke login</a>
        </div>
    </div>
</div>
</body>
</html>
