document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('book-modal');

    if (!modal) {
        return;
    }

    const modalClose = document.getElementById('modal-close');
    const modalAction = document.getElementById('modal-action');
    const modalTitle = document.getElementById('modal-title');
    const modalCoverTitle = document.getElementById('modal-cover-title');
    const modalCoverImage = document.getElementById('modal-cover-image');
    const modalBookBox = document.getElementById('modal-book-box');
    const modalAuthor = document.getElementById('modal-author');
    const modalCategory = document.getElementById('modal-category');
    const modalStatus = document.getElementById('modal-status');
    const modalDescription = document.getElementById('modal-description');
    const modalStock = document.getElementById('modal-stock');
    const modalPublisher = document.getElementById('modal-publisher');
    const modalYear = document.getElementById('modal-year');
    const modalCallNumber = document.getElementById('modal-call-number');
    const modalIsbn = document.getElementById('modal-isbn');


    /* =========================
       BUKA MODAL
    ========================= */

    document.querySelectorAll('.book-card').forEach(function (card) {

        card.addEventListener('click', function () {

            const title = card.dataset.title || '-';
            const author = card.dataset.author || '-';
            const category = card.dataset.category || '-';
            const stock = card.dataset.stock || '0';
            const status = card.dataset.status || 'Tersedia';
            const description = card.dataset.description || 'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.';
            const cover = card.dataset.cover || '';
            const publisher = card.dataset.publisher || '-';
            const year = card.dataset.year || '-';
            const callNumber = card.dataset.callNumber || '-';
            const isbn = card.dataset.isbn || '-';

            if (modalTitle) modalTitle.textContent = title;
            if (modalCoverTitle) modalCoverTitle.textContent = title;
            if (modalAuthor) modalAuthor.textContent = 'Penulis: ' + author;
            if (modalCategory) modalCategory.textContent = category;
            if (modalStock) modalStock.textContent = stock;
            if (modalPublisher) modalPublisher.textContent = publisher;
            if (modalYear) modalYear.textContent = year;
            if (modalCallNumber) modalCallNumber.textContent = callNumber;
            if (modalIsbn) modalIsbn.textContent = isbn;
            if (modalDescription) modalDescription.textContent = description;

            /* COVER IMAGE */
            if (cover && modalCoverImage && modalBookBox) {
                modalCoverImage.src = cover;
                modalCoverImage.style.display = 'block';
                modalBookBox.style.display = 'none';
            } else if (modalCoverImage && modalBookBox) {
                modalCoverImage.style.display = 'none';
                modalBookBox.style.display = 'flex';
            }

            /* STATUS */
            if (modalStatus) {
                modalStatus.textContent = status;
                modalStatus.classList.remove('available', 'borrowed');

                if (status === 'Tersedia') {
                    modalStatus.classList.add('available');
                } else {
                    modalStatus.classList.add('borrowed');
                }
            }

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

    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    if (modalAction) {
        modalAction.addEventListener('click', closeModal);
    }

    /* Klik luar modal */
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    /* ESC key */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });

});