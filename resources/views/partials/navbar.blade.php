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


            {{-- PEMINJAMAN --}}

            <a
                href="{{ route('circulation') }}"
                class="{{ request()->routeIs('circulation') ? 'active' : '' }}">
                Peminjaman Buku
            </a>


            {{-- RESERVASI --}}

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
             USER / NOTIFICATION / PROFILE
             Logic dari PUSTAKA FINAL dipertahankan
        ====================================================== --}}

        <div
            class="nav-user"
            style="display:flex;align-items:center;gap:8px;margin-left:auto;">

            @auth

            {{-- NOTIFICATION --}}

            <div
                class="nav-notif-container"
                style="position:relative;">

                <button
                    type="button"
                    id="navNotifBtn"
                    title="Notifikasi"
                    style="background:none;border:none;color:#fff;cursor:pointer;position:relative;padding:6px;display:flex;align-items:center;justify-content:center;opacity:.85;"
                    onmouseover="this.style.opacity='1'"
                    onmouseout="this.style.opacity='.85'">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        style="width:19px;height:19px;">

                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>

                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>

                    </svg>

                    <span
                        id="navNotifBadge"
                        style="display:none;position:absolute;top:1px;right:1px;background:#e05252;color:#fff;font-size:10px;font-weight:800;min-width:15px;height:15px;border-radius:8px;line-height:15px;text-align:center;padding:0 3px;">
                    </span>

                </button>


                {{-- NOTIFICATION DROPDOWN --}}

                <div
                    id="navNotifDropdown"
                    style="display:none;position:absolute;right:0;top:calc(100% + 12px);width:320px;background:#fff;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,.18);border:1px solid #e2eeee;z-index:9999;overflow:hidden;color:#1e3d3d;">

                    <div
                        style="padding:12px 16px;border-bottom:1px solid #f0f4f4;display:flex;justify-content:space-between;align-items:center;background:#fafcfc;">

                        <strong style="font-size:13px;">
                            Notifikasi
                        </strong>

                        <button
                            type="button"
                            onclick="markAllNotifAsRead()"
                            style="background:none;border:none;color:#287879;font-size:11px;font-weight:600;cursor:pointer;">

                            Tandai Semua Dibaca

                        </button>

                    </div>


                    <div
                        id="navNotifList"
                        style="max-height:280px;overflow-y:auto;font-size:12.5px;">

                        <div
                            style="padding:20px;text-align:center;color:#8fa6a6;">

                            Tidak ada notifikasi baru

                        </div>

                    </div>

                </div>

            </div>

            @endauth


            {{-- =================================================
                 PROFILE
            ================================================== --}}

            <div
                id="adminProfileDropdown"
                style="position:relative;">

                <button
                    type="button"
                    id="adminProfileBtn"
                    style="display:flex;align-items:center;gap:9px;background:transparent;border:none;padding:4px 8px;border-radius:8px;cursor:pointer;transition:background .2s;"
                    onmouseover="this.style.background='rgba(255,255,255,.12)'"
                    onmouseout="if(!document.getElementById('adminProfileMenu').classList.contains('open'))this.style.background='transparent'">

                    <div
                        style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#d7e5e5;color:#287b7b;font-size:15px;font-weight:700;flex-shrink:0;">

                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}

                    </div>


                    <div
                        style="display:flex;flex-direction:column;line-height:1.2;text-align:left;">

                        <span
                            style="font-size:13px;font-weight:700;color:#fff;white-space:nowrap;">

                            {{ Auth::user()->name ?? 'Admin' }}

                        </span>

                        <span
                            style="font-size:11px;color:rgba(255,255,255,.8);white-space:nowrap;">

                            {{ (Auth::check() && Auth::user()->role === 'admin')
                                ? 'Admin Perpustakaan'
                                : 'Anggota' }}

                        </span>

                    </div>


                    <svg
                        id="adminProfileChevron"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="rgba(255,255,255,.8)"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        style="width:14px;height:14px;margin-left:2px;transition:transform .2s;flex-shrink:0;">

                        <polyline points="6 9 12 15 18 9"></polyline>

                    </svg>

                </button>


                {{-- PROFILE DROPDOWN --}}

                <div
                    id="adminProfileMenu"
                    style="display:none;position:absolute;right:0;top:calc(100% + 10px);min-width:220px;background:#fff;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,.16);border:1px solid #e8f0f0;z-index:9999;overflow:hidden;">

                    <div
                        style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid #f0f5f5;">

                        <div
                            style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#d7e5e5;color:#287b7b;font-size:15px;font-weight:700;flex-shrink:0;">

                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}

                        </div>


                        <div
                            style="line-height:1.3;min-width:0;">

                            <div
                                style="font-size:13.5px;font-weight:700;color:#1a3a3a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">

                                {{ Auth::user()->name ?? 'Admin' }}

                            </div>

                            <div
                                style="font-size:11.5px;color:#6b8f8f;white-space:nowrap;">

                                {{ (Auth::check() && Auth::user()->role === 'admin')
                                    ? 'Admin Perpustakaan'
                                    : 'Anggota' }}

                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        style="margin:0;padding:0;">

                        @csrf

                        <button
                            type="submit"
                            style="display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;background:transparent;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#e05252;text-align:left;transition:background .15s;"
                            onmouseover="this.style.background='#fff5f5'"
                            onmouseout="this.style.background='transparent'">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="width:16px;height:16px;">

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


{{-- =========================================================
     NOTIFICATION + PROFILE JAVASCRIPT
========================================================= --}}

@auth

<script>

document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('navNotifBtn');
    const dropdown = document.getElementById('navNotifDropdown');
    const badge = document.getElementById('navNotifBadge');
    const list = document.getElementById('navNotifList');


    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION
    |--------------------------------------------------------------------------
    */

    if (btn && dropdown) {

        function fetchNotifications() {

            fetch('{{ route("notifications.index") }}')

                .then(res => res.json())

                .then(data => {

                    if (data.unread_count > 0) {

                        badge.style.display = 'inline-block';

                        badge.textContent =
                            data.unread_count > 99
                                ? '99+'
                                : data.unread_count;

                    } else {

                        badge.style.display = 'none';

                    }


                    if (
                        data.notifications &&
                        data.notifications.length > 0
                    ) {

                        let html = '';

                        data.notifications.forEach(item => {

                            const bg =
                                item.is_read
                                    ? '#ffffff'
                                    : '#f4faf9';


                            html += `

                                <div
                                    style="padding:10px 14px;border-bottom:1px solid #f0f4f4;background:${bg};cursor:pointer;transition:background .15s;"
                                    onclick="markNotifRead(${item.id})">

                                    <div
                                        style="font-weight:700;font-size:12px;color:#1e3d3d;margin-bottom:2px;">

                                        ${item.title}

                                    </div>


                                    <div
                                        style="font-size:11.5px;color:#526f6f;line-height:1.35;">

                                        ${item.message}

                                    </div>


                                    <div
                                        style="font-size:10px;color:#8fa6a6;margin-top:4px;">

                                        ${new Date(item.created_at).toLocaleDateString(
                                            'id-ID',
                                            {
                                                day:'numeric',
                                                month:'short',
                                                hour:'2-digit',
                                                minute:'2-digit'
                                            }
                                        )}

                                    </div>

                                </div>

                            `;

                        });


                        list.innerHTML = html;

                    } else {

                        list.innerHTML =
                            '<div style="padding:20px;text-align:center;color:#8fa6a6;">Tidak ada notifikasi</div>';

                    }

                })

                .catch(() => {});

        }


        btn.addEventListener('click', function (e) {

            e.stopPropagation();


            const profileMenu =
                document.getElementById('adminProfileMenu');


            if (profileMenu) {

                profileMenu.style.display = 'none';

                profileMenu.classList.remove('open');

            }


            const isOpen =
                dropdown.style.display === 'block';


            dropdown.style.display =
                isOpen
                    ? 'none'
                    : 'block';


            if (!isOpen) {

                fetchNotifications();

            }

        });


        document.addEventListener('click', function (e) {

            if (
                !dropdown.contains(e.target) &&
                !btn.contains(e.target)
            ) {

                dropdown.style.display = 'none';

            }

        });


        window.markNotifRead = function (id) {

            fetch('/api/notifications/' + id + '/read', {

                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}',

                    'Content-Type':
                        'application/json'
                }

            })

            .then(() => fetchNotifications());

        };


        window.markAllNotifAsRead = function () {

            fetch('{{ route("notifications.readAll") }}', {

                method: 'POST',

                headers: {

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}',

                    'Content-Type':
                        'application/json'

                }

            })

            .then(() => fetchNotifications());

        };


        fetchNotifications();

        setInterval(fetchNotifications, 6000);

    }


    /*
    |--------------------------------------------------------------------------
    | PROFILE DROPDOWN
    |--------------------------------------------------------------------------
    */

    const profileBtn =
        document.getElementById('adminProfileBtn');

    const profileMenu =
        document.getElementById('adminProfileMenu');

    const chevron =
        document.getElementById('adminProfileChevron');


    if (
        profileBtn &&
        profileMenu
    ) {

        profileBtn.addEventListener('click', function (e) {

            e.stopPropagation();


            if (dropdown) {

                dropdown.style.display = 'none';

            }


            const isOpen =
                profileMenu.classList.contains('open');


            if (isOpen) {

                profileMenu.style.display = 'none';

                profileMenu.classList.remove('open');

                if (chevron) {

                    chevron.style.transform =
                        'rotate(0deg)';

                }

                profileBtn.style.background =
                    'transparent';

            } else {

                profileMenu.style.display = 'block';

                profileMenu.classList.add('open');

                if (chevron) {

                    chevron.style.transform =
                        'rotate(180deg)';

                }

                profileBtn.style.background =
                    'rgba(255,255,255,.12)';

            }

        });


        document.addEventListener('click', function (e) {

            if (
                !profileMenu.contains(e.target) &&
                !profileBtn.contains(e.target)
            ) {

                profileMenu.style.display = 'none';

                profileMenu.classList.remove('open');

                if (chevron) {

                    chevron.style.transform =
                        'rotate(0deg)';

                }

                profileBtn.style.background =
                    'transparent';

            }

        });

    }

});

</script>

@endauth