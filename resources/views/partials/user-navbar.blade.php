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
                href="{{ route('user.reservations') }}"
                class="{{ request()->routeIs('user.reservations*') ? 'active' : '' }}"
            >
                Reservasi
            </a>

            <a
                href="{{ route('user.history') }}"
                class="{{ request()->routeIs('user.history*') ? 'active' : '' }}"
            >
                Riwayat
            </a>

        </div>


        {{-- RIGHT ACTIONS (NOTIFIKASI + PROFIL) --}}
        <div class="user-navbar-right-actions">

            @php
                $authUser = auth()->user();
                $unreadNotifCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
                $userNotifications = $authUser ? $authUser->notifications()->take(10)->get() : collect();
                $isNotifEnabled = $authUser ? (bool) $authUser->is_notification_enabled : true;
            @endphp

            {{-- NOTIFIKASI DI SAMPING KIRI PROFILE --}}
            <div class="user-navbar-notif-wrapper" id="userNavbarNotifWrapper">
                <button
                    type="button"
                    class="user-navbar-notif-btn"
                    id="userNavbarNotifBtn"
                    aria-label="Notifikasi"
                    aria-expanded="false"
                    style="position: relative;"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="user-notif-dot" id="userNotifDot" style="{{ $unreadNotifCount > 0 ? 'display: block;' : 'display: none;' }}"></span>
                </button>

                {{-- POPUP NOTIFIKASI --}}
                <div class="user-navbar-notif-dropdown" id="userNavbarNotifDropdown">
                    <div class="notif-dropdown-header">
                        <div class="notif-dropdown-title-wrap">
                            <strong>Notifikasi</strong>
                            <span class="notif-count-badge" id="notifCountBadge">{{ $unreadNotifCount }} Baru</span>
                        </div>
                        <button type="button" class="notif-mark-all" id="notifMarkAll" style="{{ $unreadNotifCount > 0 ? '' : 'opacity: 0.5;' }}">
                            Tandai Dibaca
                        </button>
                    </div>

                    <div class="notif-dropdown-list" id="notifDropdownList">
                        @forelse($userNotifications as $notif)
                            @php
                                $nData = $notif->data ?? [];
                                $isUnread = is_null($notif->read_at);
                                $color = $nData['color'] ?? 'blue';
                                $iconClass = match($color) {
                                    'green' => 'notif-icon-green',
                                    'amber' => 'notif-icon-amber',
                                    'red'   => 'notif-icon-amber',
                                    default => 'notif-icon-blue'
                                };
                            @endphp
                            <div class="notif-item {{ $isUnread ? 'unread' : '' }}" data-id="{{ $notif->id }}" style="cursor: pointer;" onclick="if('{{ $nData['url'] ?? '' }}') window.location.href='{{ $nData['url'] }}'">
                                <div class="notif-item-icon {{ $iconClass }}">
                                    @if($color === 'green')
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    @elseif($color === 'amber' || $color === 'red')
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                    @else
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="notif-item-text">
                                    <p><strong>{{ $nData['title'] ?? 'Notifikasi' }}:</strong> {{ $nData['message'] ?? '' }}</p>
                                    <small>{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</small>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 30px 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 6px; display: inline-block;">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <div>Belum ada notifikasi baru</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>


            {{-- USER ACCOUNT (Frame 1000006625) --}}
            <div class="user-navbar-account" data-user-id="{{ auth()->id() ?? 0 }}">

                @php
                    $userAvatar = auth()->user()->avatar ?? null;
                    $userAvatarUrl = $userAvatar ? asset('storage/' . $userAvatar) : asset('images/avatar-user.jpg');
                    $userName = auth()->user()->name ?? 'Pengguna';
                    $userEmail = auth()->user()->email ?? '';
                @endphp

                <button
                    type="button"
                    class="user-navbar-profile"
                    id="userNavbarProfile"
                    aria-label="Menu Profil"
                    aria-expanded="false"
                >

                    <span class="user-navbar-avatar">
                        <img
                            src="{{ $userAvatarUrl }}"
                            alt="Foto Profil"
                            class="nav-avatar-img user-profile-avatar-display"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <span class="nav-avatar-fallback" style="display:none;">
                            {{ strtoupper(substr($userName, 0, 1)) }}
                        </span>
                    </span>

                    <span class="user-navbar-name">
                        {{ $userName }}
                    </span>

                    <span class="user-navbar-arrow">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>

                </button>


                {{-- DROPDOWN MENU --}}
                <div
                    class="user-navbar-dropdown"
                    id="userNavbarDropdown"
                >

                    {{-- Header Dropdown --}}
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-header-avatar">
                            <img
                                src="{{ $userAvatarUrl }}"
                                alt="Foto Profil"
                                class="dropdown-avatar-img user-profile-avatar-display"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <span class="dropdown-avatar-fallback" style="display:none;">
                                {{ strtoupper(substr($userName, 0, 1)) }}
                            </span>
                        </div>

                        <div class="user-dropdown-header-info">
                            <strong class="user-profile-name-display">
                                {{ $userName }}
                            </strong>
                            <span class="user-profile-email-display">
                                {{ $userEmail }}
                            </span>
                        </div>
                    </div>

                    <div class="user-dropdown-divider"></div>

                    {{-- Menu List --}}
                    <div class="user-dropdown-menu-list">

                        {{-- 1. My Profile --}}
                        <button
                            type="button"
                            class="user-dropdown-item"
                            id="openProfileModalBtn"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">My Profile</span>
                            </div>
                            <span class="user-dropdown-arrow-right">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </button>

                        {{-- 2. Settings / Pengaturan --}}
                        <button
                            type="button"
                            class="user-dropdown-item"
                            id="openSettingsModalBtn"
                            data-target-tab="tabPreferences"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">Pengaturan & Preferensi</span>
                            </div>
                            <span class="user-dropdown-arrow-right">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </button>

                        {{-- 3. Favorit Saya --}}
                        <a
                            href="{{ route('favorites.index') }}"
                            class="user-dropdown-item user-dropdown-favorite-item"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon heart-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#e11d48" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">Favorit Saya</span>
                            </div>
                            <span class="user-dropdown-arrow-right">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </a>

                        {{-- 3b. Reservasi Saya --}}
                        <a
                            href="{{ route('user.reservations') }}"
                            class="user-dropdown-item"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">Reservasi Saya</span>
                            </div>
                            <span class="user-dropdown-arrow-right">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </a>

                        {{-- 3c. Riwayat Aktivitas --}}
                        <a
                            href="{{ route('user.history') }}"
                            class="user-dropdown-item"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">Riwayat Aktivitas</span>
                            </div>
                            <span class="user-dropdown-arrow-right">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </a>

                        {{-- 4. Notification --}}
                        <div
                            class="user-dropdown-item user-dropdown-notification-item"
                            id="userNotificationItem"
                        >
                            <div class="user-dropdown-item-left">
                                <span class="user-dropdown-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </span>
                                <span class="user-dropdown-label">Notification</span>
                            </div>

                            <div class="user-notif-action-wrapper">
                                <button
                                    type="button"
                                    class="user-notif-status-badge"
                                    id="userNotifBadge"
                                >
                                    <span id="userNotifText">{{ $isNotifEnabled ? 'Allow' : 'Mute' }}</span>
                                </button>

                                <div class="user-notif-popover" id="userNotifPopover">
                                    <button type="button" class="notif-opt {{ $isNotifEnabled ? 'active' : '' }}" data-val="Allow">
                                        Allow
                                    </button>
                                    <button type="button" class="notif-opt {{ !$isNotifEnabled ? 'active' : '' }}" data-val="Mute">
                                        Mute
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 5. Log Out --}}
                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                            class="user-dropdown-logout-form"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="user-dropdown-item user-dropdown-logout-btn"
                            >
                                <div class="user-dropdown-item-left">
                                    <span class="user-dropdown-icon logout-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    </span>
                                    <span class="user-dropdown-label logout-label">Log Out</span>
                                </div>
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</nav>