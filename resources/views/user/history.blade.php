@extends('layouts.user')

@section('title', 'Riwayat Aktivitas - Pustakawan')

@push('styles')
<style>
    .history-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 36px 20px 60px;
    }

    .history-header {
        margin-bottom: 28px;
    }

    .history-header h1 {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
        letter-spacing: -0.5px;
    }

    .history-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* Tab navigation */
    .history-tabs-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        border-bottom: 1.5px solid #e2e8f0;
        margin-bottom: 24px;
        padding-bottom: 12px;
    }

    .history-tab-group {
        display: flex;
        gap: 10px;
    }

    .history-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1.5px solid transparent;
        color: #64748b;
        background: #f8fafc;
    }

    .history-tab-btn:hover {
        color: #0f4c4c;
        background: #f1f5f9;
    }

    .history-tab-btn.active {
        background: #0f4c4c;
        color: #ffffff;
        border-color: #0f4c4c;
        box-shadow: 0 4px 12px rgba(15, 76, 76, 0.2);
    }

    .history-tab-badge {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.08);
        color: inherit;
    }

    .history-tab-btn.active .history-tab-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Search & filter toolbar */
    .history-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .history-search-wrap {
        position: relative;
        min-width: 260px;
    }

    .history-search-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s;
    }

    .history-search-input:focus {
        border-color: #0f4c4c;
    }

    .history-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .history-filter-select {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        cursor: pointer;
    }

    /* Table & cards styling */
    .history-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .history-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .history-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
    }

    .history-table tbody tr:hover {
        background: #fafbfc;
    }

    .history-book-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .history-book-cover {
        width: 44px;
        height: 62px;
        border-radius: 6px;
        object-fit: cover;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .history-book-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
        line-height: 1.4;
    }

    .history-book-author {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }

    /* Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
    }

    .badge-borrowed {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-returned {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .badge-overdue {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-approved {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .badge-cancelled {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .badge-rejected {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Empty state */
    .history-empty {
        text-align: center;
        padding: 56px 20px;
    }

    .history-empty svg {
        color: #94a3b8;
        margin-bottom: 14px;
    }

    .history-empty h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 6px;
    }

    .history-empty p {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 18px;
    }

    .history-pagination {
        padding: 16px 20px;
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .history-table th:nth-child(3),
        .history-table td:nth-child(3) {
            display: none;
        }
        .history-search-wrap {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="history-wrapper">

    {{-- Page Header --}}
    <div class="history-header">
        <h1>📜 Riwayat Aktivitas</h1>
        <p>Pantau rekam jejak peminjaman buku dan status pengajuan reservasi kamu.</p>
    </div>

    {{-- Tabs & Filter Toolbar --}}
    <div class="history-tabs-container">

        {{-- Tab Buttons --}}
        <div class="history-tab-group">
            <a
                href="{{ route('user.history', ['tab' => 'borrowings', 'search' => $search]) }}"
                class="history-tab-btn {{ $activeTab === 'borrowings' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Riwayat Peminjaman
                <span class="history-tab-badge">{{ $totalBorrowings }}</span>
            </a>

            <a
                href="{{ route('user.history', ['tab' => 'reservations', 'search' => $search]) }}"
                class="history-tab-btn {{ $activeTab === 'reservations' ? 'active' : '' }}"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Riwayat Reservasi
                <span class="history-tab-badge">{{ $totalReservations }}</span>
            </a>
        </div>

        {{-- Search & Filter Form --}}
        <form action="{{ route('user.history') }}" method="GET" class="history-toolbar">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="history-search-wrap">
                <svg class="history-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    class="history-search-input"
                    placeholder="Cari judul buku atau penulis..."
                    value="{{ $search }}"
                >
            </div>

            <select name="status" class="history-filter-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @if ($activeTab === 'borrowings')
                    <option value="dipinjam" {{ $statusFilter === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="terlambat" {{ $statusFilter === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                @else
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ $statusFilter === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ $statusFilter === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan / Ditolak</option>
                @endif
            </select>
        </form>

    </div>


    {{-- ─── TAB 1: RIWAYAT PEMINJAMAN ───────────────────────────── --}}
    @if ($activeTab === 'borrowings')

        <div class="history-card">
            @if ($borrowings->isEmpty())
                <div class="history-empty">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    <h3>Tidak ada riwayat peminjaman</h3>
                    <p>{{ $search ? 'Tidak ditemukan data peminjaman yang cocok dengan pencarian.' : 'Kamu belum pernah meminjam buku di perpustakaan.' }}</p>
                    <a href="{{ route('catalog') }}" class="history-tab-btn active" style="display: inline-flex;">
                        Jelajahi Katalog Buku
                    </a>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>BUKU</th>
                                <th>TGL PINJAM</th>
                                <th>JATUH TEMPO</th>
                                <th>TGL KEMBALI</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $b)
                                @php
                                    $firstDetail = $b->details->first();
                                    $book = $firstDetail?->book;
                                    $cover = $book?->cover_image ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset('storage/' . $book->cover_image)) : asset('images/book-placeholder.jpg');

                                    // Tentukan label status
                                    $isOverdue = in_array($b->status, ['dipinjam', 'diperpanjang', 'terlambat']) && \Carbon\Carbon::parse($b->due_at)->isPast() && !$b->returned_at;
                                    if ($b->status === 'dikembalikan' || $b->returned_at) {
                                        $statusLabel = 'Selesai';
                                        $badgeClass = 'badge-returned';
                                    } elseif ($isOverdue || $b->status === 'terlambat') {
                                        $statusLabel = 'Terlambat';
                                        $badgeClass = 'badge-overdue';
                                    } else {
                                        $statusLabel = 'Dipinjam';
                                        $badgeClass = 'badge-borrowed';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="history-book-cell">
                                            <img src="{{ $cover }}" alt="Cover" class="history-book-cover">
                                            <div>
                                                <div class="history-book-title">{{ $book?->judul_buku ?: 'Buku Tanpa Judul' }}</div>
                                                <div class="history-book-author">Penulis: {{ $book?->penulis ?: '-' }}</div>
                                                @if($b->details->count() > 1)
                                                    <span style="font-size: 11px; color: #0284c7; font-weight: 600;">+{{ $b->details->count() - 1 }} buku lainnya</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $b->borrowed_at ? \Carbon\Carbon::parse($b->borrowed_at)->translatedFormat('d M Y') : '-' }}</strong>
                                    </td>
                                    <td>
                                        <span style="{{ $isOverdue ? 'color: #dc2626; font-weight: 700;' : '' }}">
                                            {{ $b->due_at ? \Carbon\Carbon::parse($b->due_at)->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $b->returned_at ? \Carbon\Carbon::parse($b->returned_at)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($borrowings->hasPages())
                    <div class="history-pagination">
                        {{ $borrowings->links() }}
                    </div>
                @endif
            @endif
        </div>

    {{-- ─── TAB 2: RIWAYAT RESERVASI ────────────────────────────── --}}
    @else

        <div class="history-card">
            @if ($reservations->isEmpty())
                <div class="history-empty">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <h3>Tidak ada riwayat reservasi</h3>
                    <p>{{ $search ? 'Tidak ditemukan data reservasi yang cocok dengan pencarian.' : 'Kamu belum pernah mereservasi buku apapun.' }}</p>
                    <a href="{{ route('user.reservations') }}" class="history-tab-btn active" style="display: inline-flex;">
                        Buat Reservasi Baru
                    </a>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>BUKU</th>
                                <th>TGL RESERVASI</th>
                                <th>KURSI</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $res)
                                @php
                                    $book = $res->book;
                                    $cover = $book?->cover_image ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset('storage/' . $book->cover_image)) : asset('images/book-placeholder.jpg');

                                    // Mapping status
                                    $rawStatus = strtolower($res->status);
                                    if ($rawStatus === 'menunggu') {
                                        $label = 'Pending';
                                        $bClass = 'badge-pending';
                                    } elseif ($rawStatus === 'disetujui') {
                                        $label = 'Disetujui';
                                        $bClass = 'badge-approved';
                                    } elseif ($rawStatus === 'dibatalkan') {
                                        $label = 'Dibatalkan';
                                        $bClass = 'badge-cancelled';
                                    } elseif ($rawStatus === 'ditolak') {
                                        $label = 'Ditolak';
                                        $bClass = 'badge-rejected';
                                    } elseif ($rawStatus === 'selesai') {
                                        $label = 'Selesai';
                                        $bClass = 'badge-returned';
                                    } else {
                                        $label = ucfirst($rawStatus);
                                        $bClass = 'badge-cancelled';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="history-book-cell">
                                            <img src="{{ $cover }}" alt="Cover" class="history-book-cover">
                                            <div>
                                                <div class="history-book-title">{{ $book?->judul_buku ?: 'Buku Tanpa Judul' }}</div>
                                                <div class="history-book-author">Penulis: {{ $book?->penulis ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $res->reserved_at ? \Carbon\Carbon::parse($res->reserved_at)->translatedFormat('d M Y') : '-' }}</strong>
                                    </td>
                                    <td>
                                        {{ $res->seat_number ?: '-' }}
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $bClass }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($reservations->hasPages())
                    <div class="history-pagination">
                        {{ $reservations->links() }}
                    </div>
                @endif
            @endif
        </div>

    @endif

</div>

@endsection
