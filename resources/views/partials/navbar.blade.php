<header class="site-header">

    <div class="header-inner">

        {{-- =====================================================
             BRAND
        ====================================================== --}}

        <a
            href="{{ route('dashboard') }}"
            class="brand">

            <img
                src="{{ asset('images/logo-tiga-serangkai.png') }}"
                alt="Tiga Serangkai"
                class="brand-logo">

            <div class="brand-text">

                <span>
                    PERPUSTAKAAN
                </span>

                <strong>
                    TIGA SERANGKAI
                </strong>

            </div>

        </a>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}

        <nav class="main-nav">

            {{-- DASHBOARD --}}

            <a
                href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>


            {{-- KATALOG --}}

            <a
                href="{{ route('catalog') }}"
                class="{{ request()->routeIs('catalog') ? 'active' : '' }}">
                Katalog Buku
            </a>


            {{-- MANAJEMEN BUKU --}}

            <a
                href="{{ route('books.index') }}"
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


            {{-- LAPORAN --}}

            <a
                href="{{ route('reports.index') }}"
                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Laporan
            </a>

        </nav>


        {{-- =====================================================
             USER
        ====================================================== --}}
        <div class="nav-user">

            <div class="nav-user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>

            <div class="nav-user-info">

                <span>
                    {{ auth()->user()->name ?? 'User' }}
                </span>

                <small>
                    {{ auth()->user()->role === 'admin'
                ? 'Admin Perpustakaan'
                : 'Pengguna Perpustakaan' }}
                </small>

            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="logout-form">

                @csrf

                <button
                    type="submit"
                    class="logout-button">
                    Logout
                </button>

            </form>

        </div>
    </div>

</header>