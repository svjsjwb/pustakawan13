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
    | ELEMENT MODAL
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById('book-modal');

    const book =
        document.getElementById('book3D');

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
    | BOOK 3D ROTATION
    |--------------------------------------------------------------------------
    */

    if (book) {

        let isDragging = false;

        let rotX = 0;
        let rotY = -15;

        const defaultX = 0;
        const defaultY = -15;


        book.style.transform =
            'rotateX(0deg) rotateY(-15deg)';


        book.addEventListener(
            'mousedown',
            function () {

                isDragging = true;

                book.style.transition = 'none';

            }
        );


        document.addEventListener(
            'mouseup',
            function () {

                if (!isDragging) {
                    return;
                }

                isDragging = false;

                rotX = defaultX;
                rotY = defaultY;

                book.style.transition =
                    'transform .8s ease';

                book.style.transform =
                    `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

            }
        );


        document.addEventListener(
            'mousemove',
            function (e) {

                if (!isDragging) {
                    return;
                }

                rotY += e.movementX * 0.5;
                rotX -= e.movementY * 0.2;

                rotX = Math.max(
                    -40,
                    Math.min(40, rotX)
                );

                book.style.transform =
                    `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL BUKU
    |--------------------------------------------------------------------------
    |
    | Menggunakan EVENT DELEGATION.
    |
    | Jadi book-card yang muncul setelah AJAX
    | tetap bisa diklik.
    |
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const card =
                event.target.closest('.book-card');


            if (!card) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Kalau modal tidak tersedia,
            | jangan lakukan apa-apa.
            |--------------------------------------------------------------------------
            */

            if (!modal) {
                return;
            }


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

                modalTitle.textContent =
                    title;

            }


            if (modalBookTitle) {

                modalBookTitle.textContent =
                    title;

            }


            if (modalAuthor) {

                modalAuthor.textContent =
                    'Penulis: ' + author;

            }


            if (modalCategory) {

                modalCategory.textContent =
                    category;

            }


            if (modalStock) {

                modalStock.textContent =
                    stock;

            }


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


            if (modalDescription) {

                modalDescription.textContent =
                    description;

            }


            /*
            |--------------------------------------------------------------------------
            | COVER BUKU 3D
            |--------------------------------------------------------------------------
            */

            if (bookFront) {

                if (cover) {

                    bookFront.innerHTML = '';

                    bookFront.style.backgroundImage =
                        `url('${cover}')`;

                    bookFront.style.backgroundSize =
                        'cover';

                    bookFront.style.backgroundPosition =
                        'center';

                } else {

                    bookFront.style.backgroundImage =
                        '';

                    bookFront.innerHTML =
                        `<div class="book-top">
                            TIGA SERANGKAI
                        </div>

                        <div class="book-title" id="modalBookTitle">
                            ${title}
                        </div>

                        <div class="book-bottom">
                            PERPUSTAKAAN
                        </div>`;

                }

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

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove('open');

        document.body.style.overflow = '';

    }


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


    if (modal) {

        modal.addEventListener(
            'click',
            function (event) {

                if (event.target === modal) {
                    closeModal();
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

                closeModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CATALOG AJAX
    |--------------------------------------------------------------------------
    */

    const catalogForm =
        document.getElementById(
            'catalog-filter-form'
        );

    const catalogResults =
        document.getElementById(
            'catalog-results'
        );

    const catalogSearch =
        document.getElementById(
            'catalog-search'
        );


    let catalogSearchTimer = null;

    let catalogRequestController = null;


    /*
    |--------------------------------------------------------------------------
    | BUILD URL
    |--------------------------------------------------------------------------
    */

    function buildCatalogUrl(
        resetPage = true
    ) {

        if (!catalogForm) {
            return null;
        }


        const url =
            new URL(
                catalogForm.action,
                window.location.origin
            );


        const formData =
            new FormData(catalogForm);


        formData.forEach(
            function (value, key) {

                if (
                    value !== null &&
                    String(value).trim() !== ''
                ) {

                    url.searchParams.set(
                        key,
                        value
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Filter/search berubah → halaman 1
        |--------------------------------------------------------------------------
        */

        if (resetPage) {

            url.searchParams.delete(
                'page'
            );

        }


        return url;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SUBCATEGORY DARI RESPONSE
    |--------------------------------------------------------------------------
    */

    function updateSubcategoryFromResponse(
        parsedDocument
    ) {

        const currentSubcategory =
            document.getElementById(
                'catalog-subcategory'
            );


        const newSubcategory =
            parsedDocument.getElementById(
                'catalog-subcategory'
            );


        if (
            !currentSubcategory ||
            !newSubcategory
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Ganti option subcategory
        |--------------------------------------------------------------------------
        */

        currentSubcategory.innerHTML =
            newSubcategory.innerHTML;


        currentSubcategory.disabled =
            newSubcategory.disabled;

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD CATALOG AJAX
    |--------------------------------------------------------------------------
    */

    async function loadCatalog(
        urlString,
        pushHistory = true,
        keepSearchFocus = false
    ) {

        if (
            !catalogResults ||
            !catalogForm
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SEARCH
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById(
                'catalog-search'
            );


        const currentSearch =
            searchInput
                ? searchInput.value
                : '';


        /*
        |--------------------------------------------------------------------------
        | SIMPAN POSISI CURSOR
        |--------------------------------------------------------------------------
        */

        const cursorPosition =
            searchInput &&
            document.activeElement === searchInput
                ? searchInput.selectionStart
                : null;


        /*
        |--------------------------------------------------------------------------
        | BATALKAN REQUEST LAMA
        |--------------------------------------------------------------------------
        */

        if (catalogRequestController) {

            catalogRequestController.abort();

        }


        catalogRequestController =
            new AbortController();


        catalogResults.classList.add(
            'catalog-loading'
        );


        try {

            const url =
                new URL(
                    urlString,
                    window.location.origin
                );


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
                            catalogRequestController.signal
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data katalog.'
                );

            }


            const html =
                await response.text();


            /*
            |--------------------------------------------------------------------------
            | PARSE RESPONSE
            |--------------------------------------------------------------------------
            */

            const parsed =
                new DOMParser()
                    .parseFromString(
                        html,
                        'text/html'
                    );


            /*
            |--------------------------------------------------------------------------
            | HASIL KATALOG BARU
            |--------------------------------------------------------------------------
            */

            const newResults =
                parsed.getElementById(
                    'catalog-results'
                );


            if (!newResults) {

                throw new Error(
                    'Element #catalog-results tidak ditemukan pada response.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | GANTI HANYA HASIL
            |--------------------------------------------------------------------------
            */

            catalogResults.innerHTML =
                newResults.innerHTML;


            /*
            |--------------------------------------------------------------------------
            | UPDATE JUMLAH BUKU
            |--------------------------------------------------------------------------
            */

            const currentCount =
                document.getElementById(
                    'catalog-count'
                );


            const newCount =
                parsed.getElementById(
                    'catalog-count'
                );


            if (
                currentCount &&
                newCount
            ) {

                currentCount.textContent =
                    newCount.textContent;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE SUBKATEGORI
            |--------------------------------------------------------------------------
            */

            updateSubcategoryFromResponse(
                parsed
            );


            /*
            |--------------------------------------------------------------------------
            | SEARCH TETAP DI INPUT
            |--------------------------------------------------------------------------
            |
            | Karena input tidak diganti AJAX,
            | sebenarnya value sudah aman.
            |
            | Bagian ini hanya memastikan value
            | tetap sama.
            |--------------------------------------------------------------------------
            */

            if (searchInput) {

                searchInput.value =
                    currentSearch;

            }


            /*
            |--------------------------------------------------------------------------
            | KEEP SEARCH FOCUS
            |--------------------------------------------------------------------------
            */

            if (
                keepSearchFocus &&
                searchInput
            ) {

                searchInput.focus();


                const position =
                    cursorPosition !== null
                        ? Math.min(
                            cursorPosition,
                            searchInput.value.length
                        )
                        : searchInput.value.length;


                searchInput.setSelectionRange(
                    position,
                    position
                );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE URL
            |--------------------------------------------------------------------------
            */

            if (pushHistory) {

                window.history.pushState(
                    {
                        catalog: true
                    },
                    '',
                    url.toString()
                );

            }

        } catch (error) {

            /*
            |--------------------------------------------------------------------------
            | AbortError bukan error.
            |--------------------------------------------------------------------------
            */

            if (
                error.name !==
                'AbortError'
            ) {

                console.error(
                    'Catalog AJAX Error:',
                    error
                );

            }

        } finally {

            catalogResults.classList.remove(
                'catalog-loading'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | LIVE SEARCH
    |--------------------------------------------------------------------------
    |
    | FEELING SEPERTI SEARCH ANGGOTA
    |
    | ketik → tunggu 300ms → AJAX
    |
    |--------------------------------------------------------------------------
    */

    if (
        catalogSearch &&
        catalogForm
    ) {

        catalogSearch.addEventListener(
            'input',
            function () {

                clearTimeout(
                    catalogSearchTimer
                );


                catalogSearchTimer =
                    setTimeout(
                        function () {

                            const url =
                                buildCatalogUrl(
                                    true
                                );


                            if (!url) {
                                return;
                            }


                            loadCatalog(
                                url.toString(),
                                true,
                                true
                            );

                        },
                        300
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    if (catalogForm) {

        catalogForm.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                clearTimeout(
                    catalogSearchTimer
                );


                const url =
                    buildCatalogUrl(
                        true
                    );


                if (!url) {
                    return;
                }


                loadCatalog(
                    url.toString(),
                    true,
                    false
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FILTER CHANGE
    |--------------------------------------------------------------------------
    */

    if (catalogForm) {

        catalogForm.addEventListener(
            'change',
            function (event) {

                const target =
                    event.target;


                /*
                |--------------------------------------------------------------------------
                | Search bukan filter change.
                |--------------------------------------------------------------------------
                */

                if (
                    target.name ===
                    'search'
                ) {

                    return;

                }


                if (
                    ![
                        'category',
                        'subcategory',
                        'status',
                    ].includes(
                        target.name
                    )
                ) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | KATEGORI BERUBAH
                |--------------------------------------------------------------------------
                */

                if (
                    target.name ===
                    'category'
                ) {

                    const subcategory =
                        document.getElementById(
                            'catalog-subcategory'
                        );


                    if (subcategory) {

                        subcategory.value = '';

                        subcategory.disabled =
                            !target.value;

                    }

                }


                const url =
                    buildCatalogUrl(
                        true
                    );


                if (!url) {
                    return;
                }


                loadCatalog(
                    url.toString(),
                    true,
                    false
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | PAGINATION AJAX
    |--------------------------------------------------------------------------
    */

    if (catalogResults) {

        catalogResults.addEventListener(
            'click',
            function (event) {

                const link =
                    event.target.closest(
                        '.catalog-pagination a'
                    );


                if (!link) {
                    return;
                }


                event.preventDefault();


                loadCatalog(
                    link.href,
                    true,
                    false
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BACK / FORWARD BROWSER
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'popstate',
        function () {

            /*
            |--------------------------------------------------------------------------
            | Sinkronkan search dengan URL
            |--------------------------------------------------------------------------
            */

            if (catalogSearch) {

                const url =
                    new URL(
                        window.location.href
                    );


                catalogSearch.value =
                    url.searchParams.get(
                        'search'
                    ) || '';

            }


            loadCatalog(
                window.location.href,
                false,
                false
            );

        }
    );

});