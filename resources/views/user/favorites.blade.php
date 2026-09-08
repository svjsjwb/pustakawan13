@extends('layouts.user')
@section('title', 'Favorit – Perpustakaan Tiga Serangkai')
@section('page-title', 'Favorit')

@push('styles')
<style>
.ufav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 18px; }
.ufav-card { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; box-shadow:var(--shadow); transition:transform .22s, box-shadow .22s; }
.ufav-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); }
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
</style>
@endpush

@section('content')

<div class="ul-page-header">
    <div>
        <p class="ul-page-kicker">KOLEKSI SAYA</p>
        <h1 class="ul-page-h1">❤️ Favorit Saya</h1>
    </div>
    <a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-outline">
        + Tambah dari Katalog
    </a>
</div>

@if($books->isEmpty())
<div class="ul-card">
    <div class="ul-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <h3>Belum ada buku favorit</h3>
        <p>Tambahkan buku ke favorit dari Katalog agar mudah ditemukan kembali.</p>
        <a href="{{ route('user.catalog') }}" class="ul-btn ul-btn-primary">Jelajahi Katalog</a>
    </div>
</div>

@else
<p style="color:var(--muted);margin-bottom:20px;font-size:13.5px">{{ $books->count() }} buku tersimpan</p>
<div class="ufav-grid">
    @foreach($books as $book)
    <div class="ufav-card" id="favCard{{ $book->id }}">
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
                <button class="ufav-remove-btn" onclick="removeFav({{ $book->id }})">
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
@endif

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

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
            setTimeout(() => card.remove(), 180);
        }
        showToast('Buku dihapus dari favorit', 'info');
    })
    .catch(() => showToast('Gagal menghapus favorit', 'error'));
}
</script>
@endpush
