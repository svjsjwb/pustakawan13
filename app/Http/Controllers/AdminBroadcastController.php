<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class AdminBroadcastController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET /admin/broadcast
    |--------------------------------------------------------------------------
    | Halaman form kirim broadcast email.
    */
    public function index(): View
    {
        $stats = $this->getAudienceStats();
        $recentHistory = EmailLog::where('notification_type', 'broadcast')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.broadcast.index', compact('stats', 'recentHistory'));
    }

    /*
    |--------------------------------------------------------------------------
    | GET /admin/broadcast/preview (AJAX)
    |--------------------------------------------------------------------------
    | Menampilkan estimasi jumlah penerima valid & yang akan di-skip
    | SEBELUM admin menekan tombol kirim.
    */
    public function preview(Request $request): \Illuminate\Http\JsonResponse
    {
        $target = $request->input('target_audience', 'all');
        $stats  = $this->getAudienceStats($target);

        return response()->json($stats);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /admin/broadcast
    |--------------------------------------------------------------------------
    | Validasi input, dispatch queue, catat statistik awal.
    */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject'         => ['required', 'string', 'max:255'],
            'message'         => ['required', 'string', 'max:10000'],
            'target_audience' => ['required', 'in:all,members,admins'],
            'action_url'      => ['nullable', 'url', 'max:500'],
            'action_label'    => ['nullable', 'string', 'max:100'],
        ]);

        $senderName = $request->user()->name . ' (Admin Perpustakaan)';

        $result = NotificationService::broadcastNotification(
            subject:        $validated['subject'],
            message:        $validated['message'],
            targetAudience: $validated['target_audience'],
            actionUrl:      $validated['action_url'] ?? null,
            actionLabel:    $validated['action_label'] ?? 'Buka Aplikasi',
            senderName:     $senderName,
        );

        $message = sprintf(
            'Broadcast berhasil dimasukkan ke antrean. Total target: %d pengguna, %d email valid telah dijadwalkan, %d email dilewati (dummy/invalid).',
            $result['total_targeted'],
            $result['valid_queued'],
            $result['skipped_invalid']
        );

        return redirect()->route('admin.broadcast.history')
            ->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /admin/broadcast/history
    |--------------------------------------------------------------------------
    | Riwayat broadcast dan status pengiriman per email.
    */
    public function history(Request $request): View
    {
        $query = EmailLog::with('user')
            ->where('notification_type', 'broadcast');

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan subject/keyword jika ada
        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        $summary = [
            'queued'  => EmailLog::where('notification_type', 'broadcast')->where('status', 'queued')->count(),
            'sent'    => EmailLog::where('notification_type', 'broadcast')->where('status', 'sent')->count(),
            'failed'  => EmailLog::where('notification_type', 'broadcast')->where('status', 'failed')->count(),
            'skipped' => EmailLog::where('notification_type', 'broadcast')->where('status', 'skipped')->count(),
        ];

        return view('admin.broadcast.history', compact('logs', 'summary'));
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: hitung statistik audiens
    |--------------------------------------------------------------------------
    */
    private function getAudienceStats(string $target = 'all'): array
    {
        $query = User::query()->whereNotNull('email')->where('email', '!=', '');

        if ($target === 'members') {
            $query->where('role', '!=', 'admin');
        } elseif ($target === 'admins') {
            $query->where('role', 'admin');
        }

        $total   = $query->count();
        $invalid = $query->clone()->where(function ($q) {
            $q->whereRaw("LOWER(email) LIKE '%@example.com'")
              ->orWhereRaw("LOWER(email) LIKE '%@test.local'")
              ->orWhereRaw("email NOT REGEXP '^[^@]+@[^@]+\\.[^@]{2,}$'");
        })->count();

        return [
            'total_targeted'  => $total,
            'valid_queued'    => $total - $invalid,
            'skipped_invalid' => $invalid,
            'provider'        => config('mail.default', 'smtp'),
        ];
    }
}
