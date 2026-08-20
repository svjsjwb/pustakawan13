document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('book-modal');

    if (!modal) {
        return;
    }

    const modalClose =
        document.getElementById('modal-close');

    const modalAction =
        document.getElementById('modal-action');

    const modalTitle =
        document.getElementById('modal-title');

    const modalCoverTitle =
        document.getElementById('modal-cover-title');

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

    const modalCategoryDetail =
        document.getElementById('modal-category-detail');

    const modalAuthorDetail =
        document.getElementById('modal-author-detail');

    const modalStatusDetail =
        document.getElementById('modal-status-detail');


    /* =========================
       BUKA MODAL
    ========================= */

    document.querySelectorAll('.book-card').forEach(function (card) {

        card.addEventListener('click', function () {

            const title = card.dataset.title;
            const author = card.dataset.author;
            const category = card.dataset.category;
            const stock = card.dataset.stock;
            const status = card.dataset.status;


            modalTitle.textContent = title;

            modalCoverTitle.textContent = title;

            modalAuthor.textContent =
                'Penulis: ' + author;

            modalCategory.textContent =
                category;

            modalStock.textContent =
                stock;

            modalCategoryDetail.textContent =
                category;

            modalAuthorDetail.textContent =
                author;

            modalStatusDetail.textContent =
                status;


            /* STATUS */

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


            /* DESKRIPSI */

            modalDescription.textContent =
                'Informasi lengkap mengenai "' +
                title +
                '" belum tersedia dalam database.';


            /* BUKA */

            modal.classList.add('open');

            document.body.style.overflow = 'hidden';

        });

    });


    /* =========================
       TUTUP MODAL
    ========================= */

    function closeModal() {

        modal.classList.remove('open');

        document.body.style.overflow = '';

    }


    modalClose.addEventListener(
        'click',
        closeModal
    );


    modalAction.addEventListener(
        'click',
        closeModal
    );


    /* Klik luar modal */

    modal.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {

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

});