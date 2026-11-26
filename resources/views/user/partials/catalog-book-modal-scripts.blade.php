<script>
let currentRotY = -25;
let currentRotX = 10;
let isDragging = false;
let startPointerX = 0;
let startPointerY = 0;
const bookObject = document.getElementById('book3dObject');
const bookViewport = document.getElementById('book3dViewport');
const bookShadow = document.getElementById('book3dShadow');

function update3dTransform() {
    if (bookObject) {
        bookObject.style.transform = `rotateY(${currentRotY}deg) rotateX(${currentRotX}deg)`;
    }
    if (bookShadow) {
        // Subtle shadow skew/scale based on rotation
        const skewAmount = Math.sin((currentRotY * Math.PI) / 180) * 12;
        bookShadow.style.transform = `translateZ(-20px) skewX(${skewAmount}deg)`;
    }
}

function reset3dBookRotation() {
    currentRotY = -25;
    currentRotX = 10;
    if (bookObject) {
        bookObject.style.transition = 'transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
        update3dTransform();
        setTimeout(() => {
            bookObject.style.transition = 'transform 0.04s ease-out';
        }, 400);
    }
}

if (bookViewport) {
    bookViewport.addEventListener('pointerdown', (e) => {
        isDragging = true;
        startPointerX = e.clientX;
        startPointerY = e.clientY;
        bookViewport.setPointerCapture(e.pointerId);
    });

    bookViewport.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        const deltaX = e.clientX - startPointerX;
        const deltaY = e.clientY - startPointerY;

        startPointerX = e.clientX;
        startPointerY = e.clientY;

        // Smooth horizontal 360 free rotation
        currentRotY += deltaX * 0.7;

        // Clamped vertical pitch (-45 to 45 deg) to avoid flip glitch
        currentRotX = Math.max(-45, Math.min(45, currentRotX - deltaY * 0.5));

        update3dTransform();
    });

    const endDrag = (e) => {
        if (isDragging) {
            isDragging = false;
            try { bookViewport.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    };
    bookViewport.addEventListener('pointerup', endDrag);
    bookViewport.addEventListener('pointercancel', endDrag);
}

// ── 4. Modal Detail Buku Logic ──────────────────────────────────────────────
let activeBookId = null;
let activeIsFav = false;
let modalPreviousBodyOverflow = null;
let modalPreviousBodyPaddingRight = null;

function openCatalogModal(el) {
    const d = el.dataset;
    activeBookId = d.id;
    activeIsFav = d.fav === '1';

    document.getElementById('modalBookTitle').textContent = d.title || '-';
    document.getElementById('modalBookAuthor').textContent = d.author || 'Penulis Anonim';
    document.getElementById('modalBookCategory').textContent = d.category || 'Umum';
    document.getElementById('modalBookPublisher').textContent = d.publisher || '-';
    document.getElementById('modalBookYear').textContent = d.year || '-';
    document.getElementById('modalBookIsbn').textContent = d.isbn || '-';
    if (document.getElementById('modalBookDdc')) {
        document.getElementById('modalBookDdc').textContent = d.ddc || '-';
    }
    document.getElementById('modalBookRak').textContent = d.rak || '-';
    document.getElementById('modalBookDescription').textContent = d.description || 'Deskripsi belum tersedia.';

    const stock = parseInt(d.stock) || 0;
    const stockEl = document.getElementById('modalBookStock');
    const reserveBtn = document.getElementById('modalReserveBtn');
    const reserveBookIdField = document.getElementById('modalReserveBookId');
    const borrowBtn = document.getElementById('modalBorrowBtn');
    const borrowBookIdField = document.getElementById('modalBorrowBookId');

    if (reserveBookIdField) reserveBookIdField.value = activeBookId;
    if (borrowBookIdField) borrowBookIdField.value = activeBookId;

    if (borrowBtn) {
        borrowBtn.disabled = false;
        borrowBtn.classList.remove('is-borrowed');
        borrowBtn.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>Pinjam Sekarang</span>
        `;
    }

    if (d.reserved === '1') {
        setReservationSuccess();
    } else if (stock > 0) {
        const feedback = document.getElementById('modalReserveFeedback');
        if (feedback) feedback.hidden = true;
        stockEl.textContent = stock + ' Eksemplar Tersedia';
        stockEl.style.color = 'var(--primary)';
        if (reserveBtn) {
            reserveBtn.disabled = false;
            reserveBtn.classList.remove('is-reserved');
            reserveBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Reservasi Sekarang</span>
            `;
        }
    } else {
        const feedback = document.getElementById('modalReserveFeedback');
        if (feedback) feedback.hidden = true;
        stockEl.textContent = 'Stok Buku Habis';
        stockEl.style.color = '#DC2626';
        if (reserveBtn) {
            reserveBtn.disabled = true;
            reserveBtn.classList.remove('is-reserved');
            reserveBtn.innerHTML = `<span>Stok Buku Habis</span>`;
        }
    }

    if (borrowBtn && d.borrowed === '1') {
        borrowBtn.disabled = true;
        borrowBtn.classList.add('is-borrowed');
        borrowBtn.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Sedang Dipinjam</span>
        `;
    } else if (borrowBtn && stock <= 0) {
        borrowBtn.disabled = true;
        borrowBtn.innerHTML = `<span>Tidak Tersedia</span>`;
    }

    // Populate 3D Book Cover, Spine & Blurb
    const coverImg = document.getElementById('modalBookCoverImg');
    const coverPh = document.getElementById('modalBookCoverPh');
    const spineText = document.getElementById('modalBookSpineText');
    const backBlurb = document.getElementById('modalBookBackBlurb');

    if (d.cover) {
        coverImg.src = d.cover;
        coverImg.style.display = 'block';
        coverPh.style.display = 'none';
    } else {
        coverImg.style.display = 'none';
        coverPh.style.display = 'flex';
        document.getElementById('modalBookCoverPhLetter').textContent = (d.title || 'B').substring(0, 1).toUpperCase();
        document.getElementById('modalBookCoverPhTitle').textContent = d.title || '';
    }

    if (spineText) spineText.textContent = (d.title || 'PUSTAKAWAN DIGITAL').toUpperCase();
    if (backBlurb) backBlurb.textContent = d.description || 'Buku koleksi digital resmi dengan standar katalogisasi terpadu.';

    reset3dBookRotation();
    updateModalFavButton(activeIsFav);

    const backdrop = document.getElementById('saasBookModalBackdrop');
    if (!backdrop?.classList.contains('active')) {
        modalPreviousBodyOverflow = document.body.style.overflow;
        modalPreviousBodyPaddingRight = document.body.style.paddingRight;
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        if (scrollbarWidth > 0) {
            const currentPadding = parseFloat(getComputedStyle(document.body).paddingRight) || 0;
            document.body.style.paddingRight = `${currentPadding + scrollbarWidth}px`;
        }
    }
    backdrop?.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function setReservationSuccess() {
    const reserveBtn = document.getElementById('modalReserveBtn');
    const feedback = document.getElementById('modalReserveFeedback');
    const card = document.querySelector(`.saas-book-card[data-id="${activeBookId}"]`);

    if (!reserveBtn) return;

    reserveBtn.disabled = true;
    reserveBtn.classList.add('is-reserved');
    reserveBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>Sudah Direservasi</span>
    `;
    if (feedback) feedback.hidden = false;
    if (card) card.dataset.reserved = '1';
}

function closeCatalogModal() {
    closeReservationDateModal();
    const backdrop = document.getElementById('saasBookModalBackdrop');
    const wasActive = backdrop?.classList.contains('active');
    backdrop?.classList.remove('active');
    if (wasActive) {
        document.body.style.overflow = modalPreviousBodyOverflow ?? '';
        document.body.style.paddingRight = modalPreviousBodyPaddingRight ?? '';
        modalPreviousBodyOverflow = null;
        modalPreviousBodyPaddingRight = null;
    }
}

function handleBackdropClick(e) {
    if (e.target === document.getElementById('saasBookModalBackdrop')) {
        closeCatalogModal();
    }
}

function triggerReservationModal(bookId, cardEl) {
    openCatalogModal(cardEl);
}

function updateModalFavButton(isFav) {
    const btn = document.getElementById('modalFavBtn');
    if (!btn) return;
    btn.classList.toggle('favorited', isFav);
    const svg = btn.querySelector('svg');
    if (svg) svg.setAttribute('fill', isFav ? 'currentColor' : 'none');
}

function toggleModalFavorite() {
    if (!activeBookId) return;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    const cardFavBtn = document.querySelector(`.saas-book-card[data-id="${activeBookId}"] .saas-card-fav-btn`);
    const favoriteCard = document.getElementById(`favCard${activeBookId}`);

    fetch('/user/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ book_id: activeBookId })
    })
    .then(res => res.json())
    .then(data => {
        activeIsFav = data.favorited;
        updateModalFavButton(activeIsFav);

        const card = document.querySelector(`.saas-book-card[data-id="${activeBookId}"]`);
        if (card) card.dataset.fav = activeIsFav ? '1' : '0';

        if (favoriteCard) {
            if (activeIsFav) {
                favoriteCard.querySelector('.ufav-card-open')?.setAttribute('data-fav', '1');
            } else {
                closeCatalogModal();
                if (typeof removeFavoriteCardFromList === 'function') {
                    removeFavoriteCardFromList(favoriteCard);
                }
            }
        }

        if (cardFavBtn) {
            cardFavBtn.classList.toggle('favorited', activeIsFav);
            const svg = cardFavBtn.querySelector('svg');
            if (svg) svg.setAttribute('fill', activeIsFav ? 'currentColor' : 'none');
        }

        if (window.showToast) {
            window.showToast(data.message, activeIsFav ? 'success' : 'info');
        }
    })
    .catch(() => {
        if (window.showToast) window.showToast('Gagal mengubah status favorit', 'error');
    });
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const resDateModal = document.getElementById('reservationDateModal');
        if (resDateModal && resDateModal.classList.contains('active')) {
            closeReservationDateModal();
        } else {
            closeCatalogModal();
        }
    }
});

/* ==========================================================================
   LOGIKA PILIH TANGGAL RESERVASI (OPSI 3 - QUICK PRESETS)
   ========================================================================== */
let selectedReservationDate = null;
let currentReservationPreset = 'today';

function toISODateString(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

function parseISODate(str) {
    if (!str) return new Date();
    const parts = str.split('-');
    return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
}

function formatIndoDate(d, withDayName = true) {
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const day = d.getDate();
    const month = monthNames[d.getMonth()];
    const year = d.getFullYear();
    if (withDayName) {
        return `${dayNames[d.getDay()]}, ${day} ${month} ${year}`;
    }
    return `${day} ${month} ${year}`;
}

function updateReservationDeadlineDisplay() {
    // Deadline = hari ini + 3 hari (dari waktu pengajuan, bukan dari tanggal pilihan user)
    const today = new Date();
    const deadlineObj = new Date(today);
    deadlineObj.setDate(deadlineObj.getDate() + 3);

    const deadlineFormatted = formatIndoDate(deadlineObj, true);
    const deadlineEl = document.getElementById('resDeadlineFormatted');
    if (deadlineEl) {
        deadlineEl.textContent = deadlineFormatted;
    }
}

function openReservationDateModal() {
    if (!activeBookId) return;
    const reserveBtn = document.getElementById('modalReserveBtn');
    if (!reserveBtn || reserveBtn.disabled) return;

    const bookTitleEl = document.getElementById('modalBookTitle');
    const resBookTitle = document.getElementById('resModalBookTitle');
    if (resBookTitle && bookTitleEl) {
        resBookTitle.textContent = bookTitleEl.textContent.trim();
    }

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    const todayLabel = document.getElementById('presetTodayLabel');
    const tomorrowLabel = document.getElementById('presetTomorrowLabel');
    if (todayLabel) todayLabel.textContent = formatIndoDate(today, false);
    if (tomorrowLabel) tomorrowLabel.textContent = formatIndoDate(tomorrow, false);

    calendarViewDate = new Date();
    selectReservationPreset('today');

    const dateModal = document.getElementById('reservationDateModal');
    if (dateModal) {
        dateModal.classList.add('active');
    }
}

function closeReservationDateModal() {
    toggleCalendarPopover(false);
    const dateModal = document.getElementById('reservationDateModal');
    if (dateModal) {
        dateModal.classList.remove('active');
    }
}

function handleResDateBackdropClick(event) {
    if (event.target === document.getElementById('reservationDateModal')) {
        closeReservationDateModal();
    }
}

/* ==========================================================================
   INTERACTIVE MODERN FLOATING CALENDAR POPOVER LOGIC
   ========================================================================== */
let calendarViewDate = new Date();

function toggleCalendarPopover(show) {
    const popover = document.getElementById('resCalendarPopover');
    if (!popover) return;
    const isVisible = popover.style.display === 'block';
    const targetState = typeof show === 'boolean' ? show : !isVisible;
    popover.style.display = targetState ? 'block' : 'none';
    if (targetState) {
        renderModernCalendar();
    }
}

function renderModernCalendar() {
    const grid = document.getElementById('resCalDaysGrid');
    const titleEl = document.getElementById('resCalMonthYearTitle');
    const prevBtn = document.getElementById('resCalPrevBtn');
    if (!grid || !titleEl) return;

    const indonesianMonths = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const currentYear = calendarViewDate.getFullYear();
    const currentMonth = calendarViewDate.getMonth();

    titleEl.textContent = `${indonesianMonths[currentMonth]} ${currentYear}`;

    // Cek tombol bulan sebelumnya (disable jika di bulan dan tahun berjalan)
    const today = new Date();
    const isCurrentOrPastMonth = (currentYear < today.getFullYear()) ||
        (currentYear === today.getFullYear() && currentMonth <= today.getMonth());
    if (prevBtn) {
        prevBtn.disabled = isCurrentOrPastMonth;
    }

    const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay(); // 0: Min, 1: Sen, ...
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

    const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
    const selectedIso = selectedReservationDate || toISODateString(today);

    grid.innerHTML = '';

    // Slot kosong sebelum tanggal 1
    for (let i = 0; i < firstDayIndex; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'res-cal-day empty';
        grid.appendChild(emptyCell);
    }

    // Hari 1 s.d. akhir bulan
    for (let d = 1; d <= daysInMonth; d++) {
        const dayDate = new Date(currentYear, currentMonth, d);
        const dayIso = toISODateString(dayDate);
        const isPast = dayDate.getTime() < todayMidnight;
        const isToday = dayDate.getTime() === todayMidnight;
        const isSelected = dayIso === selectedIso;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'res-cal-day';
        if (isPast) btn.classList.add('disabled');
        if (isToday) btn.classList.add('today');
        if (isSelected) btn.classList.add('selected');

        btn.textContent = d;

        if (isPast) {
            btn.disabled = true;
        } else {
            btn.onclick = function () {
                selectCalendarDate(dayIso);
            };
        }

        grid.appendChild(btn);
    }
}

function navigateCalendarMonth(delta) {
    calendarViewDate.setMonth(calendarViewDate.getMonth() + delta);
    renderModernCalendar();
}

function selectCalendarDate(isoString) {
    selectedReservationDate = isoString;
    const dateObj = parseISODate(isoString);

    const customLabel = document.getElementById('presetCustomLabel');
    if (customLabel) {
        customLabel.textContent = formatIndoDate(dateObj, false);
    }

    // Pastikan kalender sinkron dengan bulan tanggal yang dipilih
    calendarViewDate = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);

    updateReservationDeadlineDisplay();
    renderModernCalendar();

    // Tutup popover kalender otomatis setelah tanggal dipilih
    setTimeout(function() {
        toggleCalendarPopover(false);
    }, 150);
}

function selectReservationPreset(preset) {
    currentReservationPreset = preset;
    const today = new Date();

    document.getElementById('btnPresetToday')?.classList.toggle('active', preset === 'today');
    document.getElementById('btnPresetTomorrow')?.classList.toggle('active', preset === 'tomorrow');
    document.getElementById('btnPresetCustom')?.classList.toggle('active', preset === 'custom');

    if (preset === 'today') {
        toggleCalendarPopover(false);
        selectedReservationDate = toISODateString(today);
        calendarViewDate = new Date();
    } else if (preset === 'tomorrow') {
        toggleCalendarPopover(false);
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        selectedReservationDate = toISODateString(tomorrow);
        calendarViewDate = new Date(tomorrow);
    } else if (preset === 'custom') {
        const popover = document.getElementById('resCalendarPopover');
        const isAlreadyOpen = popover && popover.style.display === 'block';
        if (!selectedReservationDate) {
            selectedReservationDate = toISODateString(today);
        }
        calendarViewDate = parseISODate(selectedReservationDate);
        toggleCalendarPopover(!isAlreadyOpen);
    }

    updateReservationDeadlineDisplay();
}

// Tutup popover kalender jika klik di luar popover
document.addEventListener('click', function(e) {
    const popover = document.getElementById('resCalendarPopover');
    const customBtn = document.getElementById('btnPresetCustom');
    if (!popover || popover.style.display !== 'block') return;
    if (!popover.contains(e.target) && !customBtn?.contains(e.target)) {
        toggleCalendarPopover(false);
    }
});

async function submitReservationWithOptions() {
    if (!activeBookId) return;
    const submitBtn = document.getElementById('resConfirmSubmitBtn');
    if (!submitBtn || submitBtn.disabled) return;

    if (!selectedReservationDate) {
        selectedReservationDate = toISODateString(new Date());
    }

    submitBtn.disabled = true;
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = `
        <span style="display:inline-block; width:14px; height:14px; border:2px solid #fff; border-top-color:transparent; border-radius:50%; animation:resSpin 0.6s linear infinite; margin-right:6px;"></span>
        <span>Menyimpan...</span>
    `;

    try {
        const response = await fetch("{{ route('user.reservations.store') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                book_id: activeBookId,
                reserved_at: selectedReservationDate
            })
        });

        const data = await response.json();

        if (!response.ok) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
            window.showToast?.(data.message || 'Reservasi tidak dapat diproses.', 'error');
            return;
        }

        closeReservationDateModal();
        setReservationSuccess();
        window.showToast?.('Reservasi berhasil diajukan! Admin akan memproses dalam 3 hari.', 'success');

        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    } catch (error) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        window.showToast?.('Gagal menyimpan reservasi. Coba lagi.', 'error');
    }
}

document.getElementById('modalBorrowForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const borrowBtn = document.getElementById('modalBorrowBtn');
    if (!borrowBtn || borrowBtn.disabled) return;

    borrowBtn.disabled = true;

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ book_id: document.getElementById('modalBorrowBookId').value })
        });
        const data = await response.json();

        if (!response.ok) {
            borrowBtn.disabled = false;
            window.showToast?.(data.message || 'Peminjaman tidak dapat diproses.', 'error');
            return;
        }

        window.showToast?.('Buku berhasil dipinjam.', 'success');
        window.location.href = data.redirect_url;
    } catch (error) {
        borrowBtn.disabled = false;
        window.showToast?.('Gagal memproses peminjaman. Coba lagi.', 'error');
    }
});
</script>
