<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Perpustakaan Tiga Serangkai – Temukan pengetahuan, jelajahi cerita, dan mulai perjalanan membacamu.">
    <meta name="theme-color" content="#020f10">
    <meta name="color-scheme" content="dark light">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="preload" as="image" href="{{ asset('images/bg-ts.jpeg') }}" fetchpriority="high">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">

    <style>
        /* ============================================================
           SEAMLESS GSAP STAGE ARCHITECTURE
           Menggabungkan Landing Stage dan Login Stage dalam satu DOM.
           Meniadakan page reload HTTP sehingga transisi 100% butter-smooth 60fps!
           ============================================================ */
        html, body {
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            overscroll-behavior: none;
            /* Override register.css yang mengeset background: #093033 */
            background: #020f10 !important;
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }

        .viewport-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
        }

        /* ── Stage 1: Landing Stage ── */
        #landing-stage {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            z-index: 10;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #020f10;
            cursor: pointer;
            touch-action: manipulation;
        }

        /* ── Stage 2: Login Stage ── */
        #login-stage {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            z-index: 20;
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
            overflow: hidden;
            background: #093033;
        }

        /* Block CSS keyframe internal register.css agar GSAP mengontrol timeline penuh */
        #login-stage .login-page,
        #login-stage .login-background,
        #login-stage .login-container,
        #login-stage .login-card,
        #login-stage .login-intro,
        #login-stage .intro-content {
            animation: none !important;
            filter: none !important;
        }

        /* Login page: full viewport inside stage, no scroll */
        #login-stage .login-page {
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

        /* Responsive styling untuk Login Stage */
        @media (max-height: 780px) {
            #login-stage .login-page { padding: max(8px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(8px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left)) !important; }
            #login-stage .login-intro { padding: 24px 30px !important; }
            #login-stage .login-logo { width: 70px !important; height: 70px !important; margin-bottom: 10px !important; }
            #login-stage .login-panel { padding: 18px 24px !important; }
            #login-stage .login-header h2 { font-size: 24px !important; }
            #login-stage .form-group { margin-bottom: 8px !important; }
        }

        @media (max-width: 900px) {
            #login-stage .login-page { height: 100vh !important; height: 100dvh !important; overflow: hidden !important; }
            #login-stage .login-container { grid-template-columns: 1fr !important; max-width: 430px !important; }
            #login-stage .login-intro { display: none !important; }
            #login-stage .login-panel { padding: 24px 20px !important; border-left: none !important; }
        }

        @media (max-width: 480px) {
            #login-stage .login-page { height: 100vh !important; height: 100dvh !important; overflow: hidden !important; padding: max(6px, env(safe-area-inset-top)) max(8px, env(safe-area-inset-right)) max(6px, env(safe-area-inset-bottom)) max(8px, env(safe-area-inset-left)) !important; }
            #login-stage .login-container { border-radius: 18px !important; }
            #login-stage .login-panel { padding: 18px 16px !important; }
            #login-stage .login-header h2 { font-size: 22px !important; }
            #login-stage .form-group { margin-bottom: 7px !important; }
        }

        /* ── Interactive Dimensional Portal Transition ── */
        #interactive-portal {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 999999;
            overflow: hidden;
            display: none;
        }

        .portal-ring {
            position: absolute;
            width: 40px;
            height: 40px;
            margin-left: -20px;
            margin-top: -20px;
            border-radius: 50%;
            pointer-events: none;
            box-sizing: border-box;
            will-change: transform, opacity;
        }

        #portal-ring-primary {
            border: 2.5px solid rgba(94, 234, 212, 0.95);
            box-shadow: 0 0 25px rgba(94, 234, 212, 0.9),
                        0 0 50px rgba(94, 234, 212, 0.5),
                        inset 0 0 15px rgba(94, 234, 212, 0.35);
        }

        #portal-ring-secondary {
            border: 1.5px solid rgba(94, 234, 212, 0.45);
            box-shadow: 0 0 20px rgba(94, 234, 212, 0.3);
        }

        /* ── State Final Setelah Transisi ── */
        body.is-login-active {
            background: #093033 !important;
        }
        body.is-login-active #landing-stage {
            display: none !important;
        }
        body.is-login-active #login-stage {
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            position: relative;
        }
    </style>
</head>

<body>

<div class="viewport-wrapper">

    {{-- ============================================================
         STAGE 1: LANDING PAGE
    ============================================================ --}}
    <div id="landing-stage">
        <div class="landing">
            <div class="landing-hero">
                {{-- Background photo --}}
                <div class="hero-bg" aria-hidden="true"></div>

                {{-- Overlays --}}
                <div class="hero-overlay-dark"   aria-hidden="true"></div>
                <div class="hero-overlay-teal"   aria-hidden="true"></div>
                <div class="hero-glow-edge"      aria-hidden="true"></div>

                {{-- Futuristic corner decorations --}}
                <div class="hero-decor" aria-hidden="true">
                    {{-- Top-left arcs --}}
                    <svg class="decor-arc decor-arc--tl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 180 Q0 0 180 0"   stroke="rgba(94,234,212,0.22)" stroke-width="1.5" fill="none"/>
                        <path d="M0 140 Q0 0 140 0"   stroke="rgba(94,234,212,0.14)" stroke-width="1"   fill="none"/>
                        <path d="M0 100 Q0 0 100 0"   stroke="rgba(94,234,212,0.09)" stroke-width="0.8" fill="none"/>
                        <circle cx="0" cy="0" r="6" fill="rgba(94,234,212,0.5)"/>
                        <circle cx="0" cy="0" r="3" fill="rgba(94,234,212,0.9)"/>
                    </svg>

                    {{-- Bottom-right arcs --}}
                    <svg class="decor-arc decor-arc--br" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M200 20 Q200 200 20 200" stroke="rgba(94,234,212,0.22)" stroke-width="1.5" fill="none"/>
                        <path d="M200 60 Q200 200 60 200" stroke="rgba(94,234,212,0.14)" stroke-width="1"   fill="none"/>
                        <path d="M200 100 Q200 200 100 200" stroke="rgba(94,234,212,0.09)" stroke-width="0.8" fill="none"/>
                        <circle cx="200" cy="200" r="6" fill="rgba(94,234,212,0.5)"/>
                        <circle cx="200" cy="200" r="3" fill="rgba(94,234,212,0.9)"/>
                    </svg>
                </div>

                {{-- Hero Content --}}
                <div class="hero-content">
                    {{-- Logo TS putih besar --}}
                    <div class="hero-logo-wrap">
                        <img
                            src="{{ asset('images/logo-ts-white.png') }}"
                            alt="Logo TS Perpustakaan Tiga Serangkai"
                            class="hero-logo"
                            draggable="false">
                    </div>

                    {{-- Title block --}}
                    <div class="hero-title-wrap">
                        <p class="hero-subtitle-top">PERPUSTAKAAN</p>
                        <h1 class="hero-title">TIGA SERANGKAI</h1>
                    </div>

                    {{-- Tagline --}}
                    <p class="hero-tagline">
                        Temukan pengetahuan, jelajahi cerita,<br>
                        dan mulai perjalanan membacamu.
                    </p>

                    {{-- Ornament ─── 📖 ─── --}}
                    <div class="hero-ornament" aria-hidden="true">
                        <span class="orn-line"></span>
                        <svg class="orn-book" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M2 4h6a4 4 0 0 1 4 4v12.5a5.5 5.5 0 0 0-4-1.5H2V4Z"/>
                            <path d="M22 4h-6a4 4 0 0 0-4 4v12.5a5.5 5.5 0 0 1 4-1.5h6V4Z"/>
                        </svg>
                        <span class="orn-line"></span>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            @include('partials.footer')
        </div>
    </div>

    {{-- ============================================================
         INTERACTIVE DIMENSIONAL PORTAL RINGS (GSAP)
    ============================================================ --}}
    <div id="interactive-portal" aria-hidden="true">
        <div id="portal-ring-primary" class="portal-ring"></div>
        <div id="portal-ring-secondary" class="portal-ring"></div>
    </div>

    {{-- ============================================================
         STAGE 2: LOGIN PAGE (Sama persis dengan halaman Login)
    ============================================================ --}}
    <div id="login-stage" aria-hidden="true">
        <main class="login-page">
            <div class="login-background"></div>

            <div class="login-container">
                {{-- BAGIAN KIRI: INTRO / CAROUSEL --}}
                <section class="login-intro">
                    <div class="intro-content">
                        <img
                            src="{{ asset('images/logo-ts-white.png') }}"
                            alt="Tiga Serangkai"
                            class="login-logo">

                        <span class="intro-label">
                            <span class="intro-label-icon">📖</span>
                            PERPUSTAKAAN TIGA SERANGKAI
                        </span>

                        <div class="carousel-viewport">
                            <div class="carousel-text carousel-slide is-active" id="carousel-slide-a">
                                <h1 id="carousel-title">
                                    Mulai perjalanan
                                    <span>membacamu.</span>
                                </h1>
                                <p id="carousel-desc">
                                    Buat akun untuk menemukan koleksi buku,
                                    melakukan reservasi, dan melihat riwayat
                                    peminjamanmu dengan lebih mudah.
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

                {{-- BAGIAN KANAN: CARD LOGIN --}}
                <section class="login-panel">
                    <div class="login-card">
                        <div class="login-header">
                            <h2>WELCOME!!</h2>
                            <p>Selamat menjelajah jendela dunia.</p>
                        </div>

                        @if ($errors->any())
                            <div class="login-alert">
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="login-alert">
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert-success">
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}" class="login-form" id="loginForm">
                            @csrf

                            <div class="form-group">
                                <label for="email">Username / Email</label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <input id="email" type="text" name="email" placeholder="Masukkan email atau username" value="{{ old('email') }}" required autocomplete="username">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-wrapper password-wrapper">
                                    <span class="input-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    </span>
                                    <input id="password" type="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan atau sembunyikan password" onclick="togglePwd()">
                                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="forgot-wrap">
                                <a href="#" class="forgot-link">Lupa Password?</a>
                            </div>

                            <button type="submit" class="login-button" id="loginBtn">
                                <span>LOGIN</span>
                            </button>
                        </form>

                        <div class="login-divider">
                            <span></span>
                            <p>atau</p>
                            <span></span>
                        </div>

                        <a href="{{ route('google.redirect') }}" class="google-button" id="googleLoginBtn">
                            <span class="google-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </span>
                            <span>Login dengan Google</span>
                        </a>

                        <div class="register-text">
                            <span>Belum punya akun?</span>
                            <a href="{{ route('register') }}" onclick="try{sessionStorage.setItem('landingToRegisterTransition','true')}catch(e){}">Daftar sekarang</a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

</div>

<script src="{{ asset('js/gsap.min.js') }}"></script>

<script>
/* ── Password Visibility Toggle ── */
function togglePwd() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eyeIcon');
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>

<script>
/* ── Login Intro Carousel ── */
function initCarousel() {
    var slides = [
        {
            title: 'Mulai perjalanan <span>membacamu.</span>',
            desc: 'Buat akun untuk menemukan koleksi buku, melakukan reservasi, dan melihat riwayat peminjamanmu dengan lebih mudah.'
        },
        {
            title: 'Jelajahi dunia <span>pengetahuan.</span>',
            desc: 'Temukan berbagai koleksi buku pilihan dan nikmati pengalaman membaca yang lebih praktis.'
        },
        {
            title: 'Temukan cerita <span>favoritmu.</span>',
            desc: 'Cari, reservasi, dan kelola aktivitas peminjaman buku dalam satu tempat.'
        }
    ];

    var current = 0;
    var slidesEl = [
        document.getElementById('carousel-slide-a'),
        document.getElementById('carousel-slide-b')
    ];
    if (!slidesEl[0] || !slidesEl[1]) return;

    var dots = document.querySelectorAll('#login-stage .intro-decoration span');
    var transition = 'transform .7s cubic-bezier(.22, 1, .36, 1), opacity .7s ease-in-out';
    var activeLayer = 0;

    slidesEl.forEach(function (element) {
        element.style.transition = 'none';
    });
    slidesEl[0].style.transform = 'translateX(0)';
    slidesEl[0].style.opacity = '1';
    slidesEl[1].style.transform = 'translateX(100%)';
    slidesEl[1].style.opacity = '0';
    void slidesEl[0].offsetWidth;
    slidesEl.forEach(function (element) {
        element.style.transition = '';
    });

    function renderSlide(element, slide) {
        element.innerHTML = '<h1>' + slide.title + '</h1><p>' + slide.desc + '</p>';
    }

    function goToSlide(index) {
        var active = slidesEl[activeLayer];
        var incoming = slidesEl[1 - activeLayer];

        renderSlide(incoming, slides[index]);
        incoming.setAttribute('aria-hidden', 'false');
        active.setAttribute('aria-hidden', 'true');

        dots.forEach(function (dot) { dot.classList.remove('active'); });
        if (dots[index]) dots[index].classList.add('active');

        active.style.transition = 'none';
        active.style.transform = 'translateX(0)';
        active.style.opacity = '1';
        incoming.style.transition = 'none';
        incoming.style.transform = 'translateX(100%)';
        incoming.style.opacity = '0';
        void incoming.offsetWidth;

        requestAnimationFrame(function () {
            active.style.transition = transition;
            active.style.transform = 'translateX(-100%)';
            active.style.opacity = '0';
            incoming.style.transition = transition;
            incoming.style.transform = 'translateX(0)';
            incoming.style.opacity = '1';
        });

        setTimeout(function () {
            active.style.transition = 'none';
            active.style.transform = 'translateX(100%)';
            active.style.opacity = '0';
            void active.offsetWidth;
            active.style.transition = '';
        }, 700);

        activeLayer = 1 - activeLayer;
    }

    setInterval(function () {
        current = (current + 1) % slides.length;
        goToSlide(current);
    }, 3000);
}
</script>

<script>
/* ── MODERN INTERACTIVE DIMENSIONAL IRIS PORTAL TRANSITION (GSAP) ── */
(function () {
    'use strict';

    var landingStage    = document.getElementById('landing-stage');
    var portalOverlay   = document.getElementById('interactive-portal');
    var ringPrimary     = document.getElementById('portal-ring-primary');
    var ringSecondary   = document.getElementById('portal-ring-secondary');
    var loginStage      = document.getElementById('login-stage');
    var loginCard       = document.querySelector('#login-stage .login-card');
    var loginIntro      = document.querySelector('#login-stage .intro-content');
    var loginHeader     = document.querySelector('#login-stage .login-header');
    var loginForm       = document.querySelector('#login-stage .login-form');
    var loginDivider    = document.querySelector('#login-stage .login-divider');
    var googleBtn       = document.querySelector('#login-stage .google-button');
    var registerTxt     = document.querySelector('#login-stage .register-text');

    var loginUrl        = @json(route('login'));
    var hasServerAlerts = {{ ($errors->any() || session('error') || session('success')) ? 'true' : 'false' }};
    var hasTriggered    = false;
    var autoTimer       = null;

    function completeTransition() {
        document.body.classList.add('is-login-active');
        document.body.style.pointerEvents = '';

        if (landingStage) {
            landingStage.style.display = 'none';
            landingStage.setAttribute('aria-hidden', 'true');
        }

        if (portalOverlay) {
            portalOverlay.style.display = 'none';
        }

        if (loginStage) {
            loginStage.style.visibility = 'visible';
            loginStage.style.opacity = '1';
            loginStage.style.pointerEvents = 'auto';
            loginStage.style.clipPath = '';
            loginStage.style.webkitClipPath = '';
            loginStage.setAttribute('aria-hidden', 'false');
        }

        if (typeof gsap !== 'undefined') {
            gsap.set([landingStage, loginStage, loginCard, loginIntro, loginHeader, loginForm, loginDivider, googleBtn, registerTxt].filter(Boolean), {
                clearProps: "transform,opacity,scale,willChange,clipPath,webkitClipPath,transformOrigin"
            });
        }

        try {
            window.history.replaceState(null, '', loginUrl);
        } catch (e) {}

        initCarousel();
    }

    function runPortalTransition(clientX, clientY) {
        if (hasTriggered) return;
        hasTriggered = true;
        if (autoTimer) clearTimeout(autoTimer);

        document.body.style.pointerEvents = 'none';

        if (!landingStage || !loginStage) {
            completeTransition();
            return;
        }

        var isReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (typeof gsap === 'undefined' || isReduced) {
            completeTransition();
            return;
        }

        // Hitung koordinat asal (interaktif berdasarkan klik/tap user, atau tengah layar jika auto/keyboard)
        var originX = (typeof clientX === 'number' && !isNaN(clientX)) ? clientX : Math.round(window.innerWidth / 2);
        var originY = (typeof clientY === 'number' && !isNaN(clientY)) ? clientY : Math.round(window.innerHeight / 2);

        // Radius maksimal hingga sudut layar terjauh
        var cornerX = Math.max(originX, window.innerWidth - originX);
        var cornerY = Math.max(originY, window.innerHeight - originY);
        var maxRadius = Math.ceil(Math.hypot(cornerX, cornerY)) + 80;

        // Posisikan portal energy rings
        if (portalOverlay && ringPrimary && ringSecondary) {
            portalOverlay.style.display = 'block';
            ringPrimary.style.left = originX + 'px';
            ringPrimary.style.top  = originY + 'px';
            ringSecondary.style.left = originX + 'px';
            ringSecondary.style.top  = originY + 'px';
        }

        var startClip = "circle(0px at " + originX + "px " + originY + "px)";
        var endClip   = "circle(" + maxRadius + "px at " + originX + "px " + originY + "px)";
        var ringScale = (maxRadius / 20) * 1.05;

        // GPU layer setup
        var animTargets = [landingStage, loginStage, loginCard, ringPrimary, ringSecondary].filter(Boolean);
        gsap.set(animTargets, { willChange: "transform, opacity" });

        // Set status awal
        gsap.set(landingStage, {
            transformOrigin: originX + "px " + originY + "px"
        });

        gsap.set(loginStage, {
            visibility: "visible",
            opacity: 1,
            clipPath: startClip,
            webkitClipPath: startClip,
            scale: 1.04,
            transformOrigin: originX + "px " + originY + "px"
        });

        if (loginCard) {
            gsap.set(loginCard, {
                opacity: 0,
                y: 32,
                scale: 0.965
            });
        }

        if (loginIntro) {
            gsap.set(loginIntro, {
                opacity: 0,
                x: -24
            });
        }

        // MASTER GSAP TIMELINE — 60 FPS ULTRA FLUID IRIS TRANSITION
        var tl = gsap.timeline({
            defaults: {
                force3D: true,
                overwrite: "auto"
            },
            onComplete: function () {
                gsap.set(animTargets, { clearProps: "willChange" });
                completeTransition();
            }
        });

        // 1. Landing Stage zooms back into depth and fades
        tl.to(landingStage, {
            scale: 0.92,
            opacity: 0,
            y: -10,
            duration: 0.76,
            ease: "power2.inOut"
        }, 0);

        // 2. Shockwave Rings expand from origin
        if (ringPrimary) {
            tl.fromTo(ringPrimary, {
                scale: 0,
                opacity: 1
            }, {
                scale: ringScale,
                opacity: 0,
                duration: 0.86,
                ease: "power2.out"
            }, 0);
        }

        if (ringSecondary) {
            tl.fromTo(ringSecondary, {
                scale: 0,
                opacity: 0.75
            }, {
                scale: ringScale * 1.08,
                opacity: 0,
                duration: 0.92,
                ease: "power2.out"
            }, 0.04);
        }

        // 3. Dimensional Iris Aperture opens smoothly to reveal Login Stage
        tl.to(loginStage, {
            clipPath: endClip,
            webkitClipPath: endClip,
            scale: 1.0,
            duration: 0.82,
            ease: "power3.inOut"
        }, 0.02);

        // 4. Login Card materializes forward
        if (loginCard) {
            tl.to(loginCard, {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.64,
                ease: "power4.out"
            }, 0.32);
        }

        // 5. Login Intro branding glides in (desktop)
        if (loginIntro) {
            tl.to(loginIntro, {
                opacity: 1,
                x: 0,
                duration: 0.58,
                ease: "power3.out"
            }, 0.36);
        }

        // 6. Stagger cascade for card form controls
        var cardChildren = [loginHeader, loginForm, loginDivider, googleBtn, registerTxt].filter(Boolean);
        if (cardChildren.length > 0) {
            tl.fromTo(cardChildren, {
                opacity: 0,
                y: 12
            }, {
                opacity: 1,
                y: 0,
                duration: 0.38,
                stagger: 0.04,
                ease: "power3.out"
            }, 0.42);
        }
    }

    // Direct error alerts skip directly to login
    if (hasServerAlerts) {
        completeTransition();
        return;
    }

    // INTERACTIVE TRIGGERS:
    // 1. Klik di mana saja pada Landing Page
    if (landingStage) {
        landingStage.addEventListener('click', function (e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            runPortalTransition(e.clientX, e.clientY);
        });

        landingStage.addEventListener('touchstart', function (e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            if (e.touches && e.touches[0]) {
                runPortalTransition(e.touches[0].clientX, e.touches[0].clientY);
            }
        }, { passive: true });
    }

    // 2. Keyboard interaction (Enter / Space)
    window.addEventListener('keydown', function (e) {
        if (hasTriggered) return;
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            runPortalTransition();
        }
    });

    // 3. Auto timer setelah 2.2 detik jika user tidak berinteraksi
    autoTimer = setTimeout(function () {
        runPortalTransition();
    }, 2200);

})();
</script>

</body>
</html>