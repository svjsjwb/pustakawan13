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
use App\Http\Controllers\ActivityController;


/*
|--------------------------------------------------------------------------
| LANDING
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', [
    LoginController::class,
    'showLoginForm'
])
    ->middleware(['no.back'])
    ->name('login');

Route::post('/login', [
    LoginController::class,
    'login'
])->name('login.store');


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

Route::get('/register', [
    RegisterController::class,
    'create'
])->name('register');

Route::post('/register', [
    RegisterController::class,
    'store'
])->name('register.store');


/*
|--------------------------------------------------------------------------
| GOOGLE AUTH
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [
    GoogleAuthController::class,
    'redirect'
])->name('google.redirect');

Route::get('/auth/google/callback', [
    GoogleAuthController::class,
    'callback'
])->name('google.callback');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [
    DashboardController::class,
    'index'
])
    ->middleware(['admin', 'no.back'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
|
| Semua halaman admin wajib:
| - login
| - role admin
| - no-cache / no-back
|
*/

Route::middleware(['admin', 'no.back'])
    ->group(function () {


        /*
    |--------------------------------------------------------------------------
    | KATEGORI
    |--------------------------------------------------------------------------
    */

        Route::resource(
            'categories',
            CategoryController::class
        );


        /*
    |--------------------------------------------------------------------------
    | BUKU
    |--------------------------------------------------------------------------
    */

        Route::resource(
            'books',
            BookController::class
        );


        /*
    |--------------------------------------------------------------------------
    | BOOK COPIES
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | BORROWINGS
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | SIRKULASI
    |--------------------------------------------------------------------------
    */

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

        Route::patch('/circulation/{borrowing}/extend', [
            CirculationController::class,
            'extendLoan'
        ])->name('circulation.extend');


        /*
    |--------------------------------------------------------------------------
    | RESERVASI ADMIN
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | DENDA
    |--------------------------------------------------------------------------
    */

        Route::get('/fines', [
            FineController::class,
            'index'
        ])->name('fines');


        /*
    |--------------------------------------------------------------------------
    | ANGGOTA
    |--------------------------------------------------------------------------
    */

        Route::resource(
            'members',
            MemberController::class
        );


        /*
    |--------------------------------------------------------------------------
    | KALENDER
    |--------------------------------------------------------------------------
    */

        Route::get('/calendar', [
            CalendarController::class,
            'index'
        ])->name('calendar');


        /*
    |--------------------------------------------------------------------------
    | PENGATURAN
    |--------------------------------------------------------------------------
    */

        Route::get('/settings', [
            SettingController::class,
            'index'
        ])->name('settings');


        /*
    |--------------------------------------------------------------------------
    | AKTIVITAS ADMIN
    |--------------------------------------------------------------------------
    */

        Route::post('/activities', [
            ActivityController::class,
            'store'
        ])->name('activities.store');

        Route::put('/activities/{activity}', [
            ActivityController::class,
            'update'
        ])->name('activities.update');

        Route::delete('/activities/{activity}', [
            ActivityController::class,
            'destroy'
        ])->name('activities.destroy');

        Route::patch('/activities/{activity}/pin', [
            ActivityController::class,
            'pin'
        ])->name('activities.pin');
    });


/*
|--------------------------------------------------------------------------
| KATALOG
|--------------------------------------------------------------------------
|
| Katalog tetap public.
|
*/

Route::get('/catalog', [
    CatalogController::class,
    'index'
])->name('catalog');


/*
|--------------------------------------------------------------------------
| HOME USER
|--------------------------------------------------------------------------
|
| User wajib login.
|
*/

Route::get('/home', [
    UserHomeController::class,
    'index'
])
    ->middleware(['auth', 'no.back'])
    ->name('user.home');


/*
|--------------------------------------------------------------------------
| AKTIVITAS USER
|--------------------------------------------------------------------------
*/

Route::post('/activities/{activity}/read', [
    ActivityController::class,
    'markAsRead'
])
    ->middleware('auth')
    ->name('activities.markAsRead');
