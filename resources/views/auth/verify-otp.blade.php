<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Verifikasi OTP – Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Verifikasi OTP Reset Password Perpustakaan Tiga Serangkai">
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
        .auth-header strong {
            color: #0f172a;
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
        .otp-input-field {
            width: 100%;
            height: 52px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
            padding-left: 12px;
        }
        .otp-input-field:focus {
            border-color: #1f706e;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(31, 112, 110, 0.15);
        }
        .resend-box {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }
        .resend-btn {
            background: none;
            border: none;
            color: #1f706e;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            font-size: 13px;
            font-family: inherit;
            text-decoration: underline;
            transition: color 0.2s ease;
        }
        .resend-btn:hover {
            color: #165b59;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #64748b;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .back-link:hover {
            color: #0f172a;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<main class="login-page">
    <div class="login-background"></div>

    <div class="auth-card-panel">
        <div class="auth-header">
            <h2>VERIFIKASI OTP</h2>
            <p>Kode OTP telah dikirim ke <strong>{{ $email }}</strong>. Kode berlaku selama 5 menit.</p>
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

        <form method="POST" action="{{ route('password.otp.verify') }}" class="login-form">
            @csrf

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="otp" style="text-align: center; display: block; margin-bottom: 10px;">Masukkan 6 Digit Kode OTP</label>
                <input class="otp-input-field" id="otp" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="------" required autofocus>
            </div>

            <button type="submit" class="login-button">
                <span>VERIFIKASI</span>
            </button>
        </form>

        <div class="resend-box">
            <form method="POST" action="{{ route('password.otp.resend') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="resend-btn">Kirim ulang OTP</button>
            </form>
            <a class="back-link" href="{{ route('password.request') }}">Ganti email</a>
        </div>
    </div>
</main>

</body>
</html>
