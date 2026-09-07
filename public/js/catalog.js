document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | CATEGORY DROPDOWN - CLICK
    |--------------------------------------------------------------------------
    */

    const categoryDropdown =
        document.querySelector('.catalog-category-dropdown');

    const categoryTrigger =
        document.querySelector('.catalog-category-trigger');

    const categoryMenu =
        document.querySelector('.catalog-category-menu');


    if (
        categoryDropdown &&
        categoryTrigger &&
        categoryMenu
    ) {

        /*
        |--------------------------------------------------------------------------
        | BUKA / TUTUP MENU KATEGORI
        |--------------------------------------------------------------------------
        */

        categoryTrigger.addEventListener(
            'click',
            function (event) {

                event.preventDefault();
                event.stopPropagation();

                categoryDropdown.classList.toggle('open');

            }
        );


        /*
        |--------------------------------------------------------------------------
        | KLIK KATEGORI YANG MEMILIKI SUBKATEGORI
        |--------------------------------------------------------------------------
        */

        categoryMenu
            .querySelectorAll('.catalog-category-item')
            .forEach(function (item) {

                const categoryLink =
                    item.querySelector(
                        ':scope > .catalog-category-option'
                    );

                const subcategoryMenu =
                    item.querySelector(
                        ':scope > .catalog-subcategory-menu'
                    );


                /*
                |--------------------------------------------------------------------------
                | KATEGORI PUNYA SUBKATEGORI
                |--------------------------------------------------------------------------
                */

                if (
                    categoryLink &&
                    subcategoryMenu
                ) {

                    categoryLink.addEventListener(
                        'click',
                        function (event) {

                            /*
                            |--------------------------------------------------------------------------
                            | JANGAN PINDAH HALAMAN
                            | KLIK KATEGORI = BUKA SUBMENU
                            |--------------------------------------------------------------------------
                            */

                            event.preventDefault();
                            event.stopPropagation();


                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP SUBMENU LAIN
                            |--------------------------------------------------------------------------
                            */

                            categoryMenu
                                .querySelectorAll(
                                    '.catalog-category-item.open'
                                )
                                .forEach(function (otherItem) {

                                    if (
                                        otherItem !== item
                                    ) {

                                        otherItem.classList.remove(
                                            'open'
                                        );

                                    }

                                });


                            /*
                            |--------------------------------------------------------------------------
                            | BUKA / TUTUP SUBMENU
                            |--------------------------------------------------------------------------
                            */

                            item.classList.toggle(
                                'open'
                            );

                        }
                    );

                }

            });


        /*
        |--------------------------------------------------------------------------
        | KLIK DI LUAR DROPDOWN
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function (event) {

                if (
                    !categoryDropdown.contains(
                        event.target
                    )
                ) {

                    /*
                    | Tutup menu utama
                    */

                    categoryDropdown.classList.remove(
                        'open'
                    );


                    /*
                    | Tutup semua submenu
                    */

                    categoryMenu
                        .querySelectorAll(
                            '.catalog-category-item.open'
                        )
                        .forEach(function (item) {

                            item.classList.remove(
                                'open'
                            );

                        });

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BOOK MODAL
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById('book-modal');


    /*
    |--------------------------------------------------------------------------
    | JIKA MODAL TIDAK ADA
    |--------------------------------------------------------------------------
    |
    | Jangan hentikan dropdown kategori karena dropdown
    | sudah diproses di atas.
    |--------------------------------------------------------------------------
    */

    if (!modal) {
        return;
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

    const modalCoverTitle =
        document.getElementById('modal-cover-title');

    const modalCoverImage =
        document.getElementById('modal-cover-image');

    const modalBookBox =
        document.getElementById('modal-book-box');

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

    const modalPublisher =
        document.getElementById('modal-publisher');

    const modalYear =
        document.getElementById('modal-year');

    const modalCallNumber =
        document.getElementById('modal-call-number');

    const modalIsbn =
        document.getElementById('modal-isbn');


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.book-card')
        .forEach(function (card) {

            card.addEventListener(
                'click',
                function () {

                    /*
                    |--------------------------------------------------------------------------
                    | DATA BUKU
                    |--------------------------------------------------------------------------
                    */

                    const title =
                        card.dataset.title ||
                        '-';

                    const author =
                        card.dataset.author ||
                        '-';

                    const category =
                        card.dataset.category ||
                        '-';

                    const stock =
                        card.dataset.stock ||
                        '0';

                    const status =
                        card.dataset.status ||
                        'Tersedia';

                    const description =
                        card.dataset.description ||
                        'Informasi sinopsis/deskripsi belum tersedia untuk buku ini.';

                    const cover =
                        card.dataset.cover ||
                        '';

                    const publisher =
                        card.dataset.publisher ||
                        '-';

                    const year =
                        card.dataset.year ||
                        '-';

                    const callNumber =
                        card.dataset.callNumber ||
                        '-';

                    const isbn =
                        card.dataset.isbn ||
                        '-';


                    /*
                    |--------------------------------------------------------------------------
                    | INFORMASI UTAMA
                    |--------------------------------------------------------------------------
                    */

                    if (modalTitle) {

                        modalTitle.textContent =
                            title;

                    }


                    if (modalCoverTitle) {

                        modalCoverTitle.textContent =
                            title;

                    }


                    if (modalAuthor) {

                        modalAuthor.textContent =
                            'Penulis: ' +
                            author;

                    }


                    if (modalCategory) {

                        modalCategory.textContent =
                            category;

                    }


                    if (modalStock) {

                        modalStock.textContent =
                            stock;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL
                    |--------------------------------------------------------------------------
                    */

                    if (modalCategoryDetail) {

                        modalCategoryDetail.textContent =
                            category;

                    }


                    if (modalAuthorDetail) {

                        modalAuthorDetail.textContent =
                            author;

                    }


                    if (modalStatusDetail) {

                        modalStatusDetail.textContent =
                            status;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | INFORMASI TAMBAHAN
                    |--------------------------------------------------------------------------
                    */

                    if (modalPublisher) {

                        modalPublisher.textContent =
                            publisher;

                    }


                    if (modalYear) {

                        modalYear.textContent =
                            year;

                    }


                    if (modalCallNumber) {

                        modalCallNumber.textContent =
                            callNumber;

                    }


                    if (modalIsbn) {

                        modalIsbn.textContent =
                            isbn;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DESKRIPSI
                    |--------------------------------------------------------------------------
                    */

                    if (modalDescription) {

                        modalDescription.textContent =
                            description;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | COVER IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        cover &&
                        modalCoverImage &&
                        modalBookBox
                    ) {

                        modalCoverImage.src =
                            cover;

                        modalCoverImage.style.display =
                            'block';

                        modalBookBox.style.display =
                            'none';

                    } else if (
                        modalCoverImage &&
                        modalBookBox
                    ) {

                        modalCoverImage.style.display =
                            'none';

                        modalBookBox.style.display =
                            'flex';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (modalStatus) {

                        modalStatus.textContent =
                            status;


                        modalStatus.classList.remove(
                            'available',
                            'borrowed'
                        );


                        if (
                            status ===
                            'Tersedia'
                        ) {

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
                    | BUKA MODAL
                    |--------------------------------------------------------------------------
                    */

                    modal.classList.add(
                        'open'
                    );


                    document.body.style.overflow =
                        'hidden';

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL
    |--------------------------------------------------------------------------
    */

    function closeModal() {

        modal.classList.remove(
            'open'
        );


        document.body.style.overflow =
            '';

    }


    /*
    |--------------------------------------------------------------------------
    | TOMBOL CLOSE
    |--------------------------------------------------------------------------
    */

    if (modalClose) {

        modalClose.addEventListener(
            'click',
            closeModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | TOMBOL ACTION
    |--------------------------------------------------------------------------
    */

    if (modalAction) {

        modalAction.addEventListener(
            'click',
            closeModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK LUAR MODAL
    |--------------------------------------------------------------------------
    */

    modal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                modal
            ) {

                closeModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                modal.classList.contains(
                    'open'
                )
            ) {

                closeModal();

            }

        }
    );

});