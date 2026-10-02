<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Reset Password</title>
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:40px auto;background:#fff;border-radius:16px;padding:32px;box-shadow:0 8px 30px rgba(0,0,0,.08);">
        <h2 style="margin:0 0 12px;">Reset Password</h2>
        <p style="line-height:1.6;">Gunakan kode OTP berikut untuk mengatur ulang password akun {{ config('app.name') }}:</p>

        <div style="font-size:32px;font-weight:800;letter-spacing:10px;text-align:center;padding:20px 10px;margin:24px 0;background:#f3f4f6;border-radius:12px;">
            {{ $otp }}
        </div>

        <p style="line-height:1.6;">Kode ini berlaku selama <strong>{{ $expiresInMinutes }} menit</strong> dan hanya dapat digunakan satu kali.</p>
        <p style="line-height:1.6;color:#6b7280;font-size:14px;">Jika Anda tidak meminta reset password, abaikan email ini dan jangan berikan kode kepada siapa pun.</p>
    </div>
</body>
</html>
