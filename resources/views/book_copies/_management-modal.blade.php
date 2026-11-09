@if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any() && !old('copy_form_action'))
    <div class="form-alert-error">
        <strong>Terdapat beberapa kesalahan:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="copyListView">
<div class="book-copy-toolbar">
    <div>
        <span class="book-copy-label">DAFTAR EKSEMPLAR</span>
        <h3>{{ $copies->count() }} Eksemplar</h3>
    </div>
    <button type="button" class="book-btn-save book-copy-add-btn" onclick="openCopyCreateInline(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span>Tambah Eksemplar</span>
    </button>
</div>

<div class="book-copy-table-wrapper">
    <table class="book-copy-table">
        <thead>
            <tr>
                <th>Copy</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($copies as $copy)
                @php
                    $statusLabel = [
                        'available' => 'Tersedia',
                        'reserved' => 'Direservasi',
                        'borrowed' => 'Dipinjam',
                        'lost' => 'Hilang',
                        'damaged' => 'Rusak',
                        'maintenance' => 'Maintenance',
                    ];
                    $statusClass = [
                        'available' => 'available',
                        'reserved' => 'reserved',
                        'borrowed' => 'borrowed',
                        'lost' => 'danger',
                        'damaged' => 'danger',
                        'maintenance' => 'maintenance',
                    ];
                @endphp
                <tr>
                    <td><strong class="copy-barcode">{{ $copy->copy }}</strong></td>
                    <td><span class="copy-status {{ $statusClass[$copy->status] ?? '' }}">
                        {{ $statusLabel[$copy->status] ?? $copy->status }}
                    </span></td>
                    <td>
                        @if ($copy->condition === 'baik')
                            <span class="copy-status available">Baik</span>
                        @elseif (in_array($copy->condition, ['rusak', 'hilang'], true))
                            <span class="copy-status danger">{{ ucfirst($copy->condition) }}</span>
                        @else
                            <span class="copy-status">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($copy->shelf)
                            <div class="copy-location">
                                <strong>{{ $copy->shelf->code }}</strong>
                                <span>{{ $copy->shelf->zone->floor->name }} / {{ $copy->shelf->zone->name }}</span>
                                <small>
                                    Section {{ $copy->section }} · {{ $copy->side === 'front' ? 'Depan' : 'Belakang' }}
                                    · Baris {{ $copy->row ?? '-' }} · Kolom {{ $copy->column ?? '-' }}
                                </small>
                            </div>
                        @else
                            <span class="copy-no-location">Lokasi belum diatur</span>
                        @endif
                    </td>
                    <td>
                        <div class="copy-actions">
                            <button type="button" class="copy-edit-btn" title="Edit eksemplar"
                                onclick="openCopyEditInline(this)"
                                data-update-url="{{ route('books.copies.update', [$book, $copy]) }}"
                                data-copy-id="{{ $copy->id }}"
                                data-copy-number="{{ $copy->copy }}" data-condition="{{ $copy->condition }}"
                                data-shelf-id="{{ $copy->shelf_id }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <form action="{{ route('books.copies.destroy', [$book, $copy]) }}" method="POST"
                                class="copy-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="copy-delete-btn" title="Hapus eksemplar"
                                    onclick="openCopyDeleteModal(this)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                        <path d="M10 11v6" />
                                        <path d="M14 11v6" />
                                        <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="copy-empty">
                        <div>
                            <strong>Belum ada eksemplar</strong>
                            <span>Tambahkan eksemplar pertama untuk buku ini.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<section id="copyCreateInline" class="copy-create-inline" hidden>
    @if ($errors->any() && old('copy_form_action') === 'create')
        <div class="form-alert-error">
            <strong>Terdapat beberapa kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="copyCreateForm" action="{{ route('books.copies.store', $book) }}" method="POST">
        @csrf
        <input type="hidden" name="copy_form_action" value="create">
        <div class="copy-form-section-heading">
            <span class="form-section-icon">1</span>
            <span class="form-section-title">Informasi Utama Buku</span>
        </div>
        <div class="copy-form-fields">
            <label class="copy-form-field">
                <span>Copy <span class="required">*</span></span>
                <input id="newCopyNumber" type="text" value="c.{{ $nextCopyNumber ?? '?' }}" readonly>
                <small>Nomor copy dibuat otomatis oleh sistem.</small>
            </label>
            <label class="copy-form-field">
                <span>Kondisi Fisik <span class="required">*</span></span>
                <select name="condition" required>
                    <option value="" disabled @selected(old('condition') === null)>-- Pilih Kondisi --</option>
                    <option value="baik" @selected(old('condition') === 'baik')>Baik</option>
                    <option value="rusak" @selected(old('condition') === 'rusak')>Rusak</option>
                    <option value="hilang" @selected(old('condition') === 'hilang')>Hilang</option>
                </select>
            </label>
        </div>
        <div class="copy-form-section-heading">
            <span class="form-section-icon">2</span>
            <span class="form-section-title">Lokasi Buku</span>
        </div>
        <div class="copy-form-fields">
            <label class="copy-form-field copy-form-field-shelf">
                <span>Rak <span class="required">*</span></span>
                <select name="shelf_id" required>
                    <option value="">-- Pilih Rak --</option>
                    @foreach ($floors as $floor)
                        @foreach ($floor->zones as $zone)
                            <optgroup label="{{ $floor->name }} · {{ $zone->name }}">
                                @foreach ($zone->shelves as $shelf)
                                    <option value="{{ $shelf->id }}"
                                        @selected(old('shelf_id', $book->copies->first()?->shelf_id) == $shelf->id)>
                                        {{ $shelf->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endforeach
                </select>
                <small>Rak mengikuti lokasi buku dan ditentukan otomatis oleh sistem.</small>
            </label>
        </div>
        <footer class="copy-form-modal-actions">
            <button type="button" class="copy-delete-modal-cancel" onclick="closeCopyCreateInline()">Batal</button>
            <button type="submit" class="copy-form-submit">Simpan Eksemplar</button>
        </footer>
    </form>
</section>

<section id="copyEditInline" class="copy-create-inline copy-edit-inline" hidden>
    @if ($errors->any() && old('copy_form_action') === 'edit')
        <div class="form-alert-error">
            <strong>Terdapat beberapa kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="copyUpdateForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="copy_form_action" value="edit">
        <input id="editCopyId" type="hidden" name="copy_id" value="{{ old('copy_id') }}">
        <div class="copy-form-section-heading">
            <span class="form-section-icon">1</span>
            <span class="form-section-title">Informasi Utama Buku</span>
        </div>
        <div class="copy-form-fields">
            <label class="copy-form-field">
                <span>Copy</span>
                <input id="editCopyNumber" type="text" readonly>
                <small>Nomor copy tidak dapat diubah.</small>
            </label>
            <label class="copy-form-field">
                <span>Kondisi Fisik <span class="required">*</span></span>
                <select id="editCopyCondition" name="condition" required>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="hilang">Hilang</option>
                </select>
            </label>
        </div>
        <div class="copy-form-section-heading">
            <span class="form-section-icon">2</span>
            <span class="form-section-title">Lokasi Buku</span>
        </div>
        <div class="copy-form-fields">
            <label class="copy-form-field copy-form-field-shelf">
                <span>Rak <span class="required">*</span></span>
                <select id="editCopyShelf" name="shelf_id" required>
                    <option value="">-- Pilih Rak --</option>
                    @foreach ($floors as $floor)
                        @foreach ($floor->zones as $zone)
                            <optgroup label="{{ $floor->name }} · {{ $zone->name }}">
                                @foreach ($zone->shelves as $shelf)
                                    <option value="{{ $shelf->id }}"
                                        @selected(old('shelf_id') == $shelf->id)>{{ $shelf->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endforeach
                </select>
                <small>Posisi fisik dihitung ulang otomatis jika rak diubah.</small>
            </label>
        </div>
        <footer class="copy-form-modal-actions">
            <button type="button" class="copy-delete-modal-cancel" onclick="closeCopyInline()">Batal</button>
            <button type="submit" class="copy-form-submit">Simpan Perubahan</button>
        </footer>
    </form>
</section>

<div id="copyDeleteModal" class="copy-delete-modal-overlay" aria-hidden="true">
    <div class="copy-delete-modal" role="dialog" aria-modal="true" aria-labelledby="copyDeleteModalTitle">
        <h3 id="copyDeleteModalTitle" class="copy-delete-modal-title">Hapus Eksemplar</h3>
        <p class="copy-delete-modal-description">
            Masukkan alasan penghapusan eksemplar <strong id="deleteCopyNumber">-</strong> dari
            <strong>{{ $book->title }}</strong>.
        </p>
        <label class="copy-delete-modal-reason" for="copyDeleteReason">
            Alasan Penghapusan <span class="required">*</span>
            <textarea id="copyDeleteReason" rows="3" maxlength="500"
                placeholder="Masukkan alasan eksemplar dihapus..." required></textarea>
        </label>
        <div class="copy-delete-modal-actions">
            <button type="button" class="copy-delete-modal-cancel" onclick="closeCopyDeleteModal()">Batal</button>
            <button type="button" class="copy-delete-modal-confirm" onclick="confirmCopyDelete()">Hapus Eksemplar</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const updateForm = document.getElementById('copyUpdateForm');
            const bookTitle = @json($book->title);
            const copyListView = document.getElementById('copyListView');
            const copyCreateInline = document.getElementById('copyCreateInline');
            const copyEditInline = document.getElementById('copyEditInline');
            const editTitle = document.getElementById('bookEditModalTitle');
            const editSubtitle = document.getElementById('bookEditModalSubtitle');
            const editModalPage = document.querySelector('#bookEditModal .book-form-modal-page');
            let previousTrigger = null;

            function showCopyInlineForm(mode) {
                const isEdit = mode === 'edit';
                if (copyListView) copyListView.hidden = true;
                if (copyCreateInline) copyCreateInline.hidden = isEdit;
                if (copyEditInline) copyEditInline.hidden = !isEdit;
                if (editTitle) editTitle.textContent = isEdit ? 'Edit Eksemplar' : 'Tambah Eksemplar';
                if (editSubtitle) {
                    editSubtitle.textContent = isEdit ?
                        `Perbarui informasi eksemplar untuk buku ${bookTitle}.` :
                        `Tambahan salinan fisik baru untuk buku ${bookTitle}.`;
                }
                if (editModalPage) editModalPage.scrollTop = 0;
            }

            window.openCopyCreateInline = () => {
                previousTrigger = document.querySelector('.book-copy-add-btn');
                showCopyInlineForm('create');
                copyCreateInline?.querySelector('select')?.focus();
            };

            window.openCopyEditInline = button => {
                if (!button) return;
                previousTrigger = button;
                updateForm.action = button.dataset.updateUrl;
                document.getElementById('editCopyId').value = button.dataset.copyId;
                document.getElementById('editCopyNumber').value = button.dataset.copyNumber;
                document.getElementById('editCopyCondition').value = button.dataset.condition;
                document.getElementById('editCopyShelf').value = button.dataset.shelfId || '';
                showCopyInlineForm('edit');
                document.getElementById('editCopyCondition')?.focus();
            };

            window.closeCopyInline = () => {
                if (copyListView) copyListView.hidden = false;
                if (copyCreateInline) copyCreateInline.hidden = true;
                if (copyEditInline) copyEditInline.hidden = true;
                if (editTitle) editTitle.textContent = 'Kelola Eksemplar';
                if (editSubtitle) editSubtitle.textContent = `Kelola salinan fisik buku ${bookTitle}.`;
                previousTrigger?.focus();
            };
            window.closeCopyCreateInline = window.closeCopyInline;

            @if ($errors->any() && old('copy_form_action') === 'edit')
                const copyId = @json(old('copy_id'));
                const button = document.querySelector('.copy-edit-btn[data-copy-id="' + copyId + '"]');
                if (button) {
                    window.openCopyEditInline(button);
                    document.getElementById('editCopyCondition').value = @json(old('condition'));
                    document.getElementById('editCopyShelf').value = @json(old('shelf_id'));
                }
            @elseif ($errors->any() && old('copy_form_action') === 'create')
                window.openCopyCreateInline();
            @endif
        })();
    </script>
@endpush
