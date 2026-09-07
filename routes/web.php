<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CirculationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\RealtimeEventController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\UserReservationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// =========================================================
// LANDING
// =========================================================

Route::get('/', function () {
    return view('landing');
})->name('landing');


// =========================================================
// LOGIN & REGISTER
// HANYA UNTUK USER YANG BELUM LOGIN
// =========================================================

Route::middleware('guest')->group(function () {

    // LOGIN
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [
        LoginController::class,
        'login'
    ])->name('login.store');

    // REGISTER
    Route::get('/register', [
        RegisterController::class,
        'create'
    ])->name('register');

    Route::post('/register', [
        RegisterController::class,
        'store'
    ])->name('register.store');
});


// =========================================================
// GOOGLE AUTH
// =========================================================

Route::get('/auth/google', [
    GoogleAuthController::class,
    'redirect'
])->name('google.redirect');

Route::get('/auth/google/callback', [
    GoogleAuthController::class,
    'callback'
])->name('google.callback');


// =========================================================
// LOGOUT
// =========================================================

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect()
        ->route('login')
        ->with('success', 'Anda berhasil logout.');
})->name('logout');


// =========================================================
// DASHBOARD ADMIN
// =========================================================

Route::get('/dashboard', [
    DashboardController::class,
    'index'
])
    ->middleware(['auth', 'admin', 'no.back'])
    ->name('dashboard');


// =========================================================
// KATEGORI
// =========================================================

Route::resource('categories', CategoryController::class);


// =========================================================
// KATALOG
// =========================================================

Route::get('/catalog', [
    CatalogController::class,
    'index'
])->name('catalog');


// =========================================================
// BUKU
// =========================================================

Route::resource('books', BookController::class);


// =========================================================
// BOOK COPIES
// =========================================================

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
    });


// =========================================================
// BORROWING
// =========================================================

Route::delete('/borrowings/{borrowing}', [
    BorrowingController::class,
    'destroy'
])->name('borrowings.destroy');


// =========================================================
// SIRKULASI
// =========================================================

Route::get('/circulation', [
    CirculationController::class,
    'index'
])->name('circulation');

Route::post('/circulation', [
    CirculationController::class,
    'store'
])->name('circulation.store');

Route::patch('/circulation/{borrowing}/return', [
    CirculationController::class,
    'returnBook'
])->name('circulation.return');

Route::patch(
    '/circulation/{borrowing}/extend',
    [CirculationController::class, 'extendLoan']
)->name('circulation.extend');


// =========================================================
// RESERVASI
// =========================================================

Route::get('/reservations', [
    ReservationController::class,
    'index'
])->name('reservations.index');

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


// =========================================================
// RESERVATION LOCATOR
// =========================================================

Route::get(
    '/reservations/{reservation}/locator',
    [ReservationController::class, 'locator']
)->name('reservations.locator');


// =========================================================
// LAPORAN
// =========================================================

Route::get('/laporan', [
    ReportController::class,
    'index'
])->name('reports.index');

Route::get('/laporan/create', [
    ReportController::class,
    'create'
])->name('reports.create');

Route::post('/laporan', [
    ReportController::class,
    'store'
])->name('reports.store');

Route::get('/laporan/{id}/edit', [
    ReportController::class,
    'edit'
])->name('reports.edit');

Route::put('/laporan/{id}', [
    ReportController::class,
    'update'
])->name('reports.update');

Route::delete('/laporan/{id}', [
    ReportController::class,
    'destroy'
])->name('reports.destroy');


// =========================================================
// DENDA
// =========================================================

Route::get('/fines', [
    FineController::class,
    'index'
])->name('fines');


// =========================================================
// ANGGOTA
// =========================================================

Route::get('/members/data/json', [
    MemberController::class,
    'getMembersJson'
])->name('members.json');

Route::resource('members', MemberController::class);


// =========================================================
// KALENDER
// =========================================================

Route::get('/calendar', [
    CalendarController::class,
    'index'
])->name('calendar');


// =========================================================
// PENGATURAN
// =========================================================

Route::get('/settings', [
    SettingController::class,
    'index'
])->name('settings');


// =========================================================
// USER HOME
// =========================================================

Route::get('/home', [
    UserHomeController::class,
    'index'
])
    ->middleware(['auth', 'no.back'])
    ->name('user.home');

Route::post('/profile/update', [
    UserHomeController::class,
    'updateProfile'
])
    ->middleware('auth')
    ->name('user.profile.update');

Route::post('/profile/password', [
    UserHomeController::class,
    'updatePassword'
])
    ->middleware('auth')
    ->name('user.profile.password');


// =========================================================
// FAVORIT SAYA
// =========================================================

Route::middleware(['auth', 'no.back'])->group(function () {
    Route::get('/favorit-saya', [
        FavoriteController::class,
        'index'
    ])->name('favorites.index');

    Route::post('/favorit-saya', [
        FavoriteController::class,
        'store'
    ])->name('favorites.store');

    Route::post('/favorites/toggle', [
        FavoriteController::class,
        'toggle'
    ])->name('favorites.toggle');

    Route::delete('/favorit-saya/{id}', [
        FavoriteController::class,
        'destroy'
    ])->name('favorites.destroy');
});


// =========================================================
// RESERVASI SAYA (USER)
// =========================================================

Route::middleware(['auth', 'no.back'])->group(function () {
    Route::get('/reservasi-saya', [
        UserReservationController::class,
        'index'
    ])->name('user.reservations');

    Route::post('/reservasi-saya', [
        UserReservationController::class,
        'store'
    ])->name('user.reservations.store');

    Route::patch('/reservasi-saya/{reservation}/cancel', [
        UserReservationController::class,
        'cancel'
    ])->name('user.reservations.cancel');

    // =========================================================
    // RIWAYAT AKTIVITAS (USER)
    // =========================================================
    Route::get('/riwayat', [
        UserHistoryController::class,
        'index'
    ])->name('user.history');

    // =========================================================
    // NOTIFIKASI & PERMISSION TOGGLE (USER)
    // =========================================================
    Route::post('/notifications/mark-all-read', [
        NotificationController::class,
        'markAllRead'
    ])->name('notifications.markAllRead');

    Route::post('/notifications/{id}/read', [
        NotificationController::class,
        'markAsRead'
    ])->name('notifications.markAsRead');

    Route::post('/user/toggle-notification', [
        NotificationController::class,
        'toggleNotification'
    ])->name('user.toggleNotification');
});

// =========================================================
// REAL-TIME SYNCHRONIZATION (SSE & POLL)
// =========================================================
Route::get('/events/stream', [
    RealtimeEventController::class,
    'stream'
])->name('events.stream');

Route::get('/events/poll', [
    RealtimeEventController::class,
    'poll'
])->name('events.poll');

