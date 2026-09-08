{{-- =========================================================
     NAVIGASI EDIT BUKU
========================================================= --}}

<div class="book-edit-navigation">

    <a
        href="{{ route('books.edit', $book) }}"
        class="book-edit-tab {{ request()->routeIs('books.edit') ? 'active' : '' }}">

        <span class="book-edit-tab-icon">
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">

                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                <path d="M8 6h8"/>
                <path d="M8 10h8"/>
            </svg>
        </span>

        <span>
            <strong>Edit Data Buku</strong>
            <small>Informasi utama buku</small>
        </span>

    </a>


    <a
        href="{{ route('books.copies.index', $book) }}"
        class="book-edit-tab {{ request()->routeIs('books.copies.*') ? 'active' : '' }}">

        <span class="book-edit-tab-icon">
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">

                <rect x="4" y="4" width="16" height="16" rx="2"/>
                <path d="M8 8h8"/>
                <path d="M8 12h8"/>
                <path d="M8 16h5"/>
            </svg>
        </span>

        <span>
            <strong>Edit Eksemplar</strong>
            <small>Kelola salinan fisik buku</small>
        </span>

    </a>

</div>