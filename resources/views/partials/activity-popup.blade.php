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

        <div class="shopee-popup-wrapper">

            {{-- Tombol X mengambang di LUAR card, pojok kanan atas --}}
            <button type="button" class="shopee-popup-close" id="shopeePopupCloseBtn" aria-label="Tutup Pengumuman" title="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            {{-- Main Popup Card --}}
            <div class="shopee-popup-card">

                {{-- HEADER: Gradient teal dengan megafon + divider + judul + subtitle --}}
                <div class="shopee-popup-header">
                    {{-- Dekorasi cahaya / sheen --}}
                    <div class="shopee-header-sheen" aria-hidden="true"></div>
                    <div class="shopee-header-sheen-2" aria-hidden="true"></div>

                    <div class="shopee-header-brand">
                        {{-- Megafon ilustrasi besar dengan ray kuning --}}
                        <div class="shopee-megaphone-wrap" aria-hidden="true">
                            {{-- Ray/sinar kuning di belakang --}}
                            <div class="shopee-mega-rays">
                                <span class="ray r1"></span>
                                <span class="ray r2"></span>
                                <span class="ray r3"></span>
                                <span class="ray r4"></span>
                            </div>
                            {{-- SVG Megafon ilustrasi --}}
                            <svg width="58" height="58" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                {{-- Badan megafon --}}
                                <path d="M48 10 L18 24 L10 24 C7.8 24 6 25.8 6 28 L6 36 C6 38.2 7.8 40 10 40 L18 40 L48 54 C50.4 55.1 53 53.3 53 50.7 L53 13.3 C53 10.7 50.4 8.9 48 10Z"
                                    fill="white" fill-opacity="0.95"/>
                                {{-- Lubang suara / bell --}}
                                <path d="M54 22 C57 24.5 59 27.5 59 32 C59 36.5 57 39.5 54 42"
                                    stroke="white" stroke-width="3.5" stroke-linecap="round" fill="none" opacity="0.9"/>
                                <path d="M56.5 17 C62 21 66 26.2 66 32 C66 37.8 62 43 56.5 47"
                                    stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none" opacity="0.5"/>
                                {{-- Handle bawah megafon --}}
                                <path d="M18 40 L21 52 C21.6 54.2 23.9 55.5 26.1 54.9 C28.3 54.3 29.6 52 29 49.8 L26.5 40 Z"
                                    fill="white" fill-opacity="0.75"/>
                                {{-- Sorotan kuning di ujung corong --}}
                                <circle cx="11" cy="32" r="3.5" fill="#FFD700" opacity="0.9"/>
                                <ellipse cx="31" cy="28" rx="6" ry="3" fill="#FFE135" opacity="0.35" transform="rotate(-15 31 28)"/>
                            </svg>
                        </div>

                        {{-- Garis pemisah vertikal putih --}}
                        <div class="shopee-header-divider" aria-hidden="true"></div>

                        {{-- Judul + Subtitle --}}
                        <div class="shopee-header-text">
                            <div class="shopee-header-title" id="shopeeBadgeText">
                                {!! $latestPopupActivity->pinned_at ? 'Pengumuman<br>Disematkan' : 'Pengumuman<br>Resmi' !!}
                            </div>
                            <div class="shopee-header-subtitle">Informasi terbaru dari perpustakaan</div>
                        </div>
                    </div>

                    {{-- Wave bottom melengkung --}}
                    <svg class="shopee-header-wave" viewBox="0 0 420 44" preserveAspectRatio="none" aria-hidden="true">
                        <path class="shopee-wave-back" d="M0,16 C80,42 180,0 310,20 C365,28 400,18 420,20 L420,44 L0,44 Z"></path>
                        <path class="shopee-wave-front" d="M0,26 C100,46 220,8 360,28 C385,33 408,26 420,28 L420,44 L0,44 Z"></path>
                    </svg>
                </div>

                {{-- BODY: putih, padding 24px --}}
                <div class="shopee-popup-body">

                    {{-- Kartu Quote: bg mint, tanda ", teks kiri, ilustrasi buku kanan --}}
                    <div class="shopee-desc-card">
                        <div class="shopee-desc-left">
                            <span class="shopee-quote-mark" aria-hidden="true">&ldquo;&ldquo;</span>
                            <div class="shopee-desc-box" id="shopeeActivityDesc">
                                {!! nl2br(e($latestPopupActivity->description ?? 'Tidak ada rincian tambahan untuk aktivitas ini.')) !!}
                            </div>
                        </div>
                        <div class="shopee-book-illo" aria-hidden="true">
                            {{-- Blob mint di belakang buku --}}
                            <div class="shopee-book-blob"></div>
                            {{-- Ilustrasi tumpukan buku berwarna --}}
                            <svg class="shopee-book-svg" viewBox="0 0 110 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                {{-- Buku teal (paling bawah) --}}
                                <rect x="8" y="82" width="78" height="22" rx="4" fill="#0d9488"/>
                                <rect x="8" y="82" width="10" height="22" rx="3" fill="#0a7c72"/>
                                <rect x="18" y="84" width="2" height="18" rx="1" fill="rgba(255,255,255,0.3)"/>
                                {{-- Buku orange (tengah) --}}
                                <rect x="12" y="62" width="72" height="22" rx="4" fill="#f97316"/>
                                <rect x="12" y="62" width="10" height="22" rx="3" fill="#ea6a08"/>
                                <rect x="22" y="64" width="2" height="18" rx="1" fill="rgba(255,255,255,0.3)"/>
                                {{-- Buku biru (atas) --}}
                                <rect x="16" y="44" width="66" height="20" rx="4" fill="#3b82f6"/>
                                <rect x="16" y="44" width="9" height="20" rx="3" fill="#2563eb"/>
                                <rect x="25" y="46" width="2" height="16" rx="1" fill="rgba(255,255,255,0.3)"/>
                                {{-- Buku terbuka di atas (putih + sedikit shadow) --}}
                                <path d="M22 44 C22 44 44 36 55 36 C66 36 88 44 88 44" stroke="#e2e8f0" stroke-width="2" fill="none"/>
                                <path d="M22 44 L22 18 C22 16.9 22.7 16 24 15.5 C34 12 46 10 55 10 C64 10 76 12 86 15.5 C87.3 16 88 16.9 88 18 L88 44" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.5"/>
                                {{-- Halaman kiri buku --}}
                                <path d="M22 44 L22 18 C32 14 44 11.5 55 11.5 L55 44 Z" fill="#ffffff"/>
                                {{-- Halaman kanan buku --}}
                                <path d="M88 44 L88 18 C78 14 66 11.5 55 11.5 L55 44 Z" fill="#f1f5f9"/>
                                {{-- Garis teks di halaman kiri --}}
                                <line x1="30" y1="22" x2="52" y2="20" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="30" y1="26" x2="52" y2="24.5" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="30" y1="30" x2="52" y2="29" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="30" y1="34" x2="48" y2="33.5" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                {{-- Garis teks di halaman kanan --}}
                                <line x1="58" y1="20" x2="80" y2="22" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="58" y1="24.5" x2="80" y2="26" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="58" y1="29" x2="80" y2="30" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="58" y1="33.5" x2="76" y2="34" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                                {{-- Spine tengah buku --}}
                                <line x1="55" y1="44" x2="55" y2="10" stroke="#e2e8f0" stroke-width="1.5"/>
                                {{-- Sinar kuning di atas buku --}}
                                <line x1="55" y1="4" x2="55" y2="1" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>
                                <line x1="62" y1="6" x2="64" y2="3.5" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
                                <line x1="48" y1="6" x2="46" y2="3.5" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
                                <line x1="68" y1="10" x2="71" y2="8.5" stroke="#fde68a" stroke-width="1.5" stroke-linecap="round"/>
                                <line x1="42" y1="10" x2="39" y2="8.5" stroke="#fde68a" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Dots Pagination – selalu tampil minimal 3 dots --}}
                    <div class="shopee-carousel-nav" id="shopeeCarouselNav">
                        @php
                            $totalActivities = $popupActivities->count();
                            $displayDots = max($totalActivities, 3);
                        @endphp
                        @for ($di = 0; $di < $displayDots; $di++)
                            @if ($di < $totalActivities)
                                <button type="button" class="shopee-dot {{ $di === 0 ? 'active' : '' }}"
                                    data-index="{{ $di }}" aria-label="Lihat aktivitas {{ $di + 1 }}">
                                </button>
                            @else
                                <span class="shopee-dot shopee-dot-deco" aria-hidden="true"></span>
                            @endif
                        @endfor
                    </div>

                    {{-- Judul aktivitas (hidden, diupdate JS carousel) --}}
                    <span id="shopeeActivityTitle" style="display:none">{{ $latestPopupActivity->title }}</span>

                    {{-- Tombol CTA full-width --}}
                    <button type="button" class="shopee-btn-submit" id="shopeePopupConfirmBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Saya Mengerti</span>
                    </button>

                    {{-- Checkbox "Jangan tampilkan lagi hari ini" --}}
                    <label class="shopee-popup-footer" for="shopeeHideTodayCheckbox">
                        <input type="checkbox" id="shopeeHideTodayCheckbox">
                        <span>Jangan tampilkan lagi hari ini</span>
                    </label>

                </div>

            </div>{{-- /.shopee-popup-card --}}

        </div>{{-- /.shopee-popup-wrapper --}}

    </div>{{-- /#shopeeActivityPopupOverlay --}}

    {{-- Data aktivitas untuk carousel JS --}}
    <script>
        window.__SHOPEE_ACTIVITIES__ = {!! json_encode($encodedActivities) !!};

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

            try {
                sessionStorage.setItem('shopee_popup_shown_session_u' + userId, '1');
                localStorage.setItem('shopee_last_seen_act_u' + userId, String(latestId));
                sessionStorage.setItem('shopee_dismissed_session_u' + userId + '_a' + latestId, '1');
            } catch (e) {}

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

            const sessionShownKey = 'shopee_popup_shown_session_u' + userId;
            const alreadyShownThisSession = (function() {
                try { return sessionStorage.getItem(sessionShownKey) === '1'; } catch (e) { return false; }
            })();

            if (alreadyShownThisSession) return;

            const todayKey = 'shopee_hide_today_u' + userId + '_a' + latestId;
            const todayStr = new Date().toISOString().slice(0, 10);
            const hiddenToday = (function() {
                try { return localStorage.getItem(todayKey) === todayStr; } catch (e) { return false; }
            })();

            if (hiddenToday) return;

            const sessionDismissedKey = 'shopee_dismissed_session_u' + userId + '_a' + latestId;
            const dismissedThisSession = (function() {
                try { return sessionStorage.getItem(sessionDismissedKey) === '1'; } catch (e) { return false; }
            })();

            if (dismissedThisSession) return;

            const currentPath = window.location.pathname.toLowerCase();
            const isHomeOrDashboard = currentPath === '' ||
                currentPath === '/' ||
                currentPath.endsWith('/dashboard') ||
                currentPath.endsWith('/home');

            if (!isHomeOrDashboard) return;

            try {
                sessionStorage.setItem(sessionShownKey, '1');
            } catch (e) {}

            setTimeout(function () {
                window.openShopeeActivityPopup();
            }, 300);

            // Carousel Logic
            let currentIndex = 0;
            const titleEl = document.getElementById('shopeeActivityTitle');
            const descEl = document.getElementById('shopeeActivityDesc');
            const badgeTextEl = document.getElementById('shopeeBadgeText');
            const dots = document.querySelectorAll('.shopee-dot:not(.shopee-dot-deco)');

            function switchSlide(index) {
                if (!activities[index]) return;
                currentIndex = index;
                const act = activities[index];

                if (titleEl) titleEl.textContent = act.title;
                if (descEl) descEl.innerHTML = act.description.replace(/\n/g, '<br>');
                if (badgeTextEl) {
                    badgeTextEl.innerHTML = act.pinned
                        ? 'Pengumuman<br>Disematkan'
                        : 'Pengumuman<br>Resmi';
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

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    window.closeShopeeActivityPopup();
                }
            });

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
