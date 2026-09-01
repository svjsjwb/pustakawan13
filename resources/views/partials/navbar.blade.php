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


            {{-- PINJAMAN BUKU --}}

            <a
                href="{{ route('circulation') }}"
                class="{{ request()->routeIs('circulation') ? 'active' : '' }}">
                Peminjaman Buku
            </a>


            {{-- RESERVASI BUKU --}}

            <a
                href="{{ route('reservations.index') }}"
                class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}">
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
        @auth
            <div class="nav-user-dropdown" id="navUserDropdown">
                <button type="button" class="nav-user-trigger" id="navUserTrigger" aria-expanded="false">
                    <div class="nav-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>

                    <div class="nav-user-info">
                        <span class="nav-user-name">
                            {{ auth()->user()->name ?? 'Admin Perpustakaan' }}
                        </span>
                        <small class="nav-user-role">
                            {{ (auth()->user()->role ?? 'user') === 'admin'
                                ? 'Admin Perpustakaan'
                                : 'Pengguna Perpustakaan' }}
                        </small>
                    </div>

                    <svg class="nav-user-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <div class="nav-user-menu" id="navUserMenu">
                    <a href="{{ route('settings') }}" class="nav-user-menu-item">
                        <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span>Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                        @csrf
                        <button type="submit" class="nav-user-menu-item logout-item">
                            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="nav-user">
                <a href="{{ route('login') }}" class="login-button" style="text-decoration: none; padding: 6px 14px; background: #0284c7; color: #fff; border-radius: 6px; font-weight: 500;">
                    Masuk
                </a>
            </div>
        @endauth
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdown = document.getElementById('navUserDropdown');
            const trigger = document.getElementById('navUserTrigger');

            if (dropdown && trigger) {
                trigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = dropdown.classList.toggle('open');
                    trigger.setAttribute('aria-expanded', isOpen);
                });

                document.addEventListener('click', function (e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('open');
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        dropdown.classList.remove('open');
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    </script>
</header>