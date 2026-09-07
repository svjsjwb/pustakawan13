<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Notifications\BorrowingDueWarningNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserHomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ─── Peringatan H-1 Jatuh Tempo Peminjaman (Trigger Notifikasi) ──
        if ($user && $user->is_notification_enabled) {
            $memberId = Member::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->value('id');

            if ($memberId) {
                $tomorrow = Carbon::tomorrow()->toDateString();
                $dueBorrowings = Borrowing::with('details.book')
                    ->where('member_id', $memberId)
                    ->whereIn('status', ['dipinjam', 'diperpanjang'])
                    ->whereDate('due_at', $tomorrow)
                    ->get();

                foreach ($dueBorrowings as $borrowing) {
                    // Cek apakah notifikasi untuk borrowing ini sudah pernah dikirim
                    $alreadyNotified = $user->notifications()
                        ->where('data->reference_id', $borrowing->id)
                        ->where('data->type', 'borrowing_due_soon')
                        ->exists();

                    if (!$alreadyNotified) {
                        $user->notify(new BorrowingDueWarningNotification($borrowing));
                    }
                }
            }
        }

        // Buku terbaru
        $latestBooks = Book::with('category')
            ->latest()
            ->take(8)
            ->get();

        // Buku populer
        // Untuk sementara berdasarkan jumlah stok.
        // Nanti bisa diganti berdasarkan histori peminjaman.
        $popularBooks = Book::with('category')
            ->where('stok', '>', 0)
            ->orderByDesc('stok')
            ->take(8)
            ->get();

        // Kategori
        $categories = Category::orderBy('name')
            ->take(8)
            ->get();

        return view('user.home', compact(
            'latestBooks',
            'popularBooks',
            'categories'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terautentikasi.',
            ], 401);
        }

        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'phone'                    => ['nullable', 'string', 'max:25', 'regex:/^(\+?[0-9\s\-\(\)]){8,20}$/'],
            'location'                 => 'nullable|string|max:100',
            'avatar'                   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_notification_enabled'  => 'nullable',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.regex'   => 'Format nomor telepon tidak valid (contoh: 08123456789 atau +628123456789).',
            'avatar.image'  => 'File foto profil harus berupa gambar.',
            'avatar.mimes'  => 'Format gambar yang diizinkan hanya JPG dan PNG.',
            'avatar.max'    => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $user->name = $validated['name'];
        // Email is read-only according to requirements; we preserve existing $user->email
        $user->phone = $validated['phone'] ?? null;
        $user->location = $validated['location'] ?? null;

        if ($request->has('is_notification_enabled')) {
            $val = $request->input('is_notification_enabled');
            $user->is_notification_enabled = ($val === true || $val === 'true' || $val === '1' || $val === 1 || strtolower($val) === 'allow');
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        // Update juga nama dan no telepon di tabel members yang terhubung
        Member::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->update([
                'name'  => $user->name,
                'phone' => $user->phone ?: '-',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'name'                    => $user->name,
                'email'                   => $user->email,
                'phone'                   => $user->phone,
                'location'                => $user->location,
                'is_notification_enabled' => (bool) $user->is_notification_enabled,
                'avatar_url'              => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/avatar-user.jpg'),
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terautentikasi.',
            ], 401);
        }

        // Jika user memiliki password (bukan akun OAuth murni tanpa password)
        if (!empty($user->password)) {
            $request->validate([
                'current_password' => 'required|string',
            ], [
                'current_password.required' => 'Password saat ini wajib diisi.',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'current_password' => ['Password saat ini tidak sesuai.'],
                    ],
                    'message' => 'Password saat ini tidak sesuai.',
                ], 422);
            }
        }

        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-zA-Z]/', // ada huruf
                'regex:/[0-9]/',     // ada angka
                'confirmed',         // cocok dengan new_password_confirmation
            ],
        ], [
            'new_password.required'  => 'Password baru wajib diisi.',
            'new_password.min'       => 'Password baru minimal harus 8 karakter.',
            'new_password.regex'     => 'Password baru harus merupakan kombinasi huruf dan angka.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);
    }
}