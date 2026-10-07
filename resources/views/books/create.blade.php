@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
    <style>
        .book-form-page {
            max-width: 1100px;
        }

        .isbn-panel {
            grid-column: 1 / -1;
            padding: 18px;
            border: 1px solid #d8eceb;
            border-radius: 12px;
            background: linear-gradient(135deg, #f5fbfb, #fff);
        }

        .isbn-panel-head {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .isbn-panel-title {
            margin: 0;
            color: #19364f;
            font-size: 15px;
            font-weight: 800;
        }

        .isbn-panel-help {
            margin: 4px 0 0;
            color: #64747b;
            font-size: 12.5px;
            line-height: 1.5;
        }

        .isbn-search-row {
            display: flex;
            gap: 10px;
        }

        .isbn-search-row .input {
            flex: 1;
        }

        .isbn-search-btn,
        .isbn-scan-btn {
            border: 0;
            border-radius: 8px;
            padding: 0 16px;
            min-height: 42px;
            background: #287f80;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .isbn-search-btn:hover,
        .isbn-scan-btn:hover {
            background: #236f70;
        }

        .isbn-search-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .isbn-status {
            display: none;
            margin-top: 10px;
            padding: 9px 11px;
            border-radius: 8px;
            font-size: 12.5px;
        }

        .isbn-status.show {
            display: block;
        }

        .isbn-status.success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .isbn-status.info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .isbn-status.error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .source-badge {
            display: none;
            margin-top: 10px;
            font-size: 11px;
            color: #64747b;
        }

        .source-badge.show {
            display: block;
        }

        .auto-field {
            position: relative;
        }

        .auto-field::after {
            content: 'Otomatis';
            position: absolute;
            right: 9px;
            top: 31px;
            font-size: 9px;
            color: #287f80;
            background: #eaf7f6;
            border-radius: 5px;
            padding: 2px 5px;
            pointer-events: none;
        }

        .book-form-group textarea.input {
            min-height: 120px;
        }

        .metadata-note {
            grid-column: 1/-1;
            margin: -5px 0 2px;
            font-size: 11.5px;
            color: #64747b;
        }

        .cover-source-url {
            display: none;
        }

        .cover-source-note {
            margin: 7px 0 0;
            font-size: 11.5px;
            color: #64747b;
            line-height: 1.45;
        }

        .cover-source-note strong {
            color: #287f80;
        }

        .isbn-scanner-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .isbn-scanner-modal.is-open {
            display: flex;
        }

        .isbn-scanner-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, .72);
            backdrop-filter: blur(5px);
        }

        .isbn-scanner-dialog {
            position: relative;
            z-index: 1;
            width: min(520px, 100%);
            overflow: hidden;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 25px 70px rgba(15, 23, 42, .28);
        }

        .isbn-scanner-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px 16px;
        }

        .isbn-scanner-header h3 {
            margin: 0;
            color: #19364f;
            font-size: 18px;
        }

        .isbn-scanner-header p {
            margin: 5px 0 0;
            color: #64747b;
            font-size: 12.5px;
        }

        .isbn-scanner-close {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #f1f5f9;
            color: #475569;
            font-size: 22px;
            cursor: pointer;
        }

        .isbn-scanner-camera {
            position: relative;
            margin: 0 22px;
            overflow: hidden;
            border-radius: 12px;
            background: #0f172a;
            aspect-ratio: 4/3;
        }

        #isbnReader,
        #isbnReader video {
            width: 100%;
            height: 100%;
        }

        #isbnReader video {
            object-fit: cover;
        }

        #isbnReader img,
        #isbnReader__dashboard {
            display: none !important;
        }

        .isbn-scanner-frame {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .isbn-scanner-frame::before {
            content: "";
            position: absolute;
            left: 8%;
            right: 8%;
            top: 38%;
            height: 24%;
            border: 2px solid rgba(255, 255, 255, .9);
            border-radius: 8px;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, .18);
        }

        .isbn-scanner-frame::after {
            content: "";
            position: absolute;
            left: 11%;
            right: 11%;
            top: 50%;
            height: 2px;
            background: #fff;
            opacity: .8;
        }

        .isbn-scanner-status {
            margin: 12px 22px;
            padding: 10px;
            border-radius: 8px;
            background: #f7fbfb;
            color: #64747b;
            font-size: 12.5px;
            text-align: center;
        }

        .isbn-scanner-cancel {
            width: calc(100% - 44px);
            margin: 0 22px 20px;
            min-height: 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #475569;
            font-weight: 700;
            cursor: pointer;
        }

        .book-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
        }

        @media(max-width:700px) {
            .isbn-search-row {
                flex-direction: column
            }

            .isbn-search-btn,
            .isbn-scan-btn {
                min-height: 42px
            }

            .isbn-panel-head {
                flex-direction: column
            }

            .book-form-page {
                padding-left: 10px;
                padding-right: 10px
            }

            .book-form-card {
                padding: 22px 18px
            }

            .book-form-grid {
                grid-template-columns: 1fr
            }

            .book-form-full {
                grid-column: 1
            }

            .isbn-scanner-modal {
                padding: 10px
            }
        }
    </style>
@endpush

@section('content')
    <div class="book-form-page">
        <nav class="book-breadcrumb">
            <a href="{{ route('books.index') }}">Manajemen Buku</a><span class="separator">/</span><span class="current">Tambah
                Buku Baru</span>
        </nav>

        <div class="book-form-card">
            <div class="book-form-header">
                <h2>Tambah Buku Baru</h2>
                <p class="book-form-subtitle">Masukkan ISBN untuk mengambil metadata buku secara otomatis, lalu periksa dan
                    lengkapi data koleksi perpustakaan.</p>
            </div>

            @if ($errors->any())
                <div class="form-alert-error"><strong>Periksa kembali data berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="bookCreateForm" action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="cover_url" name="cover_url" value="{{ old('cover_url') }}">

                <div class="book-form-grid">
                    <div class="form-section-heading">
                        <div class="form-section-icon">1</div><span class="form-section-title">Cari Data Buku Berdasarkan
                            ISBN</span>
                    </div>
                    <div class="isbn-panel">
                        <div class="isbn-panel-head">
                            <div>
                                <p class="isbn-panel-title">ISBN Lookup</p>
                                <p class="isbn-panel-help">ISBN-10 atau ISBN-13 dapat diketik atau dipindai dari barcode
                                    buku. Sistem akan mencoba Google Books terlebih dahulu, kemudian Open Library.</p>
                            </div>
                        </div>
                        <div class="isbn-search-row">
                            <input type="text" id="isbn" name="isbn"
                                class="input @error('isbn') is-invalid @enderror" value="{{ old('isbn') }}"
                                placeholder="Contoh: 9786020324786" autocomplete="off">
                            <button type="button" id="lookupIsbnButton" class="isbn-search-btn">Cari ISBN</button>
                            <button type="button" id="scanIsbnButton" class="isbn-scan-btn">Scan</button>
                        </div>
                        <div id="isbnStatus" class="isbn-status"></div>
                        <div id="sourceBadge" class="source-badge"></div>
                        @error('isbn')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-section-heading">
                        <div class="form-section-icon">2</div><span class="form-section-title">Metadata Bibliografi</span>
                    </div>
                    <div class="metadata-note">Data dengan label <strong>Otomatis</strong> berasal dari sumber metadata dan
                        tetap dapat diedit sebelum disimpan.</div>

                    <div class="book-form-group book-form-full auto-field">
                        <label for="title">Judul Buku <span class="required">*</span></label>
                        <input type="text" id="title" name="title"
                            class="input @error('title') is-invalid @enderror" value="{{ old('title') }}"
                            placeholder="Judul buku" required>
                        @error('title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group auto-field">
                        <label for="author">Penulis <span class="required">*</span></label>
                        <input type="text" id="author" name="author"
                            class="input @error('author') is-invalid @enderror" value="{{ old('author') }}"
                            placeholder="Nama penulis" required>
                        @error('author')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group auto-field">
                        <label for="publisher">Penerbit</label>
                        <input type="text" id="publisher" name="publisher"
                            class="input @error('publisher') is-invalid @enderror" value="{{ old('publisher') }}"
                            placeholder="Nama penerbit">
                        @error('publisher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group auto-field">
                        <label for="publication_year">Tahun Terbit</label>
                        <input type="number" id="publication_year" name="publication_year"
                            class="input @error('publication_year') is-invalid @enderror"
                            value="{{ old('publication_year') }}" min="1000" max="{{ date('Y') + 1 }}"
                            placeholder="Contoh: 2024">
                        @error('publication_year')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group auto-field">
                        <label for="edition">Edisi</label>
                        <input type="text" id="edition" name="edition"
                            class="input @error('edition') is-invalid @enderror" value="{{ old('edition') }}"
                            placeholder="Contoh: Edisi ke-2">
                        @error('edition')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="ddc">Kode DDC</label>
                        <input type="text" id="ddc" name="ddc"
                            class="input @error('ddc') is-invalid @enderror" value="{{ old('ddc') }}"
                            placeholder="Contoh: 005.13">
                        @error('ddc')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group book-form-full auto-field">
                        <label for="description">Sinopsis / Deskripsi</label>
                        <textarea id="description" name="description" class="input @error('description') is-invalid @enderror"
                            placeholder="Deskripsi buku akan diisi otomatis jika sumber menyediakannya.">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-section-heading">
                        <div class="form-section-icon">3</div><span class="form-section-title">Klasifikasi & Data Internal
                            Perpustakaan</span>
                    </div>
                    <div class="metadata-note">Bagian ini adalah data internal perpustakaan dan tidak diambil dari API.
                    </div>

                    <div class="book-form-group">
                        <label for="category_id">
                            Kategori <span class="required">*</span>
                        </label>

                        <select id="category_id" name="category_id"
                            class="input @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="subcategory_id">
                            Subkategori
                        </label>

                        <select id="subcategory_id" name="subcategory_id"
                            class="input @error('subcategory_id') is-invalid @enderror">
                            <option value="">-- Pilih Subkategori --</option>
                        </select>

                        @error('subcategory_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="sku">SKU <span class="required">*</span></label>
                        <input type="text" id="sku" name="sku"
                            class="input @error('sku') is-invalid @enderror" value="{{ old('sku') }}"
                            placeholder="Kode unik koleksi" required>
                        @error('sku')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="no_iventaris">No. Inventaris</label>
                        <input type="text" id="no_iventaris" name="no_iventaris"
                            class="input @error('no_iventaris') is-invalid @enderror" value="{{ old('no_iventaris') }}"
                            placeholder="Contoh: INV-2026-001">
                        @error('no_iventaris')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="kode_buku">Kode Buku</label>
                        <input type="text" id="kode_buku" name="kode_buku"
                            class="input @error('kode_buku') is-invalid @enderror" value="{{ old('kode_buku') }}"
                            placeholder="Contoh: BK-001">
                        @error('kode_buku')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="rak">Rak <span class="required">*</span></label>
                        <select id="rak" name="rak" class="input @error('rak') is-invalid @enderror" required>
                            <option value="">-- Pilih Rak --</option>
                            @foreach ($racks as $rack)
                                <option value="{{ $rack->code }}" {{ old('rak') == $rack->code ? 'selected' : '' }}>
                                    {{ $rack->code }} – {{ $rack->name }}</option>
                            @endforeach
                        </select>
                        @error('rak')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="book-form-group">
                        <label for="stock">Jumlah Eksemplar <span class="required">*</span></label>
                        <input type="number" id="stock" name="stock"
                            class="input @error('stock') is-invalid @enderror" min="1"
                            value="{{ old('stock', 1) }}" required>
                        <small style="font-size:11px;color:#64747b;">Sistem akan membuat data <em>book copy</em> sesuai
                            jumlah ini.</small>
                        @error('stock')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-section-heading">
                        <div class="form-section-icon">4</div><span class="form-section-title">Sampul Buku</span>
                    </div>
                    <div class="book-form-group book-form-full">
                        <div class="cover-upload-area">
                            <div class="cover-preview-box" id="coverPreviewContainer">
                                <div class="cover-placeholder-icon" id="coverPlaceholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect width="18" height="18" x="3" y="3" rx="2" />
                                        <circle cx="9" cy="9" r="2" />
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                    </svg>
                                    <span>Belum ada cover</span>
                                </div>
                                <img id="coverPreviewImage" src="#" alt="Preview cover" style="display:none;">
                            </div>
                            <div class="cover-upload-details">
                                <input type="file" id="cover" name="cover" class="cover-file-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp">
                                <label for="cover" class="cover-upload-btn">Pilih Cover Manual</label>
                                <p class="cover-source-note">Jika ISBN ditemukan dan cover tersedia, sistem akan
                                    <strong>mengunduh cover otomatis</strong> saat buku disimpan. Upload manual akan menjadi
                                    pilihan utama jika Anda memilih file sendiri.</p>
                                @error('cover')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-form-actions">
                    <a href="{{ route('books.index') }}" class="book-btn-cancel">Batal</a>
                    <button type="submit" class="book-btn-save">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>

    <div id="isbnScannerModal" class="isbn-scanner-modal" aria-hidden="true">
        <div class="isbn-scanner-backdrop"></div>
        <div class="isbn-scanner-dialog">
            <div class="isbn-scanner-header">
                <div>
                    <h3>Scan Barcode ISBN</h3>
                    <p>Arahkan kamera ke barcode ISBN pada buku.</p>
                </div>
                <button type="button" id="closeIsbnScanner" class="isbn-scanner-close">×</button>
            </div>
            <div class="isbn-scanner-camera">
                <div id="isbnReader"></div>
                <div class="isbn-scanner-frame"></div>
            </div>
            <div id="isbnScannerStatus" class="isbn-scanner-status">Menyiapkan kamera…</div>
            <button type="button" id="cancelIsbnScanner" class="isbn-scanner-cancel">Tutup</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isbnInput = document.getElementById('isbn');
            const lookupBtn = document.getElementById('lookupIsbnButton');
            const scanBtn = document.getElementById('scanIsbnButton');
            const status = document.getElementById('isbnStatus');
            const sourceBadge = document.getElementById('sourceBadge');
            const coverUrl = document.getElementById('cover_url');
            const coverPreview = document.getElementById('coverPreviewImage');
            const coverPlaceholder = document.getElementById('coverPlaceholder');
            const coverInput = document.getElementById('cover');
            const category = document.getElementById('category_id');
            const subcategory = document.getElementById('subcategory_id');
            const subcategories = @json($subcategoryData);
            const oldSubcategory = @json(old('subcategory_id'));
            const scannerModal = document.getElementById('isbnScannerModal');
            const scannerStatus = document.getElementById('isbnScannerStatus');
            let scanner = null;

            function normalizeIsbn(value) {
                return (value || '').toUpperCase().replace(/[^0-9X]/g, '');
            }

            function setStatus(message, type = 'info') {
                status.textContent = message;
                status.className = 'isbn-status show ' + type;
            }

            function setValue(id, value) {
                const el = document.getElementById(id);
                if (el && value !== null && value !== undefined && value !== '') el.value = value;
            }

            function populateSubcategories(selected = '') {
                const items = subcategories[category.value] || [];
                subcategory.innerHTML = '<option value="">-- Pilih Subkategori --</option>';
                items.forEach(item => {
                    const option = new Option(item.name, item.id, false, String(item.id) === String(
                        selected));
                    subcategory.add(option);
                });
            }
            category.addEventListener('change', () => populateSubcategories());
            populateSubcategories(oldSubcategory);

            function showCover(url) {
                if (!url) return;
                coverUrl.value = url;
                coverPreview.src = url;
                coverPreview.style.display = 'block';
                coverPlaceholder.style.display = 'none';
            }
            coverInput.addEventListener('change', () => {
                const file = coverInput.files?.[0];
                if (!file) return;
                coverUrl.value = '';
                coverPreview.src = URL.createObjectURL(file);
                coverPreview.style.display = 'block';
                coverPlaceholder.style.display = 'none';
            });

            async function lookupIsbn() {
                const isbn = normalizeIsbn(isbnInput.value);
                isbnInput.value = isbn;
                if (!/^\d{10}$|^\d{13}$/.test(isbn)) {
                    setStatus('ISBN harus berupa ISBN-10 atau ISBN-13.', 'error');
                    isbnInput.focus();
                    return;
                }
                lookupBtn.disabled = true;
                setStatus('Mencari metadata buku…', 'info');
                sourceBadge.className = 'source-badge';
                try {
                    const response = await fetch('{{ route('books.isbn.lookup') }}?isbn=' + encodeURIComponent(
                        isbn), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        setStatus(result.message ||
                            'Metadata buku tidak ditemukan. ISBN tetap disimpan untuk pengisian manual.',
                            'error');
                        return;
                    }
                    const data = result.data || {};
                    setValue('isbn', data.isbn);
                    setValue('title', data.title);
                    setValue('author', data.author);
                    setValue('publisher', data.publisher);
                    setValue('publication_year', data.publication_year);
                    setValue('edition', data.edition);
                    setValue('description', data.description);
                    if (data.cover) showCover(data.cover);
                    setStatus(result.message ||
                        'Metadata berhasil ditemukan. Periksa kembali sebelum menyimpan.', 'success');
                    sourceBadge.textContent = 'Sumber metadata: ' + (data.source || 'API') + (data.source_url ?
                        ' · tersedia untuk referensi' : '');
                    sourceBadge.className = 'source-badge show';
                    document.getElementById('title').focus();
                } catch (error) {
                    console.error(error);
                    setStatus(
                        'Terjadi kendala saat menghubungi sumber metadata. Silakan lengkapi data secara manual.',
                        'error');
                } finally {
                    lookupBtn.disabled = false;
                }
            }
            lookupBtn.addEventListener('click', lookupIsbn);
            isbnInput.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    lookupIsbn();
                }
            });

            async function stopScanner() {
                if (scanner) {
                    try {
                        if (scanner.isScanning) await scanner.stop();
                    } catch (e) {
                        console.warn(e);
                    }
                    try {
                        scanner.clear();
                    } catch (e) {}
                    scanner = null;
                }
                scannerModal.classList.remove('is-open');
                scannerModal.setAttribute('aria-hidden', 'true');
            }
            async function startScanner() {
                scannerModal.classList.add('is-open');
                scannerModal.setAttribute('aria-hidden', 'false');
                scannerStatus.textContent = 'Menyiapkan kamera…';
                try {
                    scanner = new Html5Qrcode('isbnReader');
                    await scanner.start({
                        facingMode: 'environment'
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 300,
                            height: 120
                        },
                        formatsToSupport: [Html5QrcodeSupportedFormats.EAN_13,
                            Html5QrcodeSupportedFormats.EAN_8, Html5QrcodeSupportedFormats.CODE_128
                        ]
                    }, async decoded => {
                        const normalized = normalizeIsbn(decoded);
                        if (!/^\d{10}$|^\d{13}$/.test(normalized)) {
                            scannerStatus.textContent =
                                'Barcode terbaca, tetapi bukan format ISBN-10/ISBN-13.';
                            return;
                        }
                        isbnInput.value = normalized;
                        scannerStatus.textContent = 'ISBN ditemukan. Mengambil metadata…';
                        await stopScanner();
                        lookupIsbn();
                    });
                    scannerStatus.textContent = 'Kamera aktif. Arahkan barcode ke dalam kotak.';
                } catch (error) {
                    console.error(error);
                    scannerStatus.textContent =
                        'Kamera tidak dapat digunakan. Pastikan izin kamera diberikan, lalu gunakan input ISBN manual.';
                }
            }
            scanBtn.addEventListener('click', startScanner);
            document.getElementById('closeIsbnScanner').addEventListener('click', stopScanner);
            document.getElementById('cancelIsbnScanner').addEventListener('click', stopScanner);
            document.querySelector('.isbn-scanner-backdrop').addEventListener('click', stopScanner);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && scannerModal.classList.contains('is-open')) stopScanner();
            });
        });
    </script>
@endpush
