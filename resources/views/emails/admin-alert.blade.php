@extends('emails.layout')

@section('content')
<div style="margin-bottom:24px;">
  <div style="display:inline-block;padding:4px 12px;background:#fee2e2;color:#b91c1c;border-radius:9999px;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:12px;">
    Pemberitahuan Administrator
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">{{ $eventTitle }}</h2>
  <p style="margin:0;color:#475569;font-size:15px;line-height:1.6;">{{ $messageText }}</p>
</div>

@if(!empty($details))
<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:24px;">
  <h3 style="margin:0 0 12px 0;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Detail Aktivitas</h3>
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    @foreach($details as $label => $val)
    <tr>
      <td style="padding:6px 0;color:#64748b;width:35%;font-weight:500;">{{ $label }}:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:600;">{{ is_array($val) ? json_encode($val) : $val }}</td>
    </tr>
    @endforeach
    <tr>
      <td style="padding:6px 0;color:#64748b;">Waktu Kejadian:</td>
      <td style="padding:6px 0;color:#334155;">{{ now()->translatedFormat('d F Y H:i:s') }} WIB</td>
    </tr>
  </table>
</div>
@endif

@if(!empty($actionUrl))
<div style="text-align:center;margin-top:28px;margin-bottom:16px;">
  <a href="{{ $actionUrl }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(15,23,42,0.2);">
    {{ $actionLabel ?? 'Buka Halaman Admin' }} &rarr;
  </a>
</div>
@endif

<p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;">
  Notifikasi ini dikirimkan ke pustakawan & administrator sistem perpustakaan.
</p>
@endsection
