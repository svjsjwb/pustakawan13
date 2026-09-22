<div class="topbar">
    <div>
        <div class="page-eyebrow" id="page-eyebrow">SmartLibrary Pro</div>
        <div class="page-title" id="page-title">Dashboard</div>
    </div>
    <div class="search-global">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8" />
            <path d="M21 21l-4.3-4.3" />
        </svg>
        <input type="text" id="global-search" placeholder="Cari judul, pengarang, ISBN, anggota…" oninput="globalSearch(this.value)">
    </div>
    <button class="icon-btn" title="Notifikasi" onclick="nav('circulation')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
        </svg>
        <span class="dot-alert"></span>
    </button>
    <a href="{{ route('books.create') }}" class="btn-add-top" id="topbar-cta">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <path d="M12 5v14M5 12h14" />
        </svg>
        Tambah Buku
    </a>
</div>