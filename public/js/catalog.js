document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ANTI SELECT / ANTI DRAG
    |--------------------------------------------------------------------------
    */

    document.addEventListener('selectstart', function (e) {
        e.preventDefault();
    });

    document.addEventListener('dragstart', function (e) {
        e.preventDefault();
    });

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    const modal = document.getElementById('book-modal');

    if (!modal) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | BOOK 3D ROTATION
    |--------------------------------------------------------------------------
    */

    const book = document.getElementById('book3D');

    if (book) {

        let isDragging = false;

        let rotX = 0;
        let rotY = -15;

        const defaultX = 0;
        const defaultY = -15;

       book.style.transform =
    'rotateX(0deg) rotateY(-15deg)';

        book.addEventListener('mousedown', function () {

            isDragging = true;

            book.style.transition = 'none';

        });

        document.addEventListener('mouseup', function () {

            if (!isDragging) return;

            isDragging = false;

            rotX = defaultX;
            rotY = defaultY;

            book.style.transition =
                'transform .8s ease';

            book.style.transform =
                `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

        });

        document.addEventListener('mousemove', function (e) {

            if (!isDragging) return;

            rotY += e.movementX * 0.5;
            rotX -= e.movementY * 0.2;

            rotX = Math.max(
                -40,
                Math.min(40, rotX)
            );

            book.style.transform =
                `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

        });

    }

    /*
    |--------------------------------------------------------------------------
    | ELEMENT MODAL
    |--------------------------------------------------------------------------
    */

    const modalClose =
        document.getElementById('modal-close');

    const modalAction =
        document.getElementById('modal-action');

    const modalTitle =
        document.getElementById('modal-title');

    const modalBookTitle =
        document.getElementById('modalBookTitle');

    const modalAuthor =
        document.getElementById('modal-author');

    const modalCategory =
        document.getElementById('modal-category');

    const modalStatus =
        document.getElementById('modal-status');

    const modalDescription =
        document.getElementById('modal-description');

    const modalStock =
        document.getElementById('modal-stock');

    const modalPublisher =
        document.getElementById('modal-publisher');

    const modalYear =
        document.getElementById('modal-year');

    const modalCallNumber =
        document.getElementById('modal-call-number');

    const modalIsbn =
        document.getElementById('modal-isbn');

    const bookFront =
        document.getElementById('bookFront');

    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.book-card')
        .forEach(function (card) {

            card.addEventListener('click', function () {

                const title =
                    card.dataset.title || '-';

                const author =
                    card.dataset.author || '-';

                const category =
                    card.dataset.category || '-';

                const stock =
                    card.dataset.stock || '0';

                const status =
                    card.dataset.status || 'Tersedia';

                const description =
                    card.dataset.description ||
                    'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.';

                const cover =
                    card.dataset.cover || '';

                const publisher =
                    card.dataset.publisher || '-';

                const year =
                    card.dataset.year || '-';

                const callNumber =
                    card.dataset.callNumber || '-';

                const isbn =
                    card.dataset.isbn || '-';

                /*
                |--------------------------------------------------------------------------
                | ISI MODAL
                |--------------------------------------------------------------------------
                */

                if (modalTitle) {
                    modalTitle.textContent = title;
                }

                if (modalBookTitle) {
                    modalBookTitle.textContent = title;
                }

                if (modalAuthor) {
                    modalAuthor.textContent =
                        'Penulis: ' + author;
                }

                if (modalCategory) {
                    modalCategory.textContent = category;
                }

                if (modalStock) {
                    modalStock.textContent = stock;
                }

                if (modalPublisher) {
                    modalPublisher.textContent = publisher;
                }

                if (modalYear) {
                    modalYear.textContent = year;
                }

                if (modalCallNumber) {
                    modalCallNumber.textContent =
                        callNumber;
                }

                if (modalIsbn) {
                    modalIsbn.textContent = isbn;
                }

                if (modalDescription) {
                    modalDescription.textContent =
                        description;
                }

                /*
                |--------------------------------------------------------------------------
                | COVER BUKU 3D
                |--------------------------------------------------------------------------
                */

                if (cover) {

    bookFront.innerHTML = '';

    bookFront.style.backgroundImage =
        `url('${cover}')`;

    bookFront.style.backgroundSize =
        'cover';

    bookFront.style.backgroundPosition =
        'center';
}

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                if (modalStatus) {

                    modalStatus.textContent = status;

                    modalStatus.classList.remove(
                        'available',
                        'borrowed'
                    );

                    if (status === 'Tersedia') {

                        modalStatus.classList.add(
                            'available'
                        );

                    } else {

                        modalStatus.classList.add(
                            'borrowed'
                        );

                    }

                }

                /*
                |--------------------------------------------------------------------------
                | RESET POSISI BUKU
                |--------------------------------------------------------------------------
                */

                if (book) {

                    book.style.transition =
                        'transform .6s ease';

                    book.style.transform =
                        'rotateX(-8deg) rotateY(-30deg)';

                }

                /*
                |--------------------------------------------------------------------------
                | OPEN MODAL
                |--------------------------------------------------------------------------
                */

                modal.classList.add('open');

                document.body.style.overflow =
                    'hidden';

            });

        });

    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeModal() {

        modal.classList.remove('open');

        document.body.style.overflow = '';

    }

    /*
    |--------------------------------------------------------------------------
    | BUTTONS
    |--------------------------------------------------------------------------
    */

    if (modalClose) {
        modalClose.addEventListener(
            'click',
            closeModal
        );
    }

    if (modalAction) {
        modalAction.addEventListener(
            'click',
            closeModal
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CLICK OUTSIDE
    |--------------------------------------------------------------------------
    */

    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });

    /*
    |--------------------------------------------------------------------------
    | ESC KEY
    |--------------------------------------------------------------------------
    */

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

});