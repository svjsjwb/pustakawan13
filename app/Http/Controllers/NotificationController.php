<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
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
            $pendingMembersCount = $user->isAdmin()
                ? Member::whereHas('user', function ($query) {
                    $query->where('role', 'guest');
                })->count()
                : 0;
            $pendingBorrowingsCount = $user->isAdmin()
                ? Borrowing::where(function ($query) {
                    $query->where('status', 'menunggu')
                        ->orWhere('extension_status', 'menunggu');
                })->count()
                : 0;
            $pendingReservationsCount = $user->isAdmin()
                ? Reservation::where('status', 'menunggu')->count()
                : 0;

            return response()->json([
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
                'pending_members_count' => $pendingMembersCount,
                'pending_borrowings_count' => $pendingBorrowingsCount,
                'pending_reservations_count' => $pendingReservationsCount,
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
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

        return view('user.notifications', compact('notifications', 'totalCount', 'unreadCount', 'readCount', 'tab'));
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
