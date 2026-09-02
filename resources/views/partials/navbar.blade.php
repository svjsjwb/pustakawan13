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


            {{-- ANGGOTA --}}

            <a
                href="{{ route('members.index') }}"
                class="{{ request()->routeIs('members.*') ? 'active' : '' }}">
                Anggota
            </a>


            {{-- PEMINJAMAN BUKU --}}

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
             USER PROFILE
        ====================================================== --}}

        <div class="nav-user-wrapper">

            {{-- PROFILE BUTTON --}}

            <button
                type="button"
                class="nav-user"
                id="profileDropdownButton"
                aria-expanded="false">

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

                {{-- ICON PANAH --}}

                <span class="profile-arrow" id="profileArrow">
                    ▾
                </span>

            </button>


            {{-- =================================================
                 DROPDOWN
            ================================================== --}}

            <div
                class="profile-dropdown"
                id="profileDropdown">

                {{-- INFO USER --}}

                <div class="profile-dropdown-header">

                    <div class="profile-dropdown-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="profile-dropdown-user">

                        <strong>
                            {{ auth()->user()->name ?? 'User' }}
                        </strong>

                        <span>
                            {{ auth()->user()->role === 'admin'
                                ? 'Admin Perpustakaan'
                                : 'Pengguna Perpustakaan' }}
                        </span>

                    </div>

                </div>


                {{-- GARIS PEMISAH --}}

                <div class="profile-dropdown-divider"></div>


                {{-- LOGOUT --}}

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="profile-logout-form">

                    @csrf

                    <button
                        type="submit"
                        class="profile-logout-button">

                        <span class="logout-icon">
                            ⇥
                        </span>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </div>

    </div>

</header>


{{-- =========================================================
     PROFILE DROPDOWN SCRIPT
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const profileButton =
        document.getElementById('profileDropdownButton');

    const profileDropdown =
        document.getElementById('profileDropdown');

    const profileArrow =
        document.getElementById('profileArrow');


    if (
        !profileButton ||
        !profileDropdown
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | BUKA / TUTUP DROPDOWN
    |--------------------------------------------------------------------------
    */

    profileButton.addEventListener('click', function (event) {

        event.stopPropagation();

        const isOpen =
            profileDropdown.classList.contains('show');


        if (isOpen) {

            profileDropdown.classList.remove('show');

            profileButton.setAttribute(
                'aria-expanded',
                'false'
            );

            if (profileArrow) {
                profileArrow.classList.remove('rotate');
            }

        } else {

            profileDropdown.classList.add('show');

            profileButton.setAttribute(
                'aria-expanded',
                'true'
            );

            if (profileArrow) {
                profileArrow.classList.add('rotate');
            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR DROPDOWN
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        if (
            !profileDropdown.contains(event.target) &&
            !profileButton.contains(event.target)
        ) {

            profileDropdown.classList.remove('show');

            profileButton.setAttribute(
                'aria-expanded',
                'false'
            );

            if (profileArrow) {
                profileArrow.classList.remove('rotate');
            }

        }

    });

});

</script>