<nav class="mobile-bottom-nav" aria-label="Navigasi utama">
    <ul class="mobile-bottom-nav__list">
        <li>
            <a href="{{ route('dashboard') }}" class="mobile-bottom-nav__item {{ request()->routeIs('dashboard', 'user.home') ? 'is-active' : '' }}" @if(request()->routeIs('dashboard', 'user.home')) aria-current="page" @endif>
                <span class="mobile-bottom-nav__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-7 9 7"></path><path d="M5 9v11h14V9"></path><path d="M9 20v-6h6v6"></path></svg></span>
                <span class="mobile-bottom-nav__label">Beranda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('catalog') }}" class="mobile-bottom-nav__item {{ request()->routeIs('catalog', 'user.catalog') ? 'is-active' : '' }}" @if(request()->routeIs('catalog', 'user.catalog')) aria-current="page" @endif>
                <span class="mobile-bottom-nav__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v14"></path><path d="M3 18V5a2 2 0 0 1 2-2h4a3 3 0 0 1 3 3v14a3 3 0 0 0-3-3H5a2 2 0 0 0-2 2z"></path><path d="M21 18V5a2 2 0 0 0-2-2h-4a3 3 0 0 0-3 3v14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2z"></path></svg></span>
                <span class="mobile-bottom-nav__label">Katalog</span>
            </a>
        </li>
        <li>
            <a href="{{ route('borrowings.index') }}" class="mobile-bottom-nav__item {{ request()->routeIs('borrowings.index', 'user.borrowings', 'user.loans') ? 'is-active' : '' }}" @if(request()->routeIs('borrowings.index', 'user.borrowings', 'user.loans')) aria-current="page" @endif>
                <span class="mobile-bottom-nav__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 4h12a2 2 0 0 1 2 2v15l-3-2-3 2-3-2-3 2-4-2V6a2 2 0 0 1 2-2Z"></path><path d="M9 9h6M9 13h6"></path></svg></span>
                <span class="mobile-bottom-nav__label">Peminjaman</span>
            </a>
        </li>
        <li>
            <a href="{{ route('reservations.index') }}" class="mobile-bottom-nav__item {{ request()->routeIs('reservations.index', 'user.reservations', 'user.reservations.show') ? 'is-active' : '' }}" @if(request()->routeIs('reservations.index', 'user.reservations', 'user.reservations.show')) aria-current="page" @endif>
                <span class="mobile-bottom-nav__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path><path d="m9 15 2 2 4-4"></path></svg></span>
                <span class="mobile-bottom-nav__label">Reservasi</span>
            </a>
        </li>
        <li>
            <a href="{{ route('history') }}" class="mobile-bottom-nav__item {{ request()->routeIs('history', 'user.history') ? 'is-active' : '' }}" @if(request()->routeIs('history', 'user.history')) aria-current="page" @endif>
                <span class="mobile-bottom-nav__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path><path d="M12 7v5l3 2"></path></svg></span>
                <span class="mobile-bottom-nav__label">Riwayat</span>
            </a>
        </li>
    </ul>
</nav>