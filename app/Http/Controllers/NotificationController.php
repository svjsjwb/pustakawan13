<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca
     */
    public function markAllRead()
    {
        $user = Auth::user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca.',
        ]);
    }

    /**
     * Tandai satu notifikasi tertentu sebagai telah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        if ($user) {
            $notif = $user->notifications()->where('id', $id)->first();
            if ($notif) {
                $notif->markAsRead();
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Toggle izin notifikasi (Allow / Mute) secara persisten
     */
    public function toggleNotification(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($request->has('enabled')) {
            $user->is_notification_enabled = filter_var($request->enabled, FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('value')) {
            $user->is_notification_enabled = strtolower($request->value) === 'allow';
        } else {
            $user->is_notification_enabled = !$user->is_notification_enabled;
        }

        $user->save();

        return response()->json([
            'success'                 => true,
            'is_notification_enabled' => (bool) $user->is_notification_enabled,
            'status_text'             => $user->is_notification_enabled ? 'Allow' : 'Mute',
            'message'                 => $user->is_notification_enabled ? 'Notifikasi diizinkan.' : 'Notifikasi dinonaktifkan (Mute).',
        ]);
    }
}
