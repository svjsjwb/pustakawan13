<div class="saas-modal-backdrop" id="saasBookModalBackdrop" onclick="handleBackdropClick(event)">
    <div class="saas-modal-dialog" id="saasBookModalDialog" role="dialog" aria-modal="true">
        {{-- Favorite action; modal closes through Kembali, overlay, or Escape. --}}
        <button type="button" class="saas-btn-favorite-modal saas-modal-favorite-top" id="modalFavBtn" onclick="toggleModalFavorite()" title="Tambah / Hapus Favorit" aria-label="Tambah / Hapus Favorit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </button>

        <div class="saas-modal-layout">
            {{-- SISI KIRI: BUKU 3D SOLID 6 SISI 360° DRAG MOUSE --}}
            <div class="saas-modal-3d-stage">
                <div class="saas-3d-hint">
                    <span>🔄</span> Drag mouse untuk memutar 360°
                </div>

                {{-- 3D Book Viewport --}}
                <div class="saas-book-viewport" id="book3dViewport">
                    <div class="saas-book-object" id="book3dObject">
                        {{-- 1. Front Cover (Z = +18px) --}}
                        <div class="saas-face-front">
                            <img id="modalBookCoverImg" src="" alt="Book Cover">
                            <div id="modalBookCoverPh" class="saas-card-cover-fallback" style="display:none; height:100%;">
                                <span class="fallback-letter" id="modalBookCoverPhLetter">B</span>
                                <span class="fallback-title" id="modalBookCoverPhTitle"></span>
                            </div>
                        </div>

                        {{-- 2. Back Cover (Z = -18px) --}}
                        <div class="saas-face-back">
                            <div>
                                <div style="font-weight: 800; font-size: 11px; color: #a8d5d1; margin-bottom: 8px;">PERPUSTAKAAN DIGITAL</div>
                                <div class="blurb" id="modalBookBackBlurb">
                                    Buku koleksi digital resmi dengan standar indeksasi dan katalogisasi terpadu.
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 8.5px; color: #a8d5d1; opacity: 0.8;">OFFICIAL DIGITAL EDITION</div>
                                <div class="barcode-mock"></div>
                            </div>
                        </div>

                        {{-- 3. Spine (Left side, thickness 36px) --}}
                        <div class="saas-face-spine">
                            <span class="saas-spine-text" id="modalBookSpineText">PUSTAKAWAN DIGITAL</span>
                        </div>

                        {{-- 4. Pages Edge (Right side, thickness 36px) --}}
                        <div class="saas-face-pages-right"></div>

                        {{-- 5. Top Edge (Thickness 36px) --}}
                        <div class="saas-face-pages-top"></div>

                        {{-- 6. Bottom Edge (Thickness 36px) --}}
                        <div class="saas-face-pages-bottom"></div>
                    </div>

                    {{-- Contact Shadow Underneath --}}
                    <div class="saas-book-contact-shadow" id="book3dShadow"></div>
                </div>

                {{-- 3D Reset Button --}}
                <button type="button" class="saas-reset-3d-btn" onclick="reset3dBookRotation()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                    <span>Reset Sudut 3D</span>
                </button>
            </div>

            {{-- SISI KANAN: METADATA BUKU & TOMBOL AKSI --}}
            <div class="saas-modal-detail-pane">
                <span class="saas-modal-cat-tag" id="modalBookCategory">Kategori</span>
                <h2 class="saas-modal-book-title" id="modalBookTitle">Judul Buku</h2>
                <div class="saas-modal-book-author">
                    <span id="modalBookAuthor">Penulis Buku</span>
                </div>

                {{-- Meta Grid --}}
                <div class="saas-modal-meta-grid">
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Penerbit</span>
                        <span class="saas-modal-meta-val" id="modalBookPublisher">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Tahun Terbit</span>
                        <span class="saas-modal-meta-val" id="modalBookYear">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">ISBN</span>
                        <span class="saas-modal-meta-val" id="modalBookIsbn" style="font-family: monospace;">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Kode DDC</span>
                        <span class="saas-modal-meta-val" id="modalBookDdc" style="font-family: monospace;">-</span>
                    </div>
                    <div class="saas-modal-meta-item">
                        <span class="saas-modal-meta-label">Lokasi Rak</span>
                        <span class="saas-modal-meta-val" id="modalBookRak">-</span>
                    </div>
                    <div class="saas-modal-meta-item" style="grid-column: 1 / -1;">
                        <span class="saas-modal-meta-label">Ketersediaan Stok</span>
                        <span class="saas-modal-meta-val" id="modalBookStock" style="color: var(--primary); font-size: 15px;">-</span>
                    </div>
                </div>

                {{-- Sinopsis --}}
                <div class="saas-modal-synopsis">
                    <div class="saas-modal-synopsis-label">Sinopsis Buku</div>
                    <div class="saas-modal-synopsis-text" id="modalBookDescription">
                        Deskripsi lengkap buku akan ditampilkan di sini.
                    </div>
                </div>

                {{-- Action Buttons: Reservasi, Pinjam, Kembali --}}
                <div class="saas-modal-actions">
                    <form method="POST" action="{{ route('user.reservations.store') }}" id="modalReserveForm" onsubmit="event.preventDefault(); openReservationDateModal();">
                        @csrf
                        <input type="hidden" name="book_id" id="modalReserveBookId">
                        <button type="button" class="saas-btn-primary" id="modalReserveBtn" onclick="openReservationDateModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Reservasi Sekarang</span>
                        </button>
                        <div class="saas-reservation-feedback" id="modalReserveFeedback" hidden>
                            Buku telah masuk ke daftar reservasi Anda.
                            <a href="{{ route('user.reservations') }}">Lihat Reservasi</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('user.loans.store') }}" id="modalBorrowForm">
                        @csrf
                        <input type="hidden" name="book_id" id="modalBorrowBookId">
                        <button type="submit" class="saas-btn-primary saas-btn-borrow" id="modalBorrowBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>Pinjam Sekarang</span>
                        </button>
                    </form>

                    {{-- Tombol Kembali --}}
                    <button type="button" class="saas-btn-secondary" onclick="closeCatalogModal()">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Kembali ke Katalog</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================================
     6. MODAL KONFIRMASI RESERVASI & PILIH TANGGAL (OPSI 3 - QUICK PRESETS)
     ========================================================================== --}}
<div class="res-date-modal-overlay" id="reservationDateModal" onclick="handleResDateBackdropClick(event)">
    <div class="res-date-modal-card" role="dialog" aria-modal="true" aria-labelledby="resModalTitle">
        <div class="res-date-modal-header">
            <div class="res-date-modal-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="res-date-modal-header-text">
                <h3 id="resModalTitle">Pilih Tanggal Reservasi</h3>
                <p id="resModalBookTitle">Buku</p>
            </div>
            <button type="button" class="res-date-modal-close" onclick="closeReservationDateModal()" aria-label="Tutup">
                &times;
            </button>
        </div>

        <div class="res-date-modal-body">
            <span class="res-date-section-label">Pilih Rencana Waktu Pengambilan Buku:</span>

            {{-- 3 Quick Presets Grid with Floating Calendar Popover --}}
            <div class="res-presets-wrapper">
                <div class="res-presets-grid">
                    <button type="button" class="res-preset-btn active" id="btnPresetToday" onclick="selectReservationPreset('today')">
                        <span class="res-preset-badge">Cepat</span>
                        <span class="res-preset-title">Hari Ini</span>
                        <span class="res-preset-sub" id="presetTodayLabel">-</span>
                    </button>

                    <button type="button" class="res-preset-btn" id="btnPresetTomorrow" onclick="selectReservationPreset('tomorrow')">
                        <span class="res-preset-badge">Besok</span>
                        <span class="res-preset-title">Besok</span>
                        <span class="res-preset-sub" id="presetTomorrowLabel">-</span>
                    </button>

                    <button type="button" class="res-preset-btn" id="btnPresetCustom" onclick="selectReservationPreset('custom')">
                        <span class="res-preset-badge">Kustom</span>
                        <span class="res-preset-title">Pilih Tanggal</span>
                        <span class="res-preset-sub" id="presetCustomLabel">Kalender</span>
                    </button>
                </div>

                {{-- Modern Interactive Floating Calendar Popover (Tetap Popup) --}}
                <div class="res-cal-popover" id="resCalendarPopover" style="display: none;">
                    <div class="res-cal-popover-arrow"></div>
                    <div class="res-cal-header">
                        <button type="button" class="res-cal-nav-btn" id="resCalPrevBtn" onclick="navigateCalendarMonth(-1)" title="Bulan Sebelumnya" aria-label="Bulan Sebelumnya">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <div class="res-cal-title" id="resCalMonthYearTitle">November 2026</div>
                        <button type="button" class="res-cal-nav-btn" id="resCalNextBtn" onclick="navigateCalendarMonth(1)" title="Bulan Berikutnya" aria-label="Bulan Berikutnya">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>

                    <div class="res-cal-weekdays">
                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                    </div>

                    <div class="res-cal-days" id="resCalDaysGrid">
                        {{-- Injected dynamically --}}
                    </div>

                    <div class="res-cal-footer">
                        <button type="button" class="res-cal-today-shortcut" onclick="selectCalendarDate(toISODateString(new Date()))">
                            Hari Ini
                        </button>
                        <button type="button" class="res-cal-close-btn" onclick="toggleCalendarPopover(false)">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            {{-- Deadline Card (3 Hari) --}}
            <div class="res-deadline-box">
                <div class="res-deadline-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="res-deadline-info">
                    <div class="res-deadline-label">Batas Persetujuan Admin (3 Hari)</div>
                    <div class="res-deadline-date" id="resDeadlineFormatted">-</div>
                    <div class="res-deadline-note">
                        Reservasi akan menunggu persetujuan admin maksimal 3 hari sejak pengajuan. Jika admin tidak menyetujui dalam waktu tersebut, reservasi dibatalkan otomatis.
                    </div>
                </div>
            </div>
        </div>

        <div class="res-date-modal-footer">
            <button type="button" class="res-btn-secondary" onclick="closeReservationDateModal()">
                Kembali
            </button>
            <button type="button" class="res-btn-primary" id="resConfirmSubmitBtn" onclick="submitReservationWithOptions()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Ajukan Reservasi</span>
            </button>
        </div>
    </div>
</div>
