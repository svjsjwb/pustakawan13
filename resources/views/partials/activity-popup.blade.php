@php
    $currentUser = Auth::user();
    // Pop up pemberitahuan hanya muncul jika akun user sudah disetujui (role 'member')
    $isApprovedMember = $currentUser && $currentUser->role === 'member';

    $isJustLoggedIn = session('just_logged_in') ? true : false;
    if (session()->has('just_logged_in')) {
        session()->forget('just_logged_in');
    }

    $popupActivities = ($isApprovedMember && \Illuminate\Support\Facades\Schema::hasTable('activities'))
        ? \App\Models\Activity::orderByDesc('pinned_at')->latest()->take(5)->get()
        : collect();
    $latestPopupActivity = $popupActivities->first();

    $encodedActivities = $popupActivities->map(function ($act) {
        return [
            'id' => $act->id,
            'title' => $act->title,
            'description' => $act->description ?? 'Tidak ada rincian tambahan untuk aktivitas ini.',
            'pinned' => (bool) $act->pinned_at,
            'date' => $act->created_at ? $act->created_at->translatedFormat('d M Y, H:i') . ' WIB' : 'Baru saja',
            'updated_timestamp' => $act->updated_at?->timestamp ?? ($act->created_at?->timestamp ?? time()),
        ];
    })->values();
@endphp

@if ($isApprovedMember && $popupActivities->isNotEmpty() && $latestPopupActivity)
    <div id="shopeeActivityPopupOverlay" class="shopee-popup-overlay" aria-hidden="true" role="dialog"
        aria-modal="true" data-latest-id="{{ $latestPopupActivity->id }}"
        data-latest-time="{{ $latestPopupActivity->updated_at?->timestamp ?? $latestPopupActivity->created_at?->timestamp }}"
        data-just-logged-in="{{ $isJustLoggedIn ? 'true' : 'false' }}"
        data-user-id="{{ Auth::id() ?? 0 }}">

        <div class="shopee-popup-wrapper relative w-full max-w-[440px]">

            {{-- Floating Shopee-style Circular Close Button --}}
            <button type="button" class="shopee-popup-close" id="shopeePopupCloseBtn" aria-label="Tutup Pengumuman"
                title="Tutup">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            {{-- Main Shopee Popup Card --}}
            <div class="shopee-popup-card">

                {{-- Header Banner with Gradient & Ambient Orbs --}}
                <div class="shopee-popup-header">
                    <div class="shopee-glow-orb-1"></div>
                    <div class="shopee-glow-orb-2"></div>

                    {{-- Badge Pill --}}
                    <div>
                        <span class="shopee-badge-pill" id="shopeeBadgePill">
                            <span class="shopee-badge-dot"></span>
                            <span id="shopeeBadgeText">
                                {{ $latestPopupActivity->pinned_at ? '📌 PENGUMUMAN DISEMATKAN' : '📢 PENGUMUMAN RESMI' }}
                            </span>
                        </span>
                    </div>

                    {{-- 3D-effect Glowing Megaphone Icon --}}
                    <div class="shopee-icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 5L6 9H2v6h4l5 4V5z"></path>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                        </svg>
                    </div>

                    {{-- Activity Title --}}
                    <h2 class="shopee-header-title" id="shopeeActivityTitle">
                        {{ $latestPopupActivity->title }}
                    </h2>
                </div>

                {{-- Body Content --}}
                <div class="shopee-popup-body">

                    {{-- Description Content Box --}}
                    <div class="shopee-desc-box" id="shopeeActivityDesc">
                        {!! nl2br(e($latestPopupActivity->description ?? 'Tidak ada rincian tambahan untuk aktivitas ini.')) !!}
                    </div>

                    {{-- Carousel Navigation Dots (If more than 1 announcement) --}}
                    @if ($popupActivities->count() > 1)
                        <div class="shopee-carousel-nav" id="shopeeCarouselNav">
                            @foreach ($popupActivities as $index => $item)
                                <button type="button" class="shopee-dot {{ $index === 0 ? 'active' : '' }}"
                                    data-index="{{ $index }}" aria-label="Lihat aktivitas {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Primary CTA Button --}}
                    <button type="button" class="shopee-btn-submit" id="shopeePopupConfirmBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Saya Mengerti</span>
                    </button>

                    {{-- Shopee-style "Jangan tampilkan lagi hari ini" Option --}}
                    <label class="shopee-popup-footer" for="shopeeHideTodayCheckbox">
                        <input type="checkbox" id="shopeeHideTodayCheckbox">
                        <span>Jangan tampilkan lagi hari ini</span>
                    </label>

                </div>

            </div>

        </div>

    </div>

    {{-- Data items encoded for carousel interactivity --}}
    <script>
        window.__SHOPEE_ACTIVITIES__ = {!! json_encode($encodedActivities) !!};

        // Fungsi publik yang dapat dipanggil kapan saja (misal untuk testing / tombol preview)
        window.openShopeeActivityPopup = function () {
            const overlay = document.getElementById('shopeeActivityPopupOverlay');
            if (!overlay) return;
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        };

        window.closeShopeeActivityPopup = function () {
            const overlay = document.getElementById('shopeeActivityPopupOverlay');
            if (!overlay) return;

            const hideTodayCheckbox = document.getElementById('shopeeHideTodayCheckbox');
            const activities = window.__SHOPEE_ACTIVITIES__ || [];
            const latestId = activities.length ? activities[0].id : 0;
            const userId = overlay.getAttribute('data-user-id') || '0';
            const todayKey = 'shopee_hide_today_u' + userId + '_a' + latestId;
            const todayStr = new Date().toISOString().slice(0, 10);

            if (hideTodayCheckbox && hideTodayCheckbox.checked) {
                try { localStorage.setItem(todayKey, todayStr); } catch (e) {}
            }

            // Catat bahwa aktivitas ini sudah pernah ditampilkan ke user
            try {
                sessionStorage.setItem('shopee_popup_shown_session_u' + userId, '1');
                localStorage.setItem('shopee_last_seen_act_u' + userId, String(latestId));
                sessionStorage.setItem('shopee_dismissed_session_u' + userId + '_a' + latestId, '1');
            } catch (e) {}

            // Tandai sudah dibaca via server API
            if (latestId) {
                const markUrl = '/activities/' + latestId + '/read';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    fetch(markUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    }).catch(function () {});
                }
            }

            overlay.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        function initShopeePopup() {
            const overlay = document.getElementById('shopeeActivityPopupOverlay');
            if (!overlay) return;

            const activities = window.__SHOPEE_ACTIVITIES__ || [];
            if (!activities.length) return;

            const latestActivity = activities[0];
            const latestId = latestActivity.id;
            const userId = overlay.getAttribute('data-user-id') || '0';

            // Kunci penanda sesi tab untuk mendeteksi refresh halaman
            const sessionShownKey = 'shopee_popup_shown_session_u' + userId;
            const alreadyShownThisSession = (function() {
                try { return sessionStorage.getItem(sessionShownKey) === '1'; } catch (e) { return false; }
            })();

            // Jika pada sesi tab ini web sudah pernah dibuka / di-refresh, jangan tampilkan lagi
            if (alreadyShownThisSession) {
                return;
            }

            const todayKey = 'shopee_hide_today_u' + userId + '_a' + latestId;
            const todayStr = new Date().toISOString().slice(0, 10);
            const hiddenToday = (function() {
                try { return localStorage.getItem(todayKey) === todayStr; } catch (e) { return false; }
            })();

            // Jika user memilih "Jangan tampilkan lagi hari ini", jangan munculkan
            if (hiddenToday) {
                return;
            }

            const sessionDismissedKey = 'shopee_dismissed_session_u' + userId + '_a' + latestId;
            const dismissedThisSession = (function() {
                try { return sessionStorage.getItem(sessionDismissedKey) === '1'; } catch (e) { return false; }
            })();

            if (dismissedThisSession) {
                return;
            }

            // Tampilkan hanya di halaman dashboard / home utama saat buka web
            const currentPath = window.location.pathname.toLowerCase();
            const isHomeOrDashboard = currentPath === '' ||
                currentPath === '/' ||
                currentPath.endsWith('/dashboard') ||
                currentPath.endsWith('/home');

            if (!isHomeOrDashboard) {
                return;
            }

            // Tandai sesi tab ini bahwa popup sudah diproses saat buka web
            // sehingga saat user me-refresh halaman (F5), popup tidak akan muncul kembali
            try {
                sessionStorage.setItem(sessionShownKey, '1');
            } catch (e) {}

            setTimeout(function () {
                window.openShopeeActivityPopup();
            }, 300);

            // Carousel Switcher
            let currentIndex = 0;
            const titleEl = document.getElementById('shopeeActivityTitle');
            const descEl = document.getElementById('shopeeActivityDesc');
            const badgeTextEl = document.getElementById('shopeeBadgeText');
            const dots = document.querySelectorAll('.shopee-dot');

            function switchSlide(index) {
                if (!activities[index]) return;
                currentIndex = index;
                const act = activities[index];

                if (titleEl) titleEl.textContent = act.title;
                if (descEl) descEl.innerHTML = act.description.replace(/\n/g, '<br>');
                if (badgeTextEl) {
                    badgeTextEl.textContent = act.pinned ? '📌 PENGUMUMAN DISEMATKAN' : '📢 PENGUMUMAN RESMI';
                }

                dots.forEach((dot, idx) => {
                    dot.classList.toggle('active', idx === index);
                });
            }

            dots.forEach((dot) => {
                dot.addEventListener('click', function () {
                    const idx = parseInt(this.getAttribute('data-index'), 10);
                    switchSlide(idx);
                });
            });

            const closeBtn = document.getElementById('shopeePopupCloseBtn');
            const confirmBtn = document.getElementById('shopeePopupConfirmBtn');

            if (closeBtn) closeBtn.addEventListener('click', window.closeShopeeActivityPopup);
            if (confirmBtn) confirmBtn.addEventListener('click', window.closeShopeeActivityPopup);

            // Tutup dengan klik backdrop overlay
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    window.closeShopeeActivityPopup();
                }
            });

            // Tutup dengan tombol ESC
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('active')) {
                    window.closeShopeeActivityPopup();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initShopeePopup);
        } else {
            initShopeePopup();
        }
    </script>
@endif
