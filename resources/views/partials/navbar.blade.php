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

            <a href="{{ route('catalog') }}"
                class="{{ request()->routeIs('catalog') ? 'active' : '' }}">
                Katalog Buku
            </a>

            <a href="{{ route('books.index') }}"
                class="{{ request()->routeIs('books.*') ? 'active' : '' }}">
                Manajemen Buku
            </a>

            <a href="{{ route('circulation') }}"
                class="{{ request()->routeIs('circulation') ? 'active' : '' }}">
                Peminjaman Buku
            </a>

            <a href="{{ route('reservations.index') }}"
                class="{{ request()->routeIs('reservations*') ? 'active' : '' }}">
                Reservasi Buku
            </a>

            <a href="{{ route('reports.index') }}"
                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Laporan
            </a>

        </nav>


        {{-- USER, NOTIF & LOGOUT --}}
        <div class="nav-user">

            {{-- NOTIFIKASI DROPDOWN --}}
            @auth
            <div class="nav-notif-container" style="position: relative; margin-right: 6px;">
                <button type="button" id="navNotifBtn" title="Notifikasi" style="background:none; border:none; color:#fff; cursor:pointer; position:relative; padding:6px; display:flex; align-items:center; justify-content:center; opacity: 0.85;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:19px;height:19px;">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span id="navNotifBadge" style="display:none; position:absolute; top:1px; right:1px; background:#e05252; color:#fff; font-size:10px; font-weight:800; min-width:15px; height:15px; border-radius:8px; line-height:15px; text-align:center; padding:0 3px;"></span>
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

            <div class="nav-user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>

            <div class="nav-user-info">
                <span>{{ Auth::user()->name ?? 'Pengguna' }}</span>
                <small>{{ (Auth::check() && Auth::user()->role === 'admin') ? 'Admin Perpustakaan' : 'Anggota' }}</small>
            </div>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}" style="margin-left:0.65rem;">
                @csrf
                <button
                    type="submit"
                    title="Logout"
                    style="background:none;border:none;cursor:pointer;color:inherit;font-size:0.82rem;padding:0.3rem 0.6rem;border-radius:6px;border:1px solid currentColor;opacity:0.75;"
                    onmouseover="this.style.opacity='1'"
                    onmouseout="this.style.opacity='0.75'">
                    Logout
                </button>
            </form>

        </div>

    </div>

</header>
@endif

@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('navNotifBtn');
    const dropdown = document.getElementById('navNotifDropdown');
    const badge = document.getElementById('navNotifBadge');
    const list = document.getElementById('navNotifList');

    if (!btn || !dropdown) return;

    function fetchNotifications() {
        fetch('{{ route("notifications.index") }}')
            .then(res => res.json())
            .then(data => {
                if (data.unread_count > 0) {
                    badge.style.display = 'inline-block';
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
            .catch(() => {});
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = dropdown.style.display === 'block';
        dropdown.style.display = isOpen ? 'none' : 'block';
        if (!isOpen) {
            fetchNotifications();
        }
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    window.markNotifRead = function(id) {
        fetch('/api/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => fetchNotifications());
    };

    window.markAllNotifAsRead = function() {
        fetch('{{ route("notifications.readAll") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => fetchNotifications());
    };

    // Auto-fetch every 20 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 20000);
});
</script>
@endauth