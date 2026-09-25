<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
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
