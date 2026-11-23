<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $member = $user->member;

        // ── Data Statistik Informasi Anggota ──────────────────────────────
        $totalBorrowed     = $member ? $member->borrowings()->count() : 0;
        $activeLoans       = $member ? $member->borrowings()->where('status', 'dipinjam')->count() : 0;
        $completedLoans    = $member ? $member->borrowings()->where('status', 'dikembalikan')->count() : 0;
        $totalReservations = $member
            ? \App\Models\Reservation::where('member_id', $member->id)->count()
            : 0;

        // Buku Favorit
        $favoriteBook = null;

        if ($member) {
            $mostBorrowedDetail = \App\Models\BorrowingDetail::whereHas(
                'borrowing',
                fn($q) => $q->where('member_id', $member->id)
            )
                ->with('book')
                ->latest()
                ->first();

            $favoriteBook = $mostBorrowedDetail?->book?->title;
        }

        if (!$favoriteBook) {
            $favIds = session('user_favorites', []);

            if (!empty($favIds)) {
                $favoriteBook = \App\Models\Book::whereIn(
                    'id',
                    $favIds
                )->value('judul_buku');
            }
        }

        $favoriteBook = $favoriteBook ?? 'Laskar Pelangi';

        // ── Kategori Favorit ─────────────────────────────────────────────
        $favoriteCategory = 'Buku Umum';

        if (
            !empty($user->favorite_categories) &&
            is_array($user->favorite_categories)
        ) {
            $favoriteCategory = $user->favorite_categories[0];
        }

        // Tanggal Bergabung & Terakhir Login
        $joinDate = $member?->created_at?->format('d M Y')
            ?? $user->created_at?->format('d M Y')
            ?? now()->format('d M Y');

        $lastLogin = $user->last_login_at?->format('d M Y, H:i')
            ?? now()->format('d M Y, H:i');

        // Status Keanggotaan: Guest = nonaktif, Member = aktif
        $isGuest = $user->isGuest();

        $accountStatus = $isGuest
            ? 'nonaktif'
            : ($user->isMember() ? 'aktif' : 'nonaktif');

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

        // ── Daftar Pilihan Kategori & Genre Katalog ──────────────────────
        $allCategories = [
            'Buku Pelajaran',
            'Buku Umum',
        ];

        $allGenres = [
            'Fiksi',
            'Non Fiksi',
            'Petualangan',
            'Motivasi',
            'Sains',
            'Sejarah',
            'Novel',
            'Komik',
            'Bisnis',
            'Biografi',
        ];

        // ── Daftar Divisi Perusahaan ─────────────────────────────────────
        $divisions = [
            'Center Of Excellence',
            'Digital Business',
            'E-Publishing',
            'Finance',
            'General Trading',
            'HR & GA',
            'HSE',
            'IQA',
            'IT',
            'Marketing',
            'MTIS Perpuskita dan Tisera',
            'MTIS Planning and Development',
            'People Development Center',
            'Production',
            'School Book Sales',
            'School Book Publishing',
            'SCM',
            'TAX',
        ];

        if (
            $member?->division &&
            !in_array($member->division, $divisions)
        ) {
            array_unshift($divisions, $member->division);
        }

        return view(
            'user.profile',
            compact(
                'user',
                'member',
                'stats',
                'allCategories',
                'allGenres',
                'divisions',
                'isGuest'
            )
        );
    }

    public function update(Request $request)
    {
        $request->validate(
            [
                'name'     => 'required|string|max:255',
                'division' => 'nullable|string|max:100',
                'phone'    => 'nullable|string|max:25',
            ],
            [
                'name.required' => 'Nama pengguna wajib diisi.',
                'phone.max'     => 'Nomor telepon maksimal 25 karakter.',
            ]
        );

        $user = Auth::user();

        $user->name = $request->input('name');

        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }

        $user->save();

        if ($user->member) {
            $memberData = [
                'name' => $user->name,
                'division' => $request->filled('division')
                    ? $request->input('division')
                    : $user->member->division,
            ];

            if ($request->has('phone')) {
                $memberData['phone'] = $request->input('phone');
            }

            $user->member->update($memberData);
        }

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | GOOGLE USER YANG BELUM PUNYA PASSWORD LOKAL
        |--------------------------------------------------------------------------
        */

        if (empty($user->password)) {
            $request->validate(
                [
                    'password' => 'required|string|min:8|confirmed',
                ],
                [
                    'password.required'  => 'Kata sandi baru wajib diisi.',
                    'password.min'       => 'Kata sandi baru minimal 8 karakter.',
                    'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
                ]
            );

            $user->password = Hash::make(
                $request->input('password')
            );

            $user->save();

            return redirect()->to(route('user.profile') . '#security')->with([
                'success' => 'Kata sandi berhasil dibuat. Sekarang Anda dapat login secara manual menggunakan email dan kata sandi baru.',
                'tab'     => 'security',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | USER YANG SUDAH PUNYA PASSWORD
        |--------------------------------------------------------------------------
        */

        $request->validate(
            [
                'current_password' => 'required',
                'password'         => 'required|string|min:8|confirmed',
            ],
            [
                'current_password.required' => 'Kata sandi lama wajib diisi.',
                'password.min'              => 'Kata sandi baru minimal 8 karakter.',
                'password.confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
            ]
        );

        if (!Hash::check(
            $request->input('current_password'),
            $user->password
        )) {
            return redirect()->to(route('user.profile') . '#security')
                ->withErrors([
                    'current_password' => 'Kata sandi lama tidak sesuai.',
                ])
                ->withInput()
                ->with('tab', 'security');
        }

        $user->password = Hash::make(
            $request->input('password')
        );

        $user->save();

        return redirect()->to(route('user.profile') . '#security')->with([
            'success' => 'Kata sandi berhasil diubah.',
            'tab'     => 'security',
        ]);
    }

    /**
     * Simpan preferensi pengguna
     * (tema, densitas, notifikasi, katalog) ke database.
     * Dipanggil via AJAX (JSON) dari profile page.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $selectedTheme = in_array(
            $request->input('theme'),
            ['light', 'dark']
        )
            ? $request->input('theme')
            : 'light';

        $selectedDensity = in_array(
            $request->input('layout_density'),
            ['compact', 'normal', 'comfortable']
        )
            ? $request->input('layout_density')
            : 'normal';

        $favCategories = $request->input(
            'favorite_categories',
            []
        );

        if (is_string($favCategories)) {
            $favCategories = json_decode(
                $favCategories,
                true
            ) ?: array_filter(
                array_map(
                    'trim',
                    explode(',', $favCategories)
                )
            );
        }

        $favGenres = $request->input(
            'favorite_genres',
            []
        );

        if (is_string($favGenres)) {
            $favGenres = json_decode(
                $favGenres,
                true
            ) ?: array_filter(
                array_map(
                    'trim',
                    explode(',', $favGenres)
                )
            );
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

            'favorite_categories'           => array_values(
                (array) $favCategories
            ),

            'favorite_genres'               => array_values(
                (array) $favGenres
            ),

            'last_theme_used'               => $selectedTheme,
        ];

        // Jika master switch OFF,
        // nonaktifkan seluruh sub-notifikasi
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

        return back()->with(
            'success',
            'Preferensi berhasil diperbarui.'
        );
    }

    /**
     * Kirim kode OTP 6-digit untuk fitur Lupa Password di tab Keamanan profil
     */
    public function sendSecurityOtp(Request $request)
    {
        $user = Auth::user();
        $email = strtolower(trim($request->input('email', $user->email)));

        if ($email !== strtolower(trim($user->email))) {
            return response()->json([
                'success' => false,
                'message' => 'Email yang dimasukkan tidak cocok dengan email akun Anda.',
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);

        \App\Models\PasswordResetOtp::where('email', $email)->delete();

        \App\Models\PasswordResetOtp::create([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            app(\App\Services\OtpMailService::class)->sendPasswordResetOtp($email, $otp, 5);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. Silakan periksa koneksi internet atau coba sesaat lagi.',
            ], 500);
        }

        $request->session()->put('profile_reset_email', $email);
        $request->session()->forget('profile_reset_verified');

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6-digit berhasil dikirim ke ' . $email,
            'email'   => $email,
        ]);
    }

    /**
     * Verifikasi kode OTP 6-digit
     */
    public function verifySecurityOtp(Request $request)
    {
        $user = Auth::user();
        $email = $request->session()->get('profile_reset_email', $user->email);

        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $reset = \App\Models\PasswordResetOtp::where('email', $email)
            ->latest('id')
            ->first();

        if (!$reset || $reset->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode baru.',
            ], 422);
        }

        if ($reset->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan salah. Silakan kirim ulang kode baru.',
            ], 422);
        }

        if (!Hash::check($request->input('otp'), $reset->otp_hash)) {
            $reset->increment('attempts');
            $remaining = max(0, 5 - $reset->attempts);
            return response()->json([
                'success' => false,
                'message' => $remaining > 0
                    ? "Kode OTP salah. Sisa percobaan: {$remaining}."
                    : 'Kode OTP salah dan batas percobaan sudah habis. Silakan kirim ulang kode baru.',
            ], 422);
        }

        $reset->update(['verified_at' => now()]);
        $request->session()->put('profile_reset_verified', true);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil diverifikasi.',
        ]);
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resendSecurityOtp(Request $request)
    {
        $user = Auth::user();
        $email = $request->session()->get('profile_reset_email', $user->email);

        $otp = (string) random_int(100000, 999999);

        \App\Models\PasswordResetOtp::where('email', $email)->delete();

        \App\Models\PasswordResetOtp::create([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            app(\App\Services\OtpMailService::class)->sendPasswordResetOtp($email, $otp, 5);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang OTP. Silakan coba sesaat lagi.',
            ], 500);
        }

        $request->session()->forget('profile_reset_verified');

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah berhasil dikirimkan.',
        ]);
    }

    /**
     * Simpan kata sandi baru setelah OTP berhasil diverifikasi
     */
    public function resetSecurityPassword(Request $request)
    {
        if (!$request->session()->get('profile_reset_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi OTP tidak valid atau sudah kedaluwarsa. Silakan ulangi proses verifikasi.',
            ], 403);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $email = $request->session()->get('profile_reset_email', $user->email);
        \App\Models\PasswordResetOtp::where('email', $email)->delete();

        $request->session()->forget(['profile_reset_email', 'profile_reset_verified']);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi baru berhasil disimpan! Keamanan akun Anda telah diperbarui.',
        ]);
    }
}
