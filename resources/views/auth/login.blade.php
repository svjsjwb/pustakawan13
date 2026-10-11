<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>Login – Perpustakaan Tiga Serangkai</title>
    <meta name="description" content="Login ke sistem perpustakaan Tiga Serangkai.">
    <meta name="color-scheme" content="light">

    <link rel="preload" as="image" href="{{ asset('images/bg-ts.jpeg') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>

    <main class="login-page">

        <div class="login-background"></div>

        <div class="login-container">

            {{-- =========================
                LEFT / INTRO
            ========================== --}}
            <section class="login-intro">

                <div class="intro-content">

                    <img
                        src="{{ asset('images/logo-tiga-serangkai.png') }}"
                        alt="Perpustakaan Tiga Serangkai"
                        class="login-logo"
                    >

                    <span class="intro-label">
                        PERPUSTAKAAN TIGA SERANGKAI
                    </span>

                    <h1>
                        Selamat datang di
                        <span>Perpustakaan Tiga Serangkai</span>
                    </h1>

                    <div class="carousel-viewport">
                        <div class="carousel-text">

                            <div class="carousel-slide is-active">
                                <p>
                                    Temukan berbagai koleksi buku untuk menambah
                                    wawasan dan membuka jendela dunia.
                                </p>
                            </div>

                            <div class="carousel-slide">
                                <p>
                                    Jelajahi koleksi buku pilihan dan temukan
                                    bacaan yang sesuai dengan minatmu.
                                </p>
                            </div>

                            <div class="carousel-slide">
                                <p>
                                    Nikmati pengalaman perpustakaan yang lebih
                                    mudah, cepat, dan terorganisir.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="intro-dots" aria-hidden="true">
                        <span class="intro-dot is-active"></span>
                        <span class="intro-dot"></span>
                        <span class="intro-dot"></span>
                    </div>

                </div>

            </section>

            {{-- =========================
                RIGHT / LOGIN
            ========================== --}}
            <section class="login-panel">

                <div class="login-card">

                    <div class="login-header">
                        <h2>WELCOME!!</h2>
                        <p>Selamat menjelajah jendela dunia.</p>
                    </div>

                    {{-- Error messages --}}
                    @if ($errors->any())
                        <div class="login-alert alert-error">
                            <svg width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor"
                                stroke-width="2" style="flex-shrink:0">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>

                            <span>
                                {{ $errors->first('email') ?? $errors->first() }}
                            </span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="login-alert alert-error">
                            <svg width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor"
                                stroke-width="2" style="flex-shrink:0">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>

                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="login-alert alert-success">
                            <svg width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor"
                                stroke-width="2" style="flex-shrink:0">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>

                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- LOGIN FORM --}}
                    <form method="POST"
                        action="{{ route('login.store') }}"
                        id="loginForm"
                        class="login-form">

                        @csrf

                        {{-- Email --}}
                        <div class="form-group">

                            <label for="email">
                                Username / Email
                            </label>

                            <div class="input-wrapper">

                                <input
                                    id="email"
                                    type="text"
                                    name="email"
                                    placeholder="Masukkan email atau username"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="username"
                                >

                            </div>

                        </div>

                        {{-- Password --}}
                        <div class="form-group">

                            <label for="password">
                                Password
                            </label>

                            <div class="input-wrapper password-input">

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    required
                                    autocomplete="current-password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="togglePassword"
                                    aria-label="Tampilkan/sembunyikan password"
                                    onclick="togglePwd()"
                                >
                                    <svg
                                        id="eyeIcon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>

                            </div>

                        </div>

                        {{-- Forgot password --}}
                        <div class="forgot">
                            <a href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        </div>

                        <button
                            type="submit"
                            class="login-button"
                            id="loginBtn"
                        >
                            LOGIN
                        </button>

                    </form>

                    {{-- Divider --}}
                    <div class="login-divider">
                        <span>atau</span>
                    </div>

                    {{-- Google Login --}}
                    <a
                        href="{{ route('google.redirect') }}"
                        class="google-login-button"
                        id="googleLoginBtn"
                    >
                        <span class="google-icon">

                            <svg width="18" height="18" viewBox="0 0 24 24">
                                <path fill="#4285F4"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />

                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.987.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />

                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />

                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>

                        </span>

                        Login dengan Google
                    </a>

                    {{-- Register --}}
                    <p class="register-link">
                        Belum punya akun?
                        <a href="{{ route('register') }}">
                            Daftar sekarang
                        </a>
                    </p>

                </div>

            </section>

        </div>

    </main>

    <script>
        /* =========================
           PASSWORD TOGGLE
        ========================== */
        function togglePwd() {
            var input = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');

            if (!input || !icon) {
                return;
            }

            if (input.type === 'password') {
                input.type = 'text';

                icon.innerHTML =
                    '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>' +
                    '<line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';

                icon.innerHTML =
                    '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>' +
                    '<circle cx="12" cy="12" r="3"/>';
            }
        }


        /* =========================
           CAROUSEL
        ========================== */
        document.addEventListener('DOMContentLoaded', function () {

            const slides = Array.from(
                document.querySelectorAll('.carousel-slide')
            );

            const dots = Array.from(
                document.querySelectorAll('.intro-dot')
            );

            if (slides.length <= 1) {
                return;
            }

            let current = 0;
            let timer = null;

            function showSlide(next) {

                if (next === current) {
                    return;
                }

                const currentSlide = slides[current];
                const nextSlide = slides[next];

                currentSlide.classList.remove('is-active');
                currentSlide.classList.add('is-exiting');

                nextSlide.classList.add('is-active');

                dots.forEach(function (dot, index) {
                    dot.classList.toggle(
                        'is-active',
                        index === next
                    );
                });

                setTimeout(function () {
                    currentSlide.classList.remove('is-exiting');
                }, 650);

                current = next;
            }

            function nextSlide() {
                showSlide((current + 1) % slides.length);
            }

            function startCarousel() {
                clearInterval(timer);
                timer = setInterval(nextSlide, 4500);
            }

            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    showSlide(index);
                    startCarousel();
                });
            });

            startCarousel();
        });


        /* =========================
           PAGE REVEAL
        ========================== */
        document.addEventListener('DOMContentLoaded', function () {

            requestAnimationFrame(function () {
                document.documentElement.classList.add('page-ready');
            });

        });
    </script>

</body>

</html>