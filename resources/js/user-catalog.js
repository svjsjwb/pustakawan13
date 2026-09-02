/**
 * User Catalog Interactions & Modal Integration
 * Perpustakaan Tiga Serangkai
 */

document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       ELEMENT SELECTORS
    ====================================================== */
    const modal = document.getElementById('userBookModal');
    const closeBtn = document.getElementById('userBookModalClose');
    const closeActionBtn = document.getElementById('modalCloseActionBtn');
    const overlay = document.getElementById('userBookModalOverlay');

    const modalTitle = document.getElementById('modalBookTitle');
    const modalAuthor = document.getElementById('modalBookAuthor');
    const modalCategory = document.getElementById('modalBookCategory');
    const modalStock = document.getElementById('modalBookStock');
    const modalPublisher = document.getElementById('modalBookPublisher');
    const modalYear = document.getElementById('modalBookYear');
    const modalIsbn = document.getElementById('modalBookIsbn');
    const modalRack = document.getElementById('modalBookRack');
    const modalCallNumber = document.getElementById('modalBookCallNumber');
    const modalDescription = document.getElementById('modalBookDescription');

    const book3dFallback = document.getElementById('book3dFallback');
    const book3dFallbackTitle = document.getElementById('book3dFallbackTitle');
    const book3dBackTitle = document.getElementById('book3dBackTitle');
    const book3dCover = document.getElementById('book3dCover');


    /* =====================================================
       OPEN MODAL HANDLER
    ====================================================== */
    document.querySelectorAll('.user-book-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const title = this.dataset.title || 'Buku';
            const author = this.dataset.author || 'Penulis tidak dicantumkan';
            const category = this.dataset.category || 'Koleksi';
            const stock = parseInt(this.dataset.stock, 10) || 0;
            const publisher = this.dataset.publisher || '-';
            const year = this.dataset.year || '-';
            const isbn = this.dataset.isbn || '-';
            const rack = this.dataset.rack || '-';
            const callNumber = this.dataset.callNumber || '-';
            const description = this.dataset.description || 'Deskripsi atau ringkasan buku belum tersedia saat ini.';
            const cover = this.dataset.cover || '';

            // Update Text
            if (modalTitle) modalTitle.textContent = title;
            if (modalAuthor) modalAuthor.textContent = 'Oleh ' + author;
            if (modalCategory) modalCategory.textContent = category;
            if (modalStock) {
                modalStock.textContent = stock > 0 ? `${stock} Eksemplar Tersedia` : 'Sedang Dipinjam';
                modalStock.style.color = stock > 0 ? '#15803d' : '#b91c1c';
            }
            if (modalPublisher) modalPublisher.textContent = publisher;
            if (modalYear) modalYear.textContent = year;
            if (modalIsbn) modalIsbn.textContent = isbn;
            if (modalRack) modalRack.textContent = rack;
            if (modalCallNumber) modalCallNumber.textContent = callNumber;
            if (modalDescription) modalDescription.textContent = description;

            // 3D Titles
            if (book3dFallbackTitle) book3dFallbackTitle.textContent = title;
            if (book3dBackTitle) book3dBackTitle.textContent = title;

            // 3D Cover Image
            if (cover && book3dCover) {
                book3dCover.src = cover;
                book3dCover.alt = title;

                book3dCover.onload = function () {
                    if (book3dFallback) book3dFallback.style.display = 'none';
                    book3dCover.classList.add('loaded');
                };

                book3dCover.onerror = function () {
                    book3dCover.classList.remove('loaded');
                    if (book3dFallback) book3dFallback.style.display = 'flex';
                };
            } else {
                if (book3dCover) {
                    book3dCover.removeAttribute('src');
                    book3dCover.classList.remove('loaded');
                }
                if (book3dFallback) book3dFallback.style.display = 'flex';
            }

            // Open Modal
            if (modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        });
    });


    /* =====================================================
       CLOSE MODAL HANDLER
    ====================================================== */
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeActionBtn) closeActionBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('open')) {
            closeModal();
        }
    });

});
