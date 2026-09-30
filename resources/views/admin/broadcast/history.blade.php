@extends('layouts.app')

@section('title', 'Riwayat Broadcast Email — Admin')

@push('styles')
<style>
.history-page { max-width: 1100px; margin: 40px auto; padding: 0 20px 60px; }
.history-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 28px; }
.history-header h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; }
.history-badge { display:inline-flex;align-items:center;gap:6px;background:#e0f2fe;color:#0369a1;font-size:12px;font-weight:700;padding:4px 12px;border-radius:9999px;text-transform:uppercase;letter-spacing:.5px;}
.btn-back { display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;color:#374151;font-weight:700;font-size:13px;text-decoration:none;transition:background .15s; }
.btn-back:hover { background: #f8fafc; }
.btn-new { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;font-weight:700;font-size:13px;text-decoration:none;transition:opacity .15s; }
.btn-new:hover { opacity:.85; }
/* Summary Cards */
.summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:28px; }
.summary-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px 18px; text-align:center; }
.summary-card .sc-val { font-size:32px; font-weight:800; display:block; }
.summary-card .sc-lbl { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.4px; font-weight:600; }
.sc-queued  .sc-val { color: #b45309; }
.sc-sent    .sc-val { color: #15803d; }
.sc-failed  .sc-val { color: #dc2626; }
.sc-skipped .sc-val { color: #64748b; }
/* Filter bar */
.filter-bar { background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:22px; }
.filter-bar select, .filter-bar input[type="search"] {
    padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#1e293b;background:#f8fafc;
}
.filter-bar input[type="search"] { flex:1; min-width:180px; }
.btn-filter { padding:8px 18px;border-radius:8px;background:#2563eb;color:#fff;font-weight:700;font-size:13px;border:none;cursor:pointer; }
/* Table */
.log-table-wrap { background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden; }
.log-table { width:100%;border-collapse:collapse;font-size:13px; }
.log-table th { text-align:left;padding:12px 14px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;background:#f8fafc;border-bottom:1px solid #e2e8f0; }
.log-table td { padding:10px 14px;color:#1e293b;border-bottom:1px solid #f1f5f9;vertical-align:middle; }
.log-table tr:last-child td { border-bottom: none; }
.log-table tr:hover td { background: #f8fafc; }
.status-badge { padding:2px 9px;border-radius:9999px;font-size:11px;font-weight:700; }
.status-sent    { background:#d1fae5;color:#065f46; }
.status-queued  { background:#fef3c7;color:#92400e; }
.status-failed  { background:#fee2e2;color:#991b1b; }
.status-skipped { background:#f1f5f9;color:#64748b; }
.empty-state { text-align:center;padding:60px 0;color:#94a3b8;font-size:14px; }
</style>
@endpush

@section('content')
<section class="history-page">

    <div class="history-header">
        <div>
            <div class="history-badge">📋 Riwayat</div>
            <h1>Riwayat Broadcast Email</h1>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.broadcast.index') }}" class="btn-new">📢 Kirim Broadcast Baru</a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card sc-queued">
            <span class="sc-val">{{ $summary['queued'] }}</span>
            <span class="sc-lbl">Dalam Antrean</span>
        </div>
        <div class="summary-card sc-sent">
            <span class="sc-val">{{ $summary['sent'] }}</span>
            <span class="sc-lbl">Berhasil Terkirim</span>
        </div>
        <div class="summary-card sc-failed">
            <span class="sc-val">{{ $summary['failed'] }}</span>
            <span class="sc-lbl">Gagal</span>
        </div>
        <div class="summary-card sc-skipped">
            <span class="sc-val">{{ $summary['skipped'] }}</span>
            <span class="sc-lbl">Di-skip</span>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.broadcast.history') }}" class="filter-bar">
        <input type="search" name="search" placeholder="Cari subject…" value="{{ request('search') }}">
        <select name="status">
            <option value="">Semua Status</option>
            <option value="queued"  {{ request('status') === 'queued'  ? 'selected' : '' }}>Antrean</option>
            <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>Terkirim</option>
            <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Gagal</option>
            <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Di-skip</option>
        </select>
        <button type="submit" class="btn-filter">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.broadcast.history') }}" style="font-size:13px;color:#64748b;">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="log-table-wrap">
        @if($logs->isEmpty())
            <div class="empty-state">
                📭 Belum ada riwayat broadcast email.<br>
                <a href="{{ route('admin.broadcast.index') }}" style="color:#2563eb;font-weight:600;margin-top:10px;display:inline-block;">Kirim Broadcast Pertama →</a>
            </div>
        @else
        <table class="log-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email Penerima</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Pesan Error</th>
                    <th>Dikirim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                <tr>
                    <td style="color:#94a3b8;font-size:11px;">{{ $logs->firstItem() + $i }}</td>
                    <td>{{ $log->email }}</td>
                    <td style="max-width:260px;">
                        <span title="{{ $log->subject }}">{{ \Illuminate\Support\Str::limit($log->subject, 55) }}</span>
                    </td>
                    <td><span class="status-badge status-{{ $log->status }}">{{ $log->status }}</span></td>
                    <td style="color:#ef4444;font-size:12px;max-width:200px;">
                        {{ $log->error_message ? \Illuminate\Support\Str::limit($log->error_message, 60) : '—' }}
                    </td>
                    <td style="white-space:nowrap;color:#64748b;">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div style="padding:16px 18px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>

</section>
@endsection
