@extends('emails.layout')

@section('content')
<div style="margin-bottom: 24px;">
  <span style="display:inline-block;padding:4px 12px;background:#e0f2fe;color:#0369a1;border-radius:9999px;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;margin-bottom:12px;">
    📢 Pengumuman Perpustakaan
  </span>
  <h1 style="margin:0 0 12px 0;font-size:22px;font-weight:800;color:#0f172a;line-height:1.3;">
    {{ $subjectText }}
  </h1>
  <p style="margin:0;font-size:14px;color:#64748b;">
    Halo <strong>{{ $recipientName ?? 'Anggota Perpustakaan' }}</strong>, berikut adalah informasi resmi dari pengelola perpustakaan:
  </p>
</div>

<div style="background:#f8fafc;border-left:4px solid #2563eb;border-radius:0 12px 12px 0;padding:20px;margin-bottom:28px;font-size:15px;color:#334155;line-height:1.7;white-space:pre-line;">
{!! nl2br(e($messageBody)) !!}
</div>

@if(!empty($actionUrl))
<div style="text-align:center;margin:32px 0 20px 0;">
  <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 28px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;box-shadow:0 4px 12px rgba(37,99,235,0.25);">
    {{ $actionLabel ?? 'Buka Aplikasi Perpustakaan' }} &rarr;
  </a>
</div>
@endif

<div style="border-top:1px solid #e2e8f0;padding-top:18px;margin-top:28px;font-size:13px;color:#64748b;">
  <p style="margin:0;">
    Pengumuman disiarkan oleh: <strong>{{ $senderName ?? 'Admin Perpustakaan' }}</strong>
  </p>
</div>
@endsection
