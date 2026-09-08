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

    document.addEventListener('DOMContentLoaded', function () {

    const level1 =
        document.getElementById('category_level_1');

    const level2 =
        document.getElementById('category_level_2');

    const level3 =
        document.getElementById('category_level_3');

    if (!level1 || !level2 || !level3) {
        return;
    }

    const categories = {

        "Pendidikan": {

            "SD": [
                "Kelas 1",
                "Kelas 2",
                "Kelas 3",
                "Kelas 4",
                "Kelas 5",
                "Kelas 6"
            ],

            "SMP": [
                "Kelas 7",
                "Kelas 8",
                "Kelas 9"
            ],

            "SMA": [
                "Kelas 10",
                "Kelas 11",
                "Kelas 12"
            ]
        },

        "Anak-anak": {

            "Dongeng": [
                "Fabel",
                "Cerita Rakyat",
                "Legenda"
            ],

            "Aktivitas": [
                "Mewarnai",
                "Puzzle",
                "Kerajinan"
            ]
        },

        "Remaja": {

            "Novel": [
                "Romance",
                "Fantasi",
                "Petualangan"
            ],

            "Pengembangan Diri": [
                "Motivasi",
                "Karier",
                "Public Speaking"
            ]
        },

        "Dewasa": {

            "Bisnis": [
                "Manajemen",
                "Marketing",
                "Keuangan"
            ],

            "Teknologi": [
                "Programming",
                "AI",
                "Data Science"
            ]
        }

    };

    level1.addEventListener('change', function () {

        level2.innerHTML =
            '<option value="">Pilih Subkategori</option>';

        level3.innerHTML =
            '<option value="">Pilih Detail</option>';

        if (!categories[this.value]) {
            return;
        }

        Object.keys(
            categories[this.value]
        ).forEach(function (item) {

            level2.innerHTML +=
                `<option value="${item}">${item}</option>`;

        });

    });

    level2.addEventListener('change', function () {

        level3.innerHTML =
            '<option value="">Pilih Detail</option>';

        const parent =
            level1.value;

        if (
            !categories[parent] ||
            !categories[parent][this.value]
        ) {
            return;
        }

        categories[parent][this.value]
            .forEach(function (item) {

                level3.innerHTML +=
                    `<option value="${item}">${item}</option>`;

            });

    });

});

});