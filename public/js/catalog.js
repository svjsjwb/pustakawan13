document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById('book-modal');

    const form =
        document.getElementById('catalog-filter-form');

    const searchInput =
        form?.querySelector('input[name="search"]');

    const statusSelect =
        form?.querySelector('select[name="status"]');


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let searchTimer = null;

    let requestController = null;


    /*
    |--------------------------------------------------------------------------
    | MODAL ELEMENT
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

    /*
    |--------------------------------------------------------------------------
    | STATUS LIST
    |--------------------------------------------------------------------------
    |
    | INI YANG DIPERBAIKI.
    | Sebelumnya didefinisikan sebagai modalStatus,
    | tetapi yang dipanggil di bawah adalah modalStatusList.
    |
    */

    const modalStatusList =
        document.getElementById('modal-status-list');

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
    | OPEN BOOK MODAL
    |--------------------------------------------------------------------------
    */

    function openBookModal(card) {

        if (!modal || !card) {
            return;
        }


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
        | TITLE
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


        /*
        |--------------------------------------------------------------------------
        | AUTHOR
        |--------------------------------------------------------------------------
        */

        if (modalAuthor) {

            modalAuthor.textContent =
                'Penulis: ' +
                author;

        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        if (modalCategory) {

            modalCategory.textContent =
                category;

        }


        if (modalCategoryDetail) {

            modalCategoryDetail.textContent =
                category;

        }


        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        if (modalStock) {

            modalStock.textContent =
                stock;

        }


        /*
        |--------------------------------------------------------------------------
        | AUTHOR DETAIL
        |--------------------------------------------------------------------------
        */

        if (modalAuthorDetail) {

            modalAuthorDetail.textContent =
                author;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS DETAIL
        |--------------------------------------------------------------------------
        */

        if (modalStatusDetail) {

            modalStatusDetail.textContent =
                status;

        }


        /*
        |--------------------------------------------------------------------------
        | PUBLISHER
        |--------------------------------------------------------------------------
        */

        if (modalPublisher) {

            modalPublisher.textContent =
                publisher;

        }


        /*
        |--------------------------------------------------------------------------
        | YEAR
        |--------------------------------------------------------------------------
        */

        if (modalYear) {

            modalYear.textContent =
                year;

        }


        /*
        |--------------------------------------------------------------------------
        | CALL NUMBER
        |--------------------------------------------------------------------------
        */

        if (modalCallNumber) {

            modalCallNumber.textContent =
                callNumber;

        }


        /*
        |--------------------------------------------------------------------------
        | ISBN
        |--------------------------------------------------------------------------
        */

        if (modalIsbn) {

            modalIsbn.textContent =
                isbn;

        }


        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION
        |--------------------------------------------------------------------------
        */

        if (modalDescription) {

            modalDescription.textContent =
                description;

        }


        /*
        |--------------------------------------------------------------------------
        | COVER
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

            modalCoverImage.src =
                '';

            modalCoverImage.style.display =
                'none';

            modalBookBox.style.display =
                'flex';

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS POPUP
        |--------------------------------------------------------------------------
        |
        | Status sekarang membaca:
        |
        | data-borrowed
        | data-reserved
        | data-available
        |
        | Jadi kalau ada dua status, dua-duanya muncul.
        |
        */

        if (modalStatusList) {

            const borrowed =
                parseInt(
                    card.dataset.borrowed || '0',
                    10
                );

            const reserved =
                parseInt(
                    card.dataset.reserved || '0',
                    10
                );

            const available =
                parseInt(
                    card.dataset.available ||
                    stock ||
                    '0',
                    10
                );


            /*
            |--------------------------------------------------------------------------
            | RESET STATUS
            |--------------------------------------------------------------------------
            */

            modalStatusList.innerHTML = '';


            /*
            |--------------------------------------------------------------------------
            | DIPINJAM
            |--------------------------------------------------------------------------
            */

            if (borrowed > 0) {

                const borrowedBadge =
                    document.createElement('span');

                borrowedBadge.className =
                    'catalog-modal-status borrowed';

                borrowedBadge.textContent =
                    'Dipinjam (' +
                    borrowed +
                    ')';

                modalStatusList.appendChild(
                    borrowedBadge
                );

            }


            /*
            |--------------------------------------------------------------------------
            | DIRESERVASI
            |--------------------------------------------------------------------------
            */

            if (reserved > 0) {

                const reservedBadge =
                    document.createElement('span');

                reservedBadge.className =
                    'catalog-modal-status reserved';

                reservedBadge.textContent =
                    'Direservasi (' +
                    reserved +
                    ')';

                modalStatusList.appendChild(
                    reservedBadge
                );

            }


            /*
            |--------------------------------------------------------------------------
            | TERSEDIA
            |--------------------------------------------------------------------------
            */

            if (
                borrowed === 0 &&
                reserved === 0
            ) {

                const availableBadge =
                    document.createElement('span');

                availableBadge.className =
                    'catalog-modal-status available';

                availableBadge.textContent =
                    'Tersedia (' +
                    available +
                    ')';

                modalStatusList.appendChild(
                    availableBadge
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW MODAL
        |--------------------------------------------------------------------------
        */

        modal.classList.add('open');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeBookModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove('open');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow =
            '';

    }


    /*
    |--------------------------------------------------------------------------
    | ATTACH BOOK CARD EVENT
    |--------------------------------------------------------------------------
    |
    | Dipanggil lagi setelah hasil AJAX mengganti isi book-grid.
    |
    */

    function attachBookCardEvents() {

        const cards =
            document.querySelectorAll(
                '#page-catalog .book-card'
            );


        cards.forEach(function (card) {

            /*
            |--------------------------------------------------------------------------
            | Hindari event listener ganda
            |--------------------------------------------------------------------------
            */

            if (
                card.dataset.modalReady ===
                'true'
            ) {

                return;

            }


            card.dataset.modalReady =
                'true';


            card.addEventListener(
                'click',
                function () {

                    openBookModal(card);

                }
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE BUTTON
    |--------------------------------------------------------------------------
    */

    if (modalClose) {

        modalClose.addEventListener(
            'click',
            closeBookModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | MODAL ACTION
    |--------------------------------------------------------------------------
    */

    if (modalAction) {

        modalAction.addEventListener(
            'click',
            closeBookModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLICK BACKDROP
    |--------------------------------------------------------------------------
    */

    if (modal) {

        modal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    modal
                ) {

                    closeBookModal();

                }

            }
        );

    }


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
                modal &&
                modal.classList.contains('open')
            ) {

                closeBookModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | AJAX CATALOG
    |--------------------------------------------------------------------------
    */

    async function loadCatalog(options = {}) {

        if (!form) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Batalkan request sebelumnya
        |--------------------------------------------------------------------------
        */

        if (requestController) {

            requestController.abort();

        }


        requestController =
            new AbortController();


        /*
        |--------------------------------------------------------------------------
        | URL
        |--------------------------------------------------------------------------
        */

        const url =
            new URL(
                form.action,
                window.location.origin
            );


        /*
        |--------------------------------------------------------------------------
        | FORM DATA
        |--------------------------------------------------------------------------
        */

        const formData =
            new FormData(form);

        const params =
            new URLSearchParams();


        formData.forEach(
            function (value, key) {

                if (
                    value !== null &&
                    value !== ''
                ) {

                    params.set(
                        key,
                        value
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESET PAGE
        |--------------------------------------------------------------------------
        */

        if (options.resetPage !== false) {

            params.delete('page');

        }


        /*
        |--------------------------------------------------------------------------
        | QUERY STRING
        |--------------------------------------------------------------------------
        */

        url.search =
            params.toString();


        /*
        |--------------------------------------------------------------------------
        | BOOK GRID
        |--------------------------------------------------------------------------
        */

        const bookGrid =
            document.querySelector(
                '#page-catalog .book-grid'
            );


        if (bookGrid) {

            bookGrid.classList.add(
                'catalog-loading'
            );

        }


        try {

            /*
            |--------------------------------------------------------------------------
            | REQUEST
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    url.toString(),
                    {
                        method: 'GET',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html'
                        },

                        signal:
                            requestController.signal
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data katalog.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RESPONSE HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();


            const parser =
                new DOMParser();

            const documentResult =
                parser.parseFromString(
                    html,
                    'text/html'
                );


            /*
            |--------------------------------------------------------------------------
            | GRID BARU
            |--------------------------------------------------------------------------
            */

            const newGrid =
                documentResult.querySelector(
                    '#page-catalog .book-grid'
                );

            const currentGrid =
                document.querySelector(
                    '#page-catalog .book-grid'
                );


            if (
                newGrid &&
                currentGrid
            ) {

                currentGrid.innerHTML =
                    newGrid.innerHTML;

            }


            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            const newPagination =
                documentResult.querySelector(
                    '#page-catalog .catalog-pagination'
                );

            const currentPagination =
                document.querySelector(
                    '#page-catalog .catalog-pagination'
                );


            if (
                newPagination &&
                currentPagination
            ) {

                currentPagination.innerHTML =
                    newPagination.innerHTML;

            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS PAGINATION JIKA KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                !newPagination &&
                currentPagination
            ) {

                currentPagination.remove();

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE COUNT
            |--------------------------------------------------------------------------
            */

            const newCount =
                documentResult.querySelector(
                    '#page-catalog .catalog-count'
                );

            const currentCount =
                document.querySelector(
                    '#page-catalog .catalog-count'
                );


            if (
                newCount &&
                currentCount
            ) {

                currentCount.textContent =
                    newCount.textContent;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE URL
            |--------------------------------------------------------------------------
            */

            window.history.replaceState(
                {},
                '',
                url.toString()
            );


            /*
            |--------------------------------------------------------------------------
            | ATTACH EVENT CARD
            |--------------------------------------------------------------------------
            */

            attachBookCardEvents();


        } catch (error) {

            if (
                error.name ===
                'AbortError'
            ) {

                return;

            }


            console.error(
                'Catalog AJAX Error:',
                error
            );

        } finally {

            if (bookGrid) {

                bookGrid.classList.remove(
                    'catalog-loading'
                );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH INPUT
    |--------------------------------------------------------------------------
    */

    if (searchInput) {

        searchInput.addEventListener(
            'input',
            function () {

                clearTimeout(
                    searchTimer
                );


                searchTimer =
                    setTimeout(
                        function () {

                            loadCatalog({
                                resetPage: true
                            });

                        },
                        350
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | STATUS FILTER
    |--------------------------------------------------------------------------
    */

    if (statusSelect) {

        statusSelect.addEventListener(
            'change',
            function () {

                loadCatalog({
                    resetPage: true
                });

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD CATALOG DARI URL
    |--------------------------------------------------------------------------
    */

    async function loadCatalogFromUrl(
        targetUrl
    ) {

        /*
        |--------------------------------------------------------------------------
        | Batalkan request sebelumnya
        |--------------------------------------------------------------------------
        */

        if (requestController) {

            requestController.abort();

        }


        requestController =
            new AbortController();


        /*
        |--------------------------------------------------------------------------
        | URL
        |--------------------------------------------------------------------------
        */

        const url =
            new URL(
                targetUrl,
                window.location.origin
            );


        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        const pageCatalog =
            document.getElementById(
                'page-catalog'
            );

        const bookGrid =
            pageCatalog?.querySelector(
                '.book-grid'
            );


        if (bookGrid) {

            bookGrid.classList.add(
                'catalog-loading'
            );

        }


        try {

            /*
            |--------------------------------------------------------------------------
            | REQUEST
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    url.toString(),
                    {
                        method: 'GET',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html'
                        },

                        signal:
                            requestController.signal
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data katalog.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();


            const parser =
                new DOMParser();

            const newDocument =
                parser.parseFromString(
                    html,
                    'text/html'
                );


            /*
            |--------------------------------------------------------------------------
            | GRID
            |--------------------------------------------------------------------------
            */

            const newGrid =
                newDocument.querySelector(
                    '#page-catalog .book-grid'
                );

            const currentGrid =
                document.querySelector(
                    '#page-catalog .book-grid'
                );


            if (
                newGrid &&
                currentGrid
            ) {

                currentGrid.innerHTML =
                    newGrid.innerHTML;

            }


            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            const newPagination =
                newDocument.querySelector(
                    '#page-catalog .catalog-pagination'
                );

            const currentPagination =
                document.querySelector(
                    '#page-catalog .catalog-pagination'
                );


            if (
                newPagination &&
                currentPagination
            ) {

                currentPagination.innerHTML =
                    newPagination.innerHTML;

            }


            /*
            |--------------------------------------------------------------------------
            | BUAT PAGINATION BARU
            |--------------------------------------------------------------------------
            */

            if (
                newPagination &&
                !currentPagination
            ) {

                const currentGridElement =
                    document.querySelector(
                        '#page-catalog .book-grid'
                    );


                if (currentGridElement) {

                    currentGridElement.insertAdjacentHTML(
                        'afterend',
                        newPagination.outerHTML
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS PAGINATION
            |--------------------------------------------------------------------------
            */

            if (
                !newPagination &&
                currentPagination
            ) {

                currentPagination.remove();

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE COUNT
            |--------------------------------------------------------------------------
            */

            const newCount =
                newDocument.querySelector(
                    '#page-catalog .catalog-count'
                );

            const currentCount =
                document.querySelector(
                    '#page-catalog .catalog-count'
                );


            if (
                newCount &&
                currentCount
            ) {

                currentCount.textContent =
                    newCount.textContent;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE CATEGORY
            |--------------------------------------------------------------------------
            */

            const newCategoryFilter =
                newDocument.querySelector(
                    '#page-catalog .catalog-category-filter'
                );

            const currentCategoryFilter =
                document.querySelector(
                    '#page-catalog .catalog-category-filter'
                );


            if (
                newCategoryFilter &&
                currentCategoryFilter
            ) {

                currentCategoryFilter.innerHTML =
                    newCategoryFilter.innerHTML;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS SELECT
            |--------------------------------------------------------------------------
            */

            const newStatus =
                newDocument.querySelector(
                    '#page-catalog .catalog-status-select'
                );

            const currentStatus =
                document.querySelector(
                    '#page-catalog .catalog-status-select'
                );


            if (
                newStatus &&
                currentStatus
            ) {

                currentStatus.value =
                    newStatus.value;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE URL
            |--------------------------------------------------------------------------
            */

            window.history.pushState(
                {},
                '',
                url.toString()
            );


            /*
            |--------------------------------------------------------------------------
            | ATTACH EVENT CARD
            |--------------------------------------------------------------------------
            */

            attachBookCardEvents();


        } catch (error) {

            if (
                error.name ===
                'AbortError'
            ) {

                return;

            }


            console.error(
                'Catalog AJAX Error:',
                error
            );

        } finally {

            const currentBookGrid =
                document.querySelector(
                    '#page-catalog .book-grid'
                );


            if (currentBookGrid) {

                currentBookGrid.classList.remove(
                    'catalog-loading'
                );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY CHIP
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const categoryLink =
                event.target.closest(
                    '.catalog-category-chip, .catalog-subcategory-chip'
                );


            if (!categoryLink) {
                return;
            }


            if (
                !categoryLink.href ||
                !categoryLink.href.includes('/catalog')
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Cegah reload
            |--------------------------------------------------------------------------
            */

            event.preventDefault();


            const url =
                new URL(
                    categoryLink.href,
                    window.location.origin
                );


            loadCatalogFromUrl(
                url.toString()
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const paginationLink =
                event.target.closest(
                    '#page-catalog .catalog-pagination a'
                );


            if (!paginationLink) {
                return;
            }


            event.preventDefault();


            const url =
                new URL(
                    paginationLink.href,
                    window.location.origin
                );


            if (form) {

                let pageInput =
                    form.querySelector(
                        'input[name="page"]'
                    );


                if (!pageInput) {

                    pageInput =
                        document.createElement(
                            'input'
                        );

                    pageInput.type =
                        'hidden';

                    pageInput.name =
                        'page';

                    form.appendChild(
                        pageInput
                    );

                }


                pageInput.value =
                    url.searchParams.get(
                        'page'
                    ) || '1';


                loadCatalog({
                    resetPage: false
                });

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                clearTimeout(
                    searchTimer
                );


                loadCatalog({
                    resetPage: true
                });

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL CARD EVENTS
    |--------------------------------------------------------------------------
    */

    attachBookCardEvents();

});