<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Daftar | Perpustakaan Tiga Serangkai
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/register.css') }}">
</head>

<body>

    <main class="login-page">

        {{-- BACKGROUND --}}
        <div class="login-background"></div>


        {{-- MAIN CONTAINER --}}
        <div class="login-container">


            {{-- =================================================
                 LEFT SIDE
            ================================================== --}}

            <section class="login-intro">

                <div class="intro-content">

                    <img
                        src="{{ asset('images/logo-tiga-serangkai.png') }}"
                        alt="Tiga Serangkai"
                        class="login-logo">


                    <span class="intro-label">
                        PERPUSTAKAAN TIGA SERANGKAI
                    </span>


                    <h1>
                        Mulai perjalanan
                        <span>membacamu.</span>
                    </h1>


                    <p>
                        Buat akun untuk menemukan koleksi buku,
                        melakukan reservasi, dan melihat riwayat
                        peminjamanmu dengan lebih mudah.
                    </p>


                    <div class="intro-decoration">

                        <span></span>
                        <span></span>
                        <span></span>

                    </div>

                </div>

            </section>



            {{-- =================================================
                 RIGHT SIDE
            ================================================== --}}

            <section class="login-panel">

                <div class="login-card">


                    {{-- =================================================
                         HEADER
                    ================================================== --}}

                    <div class="login-header">

                        <h2>
                            Buat Akun
                        </h2>

                        <p>
                            Daftarkan dirimu untuk mulai membaca.
                        </p>

                    </div>



                    {{-- =================================================
                         VALIDATION ERROR
                    ================================================== --}}

                    @if ($errors->any())

                    <div class="login-alert">

                        @foreach ($errors->all() as $error)

                        <span>
                            {{ $error }}
                        </span>

                        @endforeach

                    </div>

                    @endif



                    {{-- =================================================
                         REGISTER FORM
                    ================================================== --}}

                    <form
                        method="POST"
                        action="{{ route('register.store') }}"
                        class="login-form">

                        @csrf



                        {{-- =================================================
                             NAMA
                        ================================================== --}}

                        <div class="form-group">

                            <label for="name">
                                Nama Lengkap
                            </label>


                            <div class="input-wrapper">

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



                        {{-- =================================================
                             EMAIL
                        ================================================== --}}

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>


                            <div class="input-wrapper">

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



                        {{-- =================================================
                             PASSWORD
                        ================================================== --}}

                        <div class="form-group">

                            <label for="password">
                                Password
                            </label>


                            <div class="input-wrapper password-wrapper">

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



                        {{-- =================================================
                             KONFIRMASI PASSWORD
                        ================================================== --}}

                        <div class="form-group">

                            <label for="password_confirmation">
                                Konfirmasi Password
                            </label>


                            <div class="input-wrapper password-wrapper">

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



                        {{-- =================================================
                             REGISTER BUTTON
                        ================================================== --}}

                        <button
                            type="submit"
                            class="login-button">

                            <span>
                                Daftar
                            </span>

                            <span class="login-arrow">
                                →
                            </span>

                        </button>

                    </form>



                    {{-- =================================================
                         DIVIDER
                    ================================================== --}}

                    <div class="login-divider">

                        <span></span>

                        <p>
                            atau
                        </p>

                        <span></span>

                    </div>



                    {{-- =================================================
                         GOOGLE
                    ================================================== --}}

                    <a
                        href="/auth/google"
                        class="google-button">

                        <span class="google-icon">
                            G
                        </span>

                        <span>
                            Daftar dengan Google
                        </span>

                    </a>



                    {{-- =================================================
                         LOGIN LINK
                    ================================================== --}}

                    <div class="register-text">

                        <span>
                            Sudah punya akun?
                        </span>


                        <a href="{{ route('login') }}">
                            Login sekarang
                        </a>

                    </div>

                </div>

            </section>

        </div>

    </main>



    {{-- =========================================================
         PASSWORD TOGGLE
    ========================================================== --}}

    <script>
        function setupPasswordToggle(inputId, buttonId) {

            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);

            if (!input || !button) {
                return;
            }

            const eyeIcon = `
            <svg width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M3 3l18 18"/>
                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c7 0 10 7 10 7a18.5 18.5 0 0 1-3 4.2"/>
                <path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7c1.4 0 2.7-.3 3.8-.8"/>
            </svg>
        `;

            const eyeOffIcon = `
            <svg width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;

            // Kondisi awal: password tersembunyi
            button.innerHTML = eyeIcon;

            button.addEventListener('click', function() {

                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';

                button.innerHTML = isPassword ?
                    eyeOffIcon :
                    eyeIcon;

                button.setAttribute(
                    'aria-label',
                    isPassword ?
                    'Sembunyikan password' :
                    'Tampilkan password'
                );

            });
        }

        setupPasswordToggle('password', 'passwordToggle');

        setupPasswordToggle(
            'password_confirmation',
            'confirmPasswordToggle'
        );
    </script>

</body>

</html>