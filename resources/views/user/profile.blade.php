@extends('layouts.user')
@section('title', 'My Profile – Perpustakaan Digital')
@section('page-title', 'Profil Pengguna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
@endpush

@section('content')

<div class="uprof-layout">

    {{-- ── LEFT: Avatar Card ────────────────────────────────── --}}
    <div class="uprof-left">
        <div class="uprof-avatar-card">
            <div class="uprof-avatar-circle">
                {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
            </div>
            <h2 class="uprof-name">{{ $user->name }}</h2>
            <p class="uprof-role-label">Anggota Perpustakaan</p>
            @if($member)
            <div class="uprof-member-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Anggota Aktif · #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            @endif

            @if($member)
            <div class="uprof-quick-stats">
                <div class="uprof-qstat">
                    <strong>{{ $member->borrowings()->count() }}</strong>
                    <span>Total Pinjam</span>
                </div>
                <div class="uprof-qstat">
                    <strong>{{ $member->borrowings()->where('status','dipinjam')->count() }}</strong>
                    <span>Aktif</span>
                </div>
            </div>
            @endif
        </div>

    </div>

    {{-- ── RIGHT: Tabs Container ────────────────────────────── --}}
    <div class="uprof-right">

        {{-- Tabs Header Bar --}}
        <div class="uprof-tabs-bar">
            <button type="button" class="uprof-tab-btn active" id="tabBtnInfo" onclick="switchProfileTab('info')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Informasi Akun</span>
            </button>

            <button type="button" class="uprof-tab-btn" id="tabBtnSecurity" onclick="switchProfileTab('security')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Keamanan</span>
            </button>

            <button type="button" class="uprof-tab-btn" id="tabBtnPref" onclick="switchProfileTab('pref')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>Preferensi</span>
            </button>
        </div>

        {{-- ── TAB 1: INFORMASI AKUN ──────────────────────────── --}}
        <div class="uprof-tab-pane active" id="paneInfo">
            <div class="uprof-section-card">
                <div class="uprof-section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Informasi Akun & Data Diri</span>
                </div>

                <form method="POST" action="{{ route('user.profile.update') }}">
                    @csrf

                    <div class="uprof-form-row">
                        <div class="uprof-form-group">
                            <label>Nama Pengguna</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap">
                            @error('name')<span class="uprof-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="uprof-form-group">
                            <label>Alamat Email</label>
                            <input type="email" value="{{ $user->email }}" disabled>
                            <small>Email terdaftar tidak dapat diubah</small>
                        </div>
                    </div>

                    @if($member)
                    <div style="font-size: 11.5px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.08em; margin: 18px 0 10px;">
                        Status Anggota Perpustakaan
                    </div>
                    <div class="uprof-info-grid">
                        <div class="uprof-info-box">
                            <span>NOMOR ANGGOTA</span>
                            <strong>#{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</strong>
                        </div>
                        <div class="uprof-info-box">
                            <span>STATUS KEANGGOTAAN</span>
                            <strong style="color: var(--primary);">✓ {{ ucfirst($member->status) }}</strong>
                        </div>
                        <div class="uprof-info-box">
                            <span>NOMOR TELEPON</span>
                            <strong>{{ $member->phone ?? '-' }}</strong>
                        </div>
                        <div class="uprof-info-box">
                            <span>TERDAFTAR SEJAK</span>
                            <strong>{{ $member->created_at?->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div class="uprof-info-box" style="grid-column: 1 / -1;">
                            <span>ALAMAT DOMISILI</span>
                            <strong>{{ $member->address ?? '-' }}</strong>
                        </div>
                    </div>
                    @else
                    <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 14px; font-size: 13px; color: #b45309; margin-bottom: 20px;">
                        ⚠️ Akun Anda belum terdaftar sebagai anggota resmi perpustakaan. Hubungi petugas sirkulasi untuk verifikasi.
                    </div>
                    @endif

                    <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                        <button type="submit" class="eg-btn-block" style="width: auto; padding: 12px 28px;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── TAB 2: KEAMANAN ────────────────────────────────── --}}
        <div class="uprof-tab-pane" id="paneSecurity">
            <div class="uprof-section-card">
                <div class="uprof-section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Keamanan & Perubahan Kata Sandi</span>
                </div>

                <form method="POST" action="{{ route('user.profile.password') }}">
                    @csrf

                    <div class="uprof-form-row">
                        <div class="uprof-form-group" style="grid-column: 1 / -1;">
                            <label>Password Lama</label>
                            <input type="password" name="current_password" required placeholder="Masukkan kata sandi saat ini" autocomplete="current-password">
                            @error('current_password')<span class="uprof-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="uprof-form-row">
                        <div class="uprof-form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8">
                        </div>
                        <div class="uprof-form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi baru" autocomplete="new-password">
                        </div>
                    </div>

                    @error('password')<span class="uprof-error">{{ $message }}</span>@enderror

                    <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                        <button type="submit" class="eg-btn-block" style="width: auto; padding: 12px 28px;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>Simpan Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── TAB 3: PREFERENSI ──────────────────────────────── --}}
        <div class="uprof-tab-pane" id="panePref">
            <div class="uprof-section-card">
                <div class="uprof-section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>Preferensi Pengguna</span>
                </div>

                {{-- Group 1: Notifikasi --}}
                <div class="uprof-pref-group">
                    <div class="uprof-pref-group-title">Pengaturan Notifikasi</div>

                    {{-- Toggle 1: Reservasi --}}
                    <div class="uprof-pref-row">
                        <div>
                            <div class="uprof-pref-title">Notifikasi Reservasi</div>
                            <p class="uprof-pref-desc">Terima pemberitahuan saat buku yang diajukan masuk antrean</p>
                        </div>
                        <label class="eg-switch">
                            <input type="checkbox" id="prefReservasi" onchange="updateProfilePref('lib_notif_reservasi', this.checked)">
                            <span class="eg-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 2: Persetujuan --}}
                    <div class="uprof-pref-row">
                        <div>
                            <div class="uprof-pref-title">Notifikasi Persetujuan</div>
                            <p class="uprof-pref-desc">Pemberitahuan instan saat reservasi disetujui petugas</p>
                        </div>
                        <label class="eg-switch">
                            <input type="checkbox" id="prefPersetujuan" onchange="updateProfilePref('lib_notif_persetujuan', this.checked)">
                            <span class="eg-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 3: Pengembalian --}}
                    <div class="uprof-pref-row">
                        <div>
                            <div class="uprof-pref-title">Notifikasi Pengembalian</div>
                            <p class="uprof-pref-desc">Pengingat berkala sebelum tanggal jatuh tempo peminjaman buku</p>
                        </div>
                        <label class="eg-switch">
                            <input type="checkbox" id="prefPengembalian" onchange="updateProfilePref('lib_notif_pengembalian', this.checked)">
                            <span class="eg-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Group 2: Tema Tampilan --}}
                <div class="uprof-pref-group">
                    <div class="uprof-pref-group-title">Tema Tampilan</div>
                    <div class="uprof-theme-grid">
                        <button type="button" class="uprof-theme-btn" id="btnThemeLight" onclick="setProfileTheme('light')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                            <span>Mode Terang</span>
                        </button>
                        <button type="button" class="uprof-theme-btn" id="btnThemeDark" onclick="setProfileTheme('dark')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                            <span>Mode Gelap</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Profile Tab Switching Logic ──────────────────────────────────────────────
function switchProfileTab(tabName) {
    document.querySelectorAll('.uprof-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.uprof-tab-pane').forEach(pane => pane.classList.remove('active'));

    if (tabName === 'info') {
        document.getElementById('tabBtnInfo')?.classList.add('active');
        document.getElementById('paneInfo')?.classList.add('active');
    } else if (tabName === 'security') {
        document.getElementById('tabBtnSecurity')?.classList.add('active');
        document.getElementById('paneSecurity')?.classList.add('active');
    } else if (tabName === 'pref') {
        document.getElementById('tabBtnPref')?.classList.add('active');
        document.getElementById('panePref')?.classList.add('active');
    }
}

// ── Preferences: LocalStorage Handlers ───────────────────────────────────────
function updateProfilePref(key, checked) {
    localStorage.setItem(key, checked ? '1' : '0');
    if (window.showToast) {
        const labels = {
            'lib_notif_reservasi': 'Notifikasi Reservasi',
            'lib_notif_persetujuan': 'Notifikasi Persetujuan',
            'lib_notif_pengembalian': 'Notifikasi Pengembalian'
        };
        window.showToast(`${labels[key] || 'Pengaturan'} ${checked ? 'diaktifkan' : 'dinonaktifkan'}`, 'info');
    }
}

function setProfileTheme(theme) {
    localStorage.setItem('lib_theme', theme);
    applyProfileTheme(theme);
    if (window.showToast) {
        window.showToast(`Tema diubah ke ${theme === 'dark' ? 'Mode Gelap' : 'Mode Terang'}`, 'success');
    }
}

function applyProfileTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body?.classList.add('dark-theme');
        document.getElementById('btnThemeDark')?.classList.add('active');
        document.getElementById('btnThemeLight')?.classList.remove('active');
    } else {
        document.documentElement.removeAttribute('data-theme');
        document.body?.classList.remove('dark-theme');
        document.getElementById('btnThemeLight')?.classList.add('active');
        document.getElementById('btnThemeDark')?.classList.remove('active');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Theme sync
    const savedTheme = localStorage.getItem('lib_theme') || 'light';
    applyProfileTheme(savedTheme);

    // Notifications sync
    const nRes = localStorage.getItem('lib_notif_reservasi') !== '0';
    const nApr = localStorage.getItem('lib_notif_persetujuan') !== '0';
    const nRet = localStorage.getItem('lib_notif_pengembalian') !== '0';

    const elRes = document.getElementById('prefReservasi');
    const elApr = document.getElementById('prefPersetujuan');
    const elRet = document.getElementById('prefPengembalian');

    if (elRes) elRes.checked = nRes;
    if (elApr) elApr.checked = nApr;
    if (elRet) elRet.checked = nRet;
});
</script>
@endpush

@endsection
