<footer class="site-footer">

    <div class="footer-brand">

        <img
            src="{{ asset('images/logo-tiga-serangkai.png') }}"
            alt="Logo">

        <div>
            <strong>PERPUSTAKAAN</strong>
            <span>TIGA SERANGKAI</span>
        </div>

    </div>


    <div class="footer-column">

        <strong>JAM OPERASIONAL</strong>

        @if(auth()->user()?->role === 'user')
            <span>Senin - Jumat : 07:30 - 16:30</span>
        @else
            <span>Senin - Jumat: 07.00 - 17.30 WIB</span>
        @endif
        <span>Sabtu - Minggu : Libur</span>

    </div>


    <div class="footer-column">

        <strong>ALAMAT</strong>

        <span>
            @if(auth()->user()?->role === 'user')
                Jl. Prof. DR. Supomo No. 23,
            @else
                Jl. Prof. DR. Supomo No.93,
            @endif
            Sriwedari, Kec. Laweyan,
        </span>

        <span>
            Kota Surakarta, Jawa Tengah 57141
        </span>

    </div>


    <div class="footer-column">

        <strong>KONTAK</strong>

        <span>✉ perpustakaan@gmail.com</span>
        <span>◎ @perpustakaantigaserangkai</span>

    </div>

</footer>