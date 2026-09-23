(function () {
    if (window.__catalogJsInitialized) {
        return;
    }
    window.__catalogJsInitialized = true;

    function initCatalog() {

        /* =========================================================
           ELEMENT
        ========================================================= */

        const modal = document.getElementById('book-modal');

        const form = document.getElementById('catalog-filter-form');

        const searchInput =
            form?.querySelector('input[name="search"]');

        const statusSelect =
            form?.querySelector('select[name="status"]');


        /* =========================================================
           STATE
        ========================================================= */

        let searchTimer = null;
        let requestController = null;


        /* =========================================================
           MODAL ELEMENT
        ========================================================= */

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


        /* =========================================================
           BOOK MODAL
        ========================================================= */

        function openBookModal(card) {

            if (!modal || !card) return;

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


            if (modalTitle) {
                modalTitle.textContent = title;
            }

            if (modalCoverTitle) {
                modalCoverTitle.textContent = title;
            }

            if (modalAuthor) {
                modalAuthor.textContent =
                    'Penulis: ' + author;
            }

            if (modalCategory) {
                modalCategory.textContent = category;
            }

            if (modalCategoryDetail) {
                modalCategoryDetail.textContent = category;
            }

            if (modalStock) {
                modalStock.textContent = stock;
            }

            if (modalAuthorDetail) {
                modalAuthorDetail.textContent = author;
            }

            if (modalStatusDetail) {
                modalStatusDetail.textContent = status;
            }

            if (modalPublisher) {
                modalPublisher.textContent = publisher;
            }

            if (modalYear) {
                modalYear.textContent = year;
            }

            if (modalCallNumber) {
                modalCallNumber.textContent = callNumber;
            }

            if (modalIsbn) {
                modalIsbn.textContent = isbn;
            }

            if (modalDescription) {
                modalDescription.textContent = description;
            }


            /* COVER */

            if (
                cover &&
                modalCoverImage &&
                modalBookBox
            ) {

                modalCoverImage.src = cover;
                modalCoverImage.style.display = 'block';
                modalBookBox.style.display = 'none';

            } else if (
                modalCoverImage &&
                modalBookBox
            ) {

                modalCoverImage.src = '';
                modalCoverImage.style.display = 'none';
                modalBookBox.style.display = 'flex';

            }


            /* STATUS */

            if (modalStatusList) {

                const borrowed =
                    parseInt(card.dataset.borrowed || '0', 10);

                const reserved =
                    parseInt(card.dataset.reserved || '0', 10);

                const available =
                    parseInt(
                        card.dataset.available ||
                        stock ||
                        '0',
                        10
                    );

                modalStatusList.innerHTML = '';


                if (borrowed > 0) {

                    const badge =
                        document.createElement('span');

                    badge.className =
                        'catalog-modal-status borrowed';

                    badge.textContent =
                        `Dipinjam (${borrowed})`;

                    modalStatusList.appendChild(badge);
                }


                if (reserved > 0) {

                    const badge =
                        document.createElement('span');

                    badge.className =
                        'catalog-modal-status reserved';

                    badge.textContent =
                        `Direservasi (${reserved})`;

                    modalStatusList.appendChild(badge);
                }


                if (
                    borrowed === 0 &&
                    reserved === 0
                ) {

                    const badge =
                        document.createElement('span');

                    badge.className =
                        'catalog-modal-status available';

                    badge.textContent =
                        `Tersedia (${available})`;

                    modalStatusList.appendChild(badge);
                }

            }


            modal.classList.add('open');

            modal.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.style.overflow = 'hidden';
        }


        function closeBookModal() {

            if (!modal) return;

            modal.classList.remove('open');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow = '';
        }


        function attachBookCardEvents() {

            const cards =
                document.querySelectorAll(
                    '#page-catalog .book-card'
                );

            cards.forEach(function (card) {

                if (
                    card.dataset.modalReady === 'true'
                ) {
                    return;
                }

                card.dataset.modalReady = 'true';

                card.addEventListener(
                    'click',
                    function () {
                        openBookModal(card);
                    }
                );

            });
        }


        if (modalClose) {
            modalClose.addEventListener(
                'click',
                closeBookModal
            );
        }

        if (modalAction) {
            modalAction.addEventListener(
                'click',
                closeBookModal
            );
        }

        if (modal) {

            modal.addEventListener(
                'click',
                function (event) {

                    if (event.target === modal) {
                        closeBookModal();
                    }

                }
            );

        }


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


        /* =========================================================
           DROPDOWN SYSTEM
        ========================================================= */

        function closeAllDropdowns() {

            document
                .querySelectorAll(
                    '#page-catalog .catalog-filter-dropdown'
                )
                .forEach(function (dropdown) {

                    dropdown.classList.remove('show');

                    dropdown.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                });


            document
                .querySelectorAll(
                    '#page-catalog .catalog-filter-btn'
                )
                .forEach(function (button) {

                    button.classList.remove('open');

                    button.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                });

        }


        function openDropdown(
            button,
            dropdown
        ) {

            if (!button || !dropdown) {
                return;
            }

            closeAllDropdowns();

            dropdown.classList.add('show');

            dropdown.setAttribute(
                'aria-hidden',
                'false'
            );

            button.classList.add('open');

            button.setAttribute(
                'aria-expanded',
                'true'
            );

        }


        function toggleDropdown(
            buttonId,
            dropdownId
        ) {

            const button =
                document.getElementById(buttonId);

            const dropdown =
                document.getElementById(dropdownId);

            if (!button || !dropdown) {
                console.error(
                    'Dropdown element tidak ditemukan:',
                    buttonId,
                    dropdownId
                );

                return;
            }


            const isOpen =
                dropdown.classList.contains('show');


            if (isOpen) {

                closeAllDropdowns();

                return;
            }


            openDropdown(
                button,
                dropdown
            );

        }


        /* =========================================================
           DROPDOWN BUTTON & OUTSIDE CLICK
        ========================================================= */

        document.addEventListener('click', function (event) {
            const categoryButton = event.target.closest('#catalog-category-btn');
            if (categoryButton) {
                event.preventDefault();
                event.stopPropagation();
                toggleDropdown(
                    'catalog-category-btn',
                    'catalog-category-dropdown'
                );
                return;
            }

            const statusButton = event.target.closest('#catalog-status-btn');
            if (statusButton) {
                event.preventDefault();
                event.stopPropagation();
                toggleDropdown(
                    'catalog-status-btn',
                    'catalog-status-dropdown'
                );
                return;
            }

            if (!event.target.closest('#page-catalog .catalog-dropdown-wrapper')) {
                closeAllDropdowns();
            }
        });


        /* =========================================================
           SHOW SUBCATEGORY
        ========================================================= */

        function showSubcategory(categoryId) {
            const subcategoriesContainer =
                document.getElementById('catalog-dropdown-subcategories');

            const groups =
                document.querySelectorAll(
                    '#catalog-dropdown-subcategories .catalog-subcategory-group'
                );

            let found = false;

            groups.forEach(function (group) {
                const groupCategoryId =
                    String(group.dataset.groupCategoryId || '');
                const selectedId =
                    String(categoryId || '');

                if (selectedId !== '' && groupCategoryId === selectedId) {
                    group.style.display = 'block';
                    found = true;
                } else {
                    group.style.display = 'none';
                }
            });

            if (subcategoriesContainer) {
                if (found) {
                    subcategoriesContainer.style.display = 'block';
                    subcategoriesContainer.classList.add('show');
                } else {
                    subcategoriesContainer.style.display = 'none';
                    subcategoriesContainer.classList.remove('show');
                }
            }

            return found;
        }


        /* =========================================================
           LOAD CATALOG FROM URL
        ========================================================= */

        async function loadCatalogFromUrl(
            targetUrl,
            options = {}
        ) {

            if (requestController) {
                requestController.abort();
            }


            requestController =
                new AbortController();


            const url =
                new URL(
                    targetUrl,
                    window.location.origin
                );


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


                const html =
                    await response.text();


                const parser =
                    new DOMParser();


                const newDocument =
                    parser.parseFromString(
                        html,
                        'text/html'
                    );


                /* GRID */

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


                /* PAGINATION */

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


                if (
                    !newPagination &&
                    currentPagination
                ) {

                    currentPagination.remove();

                }


                /* COUNT */

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


                /* =================================================
                   UPDATE CATEGORY WRAPPER
                ================================================= */

                const newCategoryWrapper =
                    newDocument.querySelector(
                        '#page-catalog #catalog-category-wrapper'
                    );

                const currentCategoryWrapper =
                    document.querySelector(
                        '#page-catalog #catalog-category-wrapper'
                    );


                if (
                    newCategoryWrapper &&
                    currentCategoryWrapper
                ) {

                    currentCategoryWrapper.innerHTML =
                        newCategoryWrapper.innerHTML;

                }


                /* =================================================
                   UPDATE STATUS WRAPPER
                ================================================= */

                const newStatusWrapper =
                    newDocument.querySelector(
                        '#page-catalog #catalog-status-wrapper'
                    );

                const currentStatusWrapper =
                    document.querySelector(
                        '#page-catalog #catalog-status-wrapper'
                    );


                if (
                    newStatusWrapper &&
                    currentStatusWrapper
                ) {

                    currentStatusWrapper.innerHTML =
                        newStatusWrapper.innerHTML;

                }


                /* =================================================
                   UPDATE HIDDEN INPUTS
                ================================================= */

                const inputNames = [
                    'category',
                    'subcategory',
                    'status'
                ];


                inputNames.forEach(function (name) {

                    const newInput =
                        newDocument.querySelector(
                            `#catalog-${name}-input`
                        );

                    const currentInput =
                        document.querySelector(
                            `#catalog-${name}-input`
                        );


                    if (
                        newInput &&
                        currentInput
                    ) {

                        currentInput.value =
                            newInput.value;

                    }

                });


                /* =================================================
                   UPDATE LEGACY STATUS SELECT
                ================================================= */

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


                /* =================================================
                   UPDATE URL
                ================================================= */

                window.history.pushState(
                    {},
                    '',
                    url.toString()
                );


                /* =================================================
                   DROPDOWN STATE
                ================================================= */

                if (
                    options.keepCategoryOpen === true
                ) {

                    /*
                    | AJAX tadi mengganti seluruh
                    | category wrapper.
                    | Jadi ambil element BARU.
                    */

                    const updatedButton =
                        document.getElementById(
                            'catalog-category-btn'
                        );

                    const updatedDropdown =
                        document.getElementById(
                            'catalog-category-dropdown'
                        );


                    if (
                        updatedButton &&
                        updatedDropdown
                    ) {

                        updatedDropdown.classList.add(
                            'show'
                        );

                        updatedDropdown.setAttribute(
                            'aria-hidden',
                            'false'
                        );

                        updatedButton.classList.add(
                            'open'
                        );

                        updatedButton.setAttribute(
                            'aria-expanded',
                            'true'
                        );

                    }

                    if (options.activeCategoryId) {
                        showSubcategory(options.activeCategoryId);
                    }

                } else {

                    closeAllDropdowns();

                }


                /* CARD EVENTS */

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


        /* =========================================================
           FILTER CHIP
        ========================================================= */

        document.addEventListener(
            'click',
            function (event) {

                const filterLink =
                    event.target.closest(
                        '#page-catalog .catalog-category-chip, ' +
                        '#page-catalog .catalog-subcategory-chip, ' +
                        '#page-catalog .catalog-status-chip'
                    );


                if (!filterLink) {
                    return;
                }


                event.preventDefault();
                event.stopPropagation();


                const url =
                    new URL(
                        filterLink.href,
                        window.location.origin
                    );

                const currentSearchInput =
                    form?.querySelector('input[name="search"]');

                if (
                    currentSearchInput &&
                    currentSearchInput.value.trim() !== ''
                ) {
                    url.searchParams.set(
                        'search',
                        currentSearchInput.value.trim()
                    );
                } else if (currentSearchInput) {
                    url.searchParams.delete('search');
                }


                /* =================================================
                   CATEGORY
                ================================================= */

                if (
                    filterLink.classList.contains(
                        'catalog-category-chip'
                    )
                ) {

                    const categoryId =
                        filterLink.dataset.categoryId || '';


                    /* SEMUA KATEGORI */

                    if (!categoryId) {

                        showSubcategory('');

                        loadCatalogFromUrl(
                            url.toString()
                        );

                        return;
                    }


                    /*
                    | Tampilkan submenu terlebih dahulu.
                    */

                    const hasSubcategory =
                        showSubcategory(
                            categoryId
                        );


                    /*
                    | Kalau punya submenu,
                    | dropdown tetap terbuka.
                    */

                    if (hasSubcategory) {

                        loadCatalogFromUrl(
                            url.toString(),
                            {
                                keepCategoryOpen: true,
                                activeCategoryId: categoryId
                            }
                        );

                        return;
                    }


                    /*
                    | Kalau tidak punya submenu,
                    | tutup dropdown.
                    */

                    loadCatalogFromUrl(
                        url.toString()
                    );

                    return;
                }


                /* =================================================
                   SUBCATEGORY
                ================================================= */

                if (
                    filterLink.classList.contains(
                        'catalog-subcategory-chip'
                    )
                ) {

                    loadCatalogFromUrl(
                        url.toString()
                    );

                    return;
                }


                /* =================================================
                   STATUS
                ================================================= */

                if (
                    filterLink.classList.contains(
                        'catalog-status-chip'
                    )
                ) {

                    loadCatalogFromUrl(
                        url.toString()
                    );

                }

            }
        );


        /* =========================================================
           SEARCH
        ========================================================= */

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


        /* =========================================================
           AJAX CATALOG
        ========================================================= */

        async function loadCatalog(options = {}) {

            if (!form) {
                return;
            }


            if (requestController) {
                requestController.abort();
            }


            requestController =
                new AbortController();


            const url =
                new URL(
                    form.action,
                    window.location.origin
                );


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


            if (
                options.resetPage !== false
            ) {

                params.delete('page');

            }


            url.search =
                params.toString();


            try {

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


                const html =
                    await response.text();


                const parser =
                    new DOMParser();


                const newDocument =
                    parser.parseFromString(
                        html,
                        'text/html'
                    );


                /* GRID */

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


                /* PAGINATION */

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


                if (
                    !newPagination &&
                    currentPagination
                ) {

                    currentPagination.remove();

                }


                /* COUNT */

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


                window.history.replaceState(
                    {},
                    '',
                    url.toString()
                );


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

            }

        }


        /* =========================================================
           STATUS LEGACY
        ========================================================= */

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


        /* =========================================================
           PAGINATION
        ========================================================= */

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


                if (!form) {
                    return;
                }


                let pageInput =
                    form.querySelector(
                        'input[name="page"]'
                    );


                if (!pageInput) {

                    pageInput =
                        document.createElement(
                            'input'
                        );

                    pageInput.type = 'hidden';

                    pageInput.name = 'page';

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
        );


        /* =========================================================
           FORM SUBMIT
        ========================================================= */

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


        /* =========================================================
           INITIAL
        ========================================================= */

        const initialCategoryInput =
            document.getElementById('catalog-category-input');

        if (initialCategoryInput && initialCategoryInput.value) {
            showSubcategory(initialCategoryInput.value);
        }

        attachBookCardEvents();

    } // end initCatalog

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCatalog);
    } else {
        initCatalog();
    }

})();