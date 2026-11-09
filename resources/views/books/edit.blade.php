    <div class="book-form-modal-page book-edit-modal-page">
        <div class="book-form-card">

            {{-- Header Form --}}
            <div class="book-form-header">
                <div>
                    <h2 id="bookEditModalTitle">{{ ($activeBookTab ?? 'edit') === 'copies' ? 'Kelola Eksemplar' : 'Edit Data Buku' }}</h2>
                    <p id="bookEditModalSubtitle" class="book-form-subtitle">
                        @if (($activeBookTab ?? 'edit') === 'copies')
                            Kelola salinan fisik buku <strong>{{ $book->title }}</strong>.
                        @else
                        Perbarui rincian informasi, ketersediaan stok, atau sampul untuk buku
                        <strong>{{ $book->title }}</strong>.
                        @endif
                    </p>
                </div>
                <button type="button" class="book-create-modal-close" onclick="closeBookEditModal()"
                    aria-label="Tutup form edit buku">&times;</button>
            </div>

            @include('books._edit_navigation')

            <section class="book-edit-tab-panel" data-book-panel="edit" @if (($activeBookTab ?? 'edit') !== 'edit') hidden @endif>
                {{-- Alert Error Global --}}
                @if ($errors->any())
                    <div class="form-alert-error">
                        <strong>Terdapat beberapa kesalahan pengisian:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="book-form-grid">

                    <div class="form-section-heading">
                        <div class="form-section-icon">1</div>
                        <span class="form-section-title">Cari Data Buku Berdasarkan ISBN</span>
                    </div>

                    {{-- Judul Buku (Full Width) --}}
                    <div class="book-form-group book-form-full">
                        <label for="edit-title">
                            Judul Buku <span class="required">*</span>
                        </label>
                        <input type="text" id="edit-title" name="title"
                            class="input @error('title') is-invalid @enderror" value="{{ old('title', $book->title) }}"
                            required>
                        @error('title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Penulis --}}
                    <div class="book-form-group">
                        <label for="edit-author">
                            Penulis / Pengarang <span class="required">*</span>
                        </label>
                        <input type="text" id="edit-author" name="author"
                            class="input @error('author') is-invalid @enderror" value="{{ old('author', $book->author) }}"
                            required>
                        @error('author')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="book-form-group">
                        <label for="edit-category_id">
                            Kategori Buku <span class="required">*</span>
                        </label>
                        <select id="edit-category_id" name="category_id" class="input @error('category_id') is-invalid @enderror"
                            required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Subkategori --}}
                    <div class="book-form-group">
                        <label for="edit-subcategory_id">
                            Subkategori Buku
                        </label>
                        <select id="edit-subcategory_id" name="subcategory_id"
                            class="input @error('subcategory_id') is-invalid @enderror">
                            <option value="">-- Pilih Subkategori --</option>
                            @if ($book->category && $book->category->subcategories)
                                @foreach ($book->category->subcategories as $sub)
                                    <option value="{{ $sub->id }}"
                                        {{ old('subcategory_id', $book->subcategory_id) == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('subcategory_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- SKU --}}
                    <div class="book-form-group">
                        <label for="edit-sku">
                            SKU <span class="required">*</span>
                        </label>

                        <input type="text" id="edit-sku" name="sku" class="input @error('sku') is-invalid @enderror"
                            value="{{ old('sku', $book->sku) }}" placeholder="Kode unik koleksi" required>

                        @error('sku')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ISBN --}}
                    <div class="book-form-group book-form-full">
                        <label for="edit-isbn">
                            ISBN <span class="required">*</span>
                        </label>
                        <div class="isbn-panel">
                            <div class="isbn-panel-head">
                                <div>
                                    <p class="isbn-panel-title">ISBN Lookup</p>
                                    <p class="isbn-panel-help">Cari metadata menggunakan ISBN-10 atau ISBN-13. Data yang ditemukan dapat disunting sebelum disimpan.</p>
                                </div>
                            </div>
                            <div class="isbn-search-row">
                                <input type="text" id="edit-isbn" name="isbn"
                                    class="input @error('isbn') is-invalid @enderror"
                                    value="{{ old('isbn', $book->isbn) }}" placeholder="Contoh: 9786020324786"
                                    autocomplete="off" required>
                                <button type="button" id="editLookupIsbnButton" class="isbn-search-btn">Cari ISBN</button>
                            </div>
                            <div id="editIsbnStatus" class="isbn-status"></div>
                            <div id="editSourceBadge" class="source-badge"></div>
                        </div>
                        @error('isbn')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-section-heading">
                        <div class="form-section-icon">2</div>
                        <span class="form-section-title">Metadata Bibliografi</span>
                    </div>
                    <div class="metadata-note">Data dengan label <strong>Otomatis</strong> berasal dari sumber metadata dan tetap dapat diedit sebelum disimpan.</div>

                    {{-- Penerbit --}}
                    <div class="book-form-group">
                        <label for="edit-publisher">
                            Penerbit <span class="required">*</span>
                        </label>
                        <input type="text" id="edit-publisher" name="publisher"
                            class="input @error('publisher') is-invalid @enderror"
                            value="{{ old('publisher', $book->publisher) }}" required>
                        @error('publisher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tahun Terbit --}}
                    <div class="book-form-group">
                        <label for="edit-publication_year">
                            Tahun Terbit <span class="required">*</span>
                        </label>
                        <input type="number" id="edit-publication_year" name="publication_year"
                            class="input @error('publication_year') is-invalid @enderror" min="1900"
                            max="{{ date('Y') + 1 }}" value="{{ old('publication_year', $book->publication_year) }}"
                            required>
                        @error('publication_year')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Jumlah Stok --}}
                    <div class="book-form-group">
                        <label for="edit-stock">
                            Jumlah Stok Tersedia <span class="required">*</span>
                        </label>
                        <input type="number" id="edit-stock" name="stock"
                            class="input @error('stock') is-invalid @enderror" min="1"
                            value="{{ old('stock', $book->stock) }}" required>
                        @error('stock')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- No. Inventaris --}}
                    <div class="book-form-group">
                        <label for="edit-no_iventaris">
                            No. Inventaris
                        </label>
                        <input type="text" id="edit-no_iventaris" name="no_iventaris"
                            class="input @error('no_iventaris') is-invalid @enderror"
                            value="{{ old('no_iventaris', $book->no_iventaris) }}" placeholder="cth. INV-2024-001">
                        @error('no_iventaris')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kode Buku --}}
                    <div class="book-form-group">
                        <label for="edit-kode_buku">
                            Kode Buku
                        </label>
                        <input type="text" id="edit-kode_buku" name="kode_buku"
                            class="input @error('kode_buku') is-invalid @enderror"
                            value="{{ old('kode_buku', $book->kode_buku) }}" placeholder="cth. BK-001">
                        @error('kode_buku')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DDC --}}
                    <div class="book-form-group">
                        <label for="edit-ddc">
                            Kode DDC
                        </label>
                        <input type="text" id="edit-ddc" name="ddc"
                            class="input @error('ddc') is-invalid @enderror" value="{{ old('ddc', $book->ddc) }}"
                            placeholder="cth. 005.13">
                        @error('ddc')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Rak --}}
                    <div class="book-form-group">

                        <label for="edit-shelf_id">

                            Rak

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select id="edit-shelf_id" name="shelf_id" class="input @error('shelf_id') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Rak --
                            </option>

                            @foreach ($floors as $floor)
                                @foreach ($floor->zones as $zone)
                                    <optgroup label="{{ $floor->name }} · {{ $zone->name }}">

                                        @foreach ($zone->shelves as $shelf)
                                            <option value="{{ $shelf->id }}" @selected(old('shelf_id', $book->copies->first()?->shelf_id) == $shelf->id)>

                                                {{ $shelf->name }}

                                            </option>
                                        @endforeach

                                    </optgroup>
                                @endforeach
                            @endforeach

                        </select>

                        <small style="font-size:11px;color:#64747b;">
                            Rak merupakan lokasi utama seluruh eksemplar buku.
                            Jika Rak diubah, seluruh eksemplar buku akan dipindahkan
                            ke Rak baru dan posisi fisiknya akan dihitung ulang secara otomatis.
                        </small>

                        @error('shelf_id')
                            <span class="form-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- Edisi --}}
                    <div class="book-form-group">
                        <label for="edit-edition">
                            Edisi
                        </label>
                        <input type="text" id="edit-edition" name="edition"
                            class="input @error('edition') is-invalid @enderror"
                            value="{{ old('edition', $book->edition) }}" placeholder="cth. Edisi ke-3">
                        @error('edition')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-section-heading">
                        <div class="form-section-icon">3</div>
                        <span class="form-section-title">Klasifikasi & Data Internal Perpustakaan</span>
                    </div>
                    <div class="metadata-note">Bagian ini adalah data internal perpustakaan dan tidak diambil dari API.</div>

                    {{-- Deskripsi / Sinopsis --}}
                    <div class="book-form-group book-form-full">
                        <label for="edit-description">
                            Sinopsis / Deskripsi Buku
                        </label>
                        <textarea id="edit-description" name="description" class="input @error('description') is-invalid @enderror"
                            rows="4">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Cover Buku dengan Upload Box & Live Preview --}}
                    <div class="form-section-heading">
                        <div class="form-section-icon">4</div>
                        <span class="form-section-title">Sampul Buku</span>
                    </div>
                    <div class="book-form-group book-form-full">
                        <label for="edit-cover">
                            Cover / Sampul Buku
                        </label>

                        <div class="cover-upload-area">
                            <div class="cover-preview-box" id="edit-coverPreviewContainer">
                                @if ($book->cover)
                                    <img id="edit-coverPreviewImage" src="{{ asset('storage/' . $book->cover) }}"
                                        alt="Cover {{ $book->title }}">
                                    <div class="cover-placeholder-icon" id="edit-coverPlaceholder" style="display: none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"
                                                ry="2" />
                                            <circle cx="9" cy="9" r="2" />
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        </svg>
                                        <span>No Cover</span>
                                    </div>
                                @else
                                    <div class="cover-placeholder-icon" id="edit-coverPlaceholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"
                                                ry="2" />
                                            <circle cx="9" cy="9" r="2" />
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        </svg>
                                        <span>No Cover</span>
                                    </div>
                                    <img id="edit-coverPreviewImage" src="#" alt="Preview Cover"
                                        style="display: none;">
                                @endif
                            </div>

                            <div class="cover-upload-details">
                                <input type="file" id="edit-cover" name="cover" class="cover-file-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp">

                                <label for="edit-cover" class="cover-upload-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" x2="12" y1="3" y2="15" />
                                    </svg>
                                    <span>{{ $book->cover ? 'Ganti Berkas Gambar...' : 'Pilih Berkas Gambar...' }}</span>
                                </label>

                                <p class="book-cover-help">
                                    Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Ukuran maksimum: <strong>2
                                        MB</strong>. Kosongkan jika tidak ingin mengubah cover.
                                </p>
                                @error('cover')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="book-form-actions">
                    <a href="{{ route('books.index') }}" class="book-btn-cancel"
                        onclick="closeBookEditModal(event)">
                        Batal
                    </a>

                    <button type="submit" class="book-btn-save">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </form>

            </section>

            <section class="book-edit-tab-panel" data-book-panel="copies" @if (($activeBookTab ?? 'edit') !== 'copies') hidden @endif>
                @include('book_copies._management-modal', ['book' => $editBook ?? $book])
            </section>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editModal = document.getElementById('bookEditModal');
                const formGrid = editModal?.querySelector('.book-form-grid');
                const editNavigation = editModal?.querySelector('.book-edit-navigation');
                const editTabPanels = editModal?.querySelectorAll('.book-edit-tab-panel');
                const editTitle = editModal?.querySelector('#bookEditModalTitle');
                const editSubtitle = editModal?.querySelector('#bookEditModalSubtitle');
                const bookTitle = @json($book->title);
                const isbnInput = editModal?.querySelector('#edit-isbn');
                const lookupIsbnButton = editModal?.querySelector('#editLookupIsbnButton');
                const isbnStatus = editModal?.querySelector('#editIsbnStatus');
                const sourceBadge = editModal?.querySelector('#editSourceBadge');
                const coverInput = editModal?.querySelector('#edit-cover');
                const previewImage = editModal?.querySelector('#edit-coverPreviewImage');
                const placeholder = editModal?.querySelector('#edit-coverPlaceholder');

                function setActiveBookTab(tab, updateHistory = false, url = null) {
                    if (!editModal) return;
                    const activeTab = tab === 'copies' ? 'copies' : 'edit';
                    window.closeCopyCreateInline?.();

                    editNavigation?.querySelectorAll('[data-book-tab]').forEach(link => {
                        const isActive = link.dataset.bookTab === activeTab;
                        link.classList.toggle('active', isActive);
                        if (isActive) link.setAttribute('aria-current', 'page');
                        else link.removeAttribute('aria-current');
                    });
                    editTabPanels?.forEach(panel => {
                        panel.hidden = panel.dataset.bookPanel !== activeTab;
                    });

                    if (editTitle) {
                        editTitle.textContent = activeTab === 'copies' ? 'Kelola Eksemplar' : 'Edit Data Buku';
                    }
                    if (editSubtitle) {
                        editSubtitle.textContent = activeTab === 'copies'
                            ? 'Kelola salinan fisik buku ' + bookTitle + '.'
                            : 'Perbarui rincian informasi, ketersediaan stok, atau sampul untuk buku ' + bookTitle + '.';
                    }
                    editModal.querySelector('.book-form-modal-page').scrollTop = 0;

                    if (updateHistory && url) history.pushState({ bookTab: activeTab }, '', url);
                }

                editNavigation?.addEventListener('click', event => {
                    const link = event.target.closest('a[data-book-tab]');
                    if (!link || event.defaultPrevented || event.button !== 0 ||
                        event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

                    event.preventDefault();
                    setActiveBookTab(link.dataset.bookTab, true, link.href);
                });

                window.addEventListener('popstate', () => {
                    if (!editModal) return;
                    setActiveBookTab(new URLSearchParams(location.search).get('tab'));
                });

                if (formGrid) {
                    const field = id => formGrid.querySelector('#' + id)?.closest('.book-form-group');
                    const headings = formGrid.querySelectorAll('.form-section-heading');
                    const notes = formGrid.querySelectorAll('.metadata-note');
                    const orderedItems = [
                        headings[0],
                        field('edit-isbn'),
                        headings[1],
                        notes[0],
                        field('edit-title'),
                        field('edit-author'),
                        field('edit-publisher'),
                        field('edit-publication_year'),
                        field('edit-edition'),
                        field('edit-ddc'),
                        field('edit-description'),
                        headings[2],
                        notes[1],
                        field('edit-category_id'),
                        field('edit-subcategory_id'),
                        field('edit-sku'),
                        field('edit-no_iventaris'),
                        field('edit-kode_buku'),
                        field('edit-shelf_id'),
                        field('edit-stock'),
                        headings[3],
                        field('edit-cover')
                    ];

                    orderedItems.filter(Boolean).forEach(item => formGrid.appendChild(item));
                }

                function setIsbnStatus(message, type) {
                    if (!isbnStatus) return;
                    isbnStatus.textContent = message;
                    isbnStatus.className = 'isbn-status show ' + type;
                }

                async function lookupEditIsbn() {
                    const isbn = (isbnInput?.value || '').toUpperCase().replace(/[^0-9X]/g, '');
                    if (isbnInput) isbnInput.value = isbn;
                    if (!/^\d{10}$|^\d{13}$/.test(isbn)) {
                        setIsbnStatus('ISBN harus berupa ISBN-10 atau ISBN-13.', 'error');
                        isbnInput?.focus();
                        return;
                    }

                    lookupIsbnButton.disabled = true;
                    sourceBadge.className = 'source-badge';
                    setIsbnStatus('Mencari metadata buku…', 'info');

                    try {
                        const response = await fetch('{{ route('books.isbn.lookup') }}?isbn=' + encodeURIComponent(isbn), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            setIsbnStatus(result.message || 'Metadata buku tidak ditemukan. Lengkapi data secara manual.', 'error');
                            return;
                        }

                        const data = result.data || {};
                        const values = {
                            'edit-isbn': data.isbn,
                            'edit-title': data.title,
                            'edit-author': data.author,
                            'edit-publisher': data.publisher,
                            'edit-publication_year': data.publication_year,
                            'edit-edition': data.edition,
                            'edit-description': data.description
                        };
                        Object.entries(values).forEach(([id, value]) => {
                            const input = editModal.querySelector('#' + id);
                            if (input && value) input.value = value;
                        });
                        setIsbnStatus(result.message || 'Metadata berhasil ditemukan. Periksa kembali sebelum menyimpan.', 'success');
                        sourceBadge.textContent = 'Sumber metadata: ' + (data.source || 'API');
                        sourceBadge.className = 'source-badge show';
                    } catch (error) {
                        console.error(error);
                        setIsbnStatus('Terjadi kendala saat menghubungi sumber metadata. Silakan lengkapi data secara manual.', 'error');
                    } finally {
                        lookupIsbnButton.disabled = false;
                    }
                }

                lookupIsbnButton?.addEventListener('click', lookupEditIsbn);
                isbnInput?.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        lookupEditIsbn();
                    }
                });

                if (coverInput && previewImage) {
                    coverInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                previewImage.src = event.target.result;
                                previewImage.style.display = 'block';
                                if (placeholder) {
                                    placeholder.style.display = 'none';
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                const subcategoryData = @json($subcategoryData ?? []);

                const categorySelect = editModal?.querySelector('#edit-category_id');
                const subcategorySelect = editModal?.querySelector('#edit-subcategory_id');

                const selectedCategory = @json(old('category_id', $book->category_id));
                const selectedSubcategory = @json(old('subcategory_id', $book->subcategory_id));

                function populateSubcategories(categoryId, selectedSubcategoryId = '') {
                    subcategorySelect.innerHTML =
                        '<option value="">-- Pilih Subkategori --</option>';

                    const items = subcategoryData[categoryId] || [];

                    items.forEach(function(sub) {
                        const option = document.createElement('option');

                        option.value = sub.id;
                        option.textContent = sub.name;

                        if (String(sub.id) === String(selectedSubcategoryId)) {
                            option.selected = true;
                        }

                        subcategorySelect.appendChild(option);
                    });
                }

                if (categorySelect && subcategorySelect) {

                    // Jika kategori diganti
                    categorySelect.addEventListener('change', function() {
                        populateSubcategories(this.value);
                    });

                    // Saat halaman Edit pertama kali dibuka
                    populateSubcategories(
                        selectedCategory,
                        selectedSubcategory
                    );
                }
            });
        </script>
    @endpush
