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
           CANVA PRESENTATION SEAMLESS STAGE ARCHITECTURE
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
            background: #020f10; /* Asli Landing Page */
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }

        .viewport-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            background: #020f10; /* Asli Landing Page */
        }

        /* ── Stage 1: Landing Stage (Desain Asli Utuh) ── */
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

        /* Elemen hero dimulai invisible — GSAP animasikan saat masuk */
        #landing-stage .hero-decor,
        #landing-stage .site-footer {
            opacity: 0;
        }

        /* ── Stage 2: Login Stage (Identik 100% dengan Register) ── */
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
            background: #093033; /* Identik register.blade.php */
        }

        #login-stage .login-background {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(90deg,
                    rgba(9, 48, 51, .72),
                    rgba(9, 48, 51, .35) 45%,
                    rgba(9, 48, 51, .15)),
                url('{{ asset('images/bg-ts.jpeg') }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
            transform: scale(1.02);
            display: block !important;
        }

        #login-stage .login-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 75% 45%,
                    rgba(115, 218, 210, .16),
                    transparent 35%);
            pointer-events: none;
        }

        /* Matikan animasi internal CSS agar GSAP mengontrol timeline penuh */
        #login-stage .login-page,
        #login-stage .login-container,
        #login-stage .login-card,
        #login-stage .login-intro,
        #login-stage .intro-content {
            animation: none !important;
            filter: none !important;
        }

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
            background: transparent !important;
            padding: max(16px, env(safe-area-inset-top))
                     max(20px, env(safe-area-inset-right))
                     max(16px, env(safe-area-inset-bottom))
                     max(20px, env(safe-area-inset-left));
        }

        @media (max-height: 780px) {
            #login-stage .login-page {
                padding: max(14px, env(safe-area-inset-top))
                         max(18px, env(safe-area-inset-right))
                         max(14px, env(safe-area-inset-bottom))
                         max(18px, env(safe-area-inset-left)) !important;
            }
            #login-stage .login-container {
                min-height: 520px !important;
            }
            #login-stage .login-intro {
                padding: 36px 40px !important;
            }
            #login-stage .login-logo {
                width: 90px !important;
                height: 90px !important;
                margin-bottom: 16px !important;
            }
            #login-stage .login-panel {
                padding: 32px 30px !important;
            }
            #login-stage .login-header h2 {
                font-size: 28px !important;
            }
            #login-stage .form-group {
                margin-bottom: 12px !important;
            }
        }

        @media (max-height: 600px) {
            #login-stage .login-page {
                padding: max(6px, env(safe-area-inset-top))
                         max(12px, env(safe-area-inset-right))
                         max(6px, env(safe-area-inset-bottom))
                         max(12px, env(safe-area-inset-left)) !important;
                overflow-y: auto !important;
            }
            #login-stage .login-container {
                min-height: 460px !important;
            }
            #login-stage .login-intro {
                padding: 22px 28px !important;
            }
            #login-stage .login-logo {
                width: 70px !important;
                height: 70px !important;
                margin-bottom: 10px !important;
            }
            #login-stage .login-panel {
                padding: 20px 22px !important;
            }
            #login-stage .login-header h2 {
                font-size: 24px !important;
            }
            #login-stage .form-group {
                margin-bottom: 8px !important;
            }
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

        body.is-login-active #landing-stage {
            display: none !important;
        }

        body.is-login-active #login-stage {
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
    </style>
</head>

<body>

<div class="viewport-wrapper">

    {{-- ============================================================
         STAGE 1: LANDING PAGE (Desain asli 100% utuh)
    ============================================================ --}}
    <div id="landing-stage">
        <div class="landing">
            <div class="landing-hero">
                {{-- Background photo asli Landing Page --}}
                <div class="hero-bg"></div>

                {{-- Dark cinematic layer asli --}}
                <div class="hero-overlay-dark"></div>

                {{-- Teal color-grade asli --}}
                <div class="hero-overlay-teal"></div>

                {{-- Teal glow edges (vignette) asli --}}
                <div class="hero-glow-edge"></div>

                {{-- Futuristic corner decorations --}}
                <div class="hero-decor" aria-hidden="true">
                    {{-- Top-left arcs --}}
                    <svg class="decor-arc decor-arc--tl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 180 Q0 0 180 0" stroke="rgba(94,234,212,0.22)" stroke-width="1.5" fill="none"/>
                        <path d="M0 140 Q0 0 140 0" stroke="rgba(94,234,212,0.14)" stroke-width="1" fill="none"/>
                        <path d="M0 100 Q0 0 100 0" stroke="rgba(94,234,212,0.09)" stroke-width="0.8" fill="none"/>
                        <circle cx="0" cy="0" r="6" fill="rgba(94,234,212,0.5)"/>
                        <circle cx="0" cy="0" r="3" fill="rgba(94,234,212,0.9)"/>
                    </svg>

                    {{-- Bottom-right arcs --}}
                    <svg class="decor-arc decor-arc--br" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M200 20 Q200 200 20 200" stroke="rgba(94,234,212,0.22)" stroke-width="1.5" fill="none"/>
                        <path d="M200 60 Q200 200 60 200" stroke="rgba(94,234,212,0.14)" stroke-width="1" fill="none"/>
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
         STAGE 2: LOGIN PAGE (Desain asli 100% utuh)
    ============================================================ --}}
    <div id="login-stage" aria-hidden="true">
        <main class="login-page login-page--auth">
            {{-- Background login identik 100% dengan register --}}
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
                            <a href="{{ route('register') }}" onclick="try{sessionStorage.setItem('landingToRegisterTransition', String(Date.now()))}catch(e){}">Daftar sekarang</a>
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
/* ── Login Intro Carousel (Interactive Indicators + Autoplay + Slide+Fade) ── */
var carouselInstance = null;

function initCarousel() {
    if (carouselInstance) return;

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

    var dots = document.querySelectorAll('#login-stage .intro-decoration span');
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

    carouselInstance = {
        goToSlide: goToSlide,
        destroy: stopAutoplay
    };
}
</script>

<script>
/* ── CANVA PRESENTATION TRANSITION: MATCH & MOVE + DISSOLVE + SLIDE & FADE ── */
(function () {
    'use strict';

    var landingStage  = document.getElementById('landing-stage');
    var loginStage    = document.getElementById('login-stage');
    var heroLogoWrap  = document.querySelector('.hero-logo-wrap');
    var heroTitle     = document.querySelector('.hero-title-wrap');
    var heroTagline   = document.querySelector('.hero-tagline');
    var heroOrnament  = document.querySelector('.hero-ornament');
    var heroDecor     = document.querySelector('.hero-decor');
    var footer        = document.querySelector('#landing-stage .site-footer');
    var heroBg        = document.querySelector('#landing-stage .hero-bg');

    var loginCard     = document.querySelector('#login-stage .login-card');
    var loginIntro    = document.querySelector('#login-stage .login-intro');
    var loginLogo     = document.querySelector('#login-stage .login-logo');
    var introLabel    = document.querySelector('#login-stage .intro-label');
    var carouselTitle = document.querySelector('#login-stage #carousel-title');
    var carouselDesc  = document.querySelector('#login-stage #carousel-desc');
    var loginHeader   = document.querySelector('#login-stage .login-header');
    var loginForm     = document.querySelector('#login-stage .login-form');
    var loginDivider  = document.querySelector('#login-stage .login-divider');
    var googleBtn     = document.querySelector('#login-stage .google-button');
    var registerTxt   = document.querySelector('#login-stage .register-text');

    var loginUrl        = @json(route('login'));
    var hasServerAlerts = {{ ($errors->any() || session('error') || session('success')) ? 'true' : 'false' }};
    var hasTriggered    = false;
    var autoTimer       = null;
    var isGsapReady     = typeof gsap !== 'undefined';
    var isReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ─ FALLBACK: langsung tampil login jika ada alert dari server ─
    function completeTransitionImmediate() {
        document.body.classList.add('is-login-active');
        if (landingStage) landingStage.style.display = 'none';
        if (loginStage) {
            loginStage.style.visibility    = 'visible';
            loginStage.style.opacity       = '1';
            loginStage.style.pointerEvents = 'auto';
        }
        [heroLogoWrap, heroTitle, heroTagline, heroOrnament, heroDecor, footer].forEach(function (el) {
            if (el) el.style.opacity = '1';
        });
        initCarousel();
    }

    if (hasServerAlerts) {
        completeTransitionImmediate();
        return;
    }

    // ══════════════════════════════════════════════════════════
    // FASE 1: LANDING ENTRANCE (hero elements fade-in saat load)
    // ══════════════════════════════════════════════════════════
    if (isGsapReady && !isReducedMotion) {
        var entranceTl = gsap.timeline({ defaults: { ease: 'power3.out', force3D: true } });

        if (heroDecor) {
            entranceTl.fromTo(heroDecor,
                { opacity: 0 }, { opacity: 1, duration: 0.6 }, 0);
        }
        if (footer) {
            entranceTl.fromTo(footer,
                { opacity: 0 }, { opacity: 1, duration: 0.7 }, 0.1);
        }
        if (heroLogoWrap) {
            entranceTl.fromTo(heroLogoWrap,
                { opacity: 0, scale: 0.92, y: 10 },
                { opacity: 1, scale: 1, y: 0, duration: 0.75 }, 0.1);
        }
        if (heroTitle) {
            entranceTl.fromTo(heroTitle,
                { opacity: 0, y: 18 }, { opacity: 1, y: 0, duration: 0.65 }, 0.28);
        }
        if (heroTagline) {
            entranceTl.fromTo(heroTagline,
                { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: 0.55 }, 0.44);
        }
        if (heroOrnament) {
            entranceTl.fromTo(heroOrnament,
                { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: 0.45 }, 0.56);
        }
    } else {
        [heroLogoWrap, heroTitle, heroTagline, heroOrnament, heroDecor, footer].forEach(function (el) {
            if (el) el.style.opacity = '1';
        });
    }

    // ══════════════════════════════════════════════════════════
    // FASE 2: TRANSISI KE LOGIN (exit landing + entrance login)
    // ══════════════════════════════════════════════════════════
    function runCanvaTransition() {
        if (hasTriggered) return;
        hasTriggered = true;
        if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }

        try {
            sessionStorage.setItem('canva_trans_from_landing', 'true');
        } catch (e) {}

        if (!isGsapReady || isReducedMotion || !landingStage || !loginStage) {
            completeTransitionImmediate();
            try { history.pushState({ stage: 'login' }, '', loginUrl); } catch (e) {}
            return;
        }

        var isMobile     = window.innerWidth <= 900;
        var introVisible = loginIntro && window.getComputedStyle(loginIntro).display !== 'none';

        landingStage.style.pointerEvents = 'none';

        var tl = gsap.timeline({
            defaults: { force3D: true },
            onComplete: function () {
                document.body.classList.add('is-login-active');
                if (landingStage) landingStage.style.display = 'none';
                if (loginStage) {
                    loginStage.style.visibility    = 'visible';
                    loginStage.style.opacity       = '1';
                    loginStage.style.pointerEvents = 'auto';
                }
                var loginEls = [loginIntro, loginCard, loginLogo, introLabel,
                    carouselTitle, carouselDesc, loginHeader, loginForm,
                    loginDivider, googleBtn, registerTxt].filter(Boolean);
                gsap.set(loginEls, { x: 0, y: 0, opacity: 1 });
                initCarousel();
                try { history.pushState({ stage: 'login' }, '', loginUrl); } catch (e) {}
            }
        });

        // ─ EXIT LANDING (0.0 – 0.55s) ─
        var scaleExits = [heroLogoWrap, heroOrnament].filter(Boolean);
        if (scaleExits.length) {
            tl.to(scaleExits, { scale: 0.90, opacity: 0, duration: 0.42, ease: 'power2.inOut' }, 0);
        }
        if (heroTitle) {
            tl.to(heroTitle, { y: -20, opacity: 0, duration: 0.36, ease: 'power2.inOut' }, 0.05);
        }
        if (heroTagline) {
            tl.to(heroTagline, { y: -14, opacity: 0, duration: 0.34, ease: 'power2.inOut' }, 0.12);
        }
        var bgExits = [heroDecor, footer].filter(Boolean);
        if (bgExits.length) {
            tl.to(bgExits, { opacity: 0, duration: 0.38, ease: 'power2.inOut' }, 0.04);
        }
        if (heroBg) {
            tl.to(heroBg, { scale: 1.08, duration: 0.60, ease: 'power2.inOut' }, 0);
        }
        tl.to(landingStage, { opacity: 0, duration: 0.50, ease: 'power2.inOut' }, 0.05);

        // ─ AKTIFKAN LOGIN STAGE (crossfade dissolve mulai di 0.20s) ─
        tl.set(loginStage, { visibility: 'visible', pointerEvents: 'none' }, 0.20);
        tl.fromTo(loginStage,
            { opacity: 0 },
            { opacity: 1, duration: 0.55, ease: 'power2.out' }, 0.20);

        // ─ ENTRANCE LOGIN (0.32 – 0.90s) ─
        if (introVisible && loginIntro) {
            tl.fromTo(loginIntro,
                { opacity: 0, x: -28 },
                { opacity: 1, x: 0, duration: 0.52, ease: 'power3.out' }, 0.32);

            var introHeaders = [loginLogo, introLabel].filter(Boolean);
            if (introHeaders.length) {
                tl.fromTo(introHeaders,
                    { opacity: 0, y: 10 },
                    { opacity: 1, y: 0, duration: 0.40, stagger: 0.07, ease: 'power3.out' }, 0.40);
            }

            var introTexts = [carouselTitle, carouselDesc].filter(Boolean);
            if (introTexts.length) {
                tl.fromTo(introTexts,
                    { opacity: 0, y: 12 },
                    { opacity: 1, y: 0, duration: 0.42, stagger: 0.07, ease: 'power3.out' }, 0.46);
            }
        }

        if (loginCard) {
            tl.fromTo(loginCard,
                { opacity: 0, x: isMobile ? 0 : 28, y: isMobile ? 18 : 0 },
                { opacity: 1, x: 0, y: 0, duration: 0.52, ease: 'power3.out' }, 0.34);
        }

        var formItems = [loginHeader, loginForm, loginDivider, googleBtn, registerTxt].filter(Boolean);
        if (formItems.length) {
            tl.fromTo(formItems,
                { opacity: 0, y: 7 },
                { opacity: 1, y: 0, duration: 0.32, stagger: 0.028, ease: 'power2.out' }, 0.44);
        }
    }

    // ─ TRIGGERS ─
    autoTimer = setTimeout(runCanvaTransition, 2000);

    if (landingStage) {
        landingStage.addEventListener('click', function (e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            runCanvaTransition();
        });
    }

    window.addEventListener('keydown', function (e) {
        if (hasTriggered) return;
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            runCanvaTransition();
        }
    });

    window.addEventListener('popstate', function (e) {
        if (!e.state || e.state.stage !== 'login') {
            document.body.classList.remove('is-login-active');
            if (landingStage) {
                landingStage.style.display       = '';
                landingStage.style.pointerEvents = '';
                if (isGsapReady) gsap.set(landingStage, { opacity: 1 });
            }
            if (loginStage) {
                loginStage.style.visibility    = 'hidden';
                loginStage.style.opacity       = '0';
                loginStage.style.pointerEvents = 'none';
            }
            [heroLogoWrap, heroTitle, heroTagline, heroOrnament, heroDecor, footer].forEach(function (el) {
                if (!el) return;
                if (isGsapReady) gsap.set(el, { opacity: 1, y: 0, scale: 1 });
                else el.style.opacity = '1';
            });
            hasTriggered = false;
        } else {
            completeTransitionImmediate();
        }
    });

    window.addEventListener('pagehide', function () {
        if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
    });
})();
</script>

</body>
</html>
