<header class="site-header">

    <div class="header-inner">

        {{-- BRAND --}}
        <a href="{{ route('dashboard') }}" class="brand">

            <img
                src="{{ asset('images/logo-tiga-serangkai.png') }}"
                alt="Tiga Serangkai"
                class="brand-logo">

            <div class="brand-text">
                <span>PERPUSTAKAAN</span>
                <strong>TIGA SERANGKAI</strong>
            </div>

        </a>


        {{-- NAVIGATION --}}
        <nav class="main-nav">

            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('catalog') }}"
                class="{{ request()->routeIs('catalog') ? 'active' : '' }}">
                Katalog Buku
            </a>

            <a href="{{ route('books.index') }}"
                class="{{ request()->routeIs('books.*') ? 'active' : '' }}">
                Manajemen Buku
            </a>

            <a href="{{ route('members.index') }}"
               class="{{ request()->routeIs('members.*') ? 'active' : '' }}">
               Keanggotaan
            </a>

            <a href="{{ route('circulation') }}"
                class="{{ request()->routeIs('circulation') ? 'active' : '' }}">
                Peminjaman Buku
            </a>

            <a
                href="{{ route('reservations.index') }}"
                class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}"
            >
                Reservasi Buku
            </a>

            <a href="{{ route('reports.index') }}"
                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Laporan
            </a>

        </nav>


        {{-- USER --}}
        <div class="nav-user">

            <div class="nav-user-avatar">
                D
            </div>

            <div class="nav-user-info">
                <span>Ibu Desi</span>
                <small>Admin Perpustakaan</small>
            </div>

        </div>

    </div>

</header>