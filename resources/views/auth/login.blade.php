<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login – Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Login ke sistem perpustakaan Tiga Serangkai.">
    <meta name="color-scheme" content="dark light">
    <link rel="preload" as="image" href="{{ asset('images/bg-ts.jpeg') }}" fetchpriority="high">
    <style>
        /* ============================================================
           LOGIN PAGE — ZERO GREEN FLASH ON REFRESH / DIRECT LOAD
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
        .intro-content {
            animation: none !important;
            filter: none !important;
        }

        /* Login page: full viewport, no scroll */
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

        /* Responsive: short screen */
        @media (max-height: 780px) {
            .login-page { padding: max(8px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(8px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left)) !important; }
            .login-intro { padding: 24px 30px !important; }
            .login-logo { width: 70px !important; height: 70px !important; margin-bottom: 10px !important; }
            .login-panel { padding: 18px 24px !important; }
            .login-header h2 { font-size: 24px !important; }
            .form-group { margin-bottom: 8px !important; }
        }

        /* Responsive: tablet */
        @media (max-width: 900px) {
            html, body { height: auto !important; min-height: 100%; overflow-x: hidden !important; overflow-y: auto !important; }
            .login-page { min-height: 100vh !important; min-height: 100dvh !important; height: auto !important; overflow-x: hidden !important; overflow-y: visible !important; }
            .login-container { grid-template-columns: 1fr !important; max-width: 430px !important; }
            .login-intro { display: none !important; }
            .login-panel { padding: 24px 20px !important; border-left: none !important; }
        }

        /* Responsive: mobile */
        @media (max-width: 480px) {
            .login-page { padding: max(6px, env(safe-area-inset-top)) max(8px, env(safe-area-inset-right)) max(6px, env(safe-area-inset-bottom)) max(8px, env(safe-area-inset-left)) !important; }
            .login-container { width: calc(100% - 12px) !important; max-width: 300px !important; border-radius: 15px !important; }
            .login-panel { padding: 7px !important; }
            .login-card { padding: 12px 10px 10px !important; }
            .login-header { margin-bottom: 12px !important; }
            .login-header h2 { font-size: 19px !important; }
            .login-header p { font-size: 12px !important; }
            .form-group { margin-bottom: 4px !important; }
            .form-group label { margin-bottom: 4px !important; font-size: 12px !important; }
            .form-group input { height: 40px !important; padding-right: 42px !important; }
            .forgot-wrap { margin: 2px 0 10px !important; }
            .login-button { height: 40px !important; min-height: 40px !important; }
            .login-divider { margin: 9px 0 8px !important; }
            .google-button { height: 40px !important; min-height: 40px !important; }
            .register-text { margin-top: 10px !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

<main class="login-page">

    <div class="login-background"></div>

    <div class="login-container">

        {{-- BAGIAN KIRI: TAMPILAN SEPERTI HALAMAN REGISTER --}}
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

        {{-- BAGIAN KANAN: CARD LOGIN TETAP --}}
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
                <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>

        </div>
        </section>
    </div>
</main>

<script>
    (function () {
        var body = document.body;
        function revealPage() {
            if (!body) return;
            requestAnimationFrame(function () {
                body.classList.remove('page-loading');
                body.classList.add('page-ready');
            });
        }

        function startReveal() {
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(revealPage).catch(revealPage);
                return;
            }
            revealPage();
        }

        if (document.readyState === 'complete') {
            startReveal();
        } else {
            window.addEventListener('load', startReveal, { once: true });
        }
    })();
</script>

<script>
function togglePwd() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eyeIcon');
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
    /* ── Login Intro Carousel (Interactive Indicators + Slide+Fade) ── */
    (function () {
        function renderSlide(element, slide) {
            element.innerHTML = '<h1>' + slide.title + '</h1><p>' + slide.desc + '</p>';
        }

        function updateDots(index) {
            var dots = document.querySelectorAll('.intro-decoration span');
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

        try {
            var isFromLanding = sessionStorage.getItem('canva_trans_from_landing') === 'true';
            sessionStorage.removeItem('canva_trans_from_landing');

            if (isFromLanding) {
                slidesEl[0].innerHTML = '<h1>' + slides[0].title + '</h1><p>' + slides[0].desc + '</p>';
                slidesEl[1].innerHTML = '';
            }
        } catch (e) {
            // Ignore storage issues; keep the default slide state.
        }

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

        renderSlide(slidesEl[0], slides[0]);
        updateDots(0);

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
            autoTimer = window.setInterval(function () {
                if (isAnimating) return;
                goToSlide((current + 1) % slides.length, 'next');
            }, 3000);
        }

        function stopAutoplay() {
            if (autoTimer !== null) {
                window.clearInterval(autoTimer);
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

<script src="{{ asset('js/gsap.min.js') }}"></script>
<script>
/* ── CANVA PRESENTATION: SLIDE + FADE LOGIN REVEAL (350–500 ms) ── */
(function () {
    'use strict';

    // Periksa apakah navigasi berasal dari Landing Page (penanda sementara sekali pakai)
    var isFromLanding = false;
    try {
        isFromLanding = sessionStorage.getItem('canva_trans_from_landing') === 'true';
        // Hapus seketika agar saat refresh / URL langsung tidak memutar animasi ulang
        sessionStorage.removeItem('canva_trans_from_landing');
    } catch (e) {
        isFromLanding = false;
    }

    // Jika bukan dari Landing Page, refresh, URL langsung, atau reduced motion: tampilkan normal langsung tanpa animasi
    var isReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!isFromLanding || isReducedMotion || typeof gsap === 'undefined') {
        return;
    }

    var loginIntro = document.querySelector('.login-intro');
    var loginCard  = document.querySelector('.login-card');
    var isMobile   = window.innerWidth <= 900;
    var introVisible = loginIntro && window.getComputedStyle(loginIntro).display !== 'none';

    function runSlideFadeEntrance() {
        var tl = gsap.timeline({
            defaults: {
                ease: 'power2.out',
                force3D: true
            },
            onComplete: function () {
                gsap.set([loginIntro, loginCard].filter(Boolean), {
                    clearProps: 'transform,opacity,willChange'
                });
            }
        });

        // Panel informasi transparan di kiri: muncul secara bertahap (slide horizontal + fade-in, 350–500 ms)
        if (introVisible) {
            tl.fromTo(loginIntro, {
                opacity: 0,
                x: -24
            }, {
                opacity: 1,
                x: 0,
                duration: 0.46
            }, 0);
        }

        // Form login putih di kanan: muncul dengan opacity dan pergeseran horizontal sangat ringan (350–500 ms)
        // Stagger pendek antara panel kiri dan form kanan
        if (loginCard) {
            tl.fromTo(loginCard, {
                opacity: 0,
                x: isMobile ? 0 : 20,
                y: isMobile ? 16 : 0
            }, {
                opacity: 1,
                x: 0,
                y: 0,
                duration: 0.46
            }, 0.06);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runSlideFadeEntrance);
    } else {
        requestAnimationFrame(runSlideFadeEntrance);
    }
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