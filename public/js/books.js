document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('books-search');
    const searchButton = document.getElementById('books-search-button');
    const categoryFilter = document.getElementById('books-filter-kategori');
    const filterForm = document.getElementById('books-filter-form');

    const categoryDropdown = document.getElementById('books-category-dropdown');
    const categoryTrigger = document.getElementById('books-category-trigger');
    const categoryMenu = document.getElementById('books-category-menu');
    const categoryLabel = document.getElementById('books-category-label');

    const categoryOptions = Array.from(
        document.querySelectorAll('.books-category-option')
    );

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
                const text = textElement ? textElement.textContent.trim() : 'Semua Kategori';

                if (categoryFilter) {
                    categoryFilter.value = value;
                }

                if (categoryLabel) {
                    categoryLabel.textContent = text;
                }

                categoryOptions.forEach(function (item) {
                    item.classList.remove('active');
                });
                option.classList.add('active');

                categoryMenu.classList.remove('is-open');
                categoryTrigger.classList.remove('is-open');
                categoryTrigger.setAttribute('aria-expanded', 'false');

                if (typeof performLiveSearch === 'function') {
                    performLiveSearch();
                } else if (filterForm) {
                    filterForm.submit();
                }
            });
        });

        /* Klik di luar dropdown */
        document.addEventListener('click', function (event) {
            if (!categoryDropdown.contains(event.target)) {
                categoryMenu.classList.remove('is-open');
                categoryTrigger.classList.remove('is-open');
                categoryTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* =========================================================
       SEARCH (AUTOMATIC LIVE SEARCH)
    ========================================================= */

    let searchDebounceTimer = null;

    window.performLiveSearch = function () {
        if (!filterForm) return;

        const url = new URL(filterForm.action || window.location.href, window.location.origin);
        const formData = new FormData(filterForm);
        for (const [key, value] of formData.entries()) {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        }

        // Update URL browser tanpa reload
        window.history.replaceState({}, '', url.toString());

        const tbody = document.getElementById('books-table-body');
        if (tbody) {
            tbody.style.opacity = '0.5';
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newTbody = doc.getElementById('books-table-body');
            const currentTbody = document.getElementById('books-table-body');
            if (newTbody && currentTbody) {
                currentTbody.innerHTML = newTbody.innerHTML;
            }

            const newPagination = doc.getElementById('books-pagination');
            const currentPagination = document.getElementById('books-pagination');
            if (currentPagination) {
                if (newPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                    currentPagination.style.display = '';
                } else {
                    currentPagination.style.display = 'none';
                }
            }
        })
        .catch(err => {
            console.error('Live search error:', err);
        })
        .finally(() => {
            if (tbody) {
                tbody.style.opacity = '1';
            }
        });
    };

    if (searchInput && filterForm) {
        searchInput.addEventListener('input', function () {
            // Filter instan lokal pada baris yang sedang tampil
            const keyword = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#books-table-body tr:not(#no-search-results)');
            let anyMatch = false;

            if (keyword) {
                rows.forEach(function (row) {
                    const searchData = (row.dataset.search || row.textContent).toLowerCase();
                    const matches = searchData.includes(keyword);
                    row.style.display = matches ? '' : 'none';
                    if (matches) anyMatch = true;
                });
            } else {
                rows.forEach(r => r.style.display = '');
            }

            // Jalankan pencarian server setelah jeda ketik 350ms
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(window.performLiveSearch, 350);
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(searchDebounceTimer);
                window.performLiveSearch();
            }
        });
    }

    if (searchButton && filterForm) {
        searchButton.addEventListener('click', function (event) {
            event.preventDefault();
            clearTimeout(searchDebounceTimer);
            window.performLiveSearch();
        });
    }

});

/* =============================================================
   MODAL HAPUS BUKU
============================================================= */

let bookDeleteForm = null;

function openBookDeleteModal(button) {
    bookDeleteForm = button.closest('.delete-book-form');
    if (!bookDeleteForm) {
        return;
    }

    const row = button.closest('tr');
    if (!row) {
        return;
    }

    const titleElement = row.querySelector('td:nth-child(1) strong');
    const bookName = titleElement ? titleElement.textContent.trim() : 'buku ini';
    const stockElement = row.querySelector('td:nth-child(5)');
    const bookStock = stockElement ? stockElement.textContent.trim() : '-';

    const modal = document.getElementById('bookDeleteModal');
    const modalBookName = document.getElementById('deleteBookName') || document.getElementById('modalBookName');
    const modalBookStock = document.getElementById('deleteBookStock');
    const reasonInput = document.getElementById('bookDeleteReason');

    if (modalBookName) {
        modalBookName.textContent = bookName;
    }

    if (modalBookStock) {
        modalBookStock.textContent = bookStock;
    }

    if (reasonInput) {
        reasonInput.value = '';
    }

    if (modal) {
        modal.classList.add('show', 'is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (reasonInput) {
            setTimeout(function () {
                reasonInput.focus();
            }, 100);
        }
    }
}

function closeBookDeleteModal() {
    const modal = document.getElementById('bookDeleteModal');
    if (modal) {
        modal.classList.remove('show', 'is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    bookDeleteForm = null;
}

function confirmBookDelete() {
    const reasonInput = document.getElementById('bookDeleteReason');
    const reason = reasonInput ? reasonInput.value.trim() : '';

    if (!reason) {
        alert('Silakan masukkan alasan penarikan buku.');
        if (reasonInput) {
            reasonInput.focus();
        }
        return;
    }

    if (!bookDeleteForm) {
        return;
    }

    let hiddenInput = bookDeleteForm.querySelector('input[name="reason"]');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'reason';
        bookDeleteForm.appendChild(hiddenInput);
    }
    hiddenInput.value = reason;

    let legacyInput = bookDeleteForm.querySelector('input[name="withdrawal_reason"]');
    if (!legacyInput) {
        legacyInput = document.createElement('input');
        legacyInput.type = 'hidden';
        legacyInput.name = 'withdrawal_reason';
        bookDeleteForm.appendChild(legacyInput);
    }
    legacyInput.value = reason;

    bookDeleteForm.submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const bookDeleteModal = document.getElementById('bookDeleteModal');
    if (bookDeleteModal) {
        bookDeleteModal.addEventListener('click', function (event) {
            if (event.target === bookDeleteModal) {
                closeBookDeleteModal();
            }
        });
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeBookDeleteModal();
    }
});
