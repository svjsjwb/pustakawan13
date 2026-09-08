<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Simpan aktivitas manual dari dashboard admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Activity::create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    /**
     * Update aktivitas manual.
     */
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $activity->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    /**
     * Tandai announcement sebagai sudah dibaca oleh user.
     */
    public function markAsRead(Request $request, Activity $activity)
    {
        $user = $request->user();

        if ($user) {
            $activity->readers()->syncWithoutDetaching([
                $user->id => ['read_at' => now()],
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Hapus aktivitas manual.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Aktivitas berhasil dihapus.');
    }

    /**
     * Pin / lepas pin aktivitas manual.
     */
    public function pin(Activity $activity)
    {
        $activity->update([
            'pinned_at' => $activity->pinned_at ? null : now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', $activity->pinned_at
                ? 'Aktivitas berhasil dipin.'
                : 'Pin aktivitas dilepas.');
    }
}
