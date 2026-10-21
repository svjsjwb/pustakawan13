<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $emailTitle ?? config('app.name', 'Perpustakaan Tiga Serangkai') }}</title>
  <!--[if mso]>
  <style>
    table, td, p, a, h1, h2, h3, h4 { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; }
  </style>
  <![endif]-->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    * { box-sizing: border-box; }
    body, html {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      min-height: 100% !important;
      background-color: #f1f5f9;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    img {
      border: 0;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }
    
    a {
      color: #278889;
      text-decoration: none;
    }
    
    @media only screen and (max-width: 600px) {
      .email-container {
        width: 100% !important;
        margin: 0 auto !important;
        border-radius: 0 !important;
        border: none !important;
      }
      .email-body {
        padding: 24px 20px !important;
      }
      .email-header {
        padding: 28px 20px 24px !important;
      }
      .email-footer {
        padding: 20px 16px !important;
      }
      .btn-primary {
        display: block !important;
        width: 100% !important;
        text-align: center !important;
        padding: 14px 20px !important;
      }
      .info-table td {
        display: block !important;
        width: 100% !important;
        padding: 4px 0 !important;
      }
      .info-table tr {
        display: block !important;
        margin-bottom: 10px !important;
        border-bottom: 1px dashed #e2e8f0 !important;
        padding-bottom: 6px !important;
      }
      .info-table tr:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
      }
      .header-title {
        font-size: 20px !important;
      }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;line-height:1.6;">

  <!-- Preheader text for inbox preview -->
  <div style="display:none;font-size:1px;color:#f1f5f9;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    {{ $preheader ?? 'Notifikasi resmi dari Perpustakaan Tiga Serangkai.' }}
  </div>

  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f1f5f9;margin:0;padding:24px 0 36px 0;">
    <tr>
      <td align="center" style="padding:0 12px;">

        <!-- Main Card Container -->
        <table role="presentation" class="email-container" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background-color:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 30px -8px rgba(39,136,137,0.12),0 4px 12px rgba(0,0,0,0.04);border:1px solid #e2e8f0;">

          <!-- Header Section with TS Brand Teal Gradient -->
          <tr>
            <td class="email-header" style="background:linear-gradient(135deg, #1b6263 0%, #278889 50%, #2ba5a6 100%);padding:36px 32px 30px;text-align:center;">
              
              <!-- Brand Category Badge -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto 16px auto;">
                <tr>
                  <td style="background:rgba(255,255,255,0.16);border:1px solid rgba(255,255,255,0.25);border-radius:9999px;padding:6px 18px;text-align:center;">
                    <span style="color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;">
                      🏛️ Perpustakaan Tiga Serangkai
                    </span>
                  </td>
                </tr>
              </table>

              <!-- Logo & System Subtitle -->
              @php
                $logoPath = public_path('images/logo-tiga-serangkai.png');
                $logoSrc = (isset($message) && is_object($message) && method_exists($message, 'embed') && file_exists($logoPath)) 
                  ? $message->embed($logoPath) 
                  : (file_exists($logoPath) ? asset('images/logo-tiga-serangkai.png') : null);
              @endphp

              <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto;">
                <tr>
                  <td align="center">
                    @if($logoSrc)
                      <div style="background:#ffffff;padding:8px 18px;border-radius:12px;display:inline-block;box-shadow:0 4px 14px rgba(0,0,0,0.12);margin-bottom:10px;">
                        <img src="{{ $logoSrc }}" alt="Logo Tiga Serangkai" style="height:48px;max-width:180px;width:auto;display:block;margin:0 auto;">
                      </div>
                    @else
                      <div style="background:#ffffff;color:#278889;font-weight:800;font-size:22px;letter-spacing:1px;padding:8px 20px;border-radius:12px;display:inline-block;margin-bottom:10px;">
                        TS PUSTAKA
                      </div>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <p style="margin:4px 0 0;color:rgba(255,255,255,0.92);font-size:13px;letter-spacing:0.4px;font-weight:500;">
                      Sistem Informasi & Manajemen Perpustakaan
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Main Content Body -->
          <tr>
            <td class="email-body" style="padding:36px 36px 28px;background-color:#ffffff;">
              @yield('content')
            </td>
          </tr>

          <!-- Security & Contact Note -->
          <tr>
            <td style="padding:0 36px 24px;background-color:#ffffff;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px 18px;">
                <tr>
                  <td style="font-size:12px;color:#64748b;line-height:1.6;text-align:center;">
                    🔒 <strong>Pemberitahuan Keamanan:</strong> Email ini dikirimkan secara otomatis oleh sistem. Jika Anda tidak merasa melakukan aktivitas ini, silakan segera hubungi petugas perpustakaan.
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer Section -->
          <tr>
            <td class="email-footer" style="background-color:#f8fafc;border-top:1px solid #e2e8f0;padding:26px 36px;text-align:center;">
              <p style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#334155;">
                Perpustakaan Tiga Serangkai
              </p>
              <p style="margin:0 0 12px 0;font-size:12px;color:#64748b;line-height:1.5;">
                Mendukung Pembelajaran, Riset, dan Literasi Masa Depan.<br>
                Jl. Dr. Supomo No. 23, Surakarta, Jawa Tengah
              </p>
              <p style="margin:0;font-size:11px;color:#94a3b8;">
                &copy; {{ date('Y') }} <strong>Perpustakaan Tiga Serangkai</strong>. Hak cipta dilindungi undang-undang.
              </p>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
