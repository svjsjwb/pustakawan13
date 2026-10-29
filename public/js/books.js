document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('books-search');
    const searchButton = document.getElementById('books-search-button');
    const categoryFilter = document.getElementById('books-filter-kategori');
    const totalElement = document.getElementById('books-total');
    const noResults = document.getElementById('no-search-results');

    const pagination = document.getElementById('books-pagination');
    const paginationLinks = document.getElementById('books-pagination-links');
    const paginationInfo = document.getElementById('books-pagination-info');

    const rows = Array.from(
        document.querySelectorAll('#books-table-body tr[data-category]')
    );

    const categoryDropdown = document.getElementById('books-category-dropdown');
    const categoryTrigger = document.getElementById('books-category-trigger');
    const categoryMenu = document.getElementById('books-category-menu');
    const categoryLabel = document.getElementById('books-category-label');

    const categoryOptions = Array.from(
        document.querySelectorAll('.books-category-option')
    );

    const BOOKS_PER_PAGE = 25;

    let currentPage = 1;
    let filteredRows = [...rows];


    /* =========================================================
       CUSTOM CATEGORY DROPDOWN
    ========================================================= */

    if (categoryTrigger && categoryMenu && categoryDropdown) {

        categoryTrigger.addEventListener('click', function (event) {

            event.stopPropagation();

            const isOpen = categoryMenu.classList.contains('is-open');

            categoryMenu.classList.toggle('is-open', !isOpen);

            categoryTrigger.classList.toggle('is-open', !isOpen);

            categoryTrigger.setAttribute(
                'aria-expanded',
                !isOpen ? 'true' : 'false'
            );

        });


        categoryOptions.forEach(function (option) {

            option.addEventListener('click', function () {

                const value = option.dataset.value || '';

                const textElement = option.querySelector('span:first-child');

                const text = textElement
                    ? textElement.textContent.trim()
                    : 'Semua Kategori';


                /* Update hidden select */
                if (categoryFilter) {
                    categoryFilter.value = value;
                }


                /* Update tulisan tombol */
                if (categoryLabel) {
                    categoryLabel.textContent = text;
                }


                /* Update active state */
                categoryOptions.forEach(function (item) {
                    item.classList.remove('active');
                });

                option.classList.add('active');


                /* Tutup dropdown */
                categoryMenu.classList.remove('is-open');

                categoryTrigger.classList.remove('is-open');

                categoryTrigger.setAttribute(
                    'aria-expanded',
                    'false'
                );


                /* Reset pagination + render */
                resetAndRender();

            });

        });


        /* Klik di luar dropdown */
        document.addEventListener('click', function (event) {

            if (!categoryDropdown.contains(event.target)) {

                categoryMenu.classList.remove('is-open');

                categoryTrigger.classList.remove('is-open');

                categoryTrigger.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }

        });

    }


    /* =========================================================
       FILTER DATA
    ========================================================= */

    function getFilteredRows() {

        const keyword = searchInput
            ? searchInput.value.toLowerCase().trim()
            : '';

        const selectedCategory = categoryFilter
            ? categoryFilter.value
            : '';


        return rows.filter(function (row) {

            const category = row.dataset.category || '';

            const searchData = row.dataset.search || '';

            const matchSearch =
                keyword === '' ||
                searchData.includes(keyword);

            const matchCategory =
                selectedCategory === '' ||
                category === selectedCategory;


            return matchSearch && matchCategory;

        });

    }


    /* =========================================================
       NOMOR HALAMAN
    ========================================================= */

    function getPageNumbers(current, last) {

        if (last <= 0) {
            return [];
        }

        const start = Math.max(1, current - 2);

        const end = Math.min(last, current + 2);

        const pages = [];

        for (let page = start; page <= end; page++) {
            pages.push(page);
        }

        return pages;

    }


    /* =========================================================
       RENDER PAGINATION
    ========================================================= */

    function renderPagination() {

        const total = filteredRows.length;

        const lastPage = Math.max(
            1,
            Math.ceil(total / BOOKS_PER_PAGE)
        );


        if (currentPage > lastPage) {
            currentPage = lastPage;
        }


        if (!paginationLinks || !paginationInfo || !pagination) {
            return;
        }


        paginationLinks.innerHTML = '';


        /* Tidak ada hasil */
        if (total === 0) {

            paginationInfo.innerHTML =
                'Menampilkan <strong>0</strong> - <strong>0</strong> dari <strong>0</strong> hasil';

            pagination.style.display = 'none';

            return;
        }


        pagination.style.display = 'flex';


        /* =====================================================
           PREVIOUS
        ====================================================== */

        const prevButton = document.createElement('button');

        prevButton.type = 'button';

        prevButton.className =
            'books-page-btn' +
            (currentPage === 1 ? ' disabled' : '');

        prevButton.innerHTML = '&laquo; Prev';


        if (currentPage > 1) {

            prevButton.addEventListener('click', function () {

                currentPage--;

                renderBooks();

            });

        }


        paginationLinks.appendChild(prevButton);


        /* =====================================================
           NOMOR HALAMAN
        ====================================================== */

        getPageNumbers(currentPage, lastPage).forEach(function (page) {

            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'books-page-btn' +
                (page === currentPage ? ' active' : '');

            button.textContent = page;


            button.addEventListener('click', function () {

                currentPage = page;

                renderBooks();

            });


            paginationLinks.appendChild(button);

        });


        /* =====================================================
           NEXT
        ====================================================== */

        const nextButton = document.createElement('button');

        nextButton.type = 'button';

        nextButton.className =
            'books-page-btn' +
            (currentPage === lastPage ? ' disabled' : '');

        nextButton.innerHTML = 'Next &raquo;';


        if (currentPage < lastPage) {

            nextButton.addEventListener('click', function () {

                currentPage++;

                renderBooks();

            });

        }


        paginationLinks.appendChild(nextButton);


        /* =====================================================
           INFO PAGINATION
        ====================================================== */

        const first =
            ((currentPage - 1) * BOOKS_PER_PAGE) + 1;

        const last =
            Math.min(
                currentPage * BOOKS_PER_PAGE,
                total
            );


        paginationInfo.innerHTML =
            'Menampilkan <strong>' +
            first +
            '</strong> - <strong>' +
            last +
            '</strong> dari <strong>' +
            total +
            '</strong> hasil';

    }


    /* =========================================================
       RENDER BUKU
    ========================================================= */

    function renderBooks() {

        filteredRows = getFilteredRows();

        const total = filteredRows.length;

        const lastPage = Math.max(
            1,
            Math.ceil(total / BOOKS_PER_PAGE)
        );


        if (currentPage > lastPage) {
            currentPage = lastPage;
        }


        /* Sembunyikan semua row */
        rows.forEach(function (row) {
            row.style.display = 'none';
        });


        /* Tampilkan row sesuai halaman */
        const start =
            (currentPage - 1) * BOOKS_PER_PAGE;

        const end =
            start + BOOKS_PER_PAGE;


        filteredRows
            .slice(start, end)
            .forEach(function (row) {

                row.style.display = '';

            });


        /* Update total */
        if (totalElement) {

            totalElement.textContent =
                'Total: ' + total + ' Buku';

        }


        /* Pesan tidak ada hasil */
        if (noResults) {

            noResults.style.display =
                total === 0 ? '' : 'none';

        }


        renderPagination();

    }


    /* =========================================================
       RESET + RENDER
    ========================================================= */

    function resetAndRender() {

        currentPage = 1;

        renderBooks();

    }


    /* =========================================================
       SEARCH
    ========================================================= */

    if (searchInput) {

        searchInput.addEventListener(
            'input',
            resetAndRender
        );


        searchInput.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    resetAndRender();

                }

            }
        );

    }


    /* =========================================================
       SEARCH BUTTON
    ========================================================= */

    if (searchButton) {

        searchButton.addEventListener(
            'click',
            resetAndRender
        );

    }


    /* =========================================================
       CATEGORY FILTER
    ========================================================= */

    if (categoryFilter) {

        categoryFilter.addEventListener(
            'change',
            resetAndRender
        );

    }


    /* =========================================================
       INITIAL RENDER
    ========================================================= */

    renderBooks();

});


/* =============================================================
   MODAL HAPUS BUKU
============================================================= */

let bookDeleteForm = null;


/* =============================================================
   BUKA MODAL
============================================================= */

function openBookDeleteModal(button) {

    bookDeleteForm =
        button.closest('.delete-book-form');

    if (!bookDeleteForm) {
        return;
    }


    /* ---------------------------------------------------------
       AMBIL BARIS BUKU
    --------------------------------------------------------- */

    const row =
        button.closest('tr');

    if (!row) {
        return;
    }


    /* ---------------------------------------------------------
       AMBIL JUDUL
    --------------------------------------------------------- */

    const titleElement =
        row.querySelector('td:nth-child(1) strong');

    const bookName =
        titleElement
            ? titleElement.textContent.trim()
            : '-';


    /* ---------------------------------------------------------
       AMBIL STOK
    --------------------------------------------------------- */

    const stockElement =
        row.querySelector('td:nth-child(5)');

    const stock =
        stockElement
            ? stockElement.textContent.trim()
            : '0';


    /* ---------------------------------------------------------
       MASUKKAN DATA KE MODAL
    --------------------------------------------------------- */

    const bookNameTarget =
        document.getElementById(
            'deleteBookName'
        );

    const stockTarget =
        document.getElementById(
            'deleteBookStock'
        );


    if (bookNameTarget) {
        bookNameTarget.textContent =
            bookName;
    }


    if (stockTarget) {
        stockTarget.textContent =
            stock + ' eksemplar';
    }


    /* ---------------------------------------------------------
       RESET ALASAN
    --------------------------------------------------------- */

    const reasonInput =
        document.getElementById(
            'bookDeleteReason'
        );

    if (reasonInput) {
        reasonInput.value = '';
    }


    /* ---------------------------------------------------------
       TAMPILKAN MODAL
    --------------------------------------------------------- */

    const modal =
        document.getElementById(
            'bookDeleteModal'
        );

    if (!modal) {
        return;
    }


    modal.classList.add('show');

    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    /* ---------------------------------------------------------
       KUNCI SCROLL
    --------------------------------------------------------- */

    document.body.style.overflow =
        'hidden';


    /* ---------------------------------------------------------
       FOCUS ALASAN
    --------------------------------------------------------- */

    if (reasonInput) {

        setTimeout(
            function () {

                reasonInput.focus();

            },
            100
        );

    }

}


/* =============================================================
   TUTUP MODAL
============================================================= */

function closeBookDeleteModal() {

    const modal =
        document.getElementById(
            'bookDeleteModal'
        );

    if (!modal) {
        return;
    }


    modal.classList.remove('show');

    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.style.overflow =
        '';


    bookDeleteForm = null;

}


/* =============================================================
   KONFIRMASI HAPUS BUKU
============================================================= */

function confirmBookDelete() {

    if (!bookDeleteForm) {
        return;
    }


    const reasonInput =
        document.getElementById(
            'bookDeleteReason'
        );

    if (!reasonInput) {
        return;
    }


    const reason =
        reasonInput.value.trim();


    /* ---------------------------------------------------------
       ALASAN WAJIB DIISI
    --------------------------------------------------------- */

    if (!reason) {

        reasonInput.focus();

        return;

    }


    /* ---------------------------------------------------------
       INPUT REASON KE FORM DELETE
    --------------------------------------------------------- */

    let reasonField =
        bookDeleteForm.querySelector(
            'input[name="reason"]'
        );


    if (!reasonField) {

        reasonField =
            document.createElement(
                'input'
            );

        reasonField.type =
            'hidden';

        reasonField.name =
            'reason';


        bookDeleteForm.appendChild(
            reasonField
        );

    }


    reasonField.value =
        reason;


    /* ---------------------------------------------------------
       SUBMIT
    --------------------------------------------------------- */

    bookDeleteForm.submit();

}


/* =============================================================
   EVENT MODAL
============================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /* -----------------------------------------------------
           ESC
        ----------------------------------------------------- */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape'
                ) {

                    closeBookDeleteModal();

                }

            }
        );


        /* -----------------------------------------------------
           KLIK BACKDROP
        ----------------------------------------------------- */

        const modal =
            document.getElementById(
                'bookDeleteModal'
            );


        if (modal) {

            modal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === this
                    ) {

                        closeBookDeleteModal();

                    }

                }
            );

        }

    }
);