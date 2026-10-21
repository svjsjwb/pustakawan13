<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Password Baru – Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Buat password baru akun Perpustakaan Tiga Serangkai">
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
    </style>
</head>
<body>

<main class="login-page">
    <div class="login-background"></div>

    <div class="auth-card-panel">
        <div class="auth-header">
            <h2>PASSWORD BARU</h2>
            <p>Buat password baru untuk akunmu. Gunakan minimal 8 karakter.</p>
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

        <form method="POST" action="{{ route('password.update') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-wrapper password-wrapper">
                    <span class="input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input id="password" type="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password" autofocus>
                    <button type="button" class="password-toggle" onclick="toggleField('password', 'eyeIcon1')" aria-label="Tampilkan password">
                        <svg id="eyeIcon1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="input-wrapper password-wrapper">
                    <span class="input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password baru" required autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="toggleField('password_confirmation', 'eyeIcon2')" aria-label="Tampilkan konfirmasi password">
                        <svg id="eyeIcon2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-button" style="margin-top: 10px;">
                <span>UBAH PASSWORD</span>
            </button>
        </form>
    </div>
</main>

<script>
function toggleField(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8s-4 8-11 8-11-8-11-8"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>

</body>
</html>
