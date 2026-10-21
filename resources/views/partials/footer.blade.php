<footer class="site-footer">
    {{-- Top Wave Transition --}}
    <div class="footer-top-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 28" fill="none" preserveAspectRatio="none">
            <path d="M0,0 C360,20 720,28 1080,12 C1260,4 1380,14 1440,20 L1440,28 L0,28 Z" fill="#0d3b38" opacity="0.6"/>
            <path d="M0,12 C280,24 640,6 980,20 C1200,28 1360,8 1440,16 L1440,28 L0,28 Z" fill="#0e4f4b" opacity="0.8"/>
        </svg>
    </div>

    <div class="footer-main">
        {{-- Kolom 1: Branding --}}
        <div class="footer-col-brand">
            <div class="footer-logo-card">
                <img src="{{ asset('images/logo-tiga-serangkai.png') }}" alt="Logo Perpustakaan Tiga Serangkai" class="footer-logo-img">
            </div>
            <div class="footer-brand-sep" aria-hidden="true"></div>
            <div class="footer-brand-info">
                <h3 class="footer-brand-title">
                    PERPUSTAKAAN<br>TIGA SERANGKAI
                </h3>
                <p class="footer-brand-tagline">
                    Dedikasi dan Profesionalisme untuk Kemajuan Bersama.
                </p>
            </div>
        </div>

        {{-- Divider --}}
        <div class="footer-divider" aria-hidden="true"></div>

        {{-- Kolom 2: Jam Operasional --}}
        <div class="footer-col-item">
            <div class="footer-badge-icon" aria-hidden="true">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="footer-col-body">
                <h4 class="footer-col-heading">JAM OPERASIONAL</h4>
                <div class="footer-schedule-row">
                    <span class="sched-label">Senin - Jumat</span>
                    <span class="sched-colon">:</span>
                    <span class="sched-value">07:30 - 16:30</span>
                </div>
                <div class="footer-schedule-row">
                    <span class="sched-label">Sabtu - Minggu</span>
                    <span class="sched-colon">:</span>
                    <span class="sched-value">Libur</span>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="footer-divider" aria-hidden="true"></div>

        {{-- Kolom 3: Alamat --}}
        <div class="footer-col-item">
            <div class="footer-badge-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                </svg>
            </div>
            <div class="footer-col-body">
                <h4 class="footer-col-heading">ALAMAT</h4>
                <p class="footer-address-text">
                    Jl. Prof. DR. Supomo No. 23, Sriwedari,<br>
                    Kec. Laweyan, Kota Surakarta,<br>
                    Jawa Tengah 57141
                </p>
            </div>
        </div>

        {{-- Divider --}}
        <div class="footer-divider" aria-hidden="true"></div>

        {{-- Kolom 4: Kontak --}}
        <div class="footer-col-item">
            <div class="footer-badge-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
            <div class="footer-col-body">
                <h4 class="footer-col-heading">KONTAK</h4>
                <div class="footer-contact-list">
                    <a href="mailto:perpustakaan@gmail.com" class="footer-contact-line">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>perpustakaan@gmail.com</span>
                    </a>
                    <a href="https://instagram.com/perpustakaantigaserangkai" target="_blank" rel="noopener" class="footer-contact-line">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                        <span>@perpustakaantigaserangkai</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Bar & Copyright --}}
    <div class="footer-bottom-wrap">
        <div class="footer-copyright">
            &copy; {{ date('Y') }} Perpustakaan Tiga Serangkai. All rights reserved.
        </div>

        {{-- Leaf decoration watermark --}}
        <div class="footer-leaf-decor" aria-hidden="true">
            <svg viewBox="0 0 80 80" fill="none">
                <path d="M 10,75 C 22,55 35,42 62,18" stroke="#5eead4" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M 62,18 C 76,8 80,0 80,0 C 80,0 68,14 58,26 C 48,38 52,28 62,18 Z" fill="#5eead4"/>
                <path d="M 35,42 C 22,34 8,32 8,32 C 8,32 20,46 30,50 C 40,54 38,44 35,42 Z" fill="#5eead4"/>
            </svg>
        </div>
    </div>
</footer>