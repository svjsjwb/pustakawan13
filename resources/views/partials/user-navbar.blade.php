<nav class="user-navbar">

    <div class="user-navbar-inner">

        {{-- LOGO --}}
        <a
            href="{{ route('user.home') }}"
            class="user-navbar-logo"
        >
            <span class="user-navbar-logo-mark">
                P
            </span>

            <span class="user-navbar-logo-text">
                Pustakawan
            </span>
        </a>


        {{-- MENU --}}
        <div class="user-navbar-menu">

            <a
                href="{{ route('user.home') }}"
                class="{{ request()->routeIs('user.home') ? 'active' : '' }}"
            >
                Beranda
            </a>

            <a
                href="{{ route('catalog') }}"
                class="{{ request()->routeIs('catalog*') ? 'active' : '' }}"
            >
                Katalog
            </a>

            <a
                href="#"
                class="{{ request()->routeIs('reservations*') ? 'active' : '' }}"
            >
                Reservasi
            </a>

            <a
                href="#"
                class="{{ request()->routeIs('history*') ? 'active' : '' }}"
            >
                Riwayat
            </a>

        </div>


        {{-- USER --}}
        <div class="user-navbar-account">

            <button
                type="button"
                class="user-navbar-profile"
                id="userNavbarProfile"
            >

                <span class="user-navbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </span>

                <span class="user-navbar-name">
                    {{ auth()->user()->name ?? 'User' }}
                </span>

                <span class="user-navbar-arrow">
                    ▾
                </span>

            </button>


            {{-- DROPDOWN --}}

            <div
                class="user-navbar-dropdown"
                id="userNavbarDropdown"
            >

                <div class="user-navbar-dropdown-header">

                    <strong>
                        {{ auth()->user()->name ?? 'User' }}
                    </strong>

                    <span>
                        Pengguna
                    </span>

                </div>


                <div class="user-navbar-dropdown-divider"></div>


                <a href="#">
                    Profil
                </a>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                >

                    @csrf

                    <button type="submit">
                        Keluar
                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>