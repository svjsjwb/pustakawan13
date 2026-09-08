<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Perpustakaan</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

<div class="login-page">

    <div class="login-background"></div>

    <div class="login-panel">

        <img
            src="{{ asset('images/logo-tiga-serangkai.png') }}"
            alt="Logo"
            class="login-logo">

        <div class="login-card">

            <h1>WELCOME!!</h1>

            <p>Silakan login dengan email dan password Anda.</p>

            {{-- Tampilkan pesan error jika login gagal --}}
            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first('email') }}
                </div>
            @endif

            {{-- Tampilkan pesan error dari middleware (akses ditolak) --}}
            @if (session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">

                @csrf

                <div class="form-group">

                    <label for="email">Email</label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Masukkan email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email">

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password">

                </div>

                <div class="form-group form-remember">
                    <label>
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>

                <button type="submit">
                    LOGIN
                </button>

            </form>

        </div>

    </div>

</div>

<style>
/* Tambahan style untuk alert error agar tampil meski CSS utama belum ada */
.alert-error {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #991b1b;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    text-align: left;
}
.form-remember {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}
.form-remember label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    cursor: pointer;
    margin-bottom: 0;
}
</style>

</body>
</html>