<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perpustakaan Tiga Serangkai</title>

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>

    <div class="landing">

        <div class="landing-image">
            <div class="overlay"></div>

            <div class="landing-content">

                <img
                    src="{{ asset('images/logo-tiga-serangkai.png') }}"
                    alt="Tiga Serangkai"
                    class="landing-logo">

                <a href="{{ route('login') }}" class="login-button">
                    LOGIN
                </a>

            </div>
        </div>

        <footer class="landing-footer">

            <div class="footer-brand">
                <img
                    src="{{ asset('images/logo-tiga-serangkai.png') }}"
                    alt="Logo">
                <div>
                    <strong>PERPUSTAKAAN</strong>
                    <span>TIGA SERANGKAI</span>
                </div>
            </div>

            <div>
                <strong>JAM OPERASIONAL</strong>
                <span>Senin - Jumat: 07.00 - 17.30 WIB</span>
                <span>Sabtu - Minggu: Libur</span>
            </div>

            <div>
                <strong>ALAMAT</strong>
                <span>Jl. Prof. DR. Supomo No.93, Sriwedari, Kec. Laweyan,</span>
                <span>Kota Surakarta, Jawa Tengah 57141</span>
            </div>

            <div>
                <strong>KONTAK</strong>
                <span>✉ perpustakaan@gmail.com</span>
                <span>◎ @perpustakaantigaserangkai</span>
            </div>

        </footer>

    </div>

</body>

</html>