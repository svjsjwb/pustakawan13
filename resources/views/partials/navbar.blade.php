@if(Auth::check() && Auth::user()->role === 'user')
    @include('partials.user-navbar')
@else
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
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <span id="navMembersBadge" class="nav-count-badge"
                        aria-label="Pendaftar anggota menunggu persetujuan"></span>
                @endif
            </a>


            {{-- PEMINJAMAN --}}

            <a
                href="{{ route('borrowings.index') }}"
                class="{{ request()->routeIs('borrowings.*') ? 'active' : '' }}">
                Peminjaman Buku
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <span id="navBorrowingsBadge" class="nav-count-badge"
                        aria-label="Peminjaman baru atau menunggu diproses"></span>
                @endif
            </a>


            {{-- RESERVASI --}}

            <a
                href="{{ route('reservations.index') }}"
                class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}">
                Reservasi Buku
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <span id="navReservationsBadge" class="nav-count-badge"
                        aria-label="Reservasi menunggu diproses"></span>
                @endif
            </a>


            {{-- LAPORAN --}}

            <a
                href="{{ route('reports.index') }}"
                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Laporan
            </a>

        </nav>


        {{-- USER, NOTIF & PROFILE DROPDOWN --}}
        <div class="nav-user" style="display:flex;align-items:center;gap:8px;margin-left:auto;">

            {{-- NOTIFIKASI DROPDOWN --}}
            @auth
            <div class="nav-notif-container" style="position: relative;">
                <button type="button" id="navNotifBtn" class="admin-notif-button" title="Notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span id="navNotifBadge" class="admin-notif-badge" style="display:none;"></span>
                </button>

                <div id="navNotifDropdown" style="display:none; position:absolute; right:0; top:calc(100% + 12px); width:320px; background:#fff; border-radius:12px; box-shadow:0 12px 36px rgba(0,0,0,0.18); border:1px solid #e2eeee; z-index:9999; overflow:hidden; color:#1e3d3d;">
                    <div style="padding:12px 16px; border-bottom:1px solid #f0f4f4; display:flex; justify-content:space-between; align-items:center; background:#fafcfc;">
                        <strong style="font-size:13px;">Notifikasi</strong>
                        <button type="button" onclick="markAllNotifAsRead()" style="background:none; border:none; color:#287879; font-size:11px; font-weight:600; cursor:pointer;">Tandai Semua Dibaca</button>
                    </div>
                    <div id="navNotifList" style="max-height:280px; overflow-y:auto; font-size:12.5px;">
                        <div style="padding:20px; text-align:center; color:#8fa6a6;">Tidak ada notifikasi baru</div>
                    </div>
                </div>
            </div>
            @endauth

            {{-- PROFILE DROPDOWN --}}
            <div id="adminProfileDropdown" style="position:relative;">
                {{-- Trigger: avatar + nama + chevron --}}
                <button type="button" id="adminProfileBtn" class="admin-profile-button">
                    <div class="admin-profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="admin-profile-meta">
                        <span class="admin-profile-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="admin-profile-role">{{ (Auth::check() && Auth::user()->role === 'admin') ? 'Admin Perpustakaan' : 'Anggota' }}</span>
                    </div>
                    {{-- Chevron --}}
                    <svg id="adminProfileChevron" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                        class="admin-profile-chevron">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                {{-- Dropdown menu --}}
                <div id="adminProfileMenu"
                    style="display:none;position:absolute;right:0;top:calc(100% + 10px);min-width:220px;background:#fff;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,0.16);border:1px solid #e8f0f0;z-index:9999;overflow:hidden;">
                    {{-- Header profile card --}}
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid #f0f5f5;">
                        <div style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#d7e5e5;color:#287b7b;font-size:15px;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div style="line-height:1.3;min-width:0;">
                            <div style="font-size:13.5px;font-weight:700;color:#1a3a3a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div style="font-size:11.5px;color:#6b8f8f;white-space:nowrap;">{{ (Auth::check() && Auth::user()->role === 'admin') ? 'Admin Perpustakaan' : 'Anggota' }}</div>
                        </div>
                    </div>
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;padding:0;">
                        @csrf
                        <button type="submit"
                            style="display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;background:transparent;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#e05252;text-align:left;transition:background 0.15s;"
                            onmouseover="this.style.background='#fff5f5'"
                            onmouseout="this.style.background='transparent'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</header>
@endif

@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Notification Bell ────────────────────────────────────────────────────
    const btn = document.getElementById('navNotifBtn');
    const dropdown = document.getElementById('navNotifDropdown');
    const badge = document.getElementById('navNotifBadge');
    const membersBadge = document.getElementById('navMembersBadge');
    const borrowingsBadge = document.getElementById('navBorrowingsBadge');
    const borrowingsNav = borrowingsBadge ? borrowingsBadge.closest('a') : null;
    const reservationsBadge = document.getElementById('navReservationsBadge');
    const list = document.getElementById('navNotifList');

    if (btn && dropdown) {
        const borrowingBadgeSeenKey = 'navBorrowingsBadgeSeen:{{ Auth::id() ?? "guest" }}';
        let borrowingNotificationIds = [];
        let seenBorrowingNotificationIds = new Set();

        function updateCountBadge(element, count) {
            if (!element) return;
            element.style.display = count > 0 ? 'inline-flex' : 'none';
            element.textContent = count > 99 ? '99+' : count;
        }

        function getSeenBorrowingNotificationIds() {
            try {
                const storedIds = sessionStorage.getItem(borrowingBadgeSeenKey);
                const parsedIds = storedIds ? JSON.parse(storedIds) : [];
                seenBorrowingNotificationIds = new Set(
                    Array.isArray(parsedIds) ? parsedIds : []
                );
            } catch (error) {
                console.error('Status badge peminjaman gagal dimuat:', error);
            }

            return seenBorrowingNotificationIds;
        }

        function storeSeenBorrowingNotificationIds(ids) {
            seenBorrowingNotificationIds = new Set(ids);
            try {
                sessionStorage.setItem(
                    borrowingBadgeSeenKey,
                    JSON.stringify(Array.from(seenBorrowingNotificationIds))
                );
            } catch (error) {
                console.error('Status badge peminjaman gagal disimpan:', error);
            }
        }

        function markBorrowingBadgeSeen(ids) {
            storeSeenBorrowingNotificationIds(ids);
            updateCountBadge(borrowingsBadge, 0);
        }

        function fetchNotifications() {
            fetch('{{ route("notifications.index") }}', { cache: 'no-store' })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Gagal memuat notifikasi (${res.status})`);
                    }
                    return res.json();
                })
                .then(data => {
                    updateCountBadge(membersBadge, data.pending_members_count || 0);
                    borrowingNotificationIds = Array.isArray(data.pending_borrowing_notification_ids)
                        ? data.pending_borrowing_notification_ids
                        : [];
                    if (borrowingsNav && borrowingsNav.classList.contains('active')) {
                        markBorrowingBadgeSeen(borrowingNotificationIds);
                    } else {
                        const seenIds = new Set(
                            Array.from(getSeenBorrowingNotificationIds()).filter(
                                id => borrowingNotificationIds.includes(id)
                            )
                        );
                        storeSeenBorrowingNotificationIds(seenIds);
                        const unseenCount = borrowingNotificationIds.filter(
                            id => !seenIds.has(id)
                        ).length;
                        updateCountBadge(borrowingsBadge, unseenCount);
                    }
                    updateCountBadge(reservationsBadge, data.pending_reservations_count || 0);

                    if (data.unread_count > 0) {
                        badge.style.display = 'inline-flex';
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    } else {
                        badge.style.display = 'none';
                    }

                    if (data.notifications && data.notifications.length > 0) {
                        let html = '';
                        data.notifications.forEach(item => {
                            const bg = item.is_read ? '#ffffff' : '#f4faf9';
                            html += `
                                <div style="padding:10px 14px; border-bottom:1px solid #f0f4f4; background:${bg}; cursor:pointer; transition:background 0.15s;" onclick="markNotifRead(${item.id})">
                                    <div style="font-weight:700; font-size:12px; color:#1e3d3d; margin-bottom:2px;">${item.title}</div>
                                    <div style="font-size:11.5px; color:#526f6f; line-height:1.35;">${item.message}</div>
                                    <div style="font-size:10px; color:#8fa6a6; margin-top:4px;">${new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', hour:'2-digit', minute:'2-digit'})}</div>
                                </div>
                            `;
                        });
                        list.innerHTML = html;
                    } else {
                        list.innerHTML = '<div style="padding:20px; text-align:center; color:#8fa6a6;">Tidak ada notifikasi</div>';
                    }
                })
                .catch(error => console.error('Pembaruan notifikasi navbar gagal:', error));
        }

        if (borrowingsNav) {
            borrowingsNav.addEventListener('click', function () {
                markBorrowingBadgeSeen(borrowingNotificationIds);
            });
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            // Close profile dropdown if open
            const profileMenu = document.getElementById('adminProfileMenu');
            if (profileMenu) { profileMenu.style.display = 'none'; profileMenu.classList.remove('open'); }

            const isOpen = dropdown.style.display === 'block';
            dropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) fetchNotifications();
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        window.markNotifRead = function(id) {
            fetch('/api/notifications/' + id + '/read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(() => fetchNotifications());
        };

        window.markAllNotifAsRead = function() {
            fetch('{{ route("notifications.readAll") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(() => fetchNotifications());
        };

        // Auto-fetch every 6 seconds
        fetchNotifications();
        setInterval(fetchNotifications, 6000);
    }

    // ── Profile Dropdown ─────────────────────────────────────────────────────
    const profileBtn  = document.getElementById('adminProfileBtn');
    const profileMenu = document.getElementById('adminProfileMenu');
    const chevron     = document.getElementById('adminProfileChevron');

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            // Close notif dropdown if open
            if (dropdown) dropdown.style.display = 'none';

            const isOpen = profileMenu.classList.contains('open');
            if (isOpen) {
                profileMenu.style.display = 'none';
                profileMenu.classList.remove('open');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                profileBtn.style.background = 'transparent';
            } else {
                profileMenu.style.display = 'block';
                profileMenu.classList.add('open');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
                profileBtn.style.background = 'rgba(255,255,255,0.12)';
            }
        });

        document.addEventListener('click', function (e) {
            if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                profileMenu.style.display = 'none';
                profileMenu.classList.remove('open');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                if (profileBtn) profileBtn.style.background = 'transparent';
            }
        });
    }
});
</script>
@endauth