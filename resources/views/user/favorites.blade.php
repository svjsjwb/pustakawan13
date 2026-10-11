@extends('layouts.user')
@section('title', 'Favorit – Perpustakaan Tiga Serangkai')
@section('page-title', 'Favorit')

@push('styles')
<style>
.ufav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 18px; }
.ufav-card { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; box-shadow:var(--shadow); transition:transform .22s ease, box-shadow .22s ease; cursor:pointer; }
.ufav-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg); }
.ufav-card:focus-visible { outline:3px solid rgba(15, 118, 110, .28); outline-offset:3px; }
.ufav-cover { aspect-ratio:2/3; background:linear-gradient(135deg,#1d5c59,#287879); position:relative; overflow:hidden; }
.ufav-cover img { width:100%; height:100%; object-fit:cover; display:block; }
.ufav-cover-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:rgba(255,255,255,.8); text-align:center; padding:12px; box-sizing:border-box; }
.ufav-badge { position:absolute; top:8px; left:8px; background:var(--primary); color:#fff; font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; }
.ufav-info { padding:13px 14px 14px; }
.ufav-cat  { font-size:9.5px; font-weight:700; letter-spacing:.07em; color:var(--primary); text-transform:uppercase; }
.ufav-title { margin:4px 0 3px; font-size:13.5px; font-weight:700; color:var(--ink); line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.ufav-author { margin:0 0 12px; font-size:12px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ufav-actions { display:flex; gap:8px; }
.ufav-remove-btn { flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:7px; border-radius:8px; border:1px solid var(--line); background:#fff; font-size:12px; font-weight:600; color:var(--danger); cursor:pointer; transition:background .2s; }
.ufav-remove-btn:hover { background:var(--danger-soft); border-color:var(--danger); }
.ufav-remove-btn svg { width:13px; height:13px; }
.ul-page-header .ufav-remove-all-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border: 1px solid #f0c8c8;
    border-radius: 13px;
    background: #fffafa;
    color: #b83232;
    font-weight: 700;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background-color .22s ease, color .22s ease;
}
.ul-page-header .ufav-remove-all-btn:hover {
    transform: translateY(-1px);
    border-color: #e5a9a9;
    background: #fff3f3;
    box-shadow: 0 5px 14px rgba(184, 50, 50, .12);
    color: #a92727;
}
.ufav-remove-all-btn[hidden] { display: none !important; }
.ufav-remove-all-btn svg { width: 16px; height: 16px; flex: 0 0 auto; }
.ufav-remove-all-btn:disabled { opacity: .65; cursor: wait; transform: none; }
.ufav-confirm-backdrop {
    position: fixed;
    z-index: 12000;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 20px;
    background: rgba(12, 35, 34, .48);
    backdrop-filter: blur(3px);
}
.ufav-confirm-backdrop[hidden] { display: none; }
.ufav-confirm-dialog {
    width: min(420px, 100%);
    padding: 24px;
    border: 1px solid #e8eeee;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 22px 60px rgba(12, 35, 34, .22);
    animation: ufav-confirm-enter .2s ease both;
}
.ufav-confirm-dialog h2 { margin: 0 0 8px; color: #193d3b; font-size: 18px; font-weight: 750; }
.ufav-confirm-dialog p { margin: 0; color: #627674; font-size: 13.5px; line-height: 1.55; }
.ufav-confirm-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
.ufav-confirm-actions button { min-height: 40px; padding: 0 16px; border-radius: 10px; font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: background .18s ease, border-color .18s ease, transform .18s ease; }
.ufav-confirm-cancel { border: 1px solid #d9e3e1; background: #fff; color: #46615e; }
.ufav-confirm-cancel:hover { background: #f5f9f8; border-color: #bdcfcc; }
.ufav-confirm-delete { border: 1px solid #b83232; background: #b83232; color: #fff; }
.ufav-confirm-delete:hover { transform: translateY(-1px); border-color: #a92727; background: #a92727; }
.ufav-confirm-actions button:disabled { opacity: .6; cursor: wait; transform: none; }
@keyframes ufav-confirm-enter { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
.ufav-empty-banner {
    position: relative;
    isolation: isolate;
    display: grid;
    grid-template-columns: minmax(220px, .9fr) minmax(0, 1.1fr);
    align-items: center;
    gap: 28px;
    min-height: 292px;
    padding: 30px 42px;
    overflow: hidden;
    border: 1px solid #d7ebe6;
    border-radius: 22px;
    background: linear-gradient(115deg, #f0faf6 0%, #fff 52%, #f3fbf9 100%);
    box-shadow: 0 8px 26px rgba(21, 83, 77, .07);
    animation: ufav-empty-enter .5s ease both;
}
.ufav-empty-banner::before {
    position: absolute;
    z-index: -1;
    inset: 0;
    background-image: radial-gradient(rgba(20, 126, 111, .11) .8px, transparent .8px);
    background-position: 13px 15px;
    background-size: 18px 18px;
    content: "";
    opacity: .22;
    mask-image: linear-gradient(90deg, #000 0%, transparent 42%, transparent 65%, #000 100%);
}
.ufav-empty-banner::after {
    position: absolute;
    z-index: -1;
    top: -110px;
    left: -115px;
    width: 290px;
    height: 290px;
    border-radius: 50%;
    background: rgba(196, 235, 223, .22);
    content: "";
}
.ufav-empty-art {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    animation: ufav-art-enter .55s .08s ease both;
    transition: transform .3s ease;
}
.ufav-empty-art svg { display: block; width: min(100%, 280px); height: auto; }
.ufav-empty-banner:hover .ufav-empty-art { transform: translateY(-2px); }
.ufav-empty-note {
    position: absolute;
    right: 0;
    bottom: 4px;
    color: #338f7c;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-style: italic;
    font-weight: 600;
    line-height: 1.35;
    text-align: center;
    transform: rotate(-7deg);
}
.ufav-empty-copy { position: relative; z-index: 1; animation: ufav-copy-enter .5s .12s ease both; }
.ufav-empty-kicker {
    margin: 0 0 8px;
    color: #13816f;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .09em;
    text-transform: uppercase;
}
.ufav-empty-title {
    max-width: 440px;
    margin: 0 0 10px;
    color: #143c3a;
    font-family: 'Poppins', sans-serif;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.2;
}
.ufav-empty-desc {
    max-width: 390px;
    margin: 0 0 20px;
    color: #647c79;
    font-size: 14px;
    line-height: 1.6;
}
.ufav-empty-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 0 18px;
    border: 1px solid #0d796c;
    border-radius: 13px;
    background: linear-gradient(135deg, #0d766b, #138e7d);
    box-shadow: 0 5px 14px rgba(13, 118, 107, .2);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: transform .22s ease, box-shadow .22s ease, background-color .22s ease;
    animation: ufav-cta-enter .48s .25s ease both;
}
.ufav-empty-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 19px rgba(13, 118, 107, .27);
}
.ufav-empty-cta svg { width: 16px; height: 16px; flex: 0 0 auto; }
.ufav-empty-cta .ufav-cta-book { transition: transform .22s ease; }
.ufav-empty-cta:hover .ufav-cta-book { transform: translateY(-2px); }
.ufav-empty-cta .ufav-cta-arrow { transition: transform .22s ease; }
.ufav-empty-cta:hover .ufav-cta-arrow { transform: translateX(5px); }
@keyframes ufav-empty-enter { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes ufav-art-enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes ufav-copy-enter { from { opacity: 0; transform: translateY(7px); } to { opacity: 1; transform: translateY(0); } }
@keyframes ufav-cta-enter { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 700px) {
    .ul-page-header .ufav-remove-all-btn { padding: 9px 11px; font-size: 12px; }
    .ufav-empty-banner { grid-template-columns: 1fr; gap: 12px; min-height: 0; padding: 24px 22px 26px; text-align: center; }
    .ufav-empty-art svg { width: min(70%, 220px); }
    .ufav-empty-note { right: 8%; bottom: 0; }
    .ufav-empty-copy { display: flex; flex-direction: column; align-items: center; }
    .ufav-empty-title { font-size: 21px; }
    .ufav-empty-desc { margin-bottom: 16px; font-size: 13px; }
}
@media (prefers-reduced-motion: reduce) {
    .ufav-empty-banner, .ufav-empty-art, .ufav-empty-copy, .ufav-empty-cta, .ufav-remove-all-btn { animation: none; transition: none; }
    .ufav-empty-banner:hover .ufav-empty-art, .ufav-empty-cta:hover { transform: none; }
    .ufav-confirm-dialog { animation: none; }
}
</style>
@endpush

@section('content')

<div class="ul-page-header">
    <div>
        <p class="ul-page-kicker">KOLEKSI SAYA</p>
        <h1 class="ul-page-h1">Favorit Saya</h1>
    </div>
    @if($books->isNotEmpty())
        <button type="button" class="ul-btn ul-btn-outline ufav-remove-all-btn" id="ufavRemoveAllButton" onclick="openRemoveAllFavoritesDialog()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            <span>Hapus Semua Favorit</span>
        </button>
    @endif
</div>

@if($books->isEmpty())
@include('user.partials.favorites-empty')

@else
<div id="ufav-library-content">
<p id="ufavSavedCount" style="color:var(--muted);margin-bottom:20px;font-size:13.5px">{{ $books->count() }} buku tersimpan</p>
<div class="ufav-grid">
    @foreach($books as $book)
    <div class="ufav-card" id="favCard{{ $book->id }}" data-book-id="{{ $book->id }}" role="link" tabindex="0" aria-label="Lihat detail {{ $book->title }}" data-detail-url="{{ route('user.catalog', ['search' => $book->title, 'open_book' => $book->id]) }}" onclick="openFavoriteBookDetail(event, this)" onkeydown="if (event.target === this && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); openFavoriteBookDetail(event, this); }">
        <div class="ufav-cover">
            @if($book->cover)
                <img src="{{ asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
            @else
                <div class="ufav-cover-ph">{{ Str::limit($book->title, 35) }}</div>
            @endif
            @if(($book->available_stock??0) > 0)
                <div class="ufav-badge" style="background:var(--success)">Tersedia</div>
            @else
                <div class="ufav-badge" style="background:var(--danger)">Habis</div>
            @endif
        </div>
        <div class="ufav-info">
            <span class="ufav-cat">{{ $book->category->name ?? '—' }}</span>
            <h3 class="ufav-title">{{ $book->title }}</h3>
            <p class="ufav-author">{{ $book->author ?? '—' }}</p>
            <div class="ufav-actions">
                <button class="ufav-remove-btn" onclick="event.stopPropagation(); removeFav({{ $book->id }})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
</div>
<template id="ufav-empty-template">
    @include('user.partials.favorites-empty')
</template>
<div class="ufav-confirm-backdrop" id="ufavRemoveAllDialog" hidden>
    <section class="ufav-confirm-dialog" role="alertdialog" aria-modal="true" aria-labelledby="ufavConfirmTitle" aria-describedby="ufavConfirmMessage">
        <h2 id="ufavConfirmTitle">Hapus Semua Favorit?</h2>
        <p id="ufavConfirmMessage">Apakah Anda yakin ingin menghapus semua buku dari daftar favorit?</p>
        <div class="ufav-confirm-actions">
            <button type="button" class="ufav-confirm-cancel" id="ufavCancelRemoveAll">Batal</button>
            <button type="button" class="ufav-confirm-delete" id="ufavConfirmRemoveAll">Hapus Semua</button>
        </div>
    </section>
</div>
@endif

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
let isRemovingAllFavorites = false;

function updateFavoritesAfterRemoval() {
    const cards = Array.from(document.querySelectorAll('.ufav-grid .ufav-card'));
    const count = document.getElementById('ufavSavedCount');
    const removeAllButton = document.getElementById('ufavRemoveAllButton');

    if (count) count.textContent = cards.length + ' buku tersimpan';
    if (cards.length > 0) return;

    const libraryContent = document.getElementById('ufav-library-content');
    const emptyTemplate = document.getElementById('ufav-empty-template');
    if (libraryContent && emptyTemplate) {
        libraryContent.replaceWith(emptyTemplate.content.cloneNode(true));
        emptyTemplate.remove();
    }
    if (removeAllButton) removeAllButton.remove();
}

function openRemoveAllFavoritesDialog() {
    const dialog = document.getElementById('ufavRemoveAllDialog');
    if (!dialog || isRemovingAllFavorites) return;
    dialog.hidden = false;
    document.getElementById('ufavCancelRemoveAll')?.focus();
}

function closeRemoveAllFavoritesDialog() {
    const dialog = document.getElementById('ufavRemoveAllDialog');
    if (dialog) dialog.hidden = true;
    document.getElementById('ufavRemoveAllButton')?.focus();
}

async function confirmRemoveAllFavorites() {
    if (isRemovingAllFavorites) return;
    const cards = Array.from(document.querySelectorAll('.ufav-grid .ufav-card'));
    const dialog = document.getElementById('ufavRemoveAllDialog');
    const cancelButton = document.getElementById('ufavCancelRemoveAll');
    const confirmButton = document.getElementById('ufavConfirmRemoveAll');

    if (!cards.length) {
        closeRemoveAllFavoritesDialog();
        updateFavoritesAfterRemoval();
        return;
    }

    isRemovingAllFavorites = true;
    cancelButton.disabled = true;
    confirmButton.disabled = true;
    confirmButton.textContent = 'Menghapus...';

    try {
        for (const card of cards) {
            const response = await fetch('/user/favorites/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ book_id: card.dataset.bookId }),
            });
            const data = await response.json();
            if (!response.ok || data.favorited !== false) {
                throw new Error(data.message || 'Penghapusan favorit tidak berhasil.');
            }

            card.remove();
            updateFavoritesAfterRemoval();
        }

        if (dialog) dialog.hidden = true;
        showToast('Semua buku berhasil dihapus dari favorit.', 'success');
    } catch (error) {
        if (dialog) dialog.hidden = true;
        showToast(error.message || 'Gagal menghapus semua favorit.', 'error');
    } finally {
        isRemovingAllFavorites = false;
        cancelButton.disabled = false;
        confirmButton.disabled = false;
        confirmButton.textContent = 'Hapus Semua';
        updateFavoritesAfterRemoval();
    }
}

document.getElementById('ufavCancelRemoveAll')?.addEventListener('click', closeRemoveAllFavoritesDialog);
document.getElementById('ufavConfirmRemoveAll')?.addEventListener('click', confirmRemoveAllFavorites);
document.getElementById('ufavRemoveAllDialog')?.addEventListener('click', function(event) {
    if (event.target === this && !isRemovingAllFavorites) closeRemoveAllFavoritesDialog();
});
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !isRemovingAllFavorites) closeRemoveAllFavoritesDialog();
});

function openFavoriteBookDetail(event, card) {
    if (event.target.closest('.ufav-remove-btn')) return;
    window.location.href = card.dataset.detailUrl;
}

function removeFav(bookId) {
    fetch('/user/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ book_id: bookId }),
    })
    .then(r => r.json())
    .then(data => {
        const card = document.getElementById('favCard' + bookId);
        if (card) {
            card.style.animation = 'ul-toast-in .2s ease reverse';
            setTimeout(() => {
                card.remove();
                updateFavoritesAfterRemoval();
            }, 180);
        }
        showToast('Buku dihapus dari favorit', 'info');
    })
    .catch(() => showToast('Gagal menghapus favorit', 'error'));
}
</script>
@endpush
