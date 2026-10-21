@extends('emails.layout')

@section('content')
<div style="text-align:center;margin-bottom:28px;">
  <div style="display:inline-block;width:64px;height:64px;line-height:64px;border-radius:50%;background:#ffe4e6;font-size:32px;margin-bottom:14px;border:2px solid #fecdd3;">
    ℹ️
  </div>
  <h2 class="header-title" style="margin:0 0 8px 0;color:#9f1239;font-size:22px;font-weight:800;letter-spacing:-0.3px;">Status Pendaftaran Anggota</h2>
  <p style="margin:0;color:#64748b;font-size:15px;line-height:1.5;">Halo <strong>{{ $name }}</strong>, terima kasih telah mengajukan pendaftaran di Perpustakaan Tiga Serangkai.</p>
</div>

<div style="background:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #f43f5e;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
  <h3 style="margin:0 0 10px 0;font-size:15px;color:#9f1239;font-weight:700;">Informasi Verifikasi:</h3>
  <p style="margin:0;color:#4c0519;font-size:14px;line-height:1.6;">
    Mohon maaf, permohonan pendaftaran akun Anda saat ini <strong>belum dapat disetujui</strong> oleh pihak administrator.
    @if(!empty($reason))
      <br><br><strong>Alasan:</strong> {{ $reason }}
    @else
      <br><br>Hal ini kemungkinan dikarenakan ketidaksesuaian data identitas atau divisi yang Anda pilih.
    @endif
  </p>
</div>

<p style="color:#64748b;font-size:14px;line-height:1.6;margin-bottom:26px;">
  Data pendaftaran sebelumnya telah dibersihkan sehingga email <strong>{{ $email }}</strong> dapat Anda gunakan kembali apabila Anda ingin mendaftar ulang dengan data yang benar.
</p>

<div style="text-align:center;margin-bottom:20px;">
  <a href="{{ route('register') }}" class="btn-primary" style="display:inline-block;background:linear-gradient(135deg,#217475,#278889);color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:14px 34px;border-radius:10px;box-shadow:0 6px 18px rgba(39,136,137,0.25);">
    Daftar Ulang Kembali &rarr;
  </a>
</div>
@endsection
