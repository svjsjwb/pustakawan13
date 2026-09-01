<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\GoogleAuthController;
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
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BookCopyController;


// =========================================================
// LANDING
// =========================================================

Route::get('/', function () {
    return view('landing');
})->name('landing');


// =========================================================
// LOGIN
// =========================================================

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
// LOGOUT
// =========================================================

Route::post('/logout', function () {
    auth()->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');


// =========================================================
// GOOGLE LOGIN
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
// DASHBOARD
// =========================================================

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

});


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

Route::resource('books.copies', BookCopyController::class)
    ->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
    ]);


// =========================================================
// SIRKULASI / PEMINJAMAN
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

Route::get('/reservations/{reservation}/locator', [
    ReservationController::class,
    'locator'
])->name('reservations.locator');


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
// ANGGOTA / KEANGGOTAAN
// =========================================================

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

//Home user
Route::get('/home', [
    UserHomeController::class,
    'index'
])
->middleware(['auth', 'no.back'])
->name('user.home');