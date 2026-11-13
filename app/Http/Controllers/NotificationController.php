<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['unread_count' => 0, 'notifications' => []]);
            }
            return redirect()->route('login');
        }

        // Jika request dari AJAX/API navbar dropdown
        if ($request->is('api/*') || $request->expectsJson()) {
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

        // Halaman web user notifications
        $tab = $request->query('tab', 'all');
        $baseQuery = AppNotification::where('user_id', $user->id)->where('role', 'user');

        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->where('is_read', false)->count();
        $readCount = (clone $baseQuery)->where('is_read', true)->count();

        $query = clone $baseQuery;
        if ($tab === 'unread') {
            $query->where('is_read', false);
        } elseif ($tab === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        $returnPath = $request->query('from');
        $returnPathName = is_string($returnPath) ? parse_url($returnPath, PHP_URL_PATH) : null;
        $validReturnPath = is_string($returnPath)
            && str_starts_with($returnPath, '/')
            && !str_starts_with($returnPath, '//')
            && !parse_url($returnPath, PHP_URL_HOST)
            && !parse_url($returnPath, PHP_URL_SCHEME)
            && $returnPathName !== '/notifications';

        if ($validReturnPath) {
            $returnUrl = url($returnPath);
        } else {
            $returnUrl = url()->previous();
            if (
                parse_url($returnUrl, PHP_URL_HOST) !== $request->getHost()
                || parse_url($returnUrl, PHP_URL_PATH) === '/notifications'
            ) {
                $returnUrl = route('user.home');
            }
        }

        return view('user.notifications', compact('notifications', 'totalCount', 'unreadCount', 'readCount', 'tab', 'returnUrl'));
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

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
            return response()->json(['success' => false], 401);
        }

        if ($user->isAdmin()) {
            AppNotification::where('role', 'admin')->update(['is_read' => true]);
        } else {
            AppNotification::where('user_id', $user->id)->where('role', 'user')->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
