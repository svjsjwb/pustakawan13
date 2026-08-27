document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       MOTION / PAGE ANIMATION
    ====================================================== */

    const motionItems = document.querySelectorAll(
        '.motion-section, .motion-fade-up, .motion-title, .motion-book, .motion-book-card'
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

    } else {

        motionItems.forEach(function (item) {

            item.classList.add('is-visible');

        });

    }



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

    const overlay =
        document.getElementById('userBookModalOverlay');

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

    }



    closeButton?.addEventListener(
        'click',
        closeModal
    );


    overlay?.addEventListener(
        'click',
        closeModal
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

    const searchInput =
        document.getElementById('user-book-search');


    searchInput?.addEventListener(
        'input',
        function () {

            const keyword =
                searchInput.value
                    .toLowerCase()
                    .trim();


            document
                .querySelectorAll('.user-book-card')
                .forEach(function (card) {

                    const title =
                        card.dataset.title || '';

                    const author =
                        card.dataset.author || '';

                    const category =
                        card.dataset.category || '';


                    const match =
                        title.includes(keyword) ||
                        author.includes(keyword) ||
                        category.includes(keyword);


                    card.classList.toggle(
                        'is-hidden',
                        !match
                    );

                });

        }
    );



    /* =====================================================
       BOOK CARD HOVER
       Sedikit efek tambahan supaya tidak terasa kaku.
    ====================================================== */

    document
        .querySelectorAll('.user-book-card')
        .forEach(function (card) {

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