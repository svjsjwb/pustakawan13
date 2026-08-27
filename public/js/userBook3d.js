document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('userBookModal');

    if (!modal) {
        return;
    }


    /* =====================================================
       ELEMENT
    ====================================================== */

    const closeButton =
        document.getElementById('userBookModalClose');

    const book =
        document.getElementById('book3d');

    const stage =
        document.getElementById('book3dStage');

    const cover =
        document.getElementById('book3dCover');

    const fallback =
        document.querySelector('.book-cover-fallback');

    const title =
        document.getElementById('modalBookTitle');

    const fallbackTitle =
        document.getElementById('book3dFallbackTitle');

    const backTitle =
        document.getElementById('book3dBackTitle');

    const category =
        document.getElementById('modalBookCategory');

    const author =
        document.getElementById('modalBookAuthor');

    const stock =
        document.getElementById('modalBookStock');

    const publisher =
        document.getElementById('modalBookPublisher');

    const year =
        document.getElementById('modalBookYear');

    const isbn =
        document.getElementById('modalBookIsbn');

    const description =
        document.getElementById('modalBookDescription');

    const resetButton =
        document.getElementById('book3dReset');


    /* =====================================================
       ROTATION
    ====================================================== */

    let rotateX = -8;
    let rotateY = -25;

    let scale = 1;

    let dragging = false;

    let startX = 0;
    let startY = 0;

    let startRotateX = 0;
    let startRotateY = 0;


    function updateBookTransform() {

        book.style.transform =
            `scale(${scale})
             rotateX(${rotateX}deg)
             rotateY(${rotateY}deg)`;
    }


    /* =====================================================
       RESET
    ====================================================== */

    function resetBook() {

        rotateX = -8;
        rotateY = -25;
        scale = 1;

        updateBookTransform();
    }


    if (resetButton) {
        resetButton.addEventListener(
            'click',
            function () {
                resetBook();
            }
        );
    }


    /* =====================================================
       MOUSE DRAG
    ====================================================== */

    stage.addEventListener(
        'mousedown',
        function (event) {

            dragging = true;

            startX = event.clientX;
            startY = event.clientY;

            startRotateX = rotateX;
            startRotateY = rotateY;

        }
    );


    window.addEventListener(
        'mousemove',
        function (event) {

            if (!dragging) {
                return;
            }

            const deltaX =
                event.clientX - startX;

            const deltaY =
                event.clientY - startY;


            rotateY =
                startRotateY + deltaX * 0.6;

            rotateX =
                startRotateX - deltaY * 0.45;


            /* batasi kemiringan vertikal */

            rotateX =
                Math.max(
                    -75,
                    Math.min(75, rotateX)
                );


            updateBookTransform();

        }
    );


    window.addEventListener(
        'mouseup',
        function () {

            dragging = false;

        }
    );


    /* =====================================================
       TOUCH DRAG
    ====================================================== */

    stage.addEventListener(
        'touchstart',
        function (event) {

            if (!event.touches.length) {
                return;
            }

            const touch =
                event.touches[0];

            dragging = true;

            startX = touch.clientX;
            startY = touch.clientY;

            startRotateX = rotateX;
            startRotateY = rotateY;

        },
        {
            passive: true
        }
    );


    stage.addEventListener(
        'touchmove',
        function (event) {

            if (!dragging || !event.touches.length) {
                return;
            }

            const touch =
                event.touches[0];

            const deltaX =
                touch.clientX - startX;

            const deltaY =
                touch.clientY - startY;


            rotateY =
                startRotateY + deltaX * 0.6;

            rotateX =
                startRotateX - deltaY * 0.45;


            rotateX =
                Math.max(
                    -75,
                    Math.min(75, rotateX)
                );


            updateBookTransform();

        },
        {
            passive: true
        }
    );


    stage.addEventListener(
        'touchend',
        function () {

            dragging = false;

        }
    );


    /* =====================================================
       ZOOM
    ====================================================== */

    stage.addEventListener(
        'wheel',
        function (event) {

            event.preventDefault();

            if (event.deltaY < 0) {

                scale += 0.08;

            } else {

                scale -= 0.08;

            }


            scale =
                Math.max(
                    0.65,
                    Math.min(1.6, scale)
                );


            updateBookTransform();

        },
        {
            passive: false
        }
    );


    /* =====================================================
       OPEN MODAL
       
       BOOK CARD HARUS MEMILIKI:
       
       data-title
       data-author
       data-category
       data-stock
       data-publisher
       data-year
       data-isbn
       data-description
       data-cover
    ====================================================== */

    document
        .querySelectorAll('.user-book-open')
        .forEach(function (card) {

            card.addEventListener(
                'click',
                function () {

                    const bookTitle =
                        card.dataset.title || 'Buku';

                    const bookAuthor =
                        card.dataset.author || '-';

                    const bookCategory =
                        card.dataset.category || 'BUKU';

                    const bookStock =
                        card.dataset.stock || '0';

                    const bookPublisher =
                        card.dataset.publisher || '-';

                    const bookYear =
                        card.dataset.year || '-';

                    const bookIsbn =
                        card.dataset.isbn || '-';

                    const bookDescription =
                        card.dataset.description ||
                        'Deskripsi buku belum tersedia.';

                    const bookCover =
                        card.dataset.cover || '';


                    /* =====================================
                       DETAIL
                    ====================================== */

                    title.textContent =
                        bookTitle;

                    author.textContent =
                        'Penulis ' + bookAuthor;

                    category.textContent =
                        bookCategory;

                    stock.textContent =
                        bookStock;

                    publisher.textContent =
                        bookPublisher;

                    year.textContent =
                        bookYear;

                    isbn.textContent =
                        bookIsbn;

                    description.textContent =
                        bookDescription;


                    /* =====================================
                       3D BOOK
                    ====================================== */

                    fallbackTitle.textContent =
                        bookTitle;

                    backTitle.textContent =
                        bookTitle;


                    resetBook();


                    if (bookCover) {

                        cover.src =
                            bookCover;

                        cover.alt =
                            bookTitle;

                        cover.classList.add(
                            'loaded'
                        );

                        fallback.style.display =
                            'none';


                        cover.onerror =
                            function () {

                                cover.classList.remove(
                                    'loaded'
                                );

                                fallback.style.display =
                                    'flex';

                            };

                    } else {

                        cover.classList.remove(
                            'loaded'
                        );

                        fallback.style.display =
                            'flex';

                    }


                    /* =====================================
                       OPEN
                    ====================================== */

                    modal.classList.add(
                        'open'
                    );

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
       CLOSE
    ====================================================== */

    function closeModal() {

        modal.classList.remove(
            'open'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow =
            '';

        dragging = false;

    }


    if (closeButton) {
        closeButton.addEventListener(
            'click',
            closeModal
        );
    }


    /* klik overlay */

    modal.addEventListener(
        'click',
        function (event) {

            if (
                event.target.classList.contains(
                    'user-book-modal-overlay'
                )
            ) {

                closeModal();

            }

        }
    );


    /* ESC */

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


    updateBookTransform();

});