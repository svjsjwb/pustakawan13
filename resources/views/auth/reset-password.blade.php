<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru – Perpustakaan Tiga Serangkai</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .auth-card { width:min(440px,calc(100vw - 40px)); }
        .hint { color:#6b7280; font-size:14px; line-height:1.5; margin-bottom:22px; }
        .alert-error { margin-bottom:16px; padding:12px 14px; border-radius:10px; font-size:14px; background:#fee2e2; color:#991b1b; }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-background"></div>
    <div class="login-panel">
        <div class="login-card auth-card">
            <h1>PASSWORD BARU</h1>
            <p class="hint">Buat password baru untuk akunmu. Gunakan minimal 8 karakter.</p>

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input id="password" type="password" name="password" placeholder="Masukkan password baru" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password baru" required autocomplete="new-password">
                </div>
                <button type="submit">UBAH PASSWORD</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
