<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Lupa Password – Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Reset password akun Perpustakaan Tiga Serangkai">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            background: #093033;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .auth-card-panel {
            position: relative;
            z-index: 2;
            width: min(440px, 92vw);
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.45);
            padding: 40px 36px 36px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .auth-header {
            margin-bottom: 22px;
        }
        .auth-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.4px;
            margin: 0 0 6px;
        }
        .auth-header p {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }
        .alert-box {
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.45;
            margin-bottom: 18px;
        }
        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            color: #1f706e;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .back-link:hover {
            color: #165b59;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<main class="login-page">
    <div class="login-background"></div>

    <div class="auth-card-panel">
        <div class="auth-header">
            <h2>LUPA PASSWORD</h2>
            <p>Masukkan email yang terdaftar. Kami akan mengirimkan kode OTP 6 digit untuk verifikasi.</p>
        </div>

        @if ($errors->any())
            <div class="alert-box alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert-box alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-wrapper">
                    <span class="input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email akun" required autocomplete="email" autofocus>
                </div>
            </div>

            <button type="submit" class="login-button" style="margin-top: 10px;">
                <span>KIRIM OTP</span>
            </button>
        </form>

        <div>
            <a class="back-link" href="{{ route('login') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Login
            </a>
        </div>
    </div>
</main>

</body>
</html>
