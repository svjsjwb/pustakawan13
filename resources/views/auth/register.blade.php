<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Perpustakaan Tiga Serangkai</title>
    <meta name="color-scheme" content="dark light">
    <link rel="preload" as="image" href="{{ asset('images/bg-ts.jpeg') }}" fetchpriority="high">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">
    <style>
        /* ============================================================
           REGISTER PAGE — ZERO GREEN FLASH ON REFRESH / DIRECT LOAD
           Body langsung visible, tanpa opacity transition.
           Ini adalah satu-satunya cara mencegah green flash pada first paint.
           ============================================================ */
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            overflow: hidden !important;
            overscroll-behavior: none;
            background: #020f10;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            /* TIDAK ADA opacity:0 / visibility:hidden / transition di sini */
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Block semua CSS keyframe dari register.css yang menyebabkan flash/blur pada refresh */
        .login-page,
        .login-background,
        .login-container,
        .login-card,
        .login-intro,
        .intro-content,
        .login-intro .intro-content {
            animation: none !important;
            filter: none !important;
            opacity: 1 !important;
            transform: none !important;
        }

        /* Register page: full viewport, no scroll */
        .login-page {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 100vh;
            height: 100vh;
            height: 100dvh;
            overflow: hidden !important;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: max(16px, env(safe-area-inset-top))
                     max(20px, env(safe-area-inset-right))
                     max(16px, env(safe-area-inset-bottom))
                     max(20px, env(safe-area-inset-left));
        }

        /* Responsive: medium-short screen (laptops ~768px height) */
        @media (max-height: 780px) {
            .login-page {
                padding: max(10px, env(safe-area-inset-top))
                         max(18px, env(safe-area-inset-right))
                         max(10px, env(safe-area-inset-bottom))
                         max(18px, env(safe-area-inset-left)) !important;
                overflow-y: auto !important;
            }
            .login-container {
                min-height: 540px !important;
            }
            .login-intro {
                padding: 32px 36px !important;
            }
            .login-logo {
                width: 85px !important;
                height: 85px !important;
                margin-bottom: 14px !important;
            }
            .login-panel {
                padding: 26px 28px !important;
            }
            .login-header h2 {
                font-size: 26px !important;
            }
            .form-group {
                margin-bottom: 10px !important;
            }
        }

        /* Responsive: very short screen (height <= 600px) */
        @media (max-height: 600px) {
            .login-page {
                padding: max(6px, env(safe-area-inset-top))
                         max(12px, env(safe-area-inset-right))
                         max(6px, env(safe-area-inset-bottom))
                         max(12px, env(safe-area-inset-left)) !important;
                overflow-y: auto !important;
            }
            .login-container {
                min-height: 480px !important;
            }
            .login-intro {
                padding: 22px 28px !important;
            }
            .login-logo {
                width: 70px !important;
                height: 70px !important;
                margin-bottom: 10px !important;
            }
            .login-panel {
                padding: 18px 22px !important;
            }
            .login-header h2 {
                font-size: 22px !important;
            }
            .form-group {
                margin-bottom: 8px !important;
            }
        }

        /* Responsive: tablet */
        @media (max-width: 900px) {
            .login-page { height: 100vh !important; height: 100dvh !important; overflow: hidden !important; }
            .login-container { grid-template-columns: 1fr !important; max-width: 430px !important; }
            .login-intro { display: none !important; }
            .login-panel { padding: 24px 20px !important; border-left: none !important; }
        }

        /* Responsive: mobile */
        @media (max-width: 480px) {
            .login-page { height: 100vh !important; height: 100dvh !important; overflow: hidden !important; padding: max(6px, env(safe-area-inset-top)) max(8px, env(safe-area-inset-right)) max(6px, env(safe-area-inset-bottom)) max(8px, env(safe-area-inset-left)) !important; }
            .login-container { border-radius: 18px !important; }
            .login-panel { padding: 18px 16px !important; }
            .login-header h2 { font-size: 22px !important; }
            .form-group { margin-bottom: 7px !important; }
        }

        /* ============================================================
           CINEMATIC CURTAIN — STRICTLY DISPLAY: NONE BY DEFAULT
           Curtain tidak boleh dirender sama sekali saat initial load / refresh.
           Hanya diaktifkan via JS jika landingToRegisterTransition === true.
           ============================================================ */
        #register-curtain {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 999999;
            pointer-events: none;
            display: none !important;
            opacity: 0;
            visibility: hidden;
            transform: translate3d(0, 100%, 0);
            background: transparent;
            will-change: transform;
        }

        #register-curtain .curtain-glow {
            display: none;
        }
    </style>
</head>

<body>

    {{-- Curtain Transition Layer (hanya aktif jika user berpindah dari Landing Page ke Register) --}}
    {{-- CSS default: display:none !important — TIDAK DIRENDER pada refresh / direct load --}}
    <div id="register-curtain" aria-hidden="true" style="display: none; opacity: 0; visibility: hidden; pointer-events: none;">
        <div class="curtain-glow"></div>
    </div>

    <main class="login-page">

        {{-- BACKGROUND --}}
        <div class="login-background"></div>

        {{-- MAIN CONTAINER --}}
        <div class="login-container">

            {{-- LEFT SIDE --}}
            <section class="login-intro">

                <div class="intro-content">

                    <img
                        src="{{ asset('images/logo-ts-white.png') }}"
                        alt="Tiga Serangkai since 1959"
                        class="login-logo">

                    <span class="intro-label">
                        <span class="intro-label-icon">📖</span>
                        PERPUSTAKAAN TIGA SERANGKAI
                    </span>

                    <div class="carousel-viewport">
                        <div class="carousel-text carousel-slide is-active" id="carousel-slide-a">
                            <h1 id="carousel-title">
                                Siap Menjelajahi<br>
                                <span>Dunia Buku?</span>
                            </h1>

                            <p id="carousel-desc">
                                Jelajahi koleksi menarik dan temukan inspirasi baru untuk membaca lebih maksimal.
                            </p>
                        </div>
                        <div class="carousel-text carousel-slide is-entering" id="carousel-slide-b" aria-hidden="true"></div>
                    </div>

                    <div class="intro-decoration">
                        <span class="active" data-index="0"></span>
                        <span data-index="1"></span>
                        <span data-index="2"></span>
                    </div>

                </div>

            </section>

            {{-- RIGHT SIDE --}}
            <section class="login-panel">

                <div class="login-card">

                    {{-- HEADER --}}
                    <div class="login-header">
                        <h2>Buat Akun</h2>
                        <p>Daftarkan dirimu untuk mulai membaca.</p>
                    </div>

                    {{-- VALIDATION ERROR --}}
                    @if ($errors->any())
                    <div class="login-alert">
                        @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                        @endforeach
                    </div>
                    @endif

                    {{-- REGISTER FORM --}}
                    <form method="POST" action="{{ route('register.store') }}" class="login-form">

                        @csrf

                        {{-- NAMA --}}
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap"
                                    autocomplete="name"
                                    required>
                            </div>
                        </div>

                        {{-- DIVISI --}}
                        <div class="form-group">
                            <label for="division">Divisi</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="7" width="18" height="14" rx="2"/>
                                        <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <path d="M3 12h18M10 12v2h4v-2"/>
                                    </svg>
                                </span>
                                <select id="division" name="division" required>
                                    <option value="" disabled {{ old('division') ? '' : 'selected' }}>Pilih divisi</option>
                                    <option value="Center Of Excellence" {{ old('division') === 'Center Of Excellence' ? 'selected' : '' }}>Center Of Excellence</option>
                                    <option value="Digital Business" {{ old('division') === 'Digital Business' ? 'selected' : '' }}>Digital Business</option>
                                    <option value="E-Publishing" {{ old('division') === 'E-Publishing' ? 'selected' : '' }}>E-Publishing</option>
                                    <option value="Finance" {{ old('division') === 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="General Trading" {{ old('division') === 'General Trading' ? 'selected' : '' }}>General Trading</option>
                                    <option value="HR &amp; GA" {{ old('division') === 'HR &amp; GA' ? 'selected' : '' }}>HR &amp; GA</option>
                                    <option value="HSE" {{ old('division') === 'HSE' ? 'selected' : '' }}>HSE</option>
                                    <option value="IQA" {{ old('division') === 'IQA' ? 'selected' : '' }}>IQA</option>
                                    <option value="IT" {{ old('division') === 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="Marketing" {{ old('division') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="MTIS Perpuskita dan Tisera" {{ old('division') === 'MTIS Perpuskita dan Tisera' ? 'selected' : '' }}>MTIS Perpuskita dan Tisera</option>
                                    <option value="MTIS Planning and Development" {{ old('division') === 'MTIS Planning and Development' ? 'selected' : '' }}>MTIS Planning and Development</option>
                                    <option value="People Development Center" {{ old('division') === 'People Development Center' ? 'selected' : '' }}>People Development Center</option>
                                    <option value="Production" {{ old('division') === 'Production' ? 'selected' : '' }}>Production</option>
                                    <option value="School Book Sales" {{ old('division') === 'School Book Sales' ? 'selected' : '' }}>School Book Sales</option>
                                    <option value="School Book Publishing" {{ old('division') === 'School Book Publishing' ? 'selected' : '' }}>School Book Publishing</option>
                                    <option value="SCM" {{ old('division') === 'SCM' ? 'selected' : '' }}>SCM</option>
                                    <option value="TAX" {{ old('division') === 'TAX' ? 'selected' : '' }}>TAX</option>
                                </select>
                                <span class="select-arrow" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan alamat email"
                                    autocomplete="email"
                                    required>
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper password-wrapper">
                                <span class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Minimal 8 karakter"
                                    autocomplete="new-password"
                                    required>
                                {{-- SHOW / HIDE PASSWORD --}}
                                <button
                                    type="button"
                                    id="passwordToggle"
                                    class="password-toggle"
                                    aria-label="Tampilkan password">
                                    👁
                                </button>
                            </div>
                        </div>

                        {{-- KONFIRMASI PASSWORD --}}
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="input-wrapper password-wrapper">
                                <span class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Ulangi password"
                                    autocomplete="new-password"
                                    required>
                                {{-- SHOW / HIDE CONFIRM PASSWORD --}}
                                <button
                                    type="button"
                                    id="confirmPasswordToggle"
                                    class="password-toggle"
                                    aria-label="Tampilkan password">
                                    👁
                                </button>
                            </div>
                        </div>

                        {{-- REGISTER BUTTON --}}
                        <button type="submit" class="login-button">
                            <span>Daftar</span>
                            <span class="login-arrow">→</span>
                        </button>

                    </form>

                    {{-- DIVIDER --}}
                    <div class="login-divider">
                        <span></span>
                        <p>atau</p>
                        <span></span>
                    </div>

                    {{-- GOOGLE --}}
                    <a href="/auth/google" class="google-button">
                        <span class="google-icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </span>
                        <span>Daftar dengan Google</span>
                    </a>

                    {{-- LOGIN LINK --}}
                    <div class="register-text">
                        <span>Sudah punya akun?</span>
                        <a href="{{ route('login') }}">Login sekarang</a>
                    </div>

                </div>

            </section>

        </div>

    </main>

    {{-- PASSWORD TOGGLE SCRIPT --}}
    <script>
        function setupPasswordToggle(inputId, buttonId) {

            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);

            if (!input || !button) return;

            const eyeIcon = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c7 0 10 7 10 7a18.5 18.5 0 0 1-3 4.2"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7c1.4 0 2.7-.3 3.8-.8"/></svg>`;

            const eyeOffIcon = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>`;

            button.innerHTML = eyeIcon;

            button.addEventListener('click', function() {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
                button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });
        }

        setupPasswordToggle('password', 'passwordToggle');
        setupPasswordToggle('password_confirmation', 'confirmPasswordToggle');
    </script>

    <script>
        (function () {
            var select = document.getElementById('division');
            var wrapper = select && select.closest('.input-wrapper');
            if (!select || !wrapper) return;

            var label = document.querySelector('label[for="division"]');
            var trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.id = 'division-trigger';
            trigger.className = 'division-select-trigger is-placeholder';
            trigger.setAttribute('role', 'combobox');
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.setAttribute('aria-controls', 'division-options');
            trigger.setAttribute('aria-required', 'true');

            if (label) {
                label.id = 'division-label';
                label.htmlFor = trigger.id;
                trigger.setAttribute('aria-labelledby', label.id);
            }

            var optionList = document.createElement('div');
            optionList.id = 'division-options';
            optionList.className = 'division-options';
            optionList.setAttribute('role', 'listbox');
            optionList.setAttribute('aria-labelledby', label ? label.id : 'division');
            optionList.setAttribute('aria-hidden', 'true');

            var optionButtons = [];
            var searchText = '';
            var searchTimeout;

            Array.prototype.forEach.call(select.options, function (option) {
                if (option.disabled || !option.value) return;

                var optionButton = document.createElement('button');
                optionButton.type = 'button';
                optionButton.className = 'division-option';
                optionButton.setAttribute('role', 'option');
                optionButton.setAttribute('aria-selected', 'false');
                optionButton.tabIndex = -1;
                optionButton.textContent = option.textContent.trim();
                optionButton.addEventListener('click', function () {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncSelection();
                    closeOptions(true);
                });
                optionButton.addEventListener('keydown', handleOptionKeydown);

                optionButtons.push(optionButton);
                optionList.appendChild(optionButton);
            });

            wrapper.insertBefore(trigger, select);
            wrapper.appendChild(optionList);
            wrapper.classList.add('division-select');
            select.classList.add('division-native-select');
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');

            function selectedIndex() {
                return optionButtons.findIndex(function (optionButton) {
                    return optionButton.getAttribute('aria-selected') === 'true';
                });
            }

            function syncSelection() {
                var selectedOption = select.options[select.selectedIndex];
                var hasValue = Boolean(selectedOption && selectedOption.value);

                trigger.textContent = selectedOption ? selectedOption.textContent.trim() : 'Pilih divisi';
                trigger.classList.toggle('is-placeholder', !hasValue);

                optionButtons.forEach(function (optionButton) {
                    var isSelected = optionButton.textContent === trigger.textContent && hasValue;
                    optionButton.setAttribute('aria-selected', String(isSelected));
                });
            }

            function openOptions(focusIndex) {
                wrapper.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                optionList.setAttribute('aria-hidden', 'false');

                var targetIndex = selectedIndex();
                if (targetIndex < 0) targetIndex = focusIndex === 'last' ? optionButtons.length - 1 : 0;
                if (focusIndex === 'first') targetIndex = 0;
                if (focusIndex === 'last') targetIndex = optionButtons.length - 1;

                if (optionButtons[targetIndex]) {
                    optionButtons[targetIndex].focus();
                    optionButtons[targetIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            function closeOptions(returnFocus) {
                wrapper.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                optionList.setAttribute('aria-hidden', 'true');
                if (returnFocus) trigger.focus();
            }

            function handleOptionKeydown(event) {
                var currentIndex = optionButtons.indexOf(event.currentTarget);
                var nextIndex = currentIndex;

                if (event.key === 'ArrowDown') nextIndex = Math.min(currentIndex + 1, optionButtons.length - 1);
                else if (event.key === 'ArrowUp') nextIndex = Math.max(currentIndex - 1, 0);
                else if (event.key === 'Home') nextIndex = 0;
                else if (event.key === 'End') nextIndex = optionButtons.length - 1;
                else if (event.key === 'Escape') {
                    event.preventDefault();
                    closeOptions(true);
                    return;
                } else if (event.key === 'Tab') {
                    closeOptions(false);
                    return;
                } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                    searchText += event.key.toLowerCase();
                    window.clearTimeout(searchTimeout);
                    searchTimeout = window.setTimeout(function () { searchText = ''; }, 600);
                    var matchIndex = optionButtons.findIndex(function (optionButton, index) {
                        return index > currentIndex && optionButton.textContent.toLowerCase().indexOf(searchText) === 0;
                    });
                    if (matchIndex < 0) {
                        matchIndex = optionButtons.findIndex(function (optionButton) {
                            return optionButton.textContent.toLowerCase().indexOf(searchText) === 0;
                        });
                    }
                    if (matchIndex >= 0) nextIndex = matchIndex;
                    else return;
                } else {
                    return;
                }

                event.preventDefault();
                if (optionButtons[nextIndex]) {
                    optionButtons[nextIndex].focus();
                    optionButtons[nextIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            trigger.addEventListener('click', function () {
                if (trigger.getAttribute('aria-expanded') === 'true') closeOptions(false);
                else openOptions();
            });

            trigger.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openOptions(event.key === 'ArrowUp' ? 'last' : undefined);
                }
            });

            select.addEventListener('invalid', function (event) {
                event.preventDefault();
                trigger.focus();
                openOptions();
            });

            select.addEventListener('change', syncSelection);
            document.addEventListener('click', function (event) {
                if (!wrapper.contains(event.target)) closeOptions(false);
            });

            syncSelection();
        })();
    </script>

    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <script>
    /* ============================================================
       REGISTER PAGE TRANSITION
       Curtain hanya aktif jika user benar-benar berasal dari Landing/Login
       melalui flag sessionStorage 'landingToRegisterTransition'.

       Direct load / Refresh / Direct URL → Register langsung tampil normal.
       Landing/Login → Register → curtain animation aktif.
    ============================================================ */
    (function () {
        'use strict';

        var landingMarkerAt = 0;
        var navigationEntry = window.performance && performance.getEntriesByType
            ? performance.getEntriesByType('navigation')[0]
            : null;
        var navigationType = navigationEntry ? navigationEntry.type : null;

        if (!navigationEntry && window.performance && performance.navigation && performance.navigation.type === 1) {
            navigationType = 'reload';
        } else if (!navigationEntry && window.performance && performance.navigation && performance.navigation.type === 0) {
            navigationType = 'navigate';
        }

        try {
            landingMarkerAt = Number(sessionStorage.getItem('landingToRegisterTransition')) || 0;
            sessionStorage.removeItem('landingToRegisterTransition');
        } catch (e) {
            landingMarkerAt = 0;
        }

        var curtain    = document.getElementById('register-curtain');
        var container  = document.querySelector('.login-container');
        var loginCard  = document.querySelector('.login-card');
        var loginIntro = document.querySelector('.intro-content');
        var isReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var markerIsFresh = landingMarkerAt > 0
            && Date.now() >= landingMarkerAt
            && Date.now() - landingMarkerAt <= 10000;
        var shouldAnimate = markerIsFresh
            && navigationType === 'navigate'
            && !isReduced
            && typeof window.gsap !== 'undefined';

        window.addEventListener('pageshow', function (event) {
            if (!event.persisted || !curtain) return;

            if (typeof window.gsap !== 'undefined') {
                gsap.killTweensOf([curtain, container, loginCard, loginIntro].filter(Boolean));
            }
            curtain.style.setProperty('display', 'none', 'important');
            curtain.style.opacity = '0';
            curtain.style.visibility = 'hidden';
            curtain.style.pointerEvents = 'none';
            curtain.style.transform = 'translate3d(0, 100%, 0)';
            [container, loginCard, loginIntro].filter(Boolean).forEach(function (element) {
                element.style.opacity = '';
                element.style.transform = '';
                element.style.transition = '';
            });
        });

        // ── FLOW 2 / 3: Direct load, Refresh, Direct URL ──
        // Jangan jalankan animasi APAPUN. Register langsung tampil normal.
        if (!shouldAnimate) {
            return;
        }

        // ── FLOW 1: Landing/Login → Register ──
        // Jalankan cinematic curtain hanya jika shouldAnimate === true

        if (!curtain) {
            // Fallback: tampil langsung tanpa animasi
            return;
        }

        // Aktifkan curtain: naik dari bawah (curtain sudah default di bawah layar)
        curtain.style.setProperty('display', 'block', 'important');
        curtain.style.opacity      = '1';
        curtain.style.visibility   = 'visible';
        curtain.style.transform    = 'translate3d(0, 0%, 0)';

        if (container) {
            container.style.opacity   = '0';
            container.style.transform = 'translateY(20px)';
        }

        function runReveal() {
            try {
                var tl = gsap.timeline({
                    onComplete: function () {
                        // Setelah animasi selesai: sembunyikan curtain dan reset
                        curtain.style.display    = 'none';
                        curtain.style.opacity    = '';
                        curtain.style.visibility = '';
                        curtain.style.transform  = '';
                        curtain.style.pointerEvents = 'none';
                        if (container) {
                            container.style.opacity   = '';
                            container.style.transform = '';
                        }
                    }
                });

                // 1. Curtain menyapu ke atas (sweep out)
                tl.to(curtain, {
                    y: '-100%',
                    duration: 0.65,
                    ease: 'power3.inOut'
                }, 0.05);

                // 2. Register card meluncur halus ke posisinya
                if (container) {
                    tl.to(container, {
                        opacity: 1,
                        y: 0,
                        duration: 0.55,
                        ease: 'power2.out'
                    }, 0.25);
                }

                // 3. Register card detail stagger
                var cardItems = [loginCard, loginIntro].filter(Boolean);
                if (cardItems.length > 0) {
                    tl.fromTo(cardItems,
                        { opacity: 0, y: 10 },
                        { opacity: 1, y: 0, duration: 0.38, stagger: 0.05, ease: 'power3.out' },
                        0.35
                    );
                }
            } catch (e) {
                curtain.style.setProperty('display', 'none', 'important');
                curtain.style.opacity = '0';
                curtain.style.visibility = 'hidden';
                curtain.style.pointerEvents = 'none';
                [container, loginCard, loginIntro].filter(Boolean).forEach(function (element) {
                    element.style.opacity = '';
                    element.style.transform = '';
                    element.style.transition = '';
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runReveal);
        } else {
            requestAnimationFrame(runReveal);
        }

    })();
    </script>


    {{-- CAROUSEL SCRIPT --}}
    <script>
        (function () {
            var slides = [
                {
                    title: 'Siap Menjelajahi<br><span>Dunia Buku?</span>',
                    desc: 'Jelajahi koleksi menarik dan temukan inspirasi baru untuk membaca lebih maksimal.'
                },
                {
                    title: 'Yuk, Temukan <span>Buku Favoritmu!</span>',
                    desc: 'Temukan bacaan yang sesuai kebutuhan, minat, dan gaya hidupmu.'
                },
                {
                    title: 'Penasaran Ada <span class="carousel-title-nowrap">Buku Apa Aja?!</span>',
                    desc: 'Nikmati kemudahan mencari dan melakukan reservasi buku kapan pun kamu butuhkan.'
                }
            ];

            var slidesEl = [
                document.getElementById('carousel-slide-a'),
                document.getElementById('carousel-slide-b')
            ];
            if (!slidesEl[0] || !slidesEl[1]) return;

            var dots = document.querySelectorAll('.intro-decoration span');
            var current = 0;
            var activeLayer = 0;
            var isAnimating = false;
            var autoTimer = null;
            var transitionDuration = 500;
            var transitionStyle = 'transform 500ms cubic-bezier(.22, 1, .36, 1), opacity 500ms ease';

            slidesEl.forEach(function (el) { el.style.transition = 'none'; });
            slidesEl[0].style.transform = 'translateX(0)';
            slidesEl[0].style.opacity = '1';
            slidesEl[0].style.zIndex = '2';
            slidesEl[1].style.transform = 'translateX(28px)';
            slidesEl[1].style.opacity = '0';
            slidesEl[1].style.zIndex = '1';
            void slidesEl[0].offsetWidth;

            function renderSlide(element, slide) {
                element.innerHTML = '<h1>' + slide.title + '</h1><p>' + slide.desc + '</p>';
            }

            function updateDots(index) {
                dots.forEach(function (dot, i) {
                    if (i === index) {
                        dot.classList.add('active');
                        dot.setAttribute('aria-selected', 'true');
                    } else {
                        dot.classList.remove('active');
                        dot.setAttribute('aria-selected', 'false');
                    }
                });
            }

            function goToSlide(targetIndex, direction) {
                if (targetIndex === current || isAnimating) return;
                isAnimating = true;

                if (!direction) {
                    direction = targetIndex > current ? 'next' : 'prev';
                }

                var activeEl = slidesEl[activeLayer];
                var incomingEl = slidesEl[1 - activeLayer];

                renderSlide(incomingEl, slides[targetIndex]);
                incomingEl.setAttribute('aria-hidden', 'false');
                activeEl.setAttribute('aria-hidden', 'true');

                updateDots(targetIndex);

                var startOffset = direction === 'next' ? '28px' : '-28px';
                var exitOffset  = direction === 'next' ? '-28px' : '28px';

                incomingEl.style.transition = 'none';
                incomingEl.style.transform = 'translateX(' + startOffset + ')';
                incomingEl.style.opacity = '0';
                incomingEl.style.zIndex = '2';

                activeEl.style.transition = 'none';
                activeEl.style.transform = 'translateX(0)';
                activeEl.style.opacity = '1';
                activeEl.style.zIndex = '1';

                void incomingEl.offsetWidth;

                requestAnimationFrame(function () {
                    activeEl.style.transition = transitionStyle;
                    activeEl.style.transform = 'translateX(' + exitOffset + ')';
                    activeEl.style.opacity = '0';

                    incomingEl.style.transition = transitionStyle;
                    incomingEl.style.transform = 'translateX(0)';
                    incomingEl.style.opacity = '1';
                });

                setTimeout(function () {
                    activeEl.style.transition = 'none';
                    activeEl.style.transform = 'translateX(' + startOffset + ')';
                    activeEl.style.opacity = '0';
                    void activeEl.offsetWidth;

                    current = targetIndex;
                    activeLayer = 1 - activeLayer;
                    isAnimating = false;
                }, transitionDuration + 30);
            }

            function startAutoplay() {
                stopAutoplay();
                autoTimer = setInterval(function () {
                    var nextIndex = (current + 1) % slides.length;
                    goToSlide(nextIndex, 'next');
                }, 3000);
            }

            function stopAutoplay() {
                if (autoTimer) {
                    clearInterval(autoTimer);
                    autoTimer = null;
                }
            }

            dots.forEach(function (dot, index) {
                dot.setAttribute('role', 'button');
                dot.setAttribute('tabindex', '0');
                dot.setAttribute('aria-label', 'Slide ' + (index + 1));
                dot.setAttribute('aria-selected', index === 0 ? 'true' : 'false');

                function trigger() {
                    if (index === current || isAnimating) return;
                    stopAutoplay();
                    var dir = index > current ? 'next' : 'prev';
                    goToSlide(index, dir);
                    startAutoplay();
                }

                dot.addEventListener('click', trigger);
                dot.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        trigger();
                    }
                });
            });

            startAutoplay();
        })();
    </script>

    <script>
        (function () {
            var background = document.querySelector('.login-background');
            if (!background) return;

            var image = new Image();
            image.onload = function () {
                background.classList.add('is-image-ready');
            };
            image.src = @json(asset('images/bg-ts.jpeg'));

            if (image.complete && image.naturalWidth > 0) {
                background.classList.add('is-image-ready');
            }
        })();
    </script>

</body>

</html>

