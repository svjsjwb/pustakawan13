<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\UserCatalogController;
use App\Http\Controllers\UserBorrowingController;
use App\Http\Controllers\UserBorrowingsController;
use App\Http\Controllers\RoleAwareNavigationController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserFavoriteController;
use App\Http\Controllers\UserReservationController;
use App\Http\Controllers\UserAnnouncementController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\UserHelpController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BookLocatorController;

// ============================================================
// LANDING
// ============================================================
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ============================================================
// AUTENTIKASI (GUEST - tidak perlu login)
// ============================================================
Route::middleware('guest')->group(function () {

    // Tampilkan halaman login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    // Proses login (email + password → redirect berdasarkan role)
    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.store');

    // Registrasi
    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');

    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('google.callback');
});

// Logout (butuh autentikasi)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// URL navigasi bersama admin/user. Controller memilih halaman berdasarkan role.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [RoleAwareNavigationController::class, 'dashboard'])->name('dashboard');
    Route::get('/catalog', [RoleAwareNavigationController::class, 'catalog'])->name('catalog');
    Route::get('/reservations', [RoleAwareNavigationController::class, 'reservations'])->name('reservations.index');
    Route::get('/borrowings', [RoleAwareNavigationController::class, 'borrowings'])->name('borrowings.index');
    Route::get('/history', [RoleAwareNavigationController::class, 'history'])->name('history');
    Route::patch('/borrowings/{borrowing}/extend', [UserBorrowingsController::class, 'extend'])->name('borrowings.extend');
});

// ============================================================
// HALAMAN ADMIN  (middleware: auth + role admin)
// ============================================================
Route::middleware(['auth', 'admin'])->group(function () {

    // KATEGORI
    Route::resource('categories', CategoryController::class);

    // BUKU
    // Lookup ISBN tetap dipertahankan dari backend admin lama.
    Route::get('/books/isbn-lookup', [
        BookController::class,
        'isbnLookup'
    ])->name('books.isbn.lookup');

    Route::resource('books', BookController::class);

    // Book Copies
    Route::prefix('books/{book}/copies')
        ->name('books.copies.')
        ->group(function () {

            Route::get('/', [
                BookCopyController::class,
                'index'
            ])->name('index');

            Route::get('/create', [
                BookCopyController::class,
                'create'
            ])->name('create');

            Route::post('/', [
                BookCopyController::class,
                'store'
            ])->name('store');

            Route::get('/{copy}/edit', [
                BookCopyController::class,
                'edit'
            ])->name('edit');

            Route::put('/{copy}', [
                BookCopyController::class,
                'update'
            ])->name('update');

            Route::delete('/{copy}', [
                BookCopyController::class,
                'destroy'
            ])->name('destroy');
        });

    Route::post('/borrowings', [
        BorrowingController::class,
        'store'
    ])->name('borrowings.store');

    Route::patch('/borrowings/{borrowing}/return', [
        BorrowingController::class,
        'returnBook'
    ])->name('borrowings.return');

    Route::delete('/borrowings/{borrowing}', [
        BorrowingController::class,
        'destroy'
    ])->name('borrowings.destroy');

    // SIRKULASI
    Route::get('/circulation', [CirculationController::class, 'index'])
        ->name('circulation');

    Route::post('/circulation', [CirculationController::class, 'store'])
        ->name('circulation.store');

    Route::patch('/circulation/{borrowing}/return', [CirculationController::class, 'returnBook'])
        ->name('circulation.return');

    Route::patch('/circulation/{borrowing}/extend', [CirculationController::class, 'extend'])
        ->name('circulation.extend');

    Route::patch('/circulation/{borrowing}/approve-extension', [CirculationController::class, 'approveExtension'])
        ->name('circulation.approveExtension');

    Route::patch('/circulation/{borrowing}/reject-extension', [CirculationController::class, 'rejectExtension'])
        ->name('circulation.rejectExtension');

    Route::post('/reservations', [
        ReservationController::class,
        'store'
    ])->name('reservations.store');

    Route::patch('/reservations/{reservation}/status', [
        ReservationController::class,
        'updateStatus'
    ])->name('reservations.updateStatus');

    Route::delete('/reservations/{reservation}', [
        ReservationController::class,
        'destroy'
    ])->name('reservations.destroy');

    Route::get(
        '/reservations/{reservation}/locator',
        [ReservationController::class, 'locator']
    )->name('reservations.locator');

    Route::get('/reservations-feed', [ReservationController::class, 'statusFeed'])
        ->name('reservations.statusFeed');

    // LAPORAN
    Route::get('/laporan', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/laporan/create', [ReportController::class, 'create'])
        ->name('reports.create');

    Route::post('/laporan', [ReportController::class, 'store'])
        ->name('reports.store');

    Route::get('/laporan/{id}/edit', [ReportController::class, 'edit'])
        ->name('reports.edit');

    Route::put('/laporan/{id}', [ReportController::class, 'update'])
        ->name('reports.update');

    Route::delete('/laporan/{id}', [ReportController::class, 'destroy'])
        ->name('reports.destroy');

    // DENDA
    Route::get('/fines', [FineController::class, 'index'])
        ->name('fines');

    // ANGGOTA
    Route::patch(
        '/members/{member}/approve',
        [MemberController::class, 'approve']
    )->name('members.approve');

    Route::delete(
        '/members/{member}/reject',
        [MemberController::class, 'reject']
    )->name('members.reject');

    Route::resource('members', MemberController::class);

    // KALENDER
    Route::get('/calendar', [CalendarController::class, 'index'])
        ->name('calendar');

    // PENGATURAN
    Route::get('/settings', [SettingController::class, 'index'])
        ->name('settings');

    // AKTIVITAS / PENGUMUMAN (dari Pandu — fitur baru)
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::post('/', [ActivityController::class, 'store'])->name('store');
        Route::get('/create', [ActivityController::class, 'create'])->name('create');
        Route::get('/{activity}/edit', [ActivityController::class, 'edit'])->name('edit');
        Route::put('/{activity}', [ActivityController::class, 'update'])->name('update');
        Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
        Route::patch('/{activity}/pin', [ActivityController::class, 'pin'])->name('pin');
        Route::post('/{activity}/read', [ActivityController::class, 'markAsRead'])->name('markAsRead');
    });

    // BOOK LOCATOR (dari Pandu — fitur baru)
    Route::get('/book-locator', [BookLocatorController::class, 'index'])->name('book-locator.index');
    Route::get('/book-locator/{reservation}', [BookLocatorController::class, 'show'])->name('book-locator.show');
});

Route::middleware(['auth', 'role.user'])->group(function () {

    // BERANDA
    Route::get('/home', [UserHomeController::class, 'index'])->name('user.home');
    Route::get('/user/search', [UserHomeController::class, 'search'])->name('user.search');

    // KATALOG
    Route::get('/user/catalog', [UserCatalogController::class, 'index'])->name('user.catalog');

    // PEMINJAMAN BUKU - HALAMAN KHUSUS
    Route::get('/peminjaman', [UserBorrowingsController::class, 'index'])->name('user.borrowings');

    // PEMINJAMAN AKTIF & PERMINTAAN PINJAM
    Route::get('/user/loans', [UserBorrowingController::class, 'index'])->name('user.loans');
    Route::post('/user/loans/request', [UserBorrowingController::class, 'store'])->name('user.loans.store');
    Route::patch('/user/loans/{borrowing}/extend', [UserBorrowingController::class, 'requestExtension'])->name('user.loans.extend');

    // RIWAYAT
    Route::get('/user/history', [UserHistoryController::class, 'index'])->name('user.history');

    // FAVORIT
    Route::get('/user/favorites',         [UserFavoriteController::class, 'index'])->name('user.favorites');
    Route::post('/user/favorites/toggle', [UserFavoriteController::class, 'toggle'])->name('user.favorites.toggle');
    Route::get('/user/favorites/check',   [UserFavoriteController::class, 'check'])->name('user.favorites.check');

    // RESERVASI
    Route::get('/user/reservations', [UserReservationController::class, 'index'])->name('user.reservations');
    Route::get('/user/reservations-feed', [UserReservationController::class, 'statusFeed'])->name('user.reservations.statusFeed');
    Route::get('/user/reservations/{reservation}', [UserReservationController::class, 'show'])->name('user.reservations.show');
    Route::post('/user/reservations', [UserReservationController::class, 'store'])->name('user.reservations.store');

    // PENGUMUMAN
    Route::get('/user/announcements', [UserAnnouncementController::class, 'index'])->name('user.announcements');

    // NOTIFIKASI
    Route::get('/notifications',                   [NotificationController::class, 'index'])->name('user.notifications');
    Route::post('/user/notifications/dismiss',     [UserNotificationController::class, 'dismiss'])->name('user.notifications.dismiss');
    Route::post('/user/notifications/dismiss-all', [UserNotificationController::class, 'dismissAll'])->name('user.notifications.dismiss-all');

    // PROFIL
    Route::get('/user/profile',              [UserProfileController::class, 'index'])->name('user.profile');
    Route::post('/user/profile/update',      [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user/profile/password',    [UserProfileController::class, 'changePassword'])->name('user.profile.password');
    Route::post('/user/profile/preferences', [UserProfileController::class, 'updatePreferences'])->name('user.profile.preferences');

    // BANTUAN
    Route::get('/user/help', [UserHelpController::class, 'index'])->name('user.help');
});

// ============================================================
// API NOTIFIKASI (AUTH)
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/api/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/api/notifications/read-all',  [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});
