<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        // ── Data Statistik Informasi Anggota ──────────────────────────────
        $totalBorrowed     = $member ? $member->borrowings()->count() : 0;
        $activeLoans       = $member ? $member->borrowings()->where('status', 'dipinjam')->count() : 0;
        $completedLoans    = $member ? $member->borrowings()->where('status', 'dikembalikan')->count() : 0;
        $totalReservations = $member ? \App\Models\Reservation::where('member_id', $member->id)->count() : 0;

        // Buku Favorit
        $favoriteBook = null;
        if ($member) {
            $mostBorrowedDetail = \App\Models\BorrowingDetail::whereHas('borrowing', fn($q) => $q->where('member_id', $member->id))
                ->with('book')
                ->latest()
                ->first();
            $favoriteBook = $mostBorrowedDetail?->book?->title;
        }
        if (!$favoriteBook) {
            $favIds = session('user_favorites', []);
            if (!empty($favIds)) {
                $favoriteBook = \App\Models\Book::whereIn('id', $favIds)->value('judul_buku');
            }
        }
        $favoriteBook = $favoriteBook ?? 'Laskar Pelangi';

        // Kategori Favorit
        $favoriteCategory = 'Pendidikan';
        if (!empty($user->favorite_categories) && is_array($user->favorite_categories)) {
            $favoriteCategory = $user->favorite_categories[0];
        }

        // Tanggal Bergabung & Terakhir Login
        $joinDate   = $member?->created_at?->format('d M Y') ?? $user->created_at?->format('d M Y') ?? now()->format('d M Y');
        $lastLogin  = $user->last_login_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i');
        $accountStatus = $member?->status ?? 'aktif';

        $stats = [
            'totalBorrowed'     => $totalBorrowed,
            'activeLoans'       => $activeLoans,
            'completedLoans'    => $completedLoans,
            'totalReservations' => $totalReservations,
            'favoriteBook'      => $favoriteBook,
            'favoriteCategory'  => $favoriteCategory,
            'joinDate'          => $joinDate,
            'lastLogin'         => $lastLogin,
            'accountStatus'     => $accountStatus,
        ];

        // Daftar Pilihan Kategori & Genre Katalog
        $allCategories = ['Pendidikan', 'Anak-Anak', 'Remaja', 'Dewasa', 'Teknologi', 'Sains', 'Bisnis', 'Sejarah'];
        $allGenres     = ['Fiksi', 'Non Fiksi', 'Petualangan', 'Motivasi', 'Sains', 'Sejarah', 'Novel', 'Komik', 'Bisnis', 'Biografi'];

        return view('user.profile', compact('user', 'member', 'stats', 'allCategories', 'allGenres'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama tidak sesuai.'])
                ->withInput();
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    /**
     * Simpan preferensi pengguna (tema, densitas, notifikasi, katalog) ke database.
     * Dipanggil via AJAX (JSON) dari profile page.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $selectedTheme   = in_array($request->input('theme'), ['light', 'dark']) ? $request->input('theme') : 'light';
        $selectedDensity = in_array($request->input('layout_density'), ['compact', 'normal', 'comfortable']) ? $request->input('layout_density') : 'normal';

        $favCategories = $request->input('favorite_categories', []);
        if (is_string($favCategories)) {
            $favCategories = json_decode($favCategories, true) ?: array_filter(array_map('trim', explode(',', $favCategories)));
        }

        $favGenres = $request->input('favorite_genres', []);
        if (is_string($favGenres)) {
            $favGenres = json_decode($favGenres, true) ?: array_filter(array_map('trim', explode(',', $favGenres)));
        }

        $prefs = [
            'allow_notifications'           => $request->boolean('allow_notifications'),
            'email_notifications'           => $request->boolean('email_notifications'),
            'reservation_notifications'     => $request->boolean('reservation_notifications'),
            'borrowing_notifications'       => $request->boolean('borrowing_notifications'),
            'extension_notifications'       => $request->boolean('extension_notifications'),
            'return_reminder_notifications' => $request->boolean('return_reminder_notifications'),
            'late_return_notifications'     => $request->boolean('late_return_notifications'),
            'theme'                         => $selectedTheme,
            'layout_density'                => $selectedDensity,
            'favorite_categories'           => array_values((array) $favCategories),
            'favorite_genres'               => array_values((array) $favGenres),
            'last_theme_used'               => $selectedTheme,
        ];

        // Jika master switch OFF, nonaktifkan seluruh sub-notifikasi
        if (!$prefs['allow_notifications']) {
            $prefs['email_notifications']           = false;
            $prefs['reservation_notifications']     = false;
            $prefs['borrowing_notifications']       = false;
            $prefs['extension_notifications']       = false;
            $prefs['return_reminder_notifications'] = false;
            $prefs['late_return_notifications']     = false;
        }

        $user->update($prefs);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Preferensi berhasil diperbarui.',
                'prefs'   => $prefs,
            ]);
        }

        return back()->with('success', 'Preferensi berhasil diperbarui.');
    }
}
