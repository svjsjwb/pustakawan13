document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('locatorCopyModal');

    if (!modal) {
        return;
    }

    const modalTitle = document.getElementById('locatorModalTitle');
    const modalBarcode = document.getElementById('locatorModalBarcode');
    const modalStatus = document.getElementById('locatorModalStatus');
    const modalShelf = document.getElementById('locatorModalShelf');
    const modalPosition = document.getElementById('locatorModalPosition');
    const modalBookTitle =
        document.getElementById('locatorModalBookTitle');
    const closeButton = document.getElementById('locatorModalClose');
    const overlay = modal.querySelector('.locator-modal-overlay');
    const modalCopyNumber =
        document.getElementById('locatorModalCopyNumber');
    const slots = document.querySelectorAll(
        '.locator-book-slot[data-copy-id]'
    );


    slots.forEach(function (slot) {

        slot.addEventListener('click', function () {

            modalTitle.textContent =
                'Detail Eksemplar';

            modalBookTitle.textContent =
                slot.dataset.title || '-';

            modalCopyNumber.textContent =
                'Ke-' +
                (slot.dataset.copyNumber || '-') +
                ' dari ' +
                (slot.dataset.copyTotal || '-');

            modalBarcode.textContent =
                slot.dataset.barcode || '-';

            modalStatus.textContent =
                slot.dataset.status || '-';

            modalShelf.textContent =
                slot.dataset.shelf || '-';

            modalPosition.textContent =
                'Baris ' +
                (slot.dataset.row || '-') +
                ' — Kolom ' +
                (slot.dataset.column || '-');


            modal.hidden = false;

        });

    });


    function closeModal() {

        modal.hidden = true;

    }


    closeButton.addEventListener(
        'click',
        closeModal
    );


    overlay.addEventListener(
        'click',
        closeModal
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                closeModal();

            }

        }
    );

});