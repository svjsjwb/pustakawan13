@extends('layouts.app')

@section('title', 'Broadcast Email — Admin')

@push('styles')
<style>
/* =====================================================
   BROADCAST PAGE STYLES
   Hanya dipakai di halaman ini, tidak memengaruhi halaman lain.
====================================================== */
.broadcast-page {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px 60px;
}
.broadcast-header {
    margin-bottom: 32px;
}
.broadcast-header h1 {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px 0;
}
.broadcast-header p {
    color: #64748b;
    font-size: 14px;
    margin: 0;
}
.broadcast-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}
.broadcast-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    margin-bottom: 24px;
}
.broadcast-form-group {
    margin-bottom: 22px;
}
.broadcast-form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
}
.broadcast-form-group small {
    display: block;
    font-size: 12px;
    color: #94a3b8;
    margin-top: 4px;
}
.broadcast-form-group input,
.broadcast-form-group textarea,
.broadcast-form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
}
.broadcast-form-group input:focus,
.broadcast-form-group textarea:focus,
.broadcast-form-group select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    background: #fff;
}
.broadcast-form-group textarea {
    min-height: 140px;
    resize: vertical;
}
/* Preview Stats Box */
.preview-stats {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 18px 22px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.preview-stat {
    text-align: center;
}
.preview-stat .stat-val {
    font-size: 28px;
    font-weight: 800;
    color: #15803d;
    display: block;
}
.preview-stat .stat-lbl {
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    font-weight: 600;
}
.preview-stats.danger {
    background: #fef2f2;
    border-color: #fecaca;
}
.preview-stats.danger .stat-val { color: #dc2626; }
.preview-stats.loading {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.preview-stats.loading .stat-val { color: #94a3b8; }
/* Provider badge */
.provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 700;
}
.provider-resend { background: #fdf4ff; color: #7c3aed; border: 1px solid #e9d5ff; }
.provider-smtp   { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.provider-log    { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
/* Buttons */
.btn-broadcast-preview {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 22px; border-radius: 10px; font-weight: 700; font-size: 14px;
    border: 1.5px solid #2563eb; color: #2563eb; background: #fff;
    cursor: pointer; transition: all .15s;
}
.btn-broadcast-preview:hover { background: #eff6ff; }
.btn-broadcast-send {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 14px;
    background: linear-gradient(135deg,#1e3a8a,#2563eb); color: #fff;
    border: none; cursor: pointer; transition: all .15s;
    box-shadow: 0 4px 12px rgba(37,99,235,.25);
}
.btn-broadcast-send:hover { opacity: .9; transform: translateY(-1px); }
.btn-broadcast-send:disabled { opacity: .5; cursor: not-allowed; transform: none; }
/* Flash */
.alert-success {
    background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
    padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;
}
.alert-error {
    background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
    padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;
}
/* Modal Konfirmasi */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 9999;
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff; border-radius: 16px; padding: 32px;
    max-width: 460px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { transform: translateY(20px); opacity:0; }
    to   { transform: translateY(0);    opacity:1; }
}
.modal-box h3 { margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: #0f172a; }
.modal-box p  { color: #64748b; font-size: 14px; margin: 0 0 22px 0; }
.modal-actions { display: flex; gap: 12px; justify-content: flex-end; }
.btn-cancel {
    padding: 10px 20px; border-radius: 10px; border: 1.5px solid #e2e8f0;
    background: #fff; color: #374151; font-weight: 700; cursor: pointer;
}
.btn-cancel:hover { background: #f8fafc; }
/* History link */
.history-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #2563eb; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: opacity .15s;
}
.history-link:hover { opacity: .75; }
.recent-table { width:100%; border-collapse:collapse; font-size:13px; }
.recent-table th {
    text-align:left; padding:8px 10px; font-size:11px; font-weight:700;
    color:#64748b; text-transform:uppercase; letter-spacing:.4px;
    border-bottom:1px solid #e2e8f0;
}
.recent-table td {
    padding:9px 10px; color:#1e293b; border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
}
.status-badge {
    padding:2px 8px; border-radius:9999px; font-size:11px; font-weight:700;
}
.status-sent    { background:#d1fae5; color:#065f46; }
.status-queued  { background:#fef3c7; color:#92400e; }
.status-failed  { background:#fee2e2; color:#991b1b; }
.status-skipped { background:#f1f5f9; color:#64748b; }
</style>
@endpush

@section('content')
<section class="broadcast-page">

    {{-- Header --}}
    <div class="broadcast-header">
        <div class="broadcast-badge">📢 Email Notification</div>
        <h1>Kirim Broadcast Email</h1>
        <p>Kirimkan pengumuman atau informasi penting kepada pengguna perpustakaan melalui email.</p>
    </div>

    {{-- Flash Success --}}
    @if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-error">
        <strong>Perhatian:</strong>
        <ul style="margin:4px 0 0 18px;padding:0;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Preview Stats (Live) --}}
    <div class="preview-stats loading" id="previewBox">
        <div class="preview-stat">
            <span class="stat-val" id="pv-total">—</span>
            <span class="stat-lbl">Total Target</span>
        </div>
        <div class="preview-stat">
            <span class="stat-val" id="pv-valid">—</span>
            <span class="stat-lbl">Email Valid ✓</span>
        </div>
        <div class="preview-stat">
            <span class="stat-val" id="pv-skip">—</span>
            <span class="stat-lbl">Akan Di-skip</span>
        </div>
        <div class="preview-stat">
            <span class="stat-val" id="pv-provider">—</span>
            <span class="stat-lbl">Provider</span>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="broadcast-card">
        <form id="broadcastForm" action="{{ route('admin.broadcast.send') }}" method="POST">
            @csrf

            <div class="broadcast-form-group">
                <label for="bc-subject">Subject Email <span style="color:#ef4444">*</span></label>
                <input type="text" id="bc-subject" name="subject" maxlength="255"
                       placeholder="Contoh: Pengumuman Jadwal Libur Perpustakaan"
                       value="{{ old('subject') }}" required>
            </div>

            <div class="broadcast-form-group">
                <label for="bc-message">Isi Pesan <span style="color:#ef4444">*</span></label>
                <textarea id="bc-message" name="message" maxlength="10000"
                          placeholder="Tuliskan isi pengumuman di sini..." required>{{ old('message') }}</textarea>
                <small>Maksimal 10.000 karakter. Baris baru akan ditampilkan sebagaimana mestinya di email.</small>
            </div>

            <div class="broadcast-form-group">
                <label for="bc-target">Target Penerima <span style="color:#ef4444">*</span></label>
                <select id="bc-target" name="target_audience" required>
                    <option value="all"     {{ old('target_audience','all') === 'all'     ? 'selected' : '' }}>Semua pengguna (admin + anggota)</option>
                    <option value="members" {{ old('target_audience')       === 'members' ? 'selected' : '' }}>Anggota saja (bukan admin)</option>
                    <option value="admins"  {{ old('target_audience')       === 'admins'  ? 'selected' : '' }}>Admin saja</option>
                </select>
            </div>

            <div class="broadcast-form-group">
                <label for="bc-action-url">URL Tombol Aksi <span style="color:#94a3b8">(opsional)</span></label>
                <input type="url" id="bc-action-url" name="action_url"
                       placeholder="https://... (dikosongkan jika tidak diperlukan)"
                       value="{{ old('action_url') }}">
            </div>

            <div class="broadcast-form-group">
                <label for="bc-action-label">Label Tombol <span style="color:#94a3b8">(opsional)</span></label>
                <input type="text" id="bc-action-label" name="action_label" maxlength="100"
                       placeholder="Contoh: Lihat Pengumuman"
                       value="{{ old('action_label', 'Buka Aplikasi') }}">
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:28px;">
                <a href="{{ route('admin.broadcast.history') }}" class="history-link">
                    📋 Lihat Riwayat Broadcast
                </a>
                <button type="button" id="btnSend" class="btn-broadcast-send" disabled>
                    🚀 Kirim Broadcast
                </button>
            </div>
        </form>
    </div>

    {{-- Riwayat Singkat --}}
    @if($recentHistory->isNotEmpty())
    <div class="broadcast-card">
        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px 0;">5 Email Broadcast Terakhir</h3>
        <table class="recent-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentHistory as $log)
                <tr>
                    <td>{{ $log->email }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($log->subject, 40) }}</td>
                    <td><span class="status-badge status-{{ $log->status }}">{{ $log->status }}</span></td>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</section>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <h3>🚀 Konfirmasi Kirim Broadcast</h3>
        <p id="modalDesc">Apakah Anda yakin ingin mengirim broadcast ini?</p>
        <div class="modal-actions">
            <button class="btn-cancel" id="btnCancelModal" type="button">Batal</button>
            <button class="btn-broadcast-send" id="btnConfirmSend" type="button">Ya, Kirim Sekarang</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const previewUrl  = "{{ route('admin.broadcast.preview') }}";
    const previewBox  = document.getElementById('previewBox');
    const pTotal      = document.getElementById('pv-total');
    const pValid      = document.getElementById('pv-valid');
    const pSkip       = document.getElementById('pv-skip');
    const pProvider   = document.getElementById('pv-provider');
    const targetSel   = document.getElementById('bc-target');
    const btnSend     = document.getElementById('btnSend');
    const modal       = document.getElementById('confirmModal');
    const btnCancel   = document.getElementById('btnCancelModal');
    const btnConfirm  = document.getElementById('btnConfirmSend');
    const form        = document.getElementById('broadcastForm');

    let lastStats = null;

    function providerClass(p) {
        if (p === 'resend') return 'provider-resend';
        if (p === 'smtp')   return 'provider-smtp';
        return 'provider-log';
    }

    function fetchPreview(target) {
        previewBox.className = 'preview-stats loading';
        pTotal.textContent = '…'; pValid.textContent = '…';
        pSkip.textContent  = '…'; pProvider.textContent = '…';
        btnSend.disabled = true;

        fetch(previewUrl + '?target_audience=' + encodeURIComponent(target), {
            headers: { 'X-Requested-With': 'XMLHttpRequest',
                       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            lastStats = data;
            pTotal.textContent    = data.total_targeted;
            pValid.textContent    = data.valid_queued;
            pSkip.textContent     = data.skipped_invalid;
            pProvider.innerHTML   = '<span class="provider-badge ' + providerClass(data.provider) + '">' + data.provider.toUpperCase() + '</span>';
            previewBox.className  = data.valid_queued > 0 ? 'preview-stats' : 'preview-stats danger';
            btnSend.disabled      = data.valid_queued === 0;
        })
        .catch(() => {
            previewBox.className = 'preview-stats danger';
            pTotal.textContent = 'ERR';
        });
    }

    // Initial load
    fetchPreview(targetSel.value);
    targetSel.addEventListener('change', () => fetchPreview(targetSel.value));

    // Tombol kirim → buka modal konfirmasi
    btnSend.addEventListener('click', function() {
        if (!lastStats) return;
        document.getElementById('modalDesc').textContent =
            'Broadcast akan dikirim ke ' + lastStats.valid_queued + ' pengguna valid melalui provider ' +
            lastStats.provider.toUpperCase() + '. ' + lastStats.skipped_invalid + ' email akan dilewati (dummy/invalid). Apakah Anda yakin?';
        modal.classList.add('open');
    });

    btnCancel.addEventListener('click', () => modal.classList.remove('open'));
    modal.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.remove('open');
    });

    // Submit form setelah konfirmasi
    btnConfirm.addEventListener('click', function() {
        btnConfirm.disabled = true;
        btnConfirm.textContent = 'Memproses…';
        form.submit();
    });
})();
</script>
@endpush
