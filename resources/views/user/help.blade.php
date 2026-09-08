@extends('layouts.user')
@section('title', 'Bantuan – Perpustakaan Tiga Serangkai')
@section('page-title', 'Bantuan')

@push('styles')
<style>
.uhelp-grid { display:grid; grid-template-columns:2fr 1fr; gap:24px; align-items:start; }
.uhelp-faq { }
.uhelp-faq-item { background:#fff; border:1px solid var(--line); border-radius:12px; margin-bottom:10px; overflow:hidden; box-shadow:var(--shadow); }
.uhelp-faq-q { width:100%; display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 18px; background:none; border:none; font-size:14px; font-weight:600; color:var(--ink); cursor:pointer; text-align:left; font-family:inherit; transition:background .15s; }
.uhelp-faq-q:hover { background:#fbfaf6; }
.uhelp-faq-q svg { width:16px; height:16px; color:var(--muted-soft); flex-shrink:0; transition:transform .25s; }
.uhelp-faq-item.open .uhelp-faq-q svg { transform:rotate(180deg); }
.uhelp-faq-a { display:none; padding:0 18px 16px; font-size:13.5px; color:var(--muted); line-height:1.65; border-top:1px solid var(--line-soft); padding-top:14px; }
.uhelp-faq-item.open .uhelp-faq-a { display:block; }
.uhelp-sidebar-card { background:#fff; border:1px solid var(--line); border-radius:16px; padding:22px; box-shadow:var(--shadow); margin-bottom:16px; }
.uhelp-sidebar-card h3 { margin:0 0 14px; font-size:14px; font-weight:700; color:var(--ink); }
.uhelp-contact-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--line-soft); font-size:13px; color:var(--ink-soft); }
.uhelp-contact-item:last-child { border-bottom:none; }
.uhelp-contact-icon { width:34px; height:34px; border-radius:8px; background:#e0f0f0; color:var(--primary); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.uhelp-contact-icon svg { width:16px; height:16px; }
.uhelp-guide-item { display:flex; align-items:flex-start; gap:10px; padding:8px 0; font-size:13px; color:var(--ink-soft); line-height:1.5; }
.uhelp-guide-num { width:22px; height:22px; border-radius:50%; background:var(--primary); color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
</style>
@endpush

@section('content')

<div class="ul-page-header">
    <div>
        <p class="ul-page-kicker">DUKUNGAN</p>
        <h1 class="ul-page-h1">❓ Bantuan & FAQ</h1>
    </div>
</div>

<div class="uhelp-grid">

    {{-- FAQ --}}
    <div class="uhelp-faq">
        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--muted-soft);margin-bottom:14px">PERTANYAAN UMUM</p>
        @foreach($faqs as $i => $faq)
        <div class="uhelp-faq-item" id="faqItem{{ $i }}">
            <button class="uhelp-faq-q" onclick="toggleFaq({{ $i }})">
                {{ $faq['q'] }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div class="uhelp-faq-a">{{ $faq['a'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Sidebar --}}
    <div>
        {{-- Kontak --}}
        <div class="uhelp-sidebar-card">
            <h3>📞 Kontak Perpustakaan</h3>
            <div>
                <div class="uhelp-contact-item">
                    <div class="uhelp-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div><strong>Email</strong><br>perpustakaan@tiga-serangkai.co.id</div>
                </div>
                <div class="uhelp-contact-item">
                    <div class="uhelp-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div><strong>Lokasi</strong><br>Gedung Utama, Lantai 1</div>
                </div>
                <div class="uhelp-contact-item">
                    <div class="uhelp-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div><strong>Jam Operasional</strong><br>Sen–Jum: 07.30 – 17.00 WIB</div>
                </div>
            </div>
        </div>

        {{-- Panduan --}}
        <div class="uhelp-sidebar-card">
            <h3>📖 Panduan Singkat</h3>
            <div>
                <div class="uhelp-guide-item">
                    <div class="uhelp-guide-num">1</div>
                    <span>Login dengan akun karyawan Anda</span>
                </div>
                <div class="uhelp-guide-item">
                    <div class="uhelp-guide-num">2</div>
                    <span>Cari buku di <a href="{{ route('user.catalog') }}" style="color:var(--primary);font-weight:600">Katalog</a></span>
                </div>
                <div class="uhelp-guide-item">
                    <div class="uhelp-guide-num">3</div>
                    <span>Datang ke perpustakaan untuk meminjam</span>
                </div>
                <div class="uhelp-guide-item">
                    <div class="uhelp-guide-num">4</div>
                    <span>Pantau status di halaman <a href="{{ route('user.loans') }}" style="color:var(--primary);font-weight:600">Peminjaman</a></span>
                </div>
                <div class="uhelp-guide-item">
                    <div class="uhelp-guide-num">5</div>
                    <span>Kembalikan tepat waktu (14 hari)</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function toggleFaq(i) {
    const item = document.getElementById('faqItem' + i);
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.uhelp-faq-item').forEach(el => el.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
}
</script>
@endpush
