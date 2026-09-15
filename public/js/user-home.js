document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       POPULAR BOOKS ENTRANCE ANIMATION (STAGGERED LOAD)
    ====================================================== */

    let popularBooksAnimated = false;

    function triggerPopularBooksAnimation() {
        if (popularBooksAnimated) return;
        popularBooksAnimated = true;

        const inners = document.querySelectorAll('.user-popular-section .popular-card-inner');
        if (!inners.length) return;

        // Slight offset so the section title "Sedang Populer" begins first
        setTimeout(function () {
            inners.forEach(function (inner, index) {
                if (index < 5) {
                    setTimeout(function () {
                        inner.classList.add('is-visible');
                    }, index * 100);
                } else {
                    inner.classList.add('is-visible');
                }
            });
        }, 120);
    }

    /* =====================================================
       MOTION / PAGE ANIMATION
    ====================================================== */

    const motionItems = document.querySelectorAll(
        '.motion-section, .motion-fade-up, .motion-title, .motion-book, .user-section:not(.user-popular-section) .motion-book-card'
    );

    /*
     * Kalau browser mendukung IntersectionObserver,
     * elemen akan muncul ketika masuk viewport.
     *
     * Kalau tidak, langsung tampil.
     */

    if ('IntersectionObserver' in window) {

        const motionObserver = new IntersectionObserver(
            function (entries, observer) {

                entries.forEach(function (entry) {

                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');

                    if (
                        entry.target.closest('.user-popular-section') ||
                        entry.target.classList.contains('user-popular-section')
                    ) {
                        triggerPopularBooksAnimation();
                    }

                    observer.unobserve(entry.target);

                });

            },
            {
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px'
            }
        );


        motionItems.forEach(function (item) {

            motionObserver.observe(item);

        });

        const popularSection = document.querySelector('.user-popular-section');
        if (popularSection) {
            motionObserver.observe(popularSection);
        }

    } else {

        motionItems.forEach(function (item) {

            item.classList.add('is-visible');

        });

        triggerPopularBooksAnimation();

    }

    // Direct entrance check on load/refresh in case popular section is already in viewport
    function checkPopularEntrance() {
        const popularSection = document.querySelector('.user-popular-section');
        if (!popularSection) return;
        const rect = popularSection.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            const title = popularSection.querySelector('.motion-title');
            if (title) title.classList.add('is-visible');
            triggerPopularBooksAnimation();
        }
    }

    requestAnimationFrame(function () {
        checkPopularEntrance();
    });
    window.addEventListener('load', checkPopularEntrance, { once: true });



    /* =====================================================
       MODAL
    ====================================================== */

    const modal =
        document.getElementById('userBookModal');

    if (!modal) {
        return;
    }



    /* =====================================================
       ELEMENT
    ====================================================== */

    const closeButton =
        document.getElementById('userBookModalClose');

    const homeButton =
        document.getElementById('userBookHomeButton');

    const overlay =
        document.getElementById('userBookModalOverlay');

    const catalogButton =
        document.getElementById('userBookCatalogButton');

    const title =
        document.getElementById('modalBookTitle');

    const author =
        document.getElementById('modalBookAuthor');

    const category =
        document.getElementById('modalBookCategory');

    const publisher =
        document.getElementById('modalBookPublisher');

    const year =
        document.getElementById('modalBookYear');

    const isbn =
        document.getElementById('modalBookIsbn');

    let modalReturnFocus = null;

    const stock =
        document.getElementById('modalBookStock');

    const description =
        document.getElementById('modalBookDescription');



    /* =====================================================
       3D ELEMENT
    ====================================================== */

    const book3d =
        document.getElementById('book3d');

    const book3dStage =
        document.getElementById('book3dStage');

    const book3dCover =
        document.getElementById('book3dCover');

    const book3dFallback =
        document.getElementById('book3dFallback');

    const book3dFallbackTitle =
        document.getElementById('book3dFallbackTitle');

    const book3dBackTitle =
        document.getElementById('book3dBackTitle');

    const book3dReset =
        document.getElementById('book3dReset');



    /* =====================================================
       ROTATION
    ====================================================== */

    let rotationX = -8;
    let rotationY = -25;

    let isDragging = false;

    let startX = 0;
    let startY = 0;



    function updateBookRotation() {

        if (!book3d) {
            return;
        }

        book3d.style.transform =
            `rotateX(${rotationX}deg) rotateY(${rotationY}deg)`;

    }



    /* =====================================================
       OPEN BOOK
    ====================================================== */

    document
        .querySelectorAll('.user-book-open')
        .forEach(function (book) {

            book.addEventListener(
                'click',
                function () {

                    modalReturnFocus = book;

                    /* ==============================
                       DETAIL
                    =============================== */

                    if (title) {

                        title.textContent =
                            book.dataset.title || '-';

                    }


                    if (author) {

                        author.textContent =
                            book.dataset.author || '-';

                    }


                    if (category) {

                        category.textContent =
                            book.dataset.category || '-';

                    }


                    if (publisher) {

                        publisher.textContent =
                            book.dataset.publisher || '-';

                    }


                    if (year) {

                        year.textContent =
                            book.dataset.year || '-';

                    }


                    if (isbn) {

                        isbn.textContent =
                            book.dataset.isbn || '-';

                    }


                    if (stock) {

                        stock.textContent =
                            book.dataset.stock || '0';

                    }


                    if (description) {

                        description.textContent =
                            book.dataset.description ||
                            'Deskripsi buku belum tersedia.';

                    }



                    /* ==============================
                       COVER
                    =============================== */

                    const coverUrl =
                        book.dataset.cover || '';

                    const bookTitle =
                        book.dataset.title || 'Buku';



                    if (book3dFallbackTitle) {

                        book3dFallbackTitle.textContent =
                            bookTitle;

                    }


                    if (book3dBackTitle) {

                        book3dBackTitle.textContent =
                            bookTitle;

                    }


                    if (book3dCover) {

                        book3dCover.classList.remove(
                            'loaded'
                        );

                    }



                    if (coverUrl && book3dCover) {

                        book3dCover.src =
                            coverUrl;


                        book3dCover.onload =
                            function () {

                                if (book3dFallback) {

                                    book3dFallback.style.display =
                                        'none';

                                }


                                book3dCover.classList.add(
                                    'loaded'
                                );

                            };


                        book3dCover.onerror =
                            function () {

                                book3dCover.classList.remove(
                                    'loaded'
                                );


                                if (book3dFallback) {

                                    book3dFallback.style.display =
                                        'flex';

                                }

                            };

                    } else {

                        if (book3dCover) {

                            book3dCover.removeAttribute(
                                'src'
                            );

                            book3dCover.classList.remove(
                                'loaded'
                            );

                        }


                        if (book3dFallback) {

                            book3dFallback.style.display =
                                'flex';

                        }

                    }



                    /* ==============================
                       RESET ROTATION
                    =============================== */

                    rotationX = -8;
                    rotationY = -25;

                    updateBookRotation();



                    /* ==============================
                       OPEN MODAL
                    =============================== */

                    modal.classList.add('open');

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.style.overflow =
                        'hidden';

                }
            );

        });



    /* =====================================================
       CLOSE MODAL
    ====================================================== */

    function closeModal() {

        modal.classList.remove('open');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow = '';

        isDragging = false;

        if (modalReturnFocus && document.contains(modalReturnFocus)) {
            modalReturnFocus.focus({ preventScroll: true });
        }

    }



    closeButton?.addEventListener(
        'click',
        closeModal
    );

    homeButton?.addEventListener(
        'click',
        closeModal
    );


    overlay?.addEventListener(
        'click',
        closeModal
    );

    const CATALOG_PATHS = ['/catalog', '/user/catalog'];

    catalogButton?.addEventListener(
        'click',
        function (event) {

            const catalogUrl =
                new URL(catalogButton.href, window.location.origin);

            const alreadyOnCatalog =
                CATALOG_PATHS.includes(window.location.pathname);

            if (alreadyOnCatalog) {
                event.preventDefault();
                closeModal();
            }

        }
    );



    /* =====================================================
       ESCAPE
    ====================================================== */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                modal.classList.contains('open')
            ) {

                closeModal();

            }

        }
    );



    /* =====================================================
       3D DRAG - POINTER DOWN
    ====================================================== */

    book3dStage?.addEventListener(
        'pointerdown',
        function (event) {

            isDragging = true;

            startX = event.clientX;
            startY = event.clientY;

            book3dStage.setPointerCapture(
                event.pointerId
            );

        }
    );



    /* =====================================================
       3D DRAG - POINTER MOVE
    ====================================================== */

    book3dStage?.addEventListener(
        'pointermove',
        function (event) {

            if (!isDragging) {
                return;
            }


            const deltaX =
                event.clientX - startX;

            const deltaY =
                event.clientY - startY;


            rotationY +=
                deltaX * 0.6;

            rotationX -=
                deltaY * 0.4;


            rotationX =
                Math.max(
                    -75,
                    Math.min(
                        75,
                        rotationX
                    )
                );


            startX =
                event.clientX;

            startY =
                event.clientY;


            updateBookRotation();

        }
    );



    /* =====================================================
       3D DRAG - POINTER UP
    ====================================================== */

    book3dStage?.addEventListener(
        'pointerup',
        function (event) {

            isDragging = false;

            try {

                book3dStage.releasePointerCapture(
                    event.pointerId
                );

            } catch (error) {

                // Tidak perlu melakukan apa-apa

            }

        }
    );



    /* =====================================================
       3D DRAG - POINTER CANCEL
    ====================================================== */

    book3dStage?.addEventListener(
        'pointercancel',
        function () {

            isDragging = false;

        }
    );



    /* =====================================================
       RESET 3D
    ====================================================== */

    book3dReset?.addEventListener(
        'click',
        function () {

            rotationX = -8;
            rotationY = -25;

            updateBookRotation();

        }
    );



    /* =====================================================
       SEARCH
    ====================================================== */

    const searchInput = document.getElementById('user-book-search');
    const searchResults = document.getElementById('user-search-results');
    const searchWrap = document.querySelector('.user-search-wrap');
    const userHome = document.querySelector('.user-home');
    let searchTimer;
    let isSearchHovered = false;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, function (character) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character];
        });
    }

    function renderSearchResults(books, keyword) {
        if (!searchResults) return;
        if (!books.length) {
            searchResults.innerHTML = keyword ? '<div class="user-search-empty">Buku tidak ditemukan</div>' : '';
            searchResults.classList.toggle('is-visible', Boolean(keyword));
            return;
        }
        searchResults.innerHTML = books.map(function (book) {
            const cover = book.cover
                ? `<img src="${escapeHtml(book.cover)}" alt="" loading="lazy">`
                : '<span class="user-search-cover-fallback">B</span>';
            return `<a class="user-search-result" href="${escapeHtml(book.url)}" role="option">${cover}<span><strong>${escapeHtml(book.title)}</strong><small>${escapeHtml(book.author)} · ${escapeHtml(book.category)}</small></span></a>`;
        }).join('');
        searchResults.classList.add('is-visible');
    }


    searchInput?.addEventListener('input', function () {
        const keyword = searchInput.value.trim();
        clearTimeout(searchTimer);
        if (!keyword) {
            renderSearchResults([], '');
            return;
        }
        searchTimer = setTimeout(function () {
            fetch(`/user/search?q=${encodeURIComponent(keyword)}`, { headers: { Accept: 'application/json' } })
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(data => renderSearchResults(data.books || [], keyword))
                .catch(() => renderSearchResults([], keyword));
        }, 220);
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.user-search-wrap')) {
            searchResults?.classList.remove('is-visible');
        }
    });

    searchWrap?.addEventListener('mouseenter', function () {
        isSearchHovered = true;
        userHome?.classList.add('search-is-active');
    });
    searchWrap?.addEventListener('mouseleave', function () {
        isSearchHovered = false;
        userHome?.classList.remove('search-is-active');
    });

    /* =========================================================
       HERO 3D COVERFLOW CAROUSEL
    ========================================================= */
    function initHeroCoverflow() {
        const wrap = document.getElementById('heroCoverflowWrap');
        const stage = document.getElementById('coverflowStage');
        const prevBtn = document.getElementById('coverflowPrev');
        const nextBtn = document.getElementById('coverflowNext');
        const pagination = document.getElementById('coverflowPagination');

        if (!wrap || !stage) return;

        const cards = Array.from(stage.querySelectorAll('.user-coverflow-card'));
        const total = cards.length;
        if (total === 0) return;

        let activeIndex = 0;
        let autoplayTimer = null;
        let isHovered = false;
        let isDragging = false;
        let startX = 0;
        let currentX = 0;
        let dragDistance = 0;
        const SWIPE_THRESHOLD = 35;
        const AUTOPLAY_DELAY = 3000;

        // Build pagination dots
        if (pagination) {
            pagination.innerHTML = '';
            for (let i = 0; i < total; i++) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'coverflow-dot' + (i === 0 ? ' is-active' : '');
                dot.setAttribute('aria-label', `Buku ${i + 1} dari ${total}`);
                dot.addEventListener('click', (e) => {
                    e.stopPropagation();
                    goTo(i);
                });
                pagination.appendChild(dot);
            }
        }

        const dots = pagination ? Array.from(pagination.querySelectorAll('.coverflow-dot')) : [];

        function updateCoverflow() {
            cards.forEach((card, index) => {
                let diff = (index - activeIndex) % total;
                if (diff > total / 2) diff -= total;
                if (diff < -total / 2) diff += total;

                card.classList.remove(
                    'is-active',
                    'is-left-1',
                    'is-right-1',
                    'is-left-2',
                    'is-right-2',
                    'is-hidden-left',
                    'is-hidden-right'
                );

                if (diff === 0) {
                    card.classList.add('is-active');
                    card.setAttribute('aria-hidden', 'false');
                } else if (diff === -1) {
                    card.classList.add('is-left-1');
                    card.setAttribute('aria-hidden', 'true');
                } else if (diff === 1) {
                    card.classList.add('is-right-1');
                    card.setAttribute('aria-hidden', 'true');
                } else if (diff === -2) {
                    card.classList.add('is-left-2');
                    card.setAttribute('aria-hidden', 'true');
                } else if (diff === 2) {
                    card.classList.add('is-right-2');
                    card.setAttribute('aria-hidden', 'true');
                } else if (diff < -2) {
                    card.classList.add('is-hidden-left');
                    card.setAttribute('aria-hidden', 'true');
                } else {
                    card.classList.add('is-hidden-right');
                    card.setAttribute('aria-hidden', 'true');
                }
            });

            dots.forEach((dot, idx) => {
                if (idx === activeIndex) {
                    dot.classList.add('is-active');
                } else {
                    dot.classList.remove('is-active');
                }
            });
        }

        function goTo(index) {
            activeIndex = ((index % total) + total) % total;
            updateCoverflow();
            resetAutoplay();
        }

        function goNext() {
            goTo(activeIndex + 1);
        }

        function goPrev() {
            goTo(activeIndex - 1);
        }

        function startAutoplay() {
            stopAutoplay();
            if (total > 1 && !isHovered && !isSearchHovered) {
                autoplayTimer = setInterval(() => {
                    goNext();
                }, AUTOPLAY_DELAY);
            }
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        function resetAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        // Card clicks: side card rotates, center card opens modal
        cards.forEach((card, index) => {
            card.addEventListener('click', (e) => {
                if (Math.abs(dragDistance) > 10) return;

                let diff = (index - activeIndex) % total;
                if (diff > total / 2) diff -= total;
                if (diff < -total / 2) diff += total;

                if (diff === 0) {
                    openCardModal(card);
                } else {
                    goTo(index);
                }
            });
        });

        function openCardModal(card) {
            const title = card.dataset.title;
            const matchingBtn = document.querySelector(`.user-book-open[data-title="${title}"]`);
            if (matchingBtn) {
                matchingBtn.click();
                return;
            }

            const modal = document.getElementById('userBookModal');
            if (!modal) return;

            const modalTitle = document.getElementById('modalBookTitle');
            const modalAuthor = document.getElementById('modalBookAuthor');
            const modalCategory = document.getElementById('modalBookCategory');
            const modalStock = document.getElementById('modalBookStock');
            const modalPublisher = document.getElementById('modalBookPublisher');
            const modalYear = document.getElementById('modalBookYear');
            const modalIsbn = document.getElementById('modalBookIsbn');
            const modalDescription = document.getElementById('modalBookDescription');
            const cover = document.getElementById('book3dCover');
            const fallback = document.querySelector('.book-cover-fallback');

            if (modalTitle) modalTitle.textContent = card.dataset.title || 'Buku';
            if (modalAuthor) modalAuthor.textContent = 'Penulis ' + (card.dataset.author || '-');
            if (modalCategory) modalCategory.textContent = card.dataset.category || 'BUKU';
            if (modalStock) modalStock.textContent = card.dataset.stock || '0';
            if (modalPublisher) modalPublisher.textContent = card.dataset.publisher || '-';
            if (modalYear) modalYear.textContent = card.dataset.year || '-';
            if (modalIsbn) modalIsbn.textContent = card.dataset.isbn || '-';
            if (modalDescription) modalDescription.textContent = card.dataset.description || 'Deskripsi buku belum tersedia.';

            if (cover && fallback) {
                if (card.dataset.cover) {
                    cover.src = card.dataset.cover;
                    cover.classList.add('loaded');
                    fallback.style.display = 'none';
                } else {
                    cover.classList.remove('loaded');
                    fallback.style.display = 'flex';
                }
            }

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        // Pointer Drag & Swipe
        const coverflowEl = document.getElementById('heroCoverflow');
        if (coverflowEl) {
            coverflowEl.addEventListener('pointerdown', (e) => {
                isDragging = true;
                startX = e.clientX;
                currentX = e.clientX;
                dragDistance = 0;
                coverflowEl.classList.add('is-dragging');
                stopAutoplay();
            });

            window.addEventListener('pointermove', (e) => {
                if (!isDragging) return;
                currentX = e.clientX;
                dragDistance = currentX - startX;
            });

            const onPointerEnd = () => {
                if (!isDragging) return;
                isDragging = false;
                coverflowEl.classList.remove('is-dragging');

                if (dragDistance < -SWIPE_THRESHOLD) {
                    goNext();
                } else if (dragDistance > SWIPE_THRESHOLD) {
                    goPrev();
                }

                setTimeout(() => {
                    dragDistance = 0;
                }, 60);

                if (!isHovered) startAutoplay();
            };

            window.addEventListener('pointerup', onPointerEnd);
            window.addEventListener('pointercancel', onPointerEnd);
        }

        // Controls
        prevBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            goPrev();
        });

        nextBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            goNext();
        });

        // Hover pause / resume
        wrap.addEventListener('mouseenter', () => {
            isHovered = true;
            stopAutoplay();
        });

        wrap.addEventListener('mouseleave', () => {
            isHovered = false;
            startAutoplay();
        });

        // Keyboard arrow navigation
        wrap.setAttribute('tabindex', '0');
        wrap.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                goPrev();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                goNext();
            }
        });

        updateCoverflow();
        startAutoplay();
    }

    initHeroCoverflow();

    const CAROUSEL_CONFIG = Object.freeze({
        autoplayInterval:  2500,   // Autoplay slide tiap 2.5 detik
        autoplayDuration:  850,    // Animasi autoplay: lambat, smooth & elegan (850ms)
        manualDuration:    420,    // Animasi klik/swipe: responsif & cepat (420ms)
    });

    /* =========================================================
       POPULAR BOOKS CAROUSEL — 100% Stable 3D Coverflow Refactor
    ========================================================= */
    function InfiniteBookCarousel(viewport) {
        if (viewport.dataset.carouselInitialized === 'true') return;
        viewport.dataset.carouselInitialized = 'true';

        const track = viewport.querySelector('[data-book-carousel-track]');
        if (!track) return;

        // 1. Simpan template master kartu dari HTML asli
        const masterCards = Array.from(track.children).map(c => {
            const clone = c;
            const inner = clone.querySelector('.popular-card-inner');
            if (inner) inner.classList.add('is-visible');
            clone.style.left = '';
            clone.style.width = '';
            clone.style.flexBasis = '';
            return clone;
        });

        const totalBooks = masterCards.length;
        if (totalBooks === 0) return;

        // ATURAN 1: SATU SOURCE OF TRUTH
        let currentIndex = 0;
        let isAnimating = false;
        let isHovered = false;
        let autoSlideTimer = null;
        let startX = null;
        let startY = null;

        // ATURAN 4: LOOP WAJIB MODULUS
        function mod(n, m) {
            return ((n % m) + m) % m;
        }

        // ATURAN 12: SELF HEALING
        function sanitizeIndex(idx) {
            if (typeof idx !== 'number' || isNaN(idx) || !isFinite(idx)) {
                return 0;
            }
            return mod(idx, totalBooks);
        }

        // Helper membuat elemen kartu baru untuk ranking buku tertentu
        function createCardElement(bookIndex) {
            const cleanIndex = mod(bookIndex, totalBooks);
            const card = masterCards[cleanIndex].cloneNode(true);
            const inner = card.querySelector('.popular-card-inner');
            if (inner) inner.classList.add('is-visible');

            card.style.left = '';
            card.style.width = '';
            card.style.flexBasis = '';

            // Setup click handler modal buku
            const origOpenBtn = masterCards[cleanIndex].querySelector('.user-book-open');
            const cardOpenBtn = card.querySelector('.user-book-open');
            if (cardOpenBtn && origOpenBtn) {
                cardOpenBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    origOpenBtn.click();
                    modalReturnFocus = cardOpenBtn;
                });
            }
            card.addEventListener('click', function (e) {
                if (card.classList.contains('popular-pos-center') || card.classList.contains('book-carousel-active')) {
                    modalReturnFocus = cardOpenBtn || card;
                    origOpenBtn?.click();
                }
            });

            return card;
        }

        // ATURAN 2: RENDER HANYA 5 CARD PADA AWAL
        function initCards() {
            track.innerHTML = '';
            currentIndex = sanitizeIndex(currentIndex);

            const initialSlots = [
                { posClass: 'popular-pos-left-2 book-carousel-slot-0', offset: -2 },
                { posClass: 'popular-pos-left-1 book-carousel-slot-1 book-carousel-previous', offset: -1 },
                { posClass: 'popular-pos-center book-carousel-slot-2 book-carousel-active', offset: 0 },
                { posClass: 'popular-pos-right-1 book-carousel-slot-3 book-carousel-next', offset: 1 },
                { posClass: 'popular-pos-right-2 book-carousel-slot-4', offset: 2 }
            ];

            initialSlots.forEach(({ posClass, offset }) => {
                const bookIdx = mod(currentIndex + offset, totalBooks);
                const card = createCardElement(bookIdx);
                card.className = `user-book-card motion-book-card popular-book-card ${posClass} no-transition`;
                track.appendChild(card);
            });

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    viewport.classList.add('is-ready');
                    Array.from(track.children).forEach(c => c.classList.remove('no-transition'));
                });
            });
        }

        // ATURAN 7: AUTOPLAY DAN CLICK MEMAKAI SATU FUNGSI YANG SAMA
        function moveCarousel(direction, isManual = viewport.classList.contains('is-fast')) {
            if (isAnimating) return;
            isAnimating = true;

            currentIndex = sanitizeIndex(currentIndex);

            direction = direction > 0 ? 1 : -1;
            const duration = isManual ? CAROUSEL_CONFIG.manualDuration : CAROUSEL_CONFIG.autoplayDuration;

            const cardsInTrack = Array.from(track.children);
            if (cardsInTrack.length < 5) {
                initCards();
                isAnimating = false;
                return;
            }

            // ATURAN 3: JANGAN TELEPORTASI. CARD LAMA DIHAPUS, CARD BARU DIBUAT.
            if (direction > 0) {
                // NEXT: Berpindah 1 langkah ke depan
                const newIndex = mod(currentIndex + 1, totalBooks);
                const incomingBookIdx = mod(newIndex + 2, totalBooks);
                const incomingCard = createCardElement(incomingBookIdx);

                // Masuk dari buffer kanan (popular-pos-entry-right)
                incomingCard.className = 'user-book-card motion-book-card popular-book-card popular-pos-entry-right book-carousel-slot-entry no-transition';
                track.appendChild(incomingCard);
                void incomingCard.offsetWidth; // Force reflow agar posisi awal tercatat
                incomingCard.classList.remove('no-transition');

                currentIndex = newIndex;

                // ATURAN 8: GUNAKAN REQUESTANIMATIONFRAME
                requestAnimationFrame(() => {
                    const [cLeft2, cLeft1, cCenter, cRight1, cRight2] = cardsInTrack;

                    cLeft2.className = 'user-book-card motion-book-card popular-book-card popular-pos-exit-left book-carousel-slot-exit';
                    cLeft1.className = 'user-book-card motion-book-card popular-book-card popular-pos-left-2 book-carousel-slot-0';
                    cCenter.className = 'user-book-card motion-book-card popular-book-card popular-pos-left-1 book-carousel-slot-1 book-carousel-previous';
                    cRight1.className = 'user-book-card motion-book-card popular-book-card popular-pos-center book-carousel-slot-2 book-carousel-active';
                    cRight2.className = 'user-book-card motion-book-card popular-book-card popular-pos-right-1 book-carousel-slot-3 book-carousel-next';
                    incomingCard.className = 'user-book-card motion-book-card popular-book-card popular-pos-right-2 book-carousel-slot-4';

                    // ATURAN 13: DEBUG MODE
                    console.log('PopularCarousel:', {
                        currentIndex,
                        visibleCards: [
                            mod(currentIndex - 2, totalBooks) + 1,
                            mod(currentIndex - 1, totalBooks) + 1,
                            mod(currentIndex, totalBooks) + 1,
                            mod(currentIndex + 1, totalBooks) + 1,
                            mod(currentIndex + 2, totalBooks) + 1
                        ]
                    });

                    setTimeout(() => {
                        cLeft2.remove(); // Hapus kartu yang sudah meluncur keluar layar
                        isAnimating = false;
                        viewport.classList.remove('is-fast');
                    }, duration);
                });

            } else {
                // PREV: Berpindah 1 langkah ke belakang
                const newIndex = mod(currentIndex - 1, totalBooks);
                const incomingBookIdx = mod(newIndex - 2, totalBooks);
                const incomingCard = createCardElement(incomingBookIdx);

                // Masuk dari buffer kiri (popular-pos-exit-left)
                incomingCard.className = 'user-book-card motion-book-card popular-book-card popular-pos-exit-left book-carousel-slot-exit no-transition';
                track.insertBefore(incomingCard, track.firstChild);
                void incomingCard.offsetWidth; // Force reflow
                incomingCard.classList.remove('no-transition');

                currentIndex = newIndex;

                // ATURAN 8: GUNAKAN REQUESTANIMATIONFRAME
                requestAnimationFrame(() => {
                    const [cLeft2, cLeft1, cCenter, cRight1, cRight2] = cardsInTrack;

                    cRight2.className = 'user-book-card motion-book-card popular-book-card popular-pos-entry-right book-carousel-slot-entry';
                    cRight1.className = 'user-book-card motion-book-card popular-book-card popular-pos-right-2 book-carousel-slot-4';
                    cCenter.className = 'user-book-card motion-book-card popular-book-card popular-pos-right-1 book-carousel-slot-3 book-carousel-next';
                    cLeft1.className = 'user-book-card motion-book-card popular-book-card popular-pos-center book-carousel-slot-2 book-carousel-active';
                    cLeft2.className = 'user-book-card motion-book-card popular-book-card popular-pos-left-1 book-carousel-slot-1 book-carousel-previous';
                    incomingCard.className = 'user-book-card motion-book-card popular-book-card popular-pos-left-2 book-carousel-slot-0';

                    // ATURAN 13: DEBUG MODE
                    console.log('PopularCarousel:', {
                        currentIndex,
                        visibleCards: [
                            mod(currentIndex - 2, totalBooks) + 1,
                            mod(currentIndex - 1, totalBooks) + 1,
                            mod(currentIndex, totalBooks) + 1,
                            mod(currentIndex + 1, totalBooks) + 1,
                            mod(currentIndex + 2, totalBooks) + 1
                        ]
                    });

                    setTimeout(() => {
                        cRight2.remove(); // Hapus kartu yang sudah meluncur keluar layar
                        isAnimating = false;
                        viewport.classList.remove('is-fast');
                    }, duration);
                });
            }
        }

        // ATURAN 5: SATU TIMER AUTOPLAY
        function startAutoplay() {
            if (autoSlideTimer) return;

            autoSlideTimer = setInterval(() => {
                // Timer tetap hidup; langkah berikutnya menunggu queue bila sedang animasi.
                if (!isAnimating && viewport.classList.contains('is-ready')) {
                    viewport.classList.remove('is-fast');
                    moveCarousel(1, false);
                }
            }, CAROUSEL_CONFIG.autoplayInterval);
        }

        // PERBAIKAN HOVER: Hanya pause autoplay, tanpa reposition, tanpa mengubah index
        viewport.addEventListener('mouseenter', () => {
            isHovered = true;
        });

        viewport.addEventListener('mouseleave', () => {
            isHovered = false;
        });

        // Gesture Drag & Touch (Swipe)
        viewport.addEventListener('pointerdown', (e) => {
            if (isAnimating) return;
            startX = e.clientX;
            startY = e.clientY;
        });

        viewport.addEventListener('pointerup', (e) => {
            if (startX === null) return;
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - (startY || e.clientY);
            startX = null;
            startY = null;
            if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
                viewport.classList.add('is-fast');
                moveCarousel(deltaX < 0 ? 1 : -1, true);
            }
        });

        viewport.addEventListener('pointercancel', () => {
            startX = null;
            startY = null;
        });

        // Inisialisasi awal
        initCards();
        startAutoplay();

        // Navigasi tombol eksternal (Next/Prev)
        viewport._carouselAdvance = function (dir) {
            viewport.classList.add('is-fast');
            requestAnimationFrame(() => moveCarousel(dir, true));
        };
    }

    document.querySelectorAll('[data-book-carousel]').forEach(function(vp) {
        InfiniteBookCarousel(vp);
    });

    /* =====================================================
       POPULAR CAROUSEL EXTERNAL NAV BUTTONS
    ====================================================== */

    (function() {
        const prevBtn = document.getElementById('popularCarouselPrev');
        const nextBtn = document.getElementById('popularCarouselNext');
        const viewport = document.querySelector('.user-popular-carousel-wrap .user-book-carousel-viewport');

        if (!prevBtn || !nextBtn || !viewport) return;
        if (viewport.dataset.carouselNavigationInitialized === 'true') return;
        viewport.dataset.carouselNavigationInitialized = 'true';

        prevBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (typeof viewport._carouselAdvance === 'function') {
                viewport._carouselAdvance(-1);
            }
        });

        nextBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (typeof viewport._carouselAdvance === 'function') {
                viewport._carouselAdvance(1);
            }
        });
    })();



    /* =====================================================
       BOOK CARD HOVER
       Sedikit efek tambahan supaya tidak terasa kaku.
    ====================================================== */

    document
        .querySelectorAll('.user-book-card')
        .forEach(function (card) {

            card.addEventListener('click', function (event) {
                if (event.target.closest('.user-book-open')) return;
                card.querySelector('.user-book-open')?.click();
            });

            card.addEventListener(
                'mouseenter',
                function () {

                    if (
                        !card.classList.contains('is-hidden')
                    ) {

                        card.classList.add(
                            'is-hovered'
                        );

                    }

                }
            );


            card.addEventListener(
                'mouseleave',
                function () {

                    card.classList.remove(
                        'is-hovered'
                    );

                }
            );

        });



    /* =====================================================
       INITIAL 3D ROTATION
    ====================================================== */

    updateBookRotation();

});