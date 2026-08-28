<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
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
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GoogleAuthController;


// LANDING
Route::get('/', function () {
    return view('landing');
})->name('landing');


// LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [
    LoginController::class,
    'login'
])->name('login.store');


// =========================================================
// REGISTER
// =========================================================
Route::get('/register', [
    RegisterController::class,
    'create'
])->name('register');

Route::post('/register', [
    RegisterController::class,
    'store'
])->name('register.store');


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


// LOGOUT
Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');


// DASHBOARD
Route::get('/dashboard', [
    DashboardController::class,
    'index'
])
    ->middleware(['admin', 'no.back'])
    ->name('dashboard');


// KATEGORI
Route::resource('categories', CategoryController::class);


// KATALOG
Route::get('/catalog', [CatalogController::class, 'index'])
    ->name('catalog');


// BUKU
Route::resource('books', BookController::class);


//Borrowing
Route::get('/borrowings', [
    BorrowingController::class,
    'index'
])->name('borrowings.index');

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

// Tambah waktu baca
Route::patch(
    '/circulation/{borrowing}/extend',
    [CirculationController::class, 'extendLoan']
)->name('circulation.extend');

// SIRKULASI
Route::get('/circulation', [CirculationController::class, 'index'])
    ->name('circulation');

Route::post('/circulation', [CirculationController::class, 'store'])
    ->name('circulation.store');

Route::patch('/circulation/{borrowing}/return', [CirculationController::class, 'returnBook'])
    ->name('circulation.return');


// RESERVASI
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


// RESERVATION LOCATOR

Route::get(
    '/reservations/{reservation}/locator',
    [ReservationController::class, 'locator']
)->name('reservations.locator');


// DENDA

Route::get('/fines', [FineController::class, 'index'])
    ->name('fines');
// ANGGOTA
Route::get('/members', [MemberController::class, 'index'])
    ->name('members');


// KALENDER
Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar');

// PENGATURAN
Route::get('/settings', [SettingController::class, 'index'])
    ->name('settings');

//Book copies
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

//Home user
Route::get('/home', [
    UserHomeController::class,
    'index'
])
    ->middleware(['auth', 'no.back'])
    ->name('user.home');
