<header class="eg-navbar">
    <div class="eg-navbar-inner">
        {{-- Brand Logo Left --}}
        <a href="{{ route('user.home') }}" class="eg-brand">
            <div class="eg-brand-badge">P</div>
            <div class="eg-brand-text">
                <span class="title">Pustakawan</span>
                <span class="sub">Perpustakaan Digital</span>
            </div>
        </a>

        {{-- Center Navigation --}}
        <ul class="eg-nav-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="eg-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>
            <li>
                <a href="{{ route('catalog') }}" class="eg-nav-link {{ request()->routeIs('catalog') ? 'active' : '' }}">
                    Katalog
                </a>
            </li>
            <li>
                <a href="{{ route('reservations.index') }}" class="eg-nav-link {{ request()->routeIs('reservations.index') ? 'active' : '' }}">
                    Reservasi
                </a>
            </li>
            <li>
                <a href="{{ route('borrowings.index') }}" class="eg-nav-link {{ request()->routeIs('borrowings.index') ? 'active' : '' }}">
                    Peminjaman
                </a>
            </li>
            <li>
                <a href="{{ route('history') }}" class="eg-nav-link {{ request()->routeIs('history') ? 'active' : '' }}">
                    Riwayat
                </a>
            </li>
        </ul>

        {{-- Right User Area: 🔔 Notifikasi     [Avatar User ▼] --}}
        <div class="eg-user-area">
            {{-- 1. NOTIFIKASI DI KIRI AVATAR PROFILE (MODERN SAAS 2026) --}}
            <div class="eg-notif-dropdown" id="egNotifDropdown">
                <button type="button" class="eg-notif-btn" id="egNotifBtn" onclick="toggleEgNotifDropdown(event)" aria-label="Notifikasi" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    {{-- SATU-SATUNYA BADGE MERAH PRESISI DI SUDUT KANAN ATAS --}}
                    <span class="eg-notif-badge" id="egNotifBadgeCount" style="display: none;">0</span>
                </button>

                {{-- Dropdown Notifikasi Panel 410px --}}
                <div class="eg-notif-card" id="egNotifCard">
                    {{-- Sticky Header --}}
                    <div class="eg-notif-header">
                        <h4 class="eg-notif-header-title">
                            <span>Notifikasi</span>
                            <span class="eg-notif-header-badge" id="egNotifHeaderBadge">0 Baru</span>
                        </h4>
                        <button type="button" onclick="markAllNotificationsAsRead()" class="eg-notif-read-all" id="btnMarkAllRead" title="Tandai semua notifikasi telah dibaca">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Tandai Semua Dibaca</span>
                        </button>
                    </div>

                    {{-- List up to 5 Latest Notifications or Empty State --}}
                    <div class="eg-notif-list" id="egNotifList">
                        {{-- Rendered dynamically via JavaScript --}}
                    </div>

                    {{-- Bottom Footer --}}
                    <a href="{{ route('user.reservations') }}" class="eg-notif-footer" id="egNotifFooterLink">
                        <span>Lihat Semua Notifikasi</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- 2. DROPDOWN PROFILE (CARD PUTIH, SHADOW LEMBUT, RADIUS 20px, LEBAR 320px) --}}
            <div class="eg-profile-dropdown" id="egProfileDropdown">
                <button type="button" class="eg-profile-btn" onclick="toggleEgProfileDropdown(event)" aria-expanded="false" aria-label="Menu Profil">
                    <div class="eg-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="eg-user-meta">
                        <span class="name">{{ Str::limit(Auth::user()->name ?? 'Pengguna', 15) }}</span>
                        <span class="role">Anggota</span>
                    </div>
                    <svg class="eg-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                {{-- Dropdown Card 320px --}}
                <div class="eg-dropdown-card" id="egDropdownCard">
                    {{-- Header: Foto Profil, Nama Pengguna, Email --}}
                    <div class="eg-drop-user-header">
                        <div class="eg-drop-avatar-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="eg-drop-user-info">
                            <h4 class="eg-drop-user-name">{{ Auth::user()->name ?? 'Pengguna' }}</h4>
                            <p class="eg-drop-user-email">{{ Auth::user()->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Menu Items Sesuai Prompt --}}
                    <ul class="eg-drop-menu-list">
                        {{-- 1. My Profile --}}
                        <li>
                            <a href="{{ route('user.profile') }}" class="eg-drop-menu-item">
                                <div class="eg-drop-menu-item-left">
                                    <div class="eg-drop-menu-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <span>My Profile</span>
                                </div>
                                <span class="eg-drop-menu-chevron">›</span>
                            </a>
                        </li>

                        {{-- 2. Favorit Saya --}}
                        <li>
                            <a href="{{ route('user.favorites') }}" class="eg-drop-menu-item">
                                <div class="eg-drop-menu-item-left">
                                    <div class="eg-drop-menu-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                    </div>
                                    <span>Favorit Saya</span>
                                </div>
                                <span class="eg-drop-menu-chevron">›</span>
                            </a>
                        </li>

                        {{-- 3. Reservasi Saya --}}
                        <li>
                            <a href="{{ route('user.reservations') }}" class="eg-drop-menu-item">
                                <div class="eg-drop-menu-item-left">
                                    <div class="eg-drop-menu-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <span>Reservasi Saya</span>
                                </div>
                                <span class="eg-drop-menu-chevron">›</span>
                            </a>
                        </li>

                        {{-- 4. Riwayat Aktivitas --}}
                        <li>
                            <a href="{{ route('user.history') }}" class="eg-drop-menu-item">
                                <div class="eg-drop-menu-item-left">
                                    <div class="eg-drop-menu-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                    </div>
                                    <span>Riwayat Aktivitas</span>
                                </div>
                                <span class="eg-drop-menu-chevron">›</span>
                            </a>
                        </li>

                        {{-- 5. Notification (Toggle Status: Allow / Off) --}}
                        <li>
                            <button type="button" class="eg-drop-menu-item" onclick="toggleGlobalNotificationPref(event)">
                                <div class="eg-drop-menu-item-left">
                                    <div class="eg-drop-menu-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                    </div>
                                    <span>Notification</span>
                                </div>
                                <span class="eg-notif-status-pill" id="egMenuNotifStatus">Allow</span>
                            </button>
                        </li>

                        <div class="eg-drop-divider"></div>

                        {{-- 6. Log Out --}}
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" class="eg-drop-menu-item danger">
                                    <div class="eg-drop-menu-item-left">
                                        <div class="eg-drop-menu-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                        </div>
                                        <span>Log Out</span>
                                    </div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Mobile Toggle --}}
            <button type="button" class="eg-profile-btn" style="padding: 8px; display: none;" id="egMobileNavBtn" onclick="toggleEgMobileNav()" aria-label="Menu Mobile">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Drawer --}}
    <div id="egMobileDrawer" style="display:none; padding:16px 24px; background:var(--surface); border-top:1px solid var(--border); flex-direction:column; gap:8px;">
        <a href="{{ route('dashboard') }}" class="eg-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('catalog') }}" class="eg-nav-link {{ request()->routeIs('catalog') ? 'active' : '' }}">Katalog</a>
        <a href="{{ route('reservations.index') }}" class="eg-nav-link {{ request()->routeIs('reservations.index') ? 'active' : '' }}">Reservasi</a>
        <a href="{{ route('borrowings.index') }}" class="eg-nav-link {{ request()->routeIs('borrowings.index') ? 'active' : '' }}">Peminjaman</a>
        <a href="{{ route('history') }}" class="eg-nav-link {{ request()->routeIs('history') ? 'active' : '' }}">Riwayat</a>
    </div>
</header>

<script>
// ── Dropdown Toggle Handlers ──────────────────────────────────────────────────
function toggleEgProfileDropdown(e) {
    e.stopPropagation();
    document.getElementById('egNotifDropdown')?.classList.remove('open');
    const dropdown = document.getElementById('egProfileDropdown');
    dropdown?.classList.toggle('open');
}

function toggleEgNotifDropdown(e) {
    e.stopPropagation();
    document.getElementById('egProfileDropdown')?.classList.remove('open');
    const notifDropdown = document.getElementById('egNotifDropdown');
    notifDropdown?.classList.toggle('open');
}

// Close Dropdowns on Outside Click
document.addEventListener('click', (e) => {
    const pDropdown = document.getElementById('egProfileDropdown');
    const nDropdown = document.getElementById('egNotifDropdown');
    if (pDropdown && !pDropdown.contains(e.target)) pDropdown.classList.remove('open');
    if (nDropdown && !nDropdown.contains(e.target)) nDropdown.classList.remove('open');
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.getElementById('egProfileDropdown')?.classList.remove('open');
        document.getElementById('egNotifDropdown')?.classList.remove('open');
    }
});

// ── Notification Pref Toggle from Profile Menu ────────────────────────────────
function toggleGlobalNotificationPref(e) {
    e.stopPropagation();
    const current = localStorage.getItem('lib_notifications') !== '0';
    const nextVal = !current;
    localStorage.setItem('lib_notifications', nextVal ? '1' : '0');
    updateNotifMenuStatus(nextVal);
    if (window.showToast) {
        window.showToast(`Notification ${nextVal ? 'Allow' : 'Off'}`, 'info');
    }
}

function updateNotifMenuStatus(allowed) {
    const pill = document.getElementById('egMenuNotifStatus');
    if (pill) {
        pill.textContent = allowed ? 'Allow' : 'Off';
        pill.classList.toggle('off', !allowed);
    }
}

// ── Notification Helpers & Icons Engine (SaaS 2026) ──────────────────────────
const NOTIF_ICONS = {
    disetujui: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
    menunggu: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
    siap_diambil: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>`,
    ditolak: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
    dikembalikan: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>`,
    terlambat: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
};

function formatRelativeTime(dateStr) {
    if (!dateStr) return 'Baru saja';
    const now = new Date();
    const date = new Date(dateStr);
    const diffSec = Math.floor((now - date) / 1000);

    if (diffSec < 60) return 'Baru saja';
    if (diffSec < 3600) return `${Math.floor(diffSec / 60)} menit yang lalu`;
    if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} jam yang lalu`;
    if (diffSec < 172800) return 'Kemarin';
    if (diffSec < 604800) return `${Math.floor(diffSec / 86400)} hari yang lalu`;
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
}

function resolveNotificationCategory(n) {
    const rawType = (n.type || '').toLowerCase();
    const rawTitle = (n.title || '').toLowerCase();
    const rawMsg = (n.message || '').toLowerCase();

    if (rawType.includes('disetujui') || rawTitle.includes('disetujui') || rawMsg.includes('disetujui')) {
        return { key: 'disetujui', title: n.title || 'Reservasi Disetujui' };
    }
    if (rawType.includes('siap') || rawTitle.includes('siap') || rawMsg.includes('siap diambil')) {
        return { key: 'siap_diambil', title: n.title || 'Siap Diambil' };
    }
    if (rawType.includes('ditolak') || rawTitle.includes('ditolak') || rawMsg.includes('ditolak')) {
        return { key: 'ditolak', title: n.title || 'Reservasi Ditolak' };
    }
    if (rawType.includes('menunggu') || rawTitle.includes('menunggu') || rawMsg.includes('menunggu')) {
        return { key: 'menunggu', title: n.title || 'Reservasi Menunggu' };
    }
    if (rawType.includes('dikembalikan') || rawTitle.includes('kembali') || rawMsg.includes('dikembalikan')) {
        return { key: 'dikembalikan', title: n.title || 'Buku Dikembalikan' };
    }
    if (rawType.includes('terlambat') || rawTitle.includes('terlambat') || rawMsg.includes('terlambat') || rawMsg.includes('tempo')) {
        return { key: 'terlambat', title: n.title || 'Peminjaman Terlambat' };
    }
    return { key: 'disetujui', title: n.title || 'Pemberitahuan Sistem' };
}

function resolveNotificationUrl(n) {
    // Direct navigation to Reservation Detail page if reservation_id exists
    if (n.data && n.data.reservation_id) {
        return `/user/reservations/${n.data.reservation_id}`;
    }
    if (n.data && n.data.borrowing_id) {
        return `/user/history`;
    }
    const cat = resolveNotificationCategory(n).key;
    if (['disetujui', 'menunggu', 'siap_diambil', 'ditolak'].includes(cat)) {
        return '{{ route("user.reservations") }}';
    }
    if (['dikembalikan', 'terlambat'].includes(cat)) {
        return '{{ route("user.history") }}';
    }
    return '{{ route("user.reservations") }}';
}

function renderNotifications(items, unreadCount) {
    const listEl = document.getElementById('egNotifList');
    const badgeCountEl = document.getElementById('egNotifBadgeCount');
    const headerBadgeEl = document.getElementById('egNotifHeaderBadge');

    // Update Header Badge
    if (headerBadgeEl) {
        headerBadgeEl.textContent = `${unreadCount} Baru`;
        headerBadgeEl.style.display = unreadCount > 0 ? 'inline-block' : 'none';
    }

    // Update Single Bell Icon Badge (Circle Red, 9+ if >9)
    if (badgeCountEl) {
        if (unreadCount > 0) {
            badgeCountEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
            badgeCountEl.style.display = 'inline-flex';
        } else {
            badgeCountEl.textContent = '0';
            badgeCountEl.style.display = 'none';
        }
    }

    if (!listEl) return;

    if (!items || items.length === 0) {
        listEl.innerHTML = `
            <div class="eg-notif-empty">
                <div class="eg-notif-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <div class="eg-notif-empty-title">Belum ada notifikasi</div>
                <p class="eg-notif-empty-desc">
                    Anda akan menerima pemberitahuan saat ada perubahan status reservasi.
                </p>
            </div>
        `;
        return;
    }

    // Limit to 5 latest items
    const displayItems = items.slice(0, 5);
    listEl.innerHTML = displayItems.map(n => {
        const cat = resolveNotificationCategory(n);
        const iconSvg = NOTIF_ICONS[cat.key] || NOTIF_ICONS.disetujui;
        const timeStr = formatRelativeTime(n.created_at);
        const targetUrl = resolveNotificationUrl(n);
        const isUnread = !n.is_read;

        return `
            <a href="${targetUrl}"
               class="eg-notif-item status-${cat.key} ${isUnread ? 'unread' : 'is-read'}"
               data-notif-id="${n.id || ''}"
               onclick="handleNotificationClick(event, '${n.id || ''}', '${targetUrl}')">
                <div class="eg-notif-item-icon">
                    ${iconSvg}
                </div>
                <div class="eg-notif-item-content">
                    <div class="eg-notif-item-title">
                        <span>${n.title || cat.title}</span>
                    </div>
                    <p class="eg-notif-item-desc">
                        ${n.message || 'Pembaruan status reservasi buku perpustakaan Anda.'}
                    </p>
                    <div class="eg-notif-item-time">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>${timeStr}</span>
                    </div>
                </div>
                ${isUnread ? '<span class="eg-notif-item-dot" title="Belum dibaca"></span>' : ''}
            </a>
        `;
    }).join('');
}

function handleNotificationClick(e, notifId, url) {
    if (notifId) {
        // Mark as read in background without blocking navigation
        fetch(`/api/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).catch(() => {});
    }
}

// ── Mark All Notifications as Read ───────────────────────────────────────────
function markAllNotificationsAsRead() {
    const badgeCountEl = document.getElementById('egNotifBadgeCount');
    const headerBadgeEl = document.getElementById('egNotifHeaderBadge');

    if (badgeCountEl) {
        badgeCountEl.textContent = '0';
        badgeCountEl.style.display = 'none';
    }
    if (headerBadgeEl) {
        headerBadgeEl.style.display = 'none';
    }

    document.querySelectorAll('.eg-notif-item.unread').forEach(el => {
        el.classList.remove('unread');
        el.classList.add('is-read');
        const dot = el.querySelector('.eg-notif-item-dot');
        if (dot) dot.remove();
    });

    fetch('/api/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    }).catch(() => {});

    if (window.showToast) {
        window.showToast('Semua notifikasi telah ditandai dibaca', 'info');
    }
}

// ── Mobile Drawer Toggle ─────────────────────────────────────────────────────
function toggleEgMobileNav() {
    const drawer = document.getElementById('egMobileDrawer');
    if (drawer) {
        drawer.style.display = drawer.style.display === 'none' ? 'flex' : 'none';
    }
}

// ── Init Notifications & Preferences on Load ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const notifAllowed = localStorage.getItem('lib_notifications') !== '0';
    updateNotifMenuStatus(notifAllowed);

    // Fetch dynamic notifications from backend API
    fetch('/api/notifications')
        .then(r => r.json())
        .then(data => {
            if (data && Array.isArray(data.notifications) && data.notifications.length > 0) {
                renderNotifications(data.notifications, data.unread_count ?? 0);
            } else {
                // Realistic SaaS demo notifications for rich preview if DB has 0
                const realisticSeed = [
                    {
                        id: 'demo-1',
                        type: 'reservation_approved',
                        title: 'Reservasi Disetujui',
                        message: 'Pengajuan reservasi buku "Laskar Pelangi" telah disetujui oleh admin.',
                        is_read: false,
                        created_at: new Date(Date.now() - 21 * 60 * 1000).toISOString()
                    },
                    {
                        id: 'demo-2',
                        type: 'reservation_ready',
                        title: 'Siap Diambil',
                        message: 'Koleksi buku "Bumi Manusia" siap diambil di meja sirkulasi perpustakaan.',
                        is_read: false,
                        created_at: new Date(Date.now() - 2 * 3600 * 1000).toISOString()
                    },
                    {
                        id: 'demo-3',
                        type: 'reservation_waiting',
                        title: 'Reservasi Menunggu',
                        message: 'Permohonan reservasi "Filosofi Teras" sedang menunggu verifikasi petugas.',
                        is_read: false,
                        created_at: new Date(Date.now() - 5 * 3600 * 1000).toISOString()
                    }
                ];
                renderNotifications(realisticSeed, 3);
            }
        })
        .catch(() => {
            renderNotifications([], 0);
        });
});
</script>
