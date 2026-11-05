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

                if (filterForm) {
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
       SEARCH
    ========================================================= */

    if (searchInput && filterForm) {
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                filterForm.submit();
            }
        });
    }

    if (searchButton && filterForm) {
        searchButton.addEventListener('click', function () {
            filterForm.submit();
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

    const modal = document.getElementById('bookDeleteModal');
    const modalBookName = document.getElementById('modalBookName');
    const reasonInput = document.getElementById('bookDeleteReason');

    if (modalBookName) {
        modalBookName.textContent = bookName;
    }

    if (reasonInput) {
        reasonInput.value = '';
    }

    if (modal) {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
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
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
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

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeBookDeleteModal();
    }
});
