@extends('layouts.user')
@section('title', 'My Profile – Perpustakaan Digital')
@section('page-title', 'Profil Pengguna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
@endpush

@section('content')

<div class="uprof-container">

    {{-- Tabs Header Bar --}}
    <div class="uprof-tabs-bar" style="margin-bottom: 24px;">
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

    {{-- ════════════════════════════════════════════════════════════
         TAB 1: INFORMASI AKUN
    ════════════════════════════════════════════════════════════ --}}
    <div class="uprof-tab-pane active" id="paneInfo">
        <div class="uprof-layout-2col">
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
                    <div class="uprof-quick-stats">
                        <div class="uprof-qstat">
                            <strong>{{ $stats['totalBorrowed'] ?? ($member ? $member->borrowings()->count() : 0) }}</strong>
                            <span>Total Pinjam</span>
                        </div>
                        <div class="uprof-qstat">
                            <strong>{{ $stats['activeLoans'] ?? ($member ? $member->borrowings()->where('status','dipinjam')->count() : 0) }}</strong>
                            <span>Aktif</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="uprof-right">
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
                                <strong>{{ $stats['joinDate'] ?? '-' }}</strong>
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
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         TAB 2: KEAMANAN
    ════════════════════════════════════════════════════════════ --}}
    <div class="uprof-tab-pane" id="paneSecurity">
        <div class="uprof-layout-2col">
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
                </div>
            </div>

            <div class="uprof-right">
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
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         TAB 3: PREFERENSI USER (3-KOLOM + PREVIEW BAWAH)
    ════════════════════════════════════════════════════════════ --}}
    <div class="uprof-tab-pane" id="panePref">
        <form id="prefMainForm" method="POST" action="{{ route('user.profile.preferences') }}" onsubmit="submitAllPreferences(event)">
            @csrf

            <input type="hidden" name="theme" id="inputTheme" value="{{ $user->theme ?? 'light' }}">
            <input type="hidden" name="layout_density" id="inputDensity" value="{{ $user->layout_density ?? 'normal' }}">

            <div class="uprof-pref-grid-3col">

                {{-- ────────────────────────────────────────────────
                     KOLOM KIRI: Profil & Informasi Anggota
                ──────────────────────────────────────────────── --}}
                <div class="uprof-col">
                    {{-- 1. Kartu Profil User --}}
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
                        <div class="uprof-quick-stats">
                            <div class="uprof-qstat">
                                <strong>{{ $stats['totalBorrowed'] ?? 0 }}</strong>
                                <span>Total Pinjam</span>
                            </div>
                            <div class="uprof-qstat">
                                <strong>{{ $stats['activeLoans'] ?? 0 }}</strong>
                                <span>Aktif</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- 2. Card Informasi Anggota --}}
                    <div class="uprof-member-card">
                        <div class="uprof-card-head">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Informasi Anggota</span>
                        </div>
                        <div class="uprof-meta-list">
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/></svg>
                                    ID Anggota
                                </span>
                                <span class="uprof-meta-val">#{{ str_pad($member->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Tanggal Bergabung
                                </span>
                                <span class="uprof-meta-val">{{ $stats['joinDate'] ?? '-' }}</span>
                            </div>
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    Total Buku Dipinjam
                                </span>
                                <span class="uprof-meta-val">{{ $stats['totalBorrowed'] ?? 0 }} Buku</span>
                            </div>
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Total Reservasi
                                </span>
                                <span class="uprof-meta-val">{{ $stats['totalReservations'] ?? 0 }} Kali</span>
                            </div>
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Riwayat Selesai
                                </span>
                                <span class="uprof-meta-val">{{ $stats['completedLoans'] ?? 0 }} Buku</span>
                            </div>
                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                    Peminjaman Aktif
                                </span>
                                <span class="uprof-meta-val" style="color: var(--primary);">{{ $stats['activeLoans'] ?? 0 }} Buku</span>
                            </div>

                            <div class="uprof-meta-item">
                                <span class="uprof-meta-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    Terakhir Login
                                </span>
                                <span class="uprof-meta-val">{{ $stats['lastLogin'] ?? '-' }}</span>
                            </div>
                            <div class="uprof-meta-item" style="padding-top: 8px; border-top: 1px dashed var(--border-subtle);">
                                <span class="uprof-meta-label">Status Akun</span>
                                @if(($stats['accountStatus'] ?? 'aktif') === 'aktif')
                                    <span class="uprof-status-pill aktif">🟢 Aktif</span>
                                @elseif(($stats['accountStatus'] ?? '') === 'ditangguhkan')
                                    <span class="uprof-status-pill ditangguhkan">🔴 Ditangguhkan</span>
                                @else
                                    <span class="uprof-status-pill nonaktif">⚪ Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────────────
                     KOLOM TENGAH: Pusat Notifikasi Perpustakaan
                ──────────────────────────────────────────────── --}}
                <div class="uprof-col">
                    <div class="uprof-section-card">
                        <div class="uprof-section-head">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span>Pusat Notifikasi Perpustakaan</span>
                        </div>

                        {{-- Master Toggle Card --}}
                        <div class="uprof-pref-master-card">
                            <div>
                                <div class="uprof-pref-title" style="font-size: 15px; color: var(--primary); display: flex; align-items: center; gap: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                    <span>Allow Notifications</span>
                                </div>
                                <p class="uprof-pref-desc">Izinkan sistem mengirim notifikasi ke akun Anda.</p>
                            </div>
                            <label class="eg-switch">
                                <input type="checkbox" name="allow_notifications" id="prefMaster" value="1" {{ ($user->allow_notifications ?? true) ? 'checked' : '' }} onchange="handleMasterToggle(this.checked)">
                                <span class="eg-slider"></span>
                            </label>
                        </div>

                        {{-- Sub-Notifikasi List --}}
                        <div class="uprof-pref-subgroup {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}" id="prefSubgroup">
                            {{-- 1: Email Notifications --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Email Notifications</div>
                                    <p class="uprof-pref-desc">Terima notifikasi melalui email.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="email_notifications" class="sub-pref-item" value="1" {{ ($user->email_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>

                            {{-- 2: Reservasi Buku --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Reservasi Buku</div>
                                    <p class="uprof-pref-desc">Notifikasi saat buku reservasi siap diambil.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="reservation_notifications" class="sub-pref-item" value="1" {{ ($user->reservation_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>

                            {{-- 3: Persetujuan Peminjaman --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Persetujuan Peminjaman</div>
                                    <p class="uprof-pref-desc">Notifikasi saat peminjaman disetujui atau ditolak.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="borrowing_notifications" class="sub-pref-item" value="1" {{ ($user->borrowing_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>

                            {{-- 4: Perpanjangan Peminjaman --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Perpanjangan Peminjaman</div>
                                    <p class="uprof-pref-desc">Notifikasi saat pengajuan perpanjangan diproses.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="extension_notifications" class="sub-pref-item" value="1" {{ ($user->extension_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>

                            {{-- 5: Pengingat Pengembalian --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Pengingat Pengembalian</div>
                                    <p class="uprof-pref-desc">Pengingat sebelum jatuh tempo pengembalian.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="return_reminder_notifications" class="sub-pref-item" value="1" {{ ($user->return_reminder_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>

                            {{-- 6: Peringatan Keterlambatan (Sistem tidak menggunakan denda) --}}
                            <div class="uprof-pref-row">
                                <div>
                                    <div class="uprof-pref-title">Peringatan Keterlambatan</div>
                                    <p class="uprof-pref-desc">Notifikasi ketika buku melewati batas waktu pengembalian.</p>
                                </div>
                                <label class="eg-switch">
                                    <input type="checkbox" name="late_return_notifications" class="sub-pref-item" value="1" {{ ($user->late_return_notifications ?? true) ? 'checked' : '' }} {{ ($user->allow_notifications ?? true) ? '' : 'disabled' }}>
                                    <span class="eg-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────────────
                     KOLOM KANAN: Tema, Ukuran, Katalog, Simpan
                ──────────────────────────────────────────────── --}}
                <div class="uprof-col">
                    <div class="uprof-section-card">
                        {{-- 1. Tema Tampilan (HANYA Light & Dark, NO system) --}}
                        <div class="uprof-section-head">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                            </svg>
                            <span>Tema Tampilan</span>
                        </div>

                        <div class="uprof-theme-cards">
                            {{-- Light Mode Card --}}
                            <div class="uprof-theme-card {{ ($user->theme ?? 'light') === 'light' ? 'active' : '' }}" id="themeCardLight" onclick="selectTheme('light')">
                                <span class="uprof-theme-badge-active">Sedang Digunakan</span>
                                <div class="uprof-theme-card-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="5"></circle>
                                        <line x1="12" y1="1" x2="12" y2="3"></line>
                                        <line x1="12" y1="21" x2="12" y2="23"></line>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                        <line x1="1" y1="12" x2="3" y2="12"></line>
                                        <line x1="21" y1="12" x2="23" y2="12"></line>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                    </svg>
                                </div>
                                <h4 class="uprof-theme-card-title">Light Mode</h4>
                                <p class="uprof-theme-card-desc">Tampilan terang dan bersih.</p>
                            </div>

                            {{-- Dark Mode Card --}}
                            <div class="uprof-theme-card {{ ($user->theme ?? 'light') === 'dark' ? 'active' : '' }}" id="themeCardDark" onclick="selectTheme('dark')">
                                <span class="uprof-theme-badge-active">Sedang Digunakan</span>
                                <div class="uprof-theme-card-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                </div>
                                <h4 class="uprof-theme-card-title">Dark Mode</h4>
                                <p class="uprof-theme-card-desc">Tampilan nyaman untuk penggunaan malam hari.</p>
                            </div>
                        </div>

                        {{-- 2. Ukuran Tampilan (Compact / Normal / Comfortable) --}}
                        <div class="uprof-section-head" style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-subtle);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="3" y1="15" x2="21" y2="15"></line>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                            </svg>
                            <span>Ukuran Tampilan</span>
                        </div>
                        <div class="uprof-density-options">
                            @php $currDensity = $user->layout_density ?? 'normal'; @endphp
                            <button type="button" class="uprof-density-btn {{ $currDensity === 'compact' ? 'active' : '' }}" id="densityBtnCompact" onclick="selectDensity('compact')">
                                <span class="label">Compact</span>
                                <span class="sub">Lebih rapat</span>
                            </button>
                            <button type="button" class="uprof-density-btn {{ $currDensity === 'normal' ? 'active' : '' }}" id="densityBtnNormal" onclick="selectDensity('normal')">
                                <span class="label">Normal</span>
                                <span class="sub">Standar</span>
                            </button>
                            <button type="button" class="uprof-density-btn {{ $currDensity === 'comfortable' ? 'active' : '' }}" id="densityBtnComfortable" onclick="selectDensity('comfortable')">
                                <span class="label">Comfortable</span>
                                <span class="sub">Lebih lega</span>
                            </button>
                        </div>

                        {{-- 3. Tombol Simpan Preferensi --}}
                        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-subtle);">
                            <button type="submit" id="btnSaveAllPrefs" class="eg-btn-block" style="width: 100%; padding: 13px 20px; font-size: 13.5px; font-weight: 700;">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <span>Simpan Preferensi</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ────────────────────────────────────────────────
                 BAGIAN BAWAH: Preview Tema Tampilan
            ──────────────────────────────────────────────── --}}
            <div class="uprof-preview-section">
                <div class="uprof-preview-head">
                    <h3>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>Preview Tema Tampilan</span>
                    </h3>
                    <p>Pilih dan klik salah satu tampilan di bawah untuk langsung merasakan pengalaman tema secara real-time.</p>
                </div>

                <div class="uprof-preview-grid">
                    {{-- 1. Preview Light Mode Card --}}
                    <div class="uprof-preview-card {{ ($user->theme ?? 'light') === 'light' ? 'active' : '' }}" id="previewCardLight" onclick="selectTheme('light')">
                        <div class="uprof-preview-card-head">
                            <div class="uprof-preview-card-title">
                                <span>☀ Light Mode Preview</span>
                            </div>
                            <span class="uprof-theme-badge-active" id="previewBadgeLight" style="{{ ($user->theme ?? 'light') === 'light' ? 'display:inline-block;' : 'display:none;' }}">Sedang Digunakan</span>
                        </div>

                        {{-- Mock Window UI Light --}}
                        <div class="uprof-mock-window uprof-mock-light">
                            <div class="mock-nav">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span class="mock-dot"></span>
                                    <span style="font-size:11px; font-weight:800; color:#0f766e;">PUSTAKA</span>
                                </div>
                                <div style="display:flex; gap:8px; font-size:10px; color:#64748b;">
                                    <span>Katalog</span>
                                    <span>Peminjaman</span>
                                    <span style="color:#0f766e; font-weight:700;">Profil</span>
                                </div>
                            </div>
                            <div class="mock-body">
                                <div class="mock-card">
                                    <div>
                                        <div style="font-size:11px; font-weight:700; color:#0f172a;">Laskar Pelangi</div>
                                        <div style="font-size:9.5px; color:#64748b;">Andrea Hirata · Fiksi</div>
                                    </div>
                                    <span style="font-size:9px; padding:2px 8px; border-radius:999px; background:#dcfce7; color:#16a34a; font-weight:700;">Dipinjam</span>
                                </div>
                                <div class="mock-card" style="opacity:0.85;">
                                    <div>
                                        <div style="font-size:11px; font-weight:700; color:#0f172a;">Matematika Terpadu</div>
                                        <div style="font-size:9.5px; color:#64748b;">Erlangga · Pendidikan</div>
                                    </div>
                                    <span style="font-size:9px; padding:2px 8px; border-radius:999px; background:#e0f2fe; color:#0284c7; font-weight:700;">Tersedia</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Preview Dark Mode Card --}}
                    <div class="uprof-preview-card {{ ($user->theme ?? 'light') === 'dark' ? 'active' : '' }}" id="previewCardDark" onclick="selectTheme('dark')">
                        <div class="uprof-preview-card-head">
                            <div class="uprof-preview-card-title">
                                <span>🌙 Dark Mode Preview</span>
                            </div>
                            <span class="uprof-theme-badge-active" id="previewBadgeDark" style="{{ ($user->theme ?? 'light') === 'dark' ? 'display:inline-block;' : 'display:none;' }}">Sedang Digunakan</span>
                        </div>

                        {{-- Mock Window UI Dark --}}
                        <div class="uprof-mock-window uprof-mock-dark">
                            <div class="mock-nav">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span class="mock-dot"></span>
                                    <span style="font-size:11px; font-weight:800; color:#14b8a6;">PUSTAKA</span>
                                </div>
                                <div style="display:flex; gap:8px; font-size:10px; color:#99f6e4;">
                                    <span>Katalog</span>
                                    <span>Peminjaman</span>
                                    <span style="color:#14b8a6; font-weight:700;">Profil</span>
                                </div>
                            </div>
                            <div class="mock-body">
                                <div class="mock-card">
                                    <div>
                                        <div style="font-size:11px; font-weight:700; color:#f0fdfa;">Laskar Pelangi</div>
                                        <div style="font-size:9.5px; color:#99f6e4;">Andrea Hirata · Fiksi</div>
                                    </div>
                                    <span style="font-size:9px; padding:2px 8px; border-radius:999px; background:rgba(22,163,74,0.2); color:#4ade80; font-weight:700;">Dipinjam</span>
                                </div>
                                <div class="mock-card" style="opacity:0.85;">
                                    <div>
                                        <div style="font-size:11px; font-weight:700; color:#f0fdfa;">Matematika Terpadu</div>
                                        <div style="font-size:9.5px; color:#99f6e4;">Erlangga · Pendidikan</div>
                                    </div>
                                    <span style="font-size:9px; padding:2px 8px; border-radius:999px; background:rgba(2,132,199,0.2); color:#38bdf8; font-weight:700;">Tersedia</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
// ── Profile Tab Switching ───────────────────────────────────────────────────
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

// ── Master Switch Handler ───────────────────────────────────────────────────
function handleMasterToggle(isChecked) {
    const subgroup = document.getElementById('prefSubgroup');
    const subItems = document.querySelectorAll('.sub-pref-item');
    if (isChecked) {
        subgroup?.classList.remove('disabled');
        subItems.forEach(item => { item.disabled = false; });
    } else {
        subgroup?.classList.add('disabled');
        subItems.forEach(item => { item.disabled = true; });
    }
}

// ── Theme Selection (Light & Dark Only) ─────────────────────────────────────
function selectTheme(theme, showMessage = true) {
    if (theme !== 'light' && theme !== 'dark') return;

    document.getElementById('inputTheme').value = theme;
    localStorage.setItem('lib_theme', theme);

    // Apply attribute immediately
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body?.classList.add('dark-theme');
    } else {
        document.documentElement.removeAttribute('data-theme');
        document.body?.classList.remove('dark-theme');
    }

    // Update Theme Cards active class
    const cardLight = document.getElementById('themeCardLight');
    const cardDark = document.getElementById('themeCardDark');
    if (theme === 'light') {
        cardLight?.classList.add('active');
        cardDark?.classList.remove('active');
    } else {
        cardDark?.classList.add('active');
        cardLight?.classList.remove('active');
    }

    // Update Bottom Preview Cards
    const prevLight = document.getElementById('previewCardLight');
    const prevDark = document.getElementById('previewCardDark');
    const badgeLight = document.getElementById('previewBadgeLight');
    const badgeDark = document.getElementById('previewBadgeDark');

    if (theme === 'light') {
        prevLight?.classList.add('active');
        prevDark?.classList.remove('active');
        if (badgeLight) badgeLight.style.display = 'inline-block';
        if (badgeDark) badgeDark.style.display = 'none';
    } else {
        prevDark?.classList.add('active');
        prevLight?.classList.remove('active');
        if (badgeDark) badgeDark.style.display = 'inline-block';
        if (badgeLight) badgeLight.style.display = 'none';
    }

    if (showMessage && window.showToast) {
        window.showToast(`Tema diubah ke ${theme === 'dark' ? 'Mode Gelap' : 'Mode Terang'}`, 'info');
    }
}

// ── Layout Density Selection (Compact / Normal / Comfortable) ───────────────
function selectDensity(density) {
    if (!['compact', 'normal', 'comfortable'].includes(density)) return;

    document.getElementById('inputDensity').value = density;
    localStorage.setItem('lib_density', density);
    document.documentElement.setAttribute('data-density', density);

    document.querySelectorAll('.uprof-density-btn').forEach(btn => btn.classList.remove('active'));
    if (density === 'compact') document.getElementById('densityBtnCompact')?.classList.add('active');
    else if (density === 'normal') document.getElementById('densityBtnNormal')?.classList.add('active');
    else if (density === 'comfortable') document.getElementById('densityBtnComfortable')?.classList.add('active');

    const labels = { compact: 'Compact (Rapat)', normal: 'Normal (Standar)', comfortable: 'Comfortable (Lega)' };
    if (window.showToast) {
        window.showToast(`Ukuran tampilan diubah ke ${labels[density]}`, 'info');
    }
}

// ── Submit All Preferences via AJAX ─────────────────────────────────────────
async function submitAllPreferences(e) {
    e.preventDefault();
    const form = document.getElementById('prefMainForm');
    const btn = document.getElementById('btnSaveAllPrefs');
    const originalBtnHtml = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span style="display:inline-block; width:14px; height:14px; border:2px solid #ffffff; border-top-color:transparent; border-radius:50%; animation:egPrefSpin 0.7s linear infinite; margin-right:6px; vertical-align:middle;"></span> Menyimpan...`;
    }

    const masterChecked = document.getElementById('prefMaster')?.checked ?? false;
    const themeVal = document.getElementById('inputTheme')?.value || 'light';
    const densityVal = document.getElementById('inputDensity')?.value || 'normal';

    const payload = {
        allow_notifications: masterChecked,
        email_notifications: masterChecked ? (form.querySelector('[name="email_notifications"]')?.checked ?? false) : false,
        reservation_notifications: masterChecked ? (form.querySelector('[name="reservation_notifications"]')?.checked ?? false) : false,
        borrowing_notifications: masterChecked ? (form.querySelector('[name="borrowing_notifications"]')?.checked ?? false) : false,
        extension_notifications: masterChecked ? (form.querySelector('[name="extension_notifications"]')?.checked ?? false) : false,
        return_reminder_notifications: masterChecked ? (form.querySelector('[name="return_reminder_notifications"]')?.checked ?? false) : false,
        late_return_notifications: masterChecked ? (form.querySelector('[name="late_return_notifications"]')?.checked ?? false) : false,
        theme: themeVal,
        layout_density: densityVal,
    };

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (data.success) {
            if (window.showToast) {
                window.showToast(data.message || 'Preferensi berhasil diperbarui.', 'success');
            } else {
                alert(data.message || 'Preferensi berhasil diperbarui.');
            }

            // Sync navbar indicator if present
            const navPill = document.querySelector('.eg-notif-status-pill');
            if (navPill) {
                if (masterChecked) {
                    navPill.classList.remove('off');
                    navPill.textContent = 'Allowed';
                } else {
                    navPill.classList.add('off');
                    navPill.textContent = 'Disabled';
                }
            }
        } else {
            throw new Error(data.message || 'Gagal menyimpan preferensi.');
        }
    } catch (err) {
        console.error(err);
        if (window.showToast) {
            window.showToast('Gagal menyimpan preferensi: ' + err.message, 'error');
        } else {
            alert('Gagal menyimpan preferensi: ' + err.message);
        }
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Check URL hash for direct tab navigation
    if (window.location.hash === '#pref') {
        switchProfileTab('pref');
    } else if (window.location.hash === '#security') {
        switchProfileTab('security');
    }

    // Apply the persisted theme without treating page load as a user change.
    const storedTheme = localStorage.getItem('lib_theme');
    const serverTheme = document.getElementById('inputTheme')?.value;
    const initialTheme = storedTheme === 'dark' || storedTheme === 'light'
        ? storedTheme
        : (serverTheme === 'dark' || serverTheme === 'light' ? serverTheme : 'light');
    document.getElementById('inputTheme').value = initialTheme;
    selectTheme(initialTheme, false);

    // Sync active density from input
    const initialDensity = document.getElementById('inputDensity')?.value || localStorage.getItem('lib_density') || 'normal';
    selectDensity(initialDensity);
});
</script>
<style>
@keyframes egPrefSpin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@endsection
