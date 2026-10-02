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
            background: #093033;
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

        /* Cinematic Curtain — Default strictly display: none */
        #cinematic-curtain {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 999999;
            pointer-events: none;
            display: none;
            background: radial-gradient(circle at 50% 30%, #114c4f 0%, #093033 60%, #041a1c 100%);
            box-shadow: 0 0 60px rgba(0, 0, 0, 0.7);
            transform: translateY(0%);
            will-change: transform;
        }
        #cinematic-curtain .curtain-glow {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(94, 234, 212, 0.8), #5eead4, rgba(94, 234, 212, 0.8), transparent);
            box-shadow: 0 0 15px rgba(94, 234, 212, 0.9), 0 0 30px rgba(94, 234, 212, 0.5);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

{{-- Curtain Transition Layer (hanya aktif jika user berpindah dari Landing Page) --}}
<div id="cinematic-curtain" aria-hidden="true" style="display: none;">
    <div class="curtain-glow"></div>
</div>

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
    (function () {
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
        var dots = document.querySelectorAll('.intro-decoration span');
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
    })();
</script>

<script src="{{ asset('js/gsap.min.js') }}"></script>
<script>
(function () {
    var hasTransition = false;
    try {
        hasTransition = sessionStorage.getItem('transition_from_landing') === 'true';
        // HAPUS SEGERA flag ini agar jika user merefresh / reload di login page,
        // transisi curtain TIDAK PERNAH aktif kembali!
        sessionStorage.removeItem('transition_from_landing');
    } catch (e) {
        hasTransition = false;
    }

    // Cek apakah ada notifikasi error/flash dari server (login gagal, validasi, dsb)
    var hasServerAlerts = {{ ($errors->any() || session('error') || session('success')) ? 'true' : 'false' }};

    // JIKA BUKAN DARI TRANSISI LANDING PAGE ATAU ADA ERROR:
    // Jangan pernah tampilkan curtain! Halaman tampil 100% normal dan instan tanpa flicker.
    if (!hasTransition || hasServerAlerts) {
        return;
    }

    // JIKA DATANG RESMI DARI LANDING PAGE:
    var curtain = document.getElementById('cinematic-curtain');
    var container = document.querySelector('.login-container');
    if (!curtain) return;

    curtain.style.display = 'block';
    curtain.style.transform = 'translateY(0%)';

    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'translateY(16px)';
    }

    function runReveal() {
        var isReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (typeof gsap !== 'undefined' && !isReduced) {
            var tl = gsap.timeline({
                onComplete: function () {
                    curtain.style.display = 'none';
                    if (container) {
                        container.style.opacity = '';
                        container.style.transform = '';
                    }
                }
            });

            // Curtain menyapu ke atas (sweep out)
            tl.to(curtain, {
                y: '-100%',
                duration: 0.65,
                ease: 'power3.inOut'
            }, 0.05);

            // Login card meluncur halus ke posisinya
            if (container) {
                tl.to(container, {
                    opacity: 1,
                    y: 0,
                    duration: 0.55,
                    ease: 'power2.out'
                }, 0.20);
            }
        } else {
            // Fallback animasi tanpa GSAP
            curtain.style.transition = 'transform 0.45s ease-in-out';
            curtain.style.transform = 'translateY(-100%)';
            if (container) {
                container.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }
            setTimeout(function () {
                curtain.style.display = 'none';
                if (container) {
                    container.style.opacity = '';
                    container.style.transform = '';
                }
            }, 500);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runReveal);
    } else {
        requestAnimationFrame(runReveal);
    }
})();
</script>

</body>
</html>