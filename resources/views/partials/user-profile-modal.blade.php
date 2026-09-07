{{-- =========================================================
     MODAL PENGATURAN PROFIL & AKUN (TABBED INTERFACE)
========================================================= --}}

@php
    $user = auth()->user();
    $userAvatar = $user->avatar ?? null;
    $userAvatarUrl = $userAvatar ? asset('storage/' . $userAvatar) : asset('images/avatar-user.jpg');
    $userName = $user->name ?? 'User';
    $userEmail = $user->email ?? 'user@example.com';
    $userPhone = $user->phone ?? '';
    $userLocation = $user->location ?? '';
    $userNotifEnabled = (bool) ($user->is_notification_enabled ?? true);
@endphp

<div
    id="userProfileModal"
    class="user-profile-modal"
    aria-hidden="true"
>

    {{-- BACKDROP OVERLAY REDUP --}}
    <div
        class="user-profile-modal-overlay"
        id="userProfileModalOverlay"
    ></div>

    {{-- KARTU MODAL MELAYANG --}}
    <div class="user-profile-modal-container">

        <div class="user-profile-modal-card">

            {{-- TOMBOL TUTUP 'X' --}}
            <button
                type="button"
                class="user-profile-modal-close"
                id="userProfileModalClose"
                aria-label="Tutup Pengaturan Akun"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            {{-- MODAL TITLE & SUBTITLE --}}
            <div class="user-profile-card-header">
                <h3 class="user-profile-card-title">Pengaturan Akun</h3>
                <p class="user-profile-card-subtitle">Kelola informasi profil, keamanan sandi, dan preferensi aplikasi</p>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="user-profile-tabs-nav" role="tablist">
                <button
                    type="button"
                    class="profile-tab-btn active"
                    data-tab="tabProfile"
                    role="tab"
                    id="tabBtnProfile"
                    aria-selected="true"
                    aria-controls="tabProfile"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Profil Saya</span>
                </button>

                <button
                    type="button"
                    class="profile-tab-btn"
                    data-tab="tabSecurity"
                    role="tab"
                    id="tabBtnSecurity"
                    aria-selected="false"
                    aria-controls="tabSecurity"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Keamanan</span>
                </button>

                <button
                    type="button"
                    class="profile-tab-btn"
                    data-tab="tabPreferences"
                    role="tab"
                    id="tabBtnPreferences"
                    aria-selected="false"
                    aria-controls="tabPreferences"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>Preferensi</span>
                </button>
            </div>

            {{-- =========================================================
                 TAB CONTENT 1: PROFIL SAYA
            ========================================================= --}}
            <div
                class="profile-tab-pane active"
                id="tabProfile"
                role="tabpanel"
                aria-labelledby="tabBtnProfile"
            >
                <form
                    id="userProfileForm"
                    action="{{ route('user.profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    {{-- AVATAR UPLOAD SECTION --}}
                    <div class="user-profile-avatar-row">
                        <div class="user-profile-avatar-wrapper">
                            <img
                                src="{{ $userAvatarUrl }}"
                                alt="Foto Profil"
                                class="user-profile-modal-avatar user-profile-avatar-display"
                                id="userModalAvatarPreview"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <div class="user-profile-modal-avatar-fallback" style="display:none;">
                                {{ strtoupper(substr($userName, 0, 1)) }}
                            </div>

                            <label
                                for="profilePhotoInput"
                                class="user-profile-avatar-edit-btn"
                                title="Ubah Foto Profil (JPG/PNG, max 2MB)"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </label>
                            <input
                                type="file"
                                id="profilePhotoInput"
                                name="avatar"
                                accept="image/png, image/jpeg, image/jpg"
                                style="display: none;"
                            >
                        </div>
                        <div class="user-avatar-upload-info">
                            <strong class="user-profile-name-display">{{ $userName }}</strong>
                            <span class="user-profile-email-display">{{ $userEmail }}</span>
                            <span class="avatar-helper-text">Format: JPG, PNG (Maks. 2MB)</span>
                        </div>
                    </div>

                    {{-- INLINE ERROR ALERT (PROFIL) --}}
                    <div class="user-profile-alert-error" id="profileErrorAlert" style="display: none;"></div>

                    {{-- FORM FIELDS LIST --}}
                    <div class="user-profile-fields-list">

                        {{-- 1. Nama Lengkap --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="profileInputName">
                                Nama Lengkap <span class="required-mark">*</span>
                            </label>
                            <div class="user-profile-field-input-wrap">
                                <input
                                    type="text"
                                    class="user-profile-field-input"
                                    id="profileInputName"
                                    name="name"
                                    value="{{ $userName }}"
                                    placeholder="Masukkan nama lengkap Anda"
                                    required
                                >
                            </div>
                        </div>

                        {{-- 2. Email (READ-ONLY FIELD) --}}
                        <div class="user-profile-field-row read-only-row">
                            <div class="user-profile-field-label-group">
                                <label class="user-profile-field-label" for="profileInputEmail">
                                    Alamat Email (Akun)
                                </label>
                                <span class="readonly-badge">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    Read-only
                                </span>
                            </div>
                            <div class="user-profile-field-input-wrap readonly-input-wrap">
                                <input
                                    type="email"
                                    class="user-profile-field-input input-readonly"
                                    id="profileInputEmail"
                                    name="email"
                                    value="{{ $userEmail }}"
                                    readonly
                                    tabindex="-1"
                                    title="Email akun bersifat permanen dan tidak dapat diubah"
                                >
                            </div>
                            <span class="field-subtext">Alamat email terdaftar bersifat tetap untuk identifikasi anggota.</span>
                        </div>

                        {{-- 3. Nomor Telepon --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="profileInputPhone">
                                Nomor Telepon / WhatsApp
                            </label>
                            <div class="user-profile-field-input-wrap">
                                <input
                                    type="tel"
                                    class="user-profile-field-input input-phone"
                                    id="profileInputPhone"
                                    name="phone"
                                    value="{{ $userPhone }}"
                                    placeholder="Contoh: 08123456789"
                                >
                            </div>
                            <span class="field-subtext">Nomor telepon aktif untuk pemberitahuan peminjaman.</span>
                        </div>

                        {{-- 4. Alamat / Lokasi --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="profileInputLocation">
                                Alamat / Domisili
                            </label>
                            <div class="user-profile-field-input-wrap">
                                <input
                                    type="text"
                                    class="user-profile-field-input"
                                    id="profileInputLocation"
                                    name="location"
                                    value="{{ $userLocation }}"
                                    placeholder="Contoh: Jakarta Selatan, DKI Jakarta"
                                >
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER TAB 1 --}}
                    <div class="user-profile-modal-footer">
                        <button
                            type="button"
                            class="user-profile-save-btn"
                            id="userProfileSaveBtn"
                        >
                            <span class="save-btn-text">Simpan Profil</span>
                            <span class="save-btn-spinner" style="display: none;">
                                <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="user-profile-save-toast" id="userProfileSaveToast">
                            ✓ Profil berhasil diperbarui!
                        </div>
                    </div>

                </form>
            </div>


            {{-- =========================================================
                 TAB CONTENT 2: KEAMANAN (GANTI KATA SANDI)
            ========================================================= --}}
            <div
                class="profile-tab-pane"
                id="tabSecurity"
                role="tabpanel"
                aria-labelledby="tabBtnSecurity"
                style="display: none;"
            >
                <form
                    id="userSecurityForm"
                    action="{{ route('user.profile.password') }}"
                    method="POST"
                >
                    @csrf

                    <div class="security-intro-box">
                        <div class="security-intro-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div class="security-intro-text">
                            <strong>Perbarui Kata Sandi</strong>
                            <span>Gunakan kata sandi yang kuat dengan minimal 8 karakter kombinasi huruf dan angka.</span>
                        </div>
                    </div>

                    {{-- INLINE ERROR ALERT (KEAMANAN) --}}
                    <div class="user-profile-alert-error" id="securityErrorAlert" style="display: none;"></div>

                    <div class="user-profile-fields-list">

                        {{-- 1. Password Saat Ini --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="securityCurrentPassword">
                                Password Saat Ini <span class="required-mark">*</span>
                            </label>
                            <div class="user-profile-field-input-wrap password-input-wrap">
                                <input
                                    type="password"
                                    class="user-profile-field-input"
                                    id="securityCurrentPassword"
                                    name="current_password"
                                    placeholder="Masukkan kata sandi saat ini"
                                    required
                                    autocomplete="current-password"
                                >
                                <button
                                    type="button"
                                    class="toggle-password-visibility"
                                    tabindex="-1"
                                    aria-label="Tampilkan Password"
                                >
                                    <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" style="display: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- 2. Password Baru --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="securityNewPassword">
                                Password Baru <span class="required-mark">*</span>
                            </label>
                            <div class="user-profile-field-input-wrap password-input-wrap">
                                <input
                                    type="password"
                                    class="user-profile-field-input"
                                    id="securityNewPassword"
                                    name="new_password"
                                    placeholder="Minimal 8 karakter (huruf & angka)"
                                    required
                                    autocomplete="new-password"
                                >
                                <button
                                    type="button"
                                    class="toggle-password-visibility"
                                    tabindex="-1"
                                    aria-label="Tampilkan Password"
                                >
                                    <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" style="display: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <span class="field-subtext">Wajib memuat minimal 8 karakter dengan perpaduan huruf dan angka.</span>
                        </div>

                        {{-- 3. Konfirmasi Password Baru --}}
                        <div class="user-profile-field-row">
                            <label class="user-profile-field-label" for="securityConfirmPassword">
                                Konfirmasi Password Baru <span class="required-mark">*</span>
                            </label>
                            <div class="user-profile-field-input-wrap password-input-wrap">
                                <input
                                    type="password"
                                    class="user-profile-field-input"
                                    id="securityConfirmPassword"
                                    name="new_password_confirmation"
                                    placeholder="Ulangi password baru Anda"
                                    required
                                    autocomplete="new-password"
                                >
                                <button
                                    type="button"
                                    class="toggle-password-visibility"
                                    tabindex="-1"
                                    aria-label="Tampilkan Password"
                                >
                                    <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" style="display: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER TAB 2 --}}
                    <div class="user-profile-modal-footer">
                        <button
                            type="button"
                            class="user-profile-save-btn"
                            id="userSecuritySaveBtn"
                        >
                            <span class="save-btn-text">Perbarui Kata Sandi</span>
                            <span class="save-btn-spinner" style="display: none;">
                                <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="user-profile-save-toast" id="userSecuritySaveToast">
                            ✓ Kata sandi berhasil diperbarui!
                        </div>
                    </div>

                </form>
            </div>


            {{-- =========================================================
                 TAB CONTENT 3: PREFERENSI & NOTIFIKASI
            ========================================================= --}}
            <div
                class="profile-tab-pane"
                id="tabPreferences"
                role="tabpanel"
                aria-labelledby="tabBtnPreferences"
                style="display: none;"
            >
                <div class="preferences-container">

                    {{-- 1. NOTIFICATION TOGGLE --}}
                    <div class="pref-section-card">
                        <div class="pref-card-header">
                            <div class="pref-icon-circle notif-icon-bg">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                            </div>
                            <div class="pref-info-block">
                                <div class="pref-title-row">
                                    <strong class="pref-title">Allow Notification</strong>
                                    <span class="pref-status-label" id="prefNotifStatusLabel">
                                        {{ $userNotifEnabled ? 'Aktif' : 'Senyap' }}
                                    </span>
                                </div>
                                <p class="pref-desc">
                                    Terima notifikasi status persetujuan reservasi buku dan peringatan pengembalian H-1 sebelum jatuh tempo.
                                </p>
                            </div>
                        </div>

                        <div class="pref-action-row">
                            <label class="toggle-switch-container" style="position: relative; display: inline-block; width: 48px; height: 26px; cursor: pointer; margin: 0;">
                                <input
                                    type="checkbox"
                                    id="profileToggleNotification"
                                    name="is_notification_enabled"
                                    value="1"
                                    {{ $userNotifEnabled ? 'checked' : '' }}
                                    style="opacity: 0; width: 0; height: 0;"
                                >
                                <span class="toggle-switch-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $userNotifEnabled ? '#0f4c4c' : '#cbd5e1' }}; transition: .3s; border-radius: 26px;"></span>
                            </label>
                            <span class="toggle-caption" id="prefToggleCaption">
                                {{ $userNotifEnabled ? 'Notifikasi diizinkan' : 'Notifikasi dimatikan' }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. TEMA / MODE APLIKASI (LIGHT / DARK) --}}
                    <div class="pref-section-card">
                        <div class="pref-card-header">
                            <div class="pref-icon-circle theme-icon-bg">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                            <div class="pref-info-block">
                                <strong class="pref-title">Tema & Tampilan</strong>
                                <p class="pref-desc">
                                    Pilih mode tampilan kenyamanan membaca Anda saat menjelajahi katalog perpustakaan.
                                </p>
                            </div>
                        </div>

                        <div class="theme-selector-grid">
                            {{-- Pilihan 1: Mode Terang --}}
                            <button
                                type="button"
                                class="theme-option-card active"
                                id="themeOptionLight"
                                data-theme-val="light"
                            >
                                <div class="theme-preview-box light-preview">
                                    <div class="theme-preview-header"></div>
                                    <div class="theme-preview-body"></div>
                                </div>
                                <div class="theme-option-footer">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                    <span>Mode Terang</span>
                                </div>
                            </button>

                            {{-- Pilihan 2: Mode Gelap --}}
                            <button
                                type="button"
                                class="theme-option-card"
                                id="themeOptionDark"
                                data-theme-val="dark"
                            >
                                <div class="theme-preview-box dark-preview">
                                    <div class="theme-preview-header"></div>
                                    <div class="theme-preview-body"></div>
                                </div>
                                <div class="theme-option-footer">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                    <span>Mode Gelap</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="user-profile-save-toast" id="prefSaveToast" style="display: block; opacity: 0; margin-top: 10px;">
                        ✓ Preferensi diperbarui!
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
