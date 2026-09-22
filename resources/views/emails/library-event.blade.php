@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:50%;background:#e0f2fe;font-size:28px;margin-bottom:12px;">
    📢
  </div>
  <h2 style="margin:0 0 8px 0;color:#0f172a;font-size:22px;font-weight:700;">Acara Baru Perpustakaan</h2>
  <p style="margin:0;color:#64748b;font-size:15px;">{{ $event->name }}</p>
</div>

<p style="color:#334155;font-size:15px;">
  Halo Pengguna Perpustakaan, kami dengan senang hati mengundang Anda untuk mengikuti acara yang diselenggarakan oleh perpustakaan:
</p>

<div style="background:#f0f9ff;border:1px solid #bae6fd;border-left:4px solid #0284c7;border-radius:12px;padding:20px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;font-size:14px;">
    <tr>
      <td style="padding:6px 0;color:#64748b;width:30%;">Nama Acara:</td>
      <td style="padding:6px 0;color:#0f172a;font-weight:700;">{{ $event->name }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Tanggal:</td>
      <td style="padding:6px 0;color:#0369a1;font-weight:700;">
        {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : '-' }}
      </td>
    </tr>
    <tr>
      <td style="padding:6px 0;color:#64748b;">Lokasi:</td>
      <td style="padding:6px 0;color:#334155;font-weight:600;">{{ $event->location }}</td>
    </tr>
  </table>
</div>

<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:18px;margin-bottom:24px;">
  <h4 style="margin:0 0 8px 0;color:#475569;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">Deskripsi Acara:</h4>
  <p style="margin:0;color:#334155;font-size:14px;line-height:1.7;white-space:pre-line;">{{ $event->description }}</p>
</div>

<div style="text-align:center;margin-top:28px;margin-bottom:16px;">
  <a href="{{ route('user.home') }}" style="display:inline-block;background:#0284c7;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(2,132,199,0.2);">
    Kunjungi Perpustakaan &rarr;
  </a>
</div>
@endsection
