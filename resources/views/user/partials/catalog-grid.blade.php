{{-- 4. 5-COLUMN RESPONSIVE BOOK GRID --}}
@if($books->isEmpty())
    <div class="eg-card eg-empty-state">
        <div class="eg-empty-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <h3 class="eg-empty-title">Tidak Ada Buku Ditemukan</h3>
        <p class="eg-empty-desc">
            Buku yang Anda cari tidak sesuai dengan kata kunci atau filter saat ini. Silakan coba kata kunci lain atau bersihkan filter.
        </p>
        <button type="button" class="saas-btn-primary" onclick="resetAllCatalogFilters()" style="max-width: 220px; margin: 0 auto; cursor: pointer;">
            Lihat Semua Koleksi
        </button>
    </div>
@else
    <div class="saas-book-grid">
        @foreach($books as $book)
            @php
                $isFav = in_array($book->id, $favoriteIds ?? []);
                $isAvailable = ($book->available_stock ?? 0) > 0;
            @endphp
            <div class="saas-book-card"
                 onclick="openCatalogModal(this)"
                 data-id="{{ $book->id }}"
                 data-title="{{ $book->title }}"
                 data-author="{{ $book->author ?? '-' }}"
                 data-category="{{ $book->category->name ?? 'Umum' }}"
                 data-main-category="{{ $book->main_category ?? '' }}"
                 data-sub-category="{{ $book->sub_category ?? '' }}"
                 data-education-level="{{ $book->education_level ?? '' }}"
                 data-publisher="{{ $book->publisher ?? '-' }}"
                 data-year="{{ $book->publication_year ?? '-' }}"
                 data-isbn="{{ $book->isbn ?? '-' }}"
                 data-rak="{{ $book->rak ?? '-' }}"
                 data-stock="{{ $book->available_stock ?? 0 }}"
                 data-description="{{ $book->description ?? 'Deskripsi buku belum tersedia.' }}"
                 data-cover="{{ $book->cover ? asset('storage/'.$book->cover) : '' }}"
                 data-fav="{{ $isFav ? '1' : '0' }}"
                 data-borrowed="{{ in_array($book->id, $borrowedBookIds ?? []) ? '1' : '0' }}"
                 data-reserved="{{ in_array($book->id, $reservedBookIds ?? []) ? '1' : '0' }}">

                {{-- Cover & Badges --}}
                <div class="saas-card-cover-wrapper">
                    @if($book->cover)
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             alt="{{ $book->title }}"
                             class="saas-card-cover-img"
                             loading="lazy">
                    @else
                        <div class="saas-card-cover-fallback">
                            <span class="fallback-letter">{{ strtoupper(substr($book->title, 0, 1)) }}</span>
                            <span class="fallback-title">{{ Str::limit($book->title, 34) }}</span>
                        </div>
                    @endif

                    {{-- Category Badge --}}
                    <span class="saas-card-badge">
                        {{ Str::limit($book->sub_category ? ($book->main_category . ' • ' . $book->sub_category) : ($book->category->name ?? 'Umum'), 24) }}
                    </span>

                    {{-- Availability Badge --}}
                    <span class="saas-stock-badge {{ $isAvailable ? 'available' : 'unavailable' }}">
                        {{ $isAvailable ? 'Tersedia' : 'Habis' }}
                    </span>

                    {{-- Hover Overlay Action Buttons --}}
                    <div class="saas-card-overlay">
                        <button type="button" class="saas-overlay-btn btn-detail" onclick="event.stopPropagation(); openCatalogModal(this.closest('.saas-book-card'))">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span>Detail 3D</span>
                        </button>

                        <button type="button" class="saas-overlay-btn btn-reserve" onclick="event.stopPropagation(); triggerReservationModal({{ $book->id }}, this.closest('.saas-book-card'))">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Reservasi</span>
                        </button>
                    </div>
                </div>

                {{-- Book Info --}}
                <div class="saas-card-body">
                    <h3 class="saas-card-title" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="saas-card-author">{{ $book->author ?? 'Penulis Anonim' }}</p>
                    <div class="saas-card-footer">
                        <span style="font-weight: 600; color: {{ $isAvailable ? 'var(--primary)' : '#DC2626' }};">
                            {{ $isAvailable ? ($book->available_stock . ' eksemplar') : 'Stok Kosong' }}
                        </span>
                        @if($book->rak)
                            <span style="color: var(--saas-text-muted); font-weight: 500;">📍 {{ $book->rak }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
        <div class="ucat-pagination-wrapper">
            {{ $books->links('partials.user-catalog-pagination') }}
        </div>
    @endif
@endif
