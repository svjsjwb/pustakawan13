<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $query = AppNotification::where('user_id', Auth::id())
            ->where('role', 'user')
            ->latest();

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        return view('user.notifications', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'filter' => $filter,
            'unreadCount' => AppNotification::where('user_id', Auth::id())
                ->where('role', 'user')
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function dismiss(Request $request)
    {
        $key      = $request->input('key');
        $dismissed = session('notif_dismissed', []);

        if ($key && !in_array($key, $dismissed)) {
            $dismissed[] = $key;
            session(['notif_dismissed' => $dismissed]);
        }

        return response()->json(['ok' => true]);
    }

    public function dismissAll(Request $request)
    {
        $keys = $request->input('keys', []);
        $dismissed = session('notif_dismissed', []);
        $dismissed = array_unique(array_merge($dismissed, $keys));
        session(['notif_dismissed' => $dismissed]);

        return response()->json(['ok' => true]);
    }
}
