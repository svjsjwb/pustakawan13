<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->back()
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
            ->back()
            ->with('success', 'Aktivitas berhasil diperbarui.');
    }


    /**
     * Tandai announcement sebagai sudah dibaca oleh user.
     */
    public function markAsRead(
        Request $request,
        Activity $activity
    ) {
        $user = $request->user();

        if ($user) {
            $activity->readers()->syncWithoutDetaching([
                $user->id => [
                    'read_at' => now(),
                ],
            ]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }


    /**
     * Tampilkan seluruh aktivitas perpustakaan.
     */
    public function index(Request $request)
    {
        $activities = collect();


        /*
        |--------------------------------------------------------------------------
        | 1. ANGGOTA BARU
        |--------------------------------------------------------------------------
        */

        $members = Member::latest()
            ->take(100)
            ->get();

        foreach ($members as $member) {

            $activities->push([
                'id' => null,
                'type' => 'member',
                'title' => 'Anggota baru',
                'description' => $member->name ?? '-',
                'created_at' => $member->created_at,
                'icon' => '👥',
                'pinned_at' => null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 2. BUKU BARU
        |--------------------------------------------------------------------------
        */

        $books = Book::latest()
            ->take(100)
            ->get();

        foreach ($books as $book) {

            $activities->push([
                'id' => null,
                'type' => 'book',
                'title' => 'Koleksi buku baru',
                'description' => $book->title ?? '-',
                'created_at' => $book->created_at,
                'icon' => '📚',
                'pinned_at' => null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. RESERVASI BARU
        |--------------------------------------------------------------------------
        */

        $reservations = Reservation::with([
            'member',
            'book',
        ])
            ->latest()
            ->take(100)
            ->get();

        foreach ($reservations as $reservation) {

            $activities->push([
                'id' => null,
                'type' => 'reservation',
                'title' => 'Reservasi baru',
                'description' => $reservation->member?->name ?? '-',
                'created_at' => $reservation->created_at,
                'icon' => '📅',
                'pinned_at' => null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. PEMINJAMAN BARU
        |--------------------------------------------------------------------------
        */

        $borrowings = Borrowing::with([
            'member',
            'details.book',
        ])
            ->latest()
            ->take(100)
            ->get();

        foreach ($borrowings as $borrowing) {

            $activities->push([
                'id' => null,
                'type' => 'borrowing',
                'title' => 'Peminjaman baru',
                'description' => $borrowing->member?->name ?? '-',
                'created_at' => $borrowing->created_at,
                'icon' => '📖',
                'pinned_at' => null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. ANNOUNCEMENT MANUAL
        |--------------------------------------------------------------------------
        */

        $manualActivities = Activity::latest()
            ->get();

        foreach ($manualActivities as $activity) {

            $activities->push([
                'id' => $activity->id,
                'type' => 'manual',
                'title' => $activity->title,
                'description' => $activity->description ?? '-',
                'created_at' => $activity->created_at,
                'icon' => '📢',
                'pinned_at' => $activity->pinned_at,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | URUTKAN BERDASARKAN AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        $activities = $activities
            ->sortByDesc('created_at')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL
        |--------------------------------------------------------------------------
        */

        $totalActivities = $activities->count();

        $pinnedActivities = $activities
            ->filter(function ($activity) {
                return !empty($activity['pinned_at']);
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filter = $request->input(
            'filter',
            'all'
        );

        if ($filter === 'pinned') {

            $filteredActivities = $activities
                ->filter(function ($activity) {
                    return !empty($activity['pinned_at']);
                })
                ->values();

        } else {

            $filteredActivities = $activities;
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        | DEFAULT = 10
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input(
            'per_page',
            10
        );

        if (!in_array(
            $perPage,
            [10, 25, 50, 100]
        )) {
            $perPage = 10;
        }


        /*
        |--------------------------------------------------------------------------
        | CURRENT PAGE
        |--------------------------------------------------------------------------
        */

        $currentPage =
            LengthAwarePaginator::resolveCurrentPage();


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA SESUAI HALAMAN
        |--------------------------------------------------------------------------
        */

        $currentItems = $filteredActivities
            ->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | BUAT PAGINATOR
        |--------------------------------------------------------------------------
        */

        $activities = new LengthAwarePaginator(
            $currentItems,
            $filteredActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'activities.index',
            compact(
                'activities',
                'totalActivities',
                'pinnedActivities',
                'filter',
                'perPage'
            )
        );
    }


    /**
     * Hapus aktivitas manual.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Aktivitas berhasil dihapus.'
            );
    }


    /**
     * Pin / lepas pin aktivitas manual.
     */
    public function pin(Activity $activity)
    {
        /*
        |--------------------------------------------------------------------------
        | Simpan status sebelum diubah
        |--------------------------------------------------------------------------
        */

        $isPinned = !empty(
            $activity->pinned_at
        );


        /*
        |--------------------------------------------------------------------------
        | Toggle PIN
        |--------------------------------------------------------------------------
        */

        $activity->update([
            'pinned_at' => $isPinned
                ? null
                : now(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Pesan
        |--------------------------------------------------------------------------
        */

        $message = $isPinned
            ? 'Pin aktivitas dilepas.'
            : 'Aktivitas berhasil dipin.';


        /*
        |--------------------------------------------------------------------------
        | Kembali ke halaman asal
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->back()
            ->with(
                'success',
                $message
            );
    }
}