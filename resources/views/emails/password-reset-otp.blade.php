@extends('emails.layout', [
    'emailTitle' => 'Kode OTP Reset Password',
    'preheader'  => 'Gunakan kode OTP ' . $otp . ' untuk mengatur ulang kata sandi dan masuk ke akun Perpustakaan Tiga Serangkai Anda.',
])

@section('content')
  <div style="text-align: center; margin-bottom: 24px;">
    <h1 class="header-title" style="margin: 0 0 10px; font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
      Reset Kata Sandi Akun
    </h1>
    <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
      Kami menerima permintaan untuk mereset kata sandi akun Perpustakaan Tiga Serangkai Anda. Gunakan kode verifikasi (OTP) di bawah ini:
    </p>
  </div>

  <!-- OTP Code Display Card -->
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 24px 0;">
    <tr>
      <td align="center">
        <div style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border: 2px dashed #0f766e; border-radius: 16px; padding: 20px 28px; display: inline-block; box-shadow: 0 4px 16px rgba(15, 118, 110, 0.08);">
          <span style="display: block; font-size: 11px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;">
            KODE VERIFIKASI OTP
          </span>
          <span style="font-size: 36px; font-weight: 800; letter-spacing: 10px; color: #0f766e; font-family: 'Courier New', Courier, monospace; display: block; padding-left: 10px;">
            {{ $otp }}
          </span>
        </div>
      </td>
    </tr>
  </table>

  <!-- Expiry & Security Notice -->
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin: 20px 0 24px 0;">
    <tr>
      <td>
        <p style="margin: 0 0 6px 0; font-size: 13.5px; color: #334155; line-height: 1.5;">
          ⏱️ Kode ini hanya berlaku selama <strong>{{ $expiresInMinutes }} menit</strong> dan hanya dapat digunakan 1 (satu) kali.
        </p>
        <p style="margin: 0; font-size: 12.5px; color: #64748b; line-height: 1.5;">
          Setelah memasukkan kode OTP di halaman verifikasi, Anda dapat langsung membuat kata sandi baru dan otomatis masuk ke akun Anda.
        </p>
      </td>
    </tr>
  </table>

  <p style="font-size: 12.5px; color: #94a3b8; line-height: 1.5; margin: 0; text-align: center;">
    Jika Anda tidak meminta perubahan kata sandi ini, abaikan email ini. Akun Anda tetap aman.
  </p>
@endsection
