<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        $query = AppNotification::latest();

        if ($user->isAdmin()) {
            $query->where('role', 'admin');
        } else {
            $query->where('user_id', $user->id)->where('role', 'user');
        }

        $unreadCount = (clone $query)->where('is_read', false)->count();
        $notifications = $query->take(15)->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notif = AppNotification::findOrFail($id);

        if ($user->isAdmin() && $notif->role === 'admin') {
            $notif->update(['is_read' => true]);
        } elseif ($notif->user_id === $user->id) {
            $notif->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false]);
        }

        if ($user->isAdmin()) {
            AppNotification::where('role', 'admin')->update(['is_read' => true]);
        } else {
            AppNotification::where('user_id', $user->id)->where('role', 'user')->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
