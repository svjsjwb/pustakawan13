<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-page">

        <div class="login-background"></div>

        <div class="login-panel">

            <img
                src="{{ asset('images/logo-tiga-serangkai.png') }}"
                alt="Tiga Serangkai"
                class="login-logo">

            <div class="login-card">

                <h1>WELCOME!!</h1>

                <p>Selamat menjelajah jendela dunia.</p>

                {{-- LOGIN EMAIL / PASSWORD --}}
                <form method="POST" action="{{ route('login.store') }}">

                    @csrf

                    <div class="form-group">
                        <label for="email">Username / Email</label>

                        <input
                            id="email"
                            type="text"
                            name="email"
                            placeholder="Masukkan username atau email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>

                        <div class="password-input">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                required
                                autocomplete="current-password">

                            <button
                                type="button"
                                class="password-toggle"
                                id="passwordToggle"
                                aria-label="Tampilkan password">

                                <svg
                                    id="passwordEye"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>

                                </svg>

                            </button>

                        </div>
                    </div>

                    <div class="forgot">
                        <a href="#">
                            Lupa Password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="login-button">

                        LOGIN

                    </button>

                </form>


                {{-- PEMISAH --}}
                <div class="login-divider">
                    <span>atau</span>
                </div>


                {{-- LOGIN GOOGLE --}}
                <a
                    href="{{ route('google.redirect') }}"
                    class="google-login-button">

                    <span class="google-icon">

                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24">

                            <path
                                fill="#4285F4"
                                d="M21.35 12.27c0-.72-.06-1.42-.18-2.09H12v3.95h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.25Z"/>

                            <path
                                fill="#34A853"
                                d="M12 21.75c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.53A9.75 9.75 0 0 0 12 21.75Z"/>

                            <path
                                fill="#FBBC05"
                                d="M6.54 13.83a5.86 5.86 0 0 1 0-3.66V7.64H3.3a9.76 9.76 0 0 0 0 8.72l3.24-2.53Z"/>

                            <path
                                fill="#EA4335"
                                d="M12 6.14c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.83 3.16 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.39l3.24 2.53C7.31 7.86 9.46 6.14 12 6.14Z"/>

                        </svg>

                    </span>

                    <span>
                        Login dengan Google
                    </span>

                </a>


                {{-- DAFTAR --}}
                <p class="register-link">

                    Belum punya akun?

                    <a href="{{ route('register') }}">
                        Daftar sekarang
                    </a>

                </p>

            </div>

        </div>

    </div>


    {{-- PASSWORD TOGGLE --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const passwordInput =
                document.getElementById('password');

            const passwordToggle =
                document.getElementById('passwordToggle');

            const passwordEye =
                document.getElementById('passwordEye');


            if (!passwordInput || !passwordToggle) {
                return;
            }


            passwordToggle.addEventListener('click', function () {

                const isPassword =
                    passwordInput.type === 'password';


                passwordInput.type =
                    isPassword ? 'text' : 'password';


                passwordToggle.setAttribute(
                    'aria-label',
                    isPassword
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                );


                if (isPassword) {

                    // ICON MATA DICORET
                    passwordEye.innerHTML = `
                        <path d="M3 3l18 18"/>
                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"/>
                        <path d="M9.88 4.24A9.6 9.6 0 0 1 12 4c6.5 0 10 8 10 8a17.4 17.4 0 0 1-3.02 4.11"/>
                        <path d="M6.61 6.61C3.98 8.38 2 12 2 12s3.5 8 10 8a9.9 9.9 0 0 0 4.5-1.07"/>
                    `;

                } else {

                    // ICON MATA NORMAL
                    passwordEye.innerHTML = `
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    `;

                }

            });

        });

    </script>

</body>

</html>