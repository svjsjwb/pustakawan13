/* ============================================================
   USER LAYOUT JS
   Sidebar collapse, dropdowns, notifications, toast system
   ============================================================ */

(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    /* ── Sidebar collapse ────────────────────────────────────── */
    const sidebar       = document.getElementById('ulSidebar');
    const collapseBtn   = document.getElementById('ulSidebarCollapse');
    const mobileMenuBtn = document.getElementById('ulMobileMenu');
    const mainEl        = document.getElementById('ulMain');

    // Restore state
    if (localStorage.getItem('sidebarCollapsed') === '1' && sidebar) {
        sidebar.classList.add('collapsed');
    }

    collapseBtn?.addEventListener('click', () => {
        const collapsed = sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    });

    // Mobile overlay
    mobileMenuBtn?.addEventListener('click', () => {
        sidebar?.classList.toggle('mobile-open');
    });

    document.addEventListener('click', (e) => {
        if (sidebar?.classList.contains('mobile-open')) {
            if (!sidebar.contains(e.target) && !mobileMenuBtn?.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        }
    });

    /* ── Notification dropdown ───────────────────────────────── */
    const notifBtn      = document.getElementById('ulNotifBtn');
    const notifDropdown = document.getElementById('ulNotifDropdown');

    notifBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown?.classList.toggle('open');
        document.getElementById('ulUserDropdown')?.classList.remove('open');
        document.getElementById('ulUserBtn')?.classList.remove('open');
    });

    /* ── User dropdown ───────────────────────────────────────── */
    const userBtn      = document.getElementById('ulUserBtn');
    const userDropdown = document.getElementById('ulUserDropdown');

    userBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown?.classList.toggle('open');
        userBtn?.classList.toggle('open');
        notifDropdown?.classList.remove('open');
    });

    document.addEventListener('click', () => {
        notifDropdown?.classList.remove('open');
        userDropdown?.classList.remove('open');
        userBtn?.classList.remove('open');
    });

    /* ── Dismiss notification ───────────────────────────────── */
    window.dismissNotif = function (key, btn) {
        const item = btn?.closest('.ul-notif-item');
        if (item) {
            item.style.animation = 'ul-toast-in .2s ease reverse';
            setTimeout(() => {
                item.remove();
                updateNotifBadge();
            }, 180);
        }
        // Save dismissed to session via AJAX
        fetch('/user/notifications/dismiss', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ key }),
        }).catch(() => {});
    };

    window.clearAllNotif = function () {
        const items = document.querySelectorAll('.ul-notif-item');
        const keys  = Array.from(items).map(i => i.dataset.key);
        items.forEach(i => i.remove());
        const list = document.getElementById('ulNotifList');
        if (list) {
            list.innerHTML = `
                <div class="ul-notif-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>Tidak ada notifikasi baru</p>
                </div>`;
        }
        updateNotifBadge();
        fetch('/user/notifications/dismiss-all', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ keys }),
        }).catch(() => {});
    };

    function updateNotifBadge() {
        const count = document.querySelectorAll('.ul-notif-item').length;
        const badge = document.getElementById('ulNotifBadge');
        if (badge) {
            if (count > 0) { badge.textContent = count; }
            else { badge.remove(); }
        }
    }

    /* ── Auto-hide flash alerts ──────────────────────────────── */
    setTimeout(() => {
        document.getElementById('ul-flash-success')?.remove();
        document.getElementById('ul-flash-error')?.remove();
    }, 4000);

    /* ── Toast system ────────────────────────────────────────── */
    window.showToast = function (message, type = 'info') {
        const container = document.getElementById('ulToastContainer');
        if (!container) return;

        const icons = {
            success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            error:   '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
            info:    '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
            warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        };

        const toast = document.createElement('div');
        toast.className = `ul-toast ul-toast-${type}`;
        toast.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${icons[type] || icons.info}</svg>
            <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'ul-toast-in .22s ease reverse';
            setTimeout(() => toast.remove(), 200);
        }, 3200);
    };

    /* ── Favorite toggle ─────────────────────────────────────── */
    window.toggleFavorite = function (bookId, btn) {
        fetch('/user/favorites/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ book_id: bookId }),
        })
        .then(r => r.json())
        .then(data => {
            const isFav = data.favorited;
            if (btn) {
                btn.classList.toggle('favorited', isFav);
                btn.title = isFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit';
                const svg = btn.querySelector('svg');
                if (svg) svg.setAttribute('fill', isFav ? 'currentColor' : 'none');
            }
            showToast(data.message, isFav ? 'success' : 'info');
        })
        .catch(() => showToast('Gagal mengubah favorit', 'error'));
    };

    /* ── Topbar search shortcut (Ctrl+K) ─────────────────────── */
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('topbarSearch')?.focus();
        }
    });

})();
