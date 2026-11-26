@extends('layouts.user')
@section('title', 'Favorit – Perpustakaan Tiga Serangkai')
@section('page-title', 'Favorit')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-catalog.css') }}?v={{ file_exists(public_path('css/user-catalog.css')) ? filemtime(public_path('css/user-catalog.css')) : time() }}">
<style>
/* Empty State Card - Modern, Elegan & Human-Crafted */
.ufav-empty-card {
    position: relative;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 44px 52px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
    display: grid;
    grid-template-columns: 240px 1fr;
    align-items: center;
    gap: 48px;
    overflow: hidden;
    margin-top: 6px;
}

.ufav-illustration-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
}

.ufav-illustration-svg {
    display: block;
    max-width: 100%;
    height: auto;
}

.ufav-empty-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.ufav-empty-kicker {
    font-size: 11px;
    font-weight: 800;
    color: #0f766e;
    letter-spacing: 1.3px;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.ufav-empty-title {
    font-size: clamp(22px, 2.2vw, 27px);
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px 0;
    line-height: 1.25;
    letter-spacing: -0.02em;
}

.ufav-empty-desc {
    font-size: 14.5px;
    color: #64748b;
    margin: 0 0 24px 0;
    line-height: 1.55;
    max-width: 440px;
}

.ufav-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #1d5c59;
    color: #ffffff !important;
    font-size: 13.5px;
    font-weight: 700;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none !important;
    box-shadow: 0 4px 14px rgba(29, 92, 89, 0.24);
    transition: all .2s ease;
}

.ufav-empty-btn:hover {
    background: #164745;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(29, 92, 89, 0.32);
    color: #ffffff !important;
}

.ufav-empty-arrow {
    font-size: 15px;
    font-weight: 700;
    line-height: 1;
    transition: transform .2s ease;
}

.ufav-empty-btn:hover .ufav-empty-arrow {
    transform: translateX(3px);
}

@media (max-width: 820px) {
    .ufav-empty-card {
        grid-template-columns: 1fr;
        padding: 36px 24px;
        gap: 28px;
        text-align: center;
    }

    .ufav-empty-content {
        align-items: center;
    }
}

/* Grid Favorites Jika Ada Buku */
.ufav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 18px; }
.ufav-card { 
    background: #fff; 
    border: 1px solid var(--line); 
    border-radius: 16px; 
    overflow: hidden; 
    box-shadow: var(--shadow); 
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.28s ease, box-shadow 0.22s ease; 
}
.ufav-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }

/* Animasi Hapus Kartu (Smooth Exit) */
.ufav-card-exit {
    transform: scale(.94) translateY(-10px) rotate(-.6deg) !important;
    opacity: 0 !important;
    pointer-events: none !important;
    filter: blur(2px);
}

/* Transisi Empty State */
.ufav-hidden {
    display: none !important;
}

.ufav-fade-in {
    animation: ufavEmptyFadeIn 0.42s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes ufavEmptyFadeIn {
    0% {
        opacity: 0;
        transform: translateY(18px) scale(0.97);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.ufav-cover { aspect-ratio:2/3; background:linear-gradient(135deg,#1d5c59,#287879); position:relative; overflow:hidden; }
.ufav-cover img { width:100%; height:100%; object-fit:cover; display:block; }
.ufav-cover-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:rgba(255,255,255,.8); text-align:center; padding:12px; box-sizing:border-box; }
.ufav-badge { position:absolute; top:8px; left:8px; background:var(--primary); color:#fff; font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; }
.ufav-info { padding:13px 14px 14px; }
.ufav-cat  { font-size:9.5px; font-weight:700; letter-spacing:.07em; color:var(--primary); text-transform:uppercase; }
.ufav-title { margin:4px 0 3px; font-size:13.5px; font-weight:700; color:var(--ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.ufav-author { margin:0 0 12px; font-size:12px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ufav-actions { display:flex; gap:8px; }
.ufav-remove-btn { 
    flex: 1; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 5px; 
    padding: 7px; 
    border-radius: 8px; 
    border: 1px solid var(--line); 
    background: #fff; 
    font-size: 12px; 
    font-weight: 600; 
    color: var(--danger); 
    cursor: pointer; 
    transition: all .2s; 
}
.ufav-remove-btn:hover { background: var(--danger-soft); border-color: var(--danger); }
.ufav-remove-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.ufav-remove-btn svg { width:13px; height:13px; }

/* Refined collection cards */
.ufav-page-shell { width:min(100% - 40px,1180px); margin:0 auto; padding:32px 0 60px; }
.ufav-page-header { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; margin-bottom:26px; }
.ufav-heading h1 { margin:0; color:#172b36; font-size:clamp(28px,3vw,36px); line-height:1.15; letter-spacing:-.035em; }
.ufav-kicker { margin:0 0 7px; color:#0f766e; font-size:11px; font-weight:800; letter-spacing:.12em; }
.ufav-summary { margin:10px 0 0; color:#64748b; font-size:14px; }
.ufav-add-link { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 16px; border:1px solid #cbdedb; border-radius:10px; color:#176b66; background:#fff; text-decoration:none; font-size:13px; font-weight:700; transition:background .18s ease,border-color .18s ease,transform .18s ease; }
.ufav-add-link:hover { background:#f0faf8; border-color:#9bc9c3; transform:translateY(-1px); }
.ufav-grid { grid-template-columns:repeat(auto-fill,minmax(min(100%,260px),280px)); justify-content:start; gap:18px; }
.ufav-card { display:flex; flex-direction:column; min-width:0; border:1px solid #e1e8e7; border-radius:14px; box-shadow:0 2px 8px rgba(15,23,42,.035); transition:transform .24s ease,border-color .2s ease,box-shadow .22s ease,opacity .34s ease,filter .34s ease; }
.ufav-card:hover { transform:translateY(-3px); border-color:#bfd8d4; box-shadow:0 8px 22px rgba(20,53,51,.08); }
.ufav-card-open { display:flex; flex:1; flex-direction:column; min-width:0; color:inherit; text-decoration:none; outline-offset:-3px; cursor:pointer; }
.ufav-card-open:focus-visible { outline:3px solid #70b8ac; border-radius:12px; }
.ufav-cover { height:300px; aspect-ratio:auto; display:flex; align-items:center; justify-content:center; padding:8px; background:#f4f7f6; }
.ufav-cover img { object-fit:contain; transition:transform .28s ease; }
.ufav-card:hover .ufav-cover img,.ufav-card-open:focus-visible .ufav-cover img { transform:scale(1.035); }
.ufav-cover::after { content:'Lihat detail'; position:absolute; right:12px; bottom:12px; padding:7px 10px; border:1px solid rgba(212,231,227,.9); border-radius:8px; background:rgba(255,255,255,.96); color:#176b66; font-size:11px; font-weight:700; opacity:0; transform:translateY(4px); transition:opacity .18s ease,transform .18s ease; pointer-events:none; }
.ufav-card:hover .ufav-cover::after,.ufav-card-open:focus-visible .ufav-cover::after { opacity:1; transform:translateY(0); }
.ufav-cover-ph { border:1px dashed #cbd5d1; border-radius:8px; background:#edf4f2; color:#52726e; font-size:14px; }
.ufav-cover-ph[hidden] { display:none !important; }
.ufav-badge { top:12px; left:12px; padding:5px 9px; font-size:11px; box-shadow:0 1px 3px rgba(15,23,42,.12); }
.ufav-info { display:flex; flex:1; flex-direction:column; padding:15px 17px 12px; }
.ufav-cat { width:fit-content; max-width:100%; margin-bottom:7px; padding:4px 8px; border:1px solid #d4e7e3; border-radius:999px; background:#f0f8f6; color:#176b66; font-size:10px; letter-spacing:.04em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ufav-title { margin:0 0 5px; color:#172b36; font-size:16px; line-height:1.35; }
.ufav-author { margin:0; color:#667781; font-size:13px; line-height:1.45; }
.ufav-stock { margin-top:13px; color:#176b66; font-size:12px; font-weight:700; }
.ufav-stock.is-empty { color:#b45345; }
.ufav-actions { padding:0 17px 16px; }
.ufav-remove-btn { padding:9px; border:1px solid #eadad6; border-radius:9px; color:#a84b3f; font-weight:650; }
.ufav-remove-btn:hover { background:#fff7f5; border-color:#d6a49d; }
.ufav-remove-btn:active { transform:scale(.98); }
.ufav-remove-btn span { transition:color .18s ease; }
.ufav-remove-btn.is-loading { background:#fff7f5; border-color:#d6a49d; cursor:wait; }
.ufav-remove-btn.is-loading svg { animation:ufavTrashWiggle .42s ease-in-out infinite alternate; }
.ufav-remove-btn.is-loading span { color:#8e4037; }
@keyframes ufavTrashWiggle { from { transform:rotate(-9deg) scale(.94); } to { transform:rotate(9deg) scale(1.04); } }
@media (max-width:620px) {
    .ufav-page-shell { width:min(100% - 28px,520px); padding:24px 0 44px; }
    .ufav-page-header { align-items:flex-start; flex-direction:column; gap:16px; margin-bottom:22px; }
    .ufav-add-link { width:100%; }
    .ufav-grid { grid-template-columns:minmax(0,320px); justify-content:center; gap:14px; }
    .ufav-cover { height:280px; }
}
[data-theme="dark"] .ufav-heading h1 { color:#e7efef; }
[data-theme="dark"] .ufav-summary,[data-theme="dark"] .ufav-author { color:#a3b4b8; }
[data-theme="dark"] .ufav-card,[data-theme="dark"] .ufav-add-link,[data-theme="dark"] .ufav-remove-btn { background:#162625; border-color:#2b4442; }
[data-theme="dark"] .ufav-card:hover { border-color:#47736e; }
[data-theme="dark"] .ufav-cover { background:#1d302f; }
[data-theme="dark"] .ufav-title { color:#e7efef; }
[data-theme="dark"] .ufav-cat { background:#1d3633; border-color:#365650; color:#9ed8cd; }
[data-theme="dark"] .ufav-stock { color:#75d2b6; }
[data-theme="dark"] .ufav-stock.is-empty { color:#f2a49a; }
[data-theme="dark"] .ufav-remove-btn { color:#f0a49a; }
[data-theme="dark"] .ufav-remove-btn:hover { background:#2b2423; border-color:#84564f; }
[data-theme="dark"] .ufav-cover::after { background:rgba(22,38,37,.96); border-color:#365650; color:#9ed8cd; }
@media (prefers-reduced-motion:reduce) { .ufav-card,.ufav-cover img,.ufav-cover::after,.ufav-remove-btn,.ufav-remove-btn svg { transition:none; animation:none !important; } }
</style>
@endpush

@section('content')

<div class="ufav-page-shell">
<header class="ufav-page-header">
    <div class="ufav-heading">
        <p class="ufav-kicker">KOLEKSI SAYA</p>
        <h1>Favorit Saya</h1>
        <p class="ufav-summary"><span id="favCountNum">{{ $books->count() }}</span> buku tersimpan</p>
    </div>
    <a href="{{ route('user.catalog') }}" class="ufav-add-link" id="favHeaderAddBtn">+ Tambah dari Katalog</a>
</header>

{{-- Container Empty State (Tampil jika kosong, atau muncul dengan animasi saat buku terakhir dihapus) --}}
<div id="favEmptyStateContainer" class="{{ $books->isEmpty() ? '' : 'ufav-hidden' }}">
    <div class="ufav-empty-card">
        <div class="ufav-illustration-wrap">
            <svg class="ufav-illustration-svg" width="220" height="190" viewBox="0 0 220 190" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Soft Ambient Backdrop -->
                <circle cx="110" cy="95" r="82" fill="#f0fdfa"/>
                <circle cx="110" cy="95" r="60" fill="#e6f7f4"/>
                
                <!-- Ground Contact Shadow -->
                <ellipse cx="110" cy="156" rx="66" ry="7" fill="#0f172a" fill-opacity="0.05"/>
                <ellipse cx="108" cy="154" rx="48" ry="4.5" fill="#0f172a" fill-opacity="0.07"/>

                <!-- Book Composition -->
                <g transform="translate(18, 6)">
                    <!-- Base Foundation Book (Laying Flat) -->
                    <g id="book-base">
                        <!-- Bottom Cover -->
                        <path d="M42 124 L128 142 L152 134 L66 116 Z" fill="#134e4a"/>
                        <!-- Pages Block Thickness -->
                        <path d="M45 122 L127 139 L127 129 L45 112 Z" fill="#fcfaf6"/>
                        <path d="M127 139 L150 131 L150 121 L127 129 Z" fill="#e2ded6"/>
                        <!-- Top Cover of Base Book -->
                        <path d="M40 112 L126 129 L150 121 L64 104 Z" fill="#1d5c59"/>
                        <!-- Spine Edge -->
                        <path d="M40 112 L42 124 L66 128 L64 116 Z" fill="#134e4a"/>
                    </g>

                    <!-- Main Hardcover Book (Gracefully Resting at Angle) -->
                    <g id="book-main">
                        <!-- Inter-book Soft Contact Shadow -->
                        <ellipse cx="94" cy="114" rx="40" ry="6" fill="#000000" fill-opacity="0.12"/>

                        <!-- Hardcover Back -->
                        <rect x="52" y="44" width="76" height="100" rx="4" fill="#0f3d3a" transform="rotate(-12 52 44)"/>

                        <!-- Book Pages Block -->
                        <g transform="rotate(-12 52 44)">
                            <rect x="58" y="46" width="68" height="96" rx="2" fill="#fcfaf6"/>
                            <!-- Fine Paper Edge Lines -->
                            <line x1="124" y1="52" x2="124" y2="138" stroke="#e6e1d8" stroke-width="1.2"/>
                            <line x1="122" y1="50" x2="122" y2="140" stroke="#e6e1d8" stroke-width="1.2"/>
                            <line x1="120" y1="48" x2="120" y2="141" stroke="#e6e1d8" stroke-width="1.2"/>
                        </g>

                        <!-- Hardcover Front -->
                        <g transform="rotate(-12 52 44)">
                            <rect x="50" y="42" width="74" height="100" rx="4" fill="#1d5c59"/>
                            <!-- Spine Band -->
                            <rect x="50" y="42" width="10" height="100" rx="2" fill="#154946"/>
                            <line x1="60" y1="42" x2="60" y2="142" stroke="#0f3d3a" stroke-width="1"/>
                            
                            <!-- Elegant Debossed Frame -->
                            <rect x="66" y="52" width="50" height="80" rx="2" fill="none" stroke="#2dd4bf" stroke-width="1" stroke-opacity="0.35"/>
                            
                            <!-- Debossed Monogram / Heart Emblem -->
                            <circle cx="91" cy="92" r="13" fill="#144643" fill-opacity="0.6"/>
                            <path d="M91 96 C87.8 93.7 85.5 91.4 85.5 89 C85.5 87.4 86.8 86.2 88.4 86.2 C89.4 86.2 90.3 86.8 91 87.7 C91.7 86.8 92.6 86.2 93.6 86.2 C95.2 86.2 96.5 87.4 96.5 89 C96.5 91.4 94.2 93.7 91 96 Z" fill="#2dd4bf"/>
                        </g>

                        <!-- Silk Bookmark Ribbon -->
                        <g transform="rotate(-12 52 44)">
                            <!-- Top Headband Ribbon -->
                            <path d="M85 36 L94 36 L94 44 L85 44 Z" fill="#c27803"/>
                            <!-- Bottom Ribbon Tails with Notch -->
                            <path d="M85 138 L94 138 L94 154 L89.5 149 L85 154 Z" fill="#d97706"/>
                            <path d="M85 138 L88 138 L88 151 L85 154 Z" fill="#b45309"/>
                        </g>
                    </g>

                    <!-- Refined Floating Heart Badge (Clean Modern Glassmorphic Pill) -->
                    <g transform="translate(126, 38)">
                        <rect x="0" y="0" width="38" height="38" rx="12" fill="#ffffff" stroke="#ccfbf1" stroke-width="1.5"/>
                        <path d="M19 25 C14.5 22 11.5 19 11.5 16 C11.5 13.8 13.2 12.2 15.4 12.2 C16.8 12.2 18.1 13 19 14.1 C19.9 13 21.2 12.2 22.6 12.2 C24.8 12.2 26.5 13.8 26.5 16 C26.5 19 23.5 22 19 25 Z" fill="#0f766e"/>
                    </g>
                </g>
            </svg>
        </div>

        <div class="ufav-empty-content">
            <div class="ufav-empty-kicker">BELUM ADA BUKU FAVORIT</div>
            <h2 class="ufav-empty-title">Koleksi favoritmu masih kosong</h2>
            <p class="ufav-empty-desc">Simpan buku-buku favorit dari Katalog agar mudah ditemukan kembali.</p>
            <a href="{{ route('user.catalog') }}" class="ufav-empty-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span>Jelajahi Katalog</span>
                <span class="ufav-empty-arrow">&rarr;</span>
            </a>
        </div>
    </div>
</div>

@if($books->isNotEmpty())
<div id="favListContainer">
    <div class="ufav-grid" id="favGrid">
        @foreach($books as $book)
        <article class="ufav-card" id="favCard{{ $book->id }}">
            <div class="ufav-card-open"
                 role="button"
                 tabindex="0"
                 aria-haspopup="dialog"
                 aria-controls="saasBookModalDialog"
                 aria-label="Lihat detail {{ $book->title }}"
                 data-id="{{ $book->id }}"
                 data-title="{{ $book->title }}"
                 data-author="{{ $book->author ?? '-' }}"
                 data-category="{{ $book->category?->name ?? 'Umum' }}"
                 data-main-category="{{ $book->category?->name ?? 'Umum' }}"
                 data-sub-category="{{ $book->subcategory?->name ?? '' }}"
                 data-publisher="{{ $book->publisher ?? '-' }}"
                 data-year="{{ $book->publication_year ?? '-' }}"
                 data-isbn="{{ $book->isbn ?? '-' }}"
                 data-ddc="{{ $book->ddc ?? '-' }}"
                 data-rak="{{ $book->rak ?? '-' }}"
                 data-stock="{{ $book->available_stock ?? 0 }}"
                 data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                 data-cover="{{ $book->cover ? asset('storage/'.$book->cover) : '' }}"
                 data-fav="1"
                 data-borrowed="0"
                 data-reserved="0"
                 onclick="openFavoriteBook(this)"
                 onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openFavoriteBook(this); }">
                <div class="ufav-cover">
                    @if($book->cover)
                        <img src="{{ asset('storage/'.$book->cover) }}" alt="Sampul {{ $book->title }}" loading="lazy" decoding="async" onerror="this.hidden=true; this.nextElementSibling.hidden=false">
                        <div class="ufav-cover-ph" hidden>{{ Str::limit($book->title, 35) }}</div>
                    @else
                        <div class="ufav-cover-ph">{{ Str::limit($book->title, 35) }}</div>
                    @endif
                    @if(($book->available_stock ?? 0) > 0)
                        <div class="ufav-badge" style="background:#167461">Tersedia</div>
                    @else
                        <div class="ufav-badge" style="background:#b45345">Habis</div>
                    @endif
                </div>
                <div class="ufav-info">
                    <span class="ufav-cat">{{ $book->category?->name ?? 'Buku Umum' }}</span>
                    <h2 class="ufav-title">{{ $book->title }}</h2>
                    <p class="ufav-author">{{ $book->author ?? 'Penulis tidak diketahui' }}</p>
                    <span class="ufav-stock {{ ($book->available_stock ?? 0) > 0 ? '' : 'is-empty' }}">
                        {{ ($book->available_stock ?? 0) > 0 ? $book->available_stock . ' eksemplar tersedia' : 'Stok sedang habis' }}
                    </span>
                </div>
            </div>
            <div class="ufav-actions">
                <button type="button" class="ufav-remove-btn" onclick="event.stopPropagation(); removeFav(this, {{ $book->id }})" onkeydown="event.stopPropagation()" aria-label="Hapus {{ $book->title }} dari favorit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                        <span>Hapus</span>
                </button>
            </div>
        </article>
        @endforeach
    </div>
</div>
@endif

</div>
@include('user.partials.catalog-book-modal')

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

function removeFav(btn, bookId) {
    if (btn) {
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
        const label = btn.querySelector('span');
        if (label) label.textContent = 'Menghapus...';
    }
    const card = document.getElementById('favCard' + bookId);

    fetch('/user/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ book_id: bookId }),
    })
    .then(r => {
        if (!r.ok) throw new Error('Request favorit gagal');
        return r.json();
    })
    .then(data => {
        if (!card) return;
        if (data.favorited !== false) throw new Error('Status favorit tidak berubah menjadi nonaktif');

        // Animate the card out after the server confirms the removal.
        card.classList.add('ufav-card-exit');

        setTimeout(() => {
            card.remove();

            const remainingCards = document.querySelectorAll('.ufav-card');
            const countNum = document.getElementById('favCountNum');
            if (countNum) countNum.textContent = remainingCards.length;

            // 2. Jika seluruh buku favorit telah dihapus, transisi halus ke empty state (tanpa reload layar)
            if (remainingCards.length === 0) {
                const listContainer = document.getElementById('favListContainer');
                const emptyContainer = document.getElementById('favEmptyStateContainer');
                if (listContainer) {
                    listContainer.style.transition = 'opacity 0.22s ease, transform 0.22s ease';
                    listContainer.style.opacity = '0';
                    listContainer.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        listContainer.remove();
                        if (emptyContainer) {
                            emptyContainer.classList.remove('ufav-hidden');
                            emptyContainer.classList.add('ufav-fade-in');
                        }
                    }, 220);
                }
            }
        }, 340);

        showToast('Buku dihapus dari favorit', 'info');
    })
    .catch(() => {
        if (card) card.classList.remove('ufav-card-exit');
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('is-loading');
            btn.removeAttribute('aria-busy');
            const label = btn.querySelector('span');
            if (label) label.textContent = 'Hapus';
        }
        showToast('Gagal menghapus favorit', 'error');
    });
}

function removeFavoriteCardFromList(card) {
    if (!card) return;
    card.classList.add('ufav-card-exit');

    setTimeout(() => {
        card.remove();
        const remainingCards = document.querySelectorAll('.ufav-card');
        const countNum = document.getElementById('favCountNum');
        if (countNum) countNum.textContent = remainingCards.length;

        if (remainingCards.length === 0) {
            const listContainer = document.getElementById('favListContainer');
            const emptyContainer = document.getElementById('favEmptyStateContainer');
            if (listContainer) {
                listContainer.style.transition = 'opacity 0.22s ease, transform 0.22s ease';
                listContainer.style.opacity = '0';
                listContainer.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    listContainer.remove();
                    if (emptyContainer) {
                        emptyContainer.classList.remove('ufav-hidden');
                        emptyContainer.classList.add('ufav-fade-in');
                    }
                }, 220);
            }
        }
    }, 340);
}

function openFavoriteBook(card) {
    if (!card) return;

    // Show the shared catalog popup immediately using data already on this favorite card.
    openCatalogModal(card);
    refreshFavoriteBookModalData(card);
}

async function refreshFavoriteBookModalData(card) {
    try {
        const params = new URLSearchParams({ ajax: '1', search: card.dataset.title || '' });
        const response = await fetch(`{{ route('user.catalog') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (response.ok) {
            const result = await response.json();
            if (result.success && result.grid_html) {
                const catalogDoc = new DOMParser().parseFromString(result.grid_html, 'text/html');
                const catalogCard = Array.from(catalogDoc.querySelectorAll('.saas-book-card'))
                    .find(item => item.dataset.id === card.dataset.id);
                if (catalogCard) {
                    Object.assign(card.dataset, catalogCard.dataset, { fav: '1' });

                    // Refresh exact availability details only if the user still has this book open.
                    const modalIsOpen = document.getElementById('saasBookModalBackdrop')?.classList.contains('active');
                    if (modalIsOpen && String(activeBookId) === String(card.dataset.id)) {
                        openCatalogModal(card);
                    }
                }
            }
        }
    } catch (error) {
        // Fall back to the favorite card's current book fields if catalog data cannot be refreshed.
    }
}
</script>
@include('user.partials.catalog-book-modal-scripts')
@endpush
