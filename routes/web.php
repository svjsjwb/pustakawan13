<?php

use Illuminate\Support\Facades\Route;

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


// LANDING
Route::get('/', function () {
    return view('landing');
})->name('landing');


// LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', function () {
    return redirect()->route('dashboard');
})->name('login.store');


// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// KATEGORI
Route::resource('categories', CategoryController::class);


// KATALOG
Route::get('/catalog', [CatalogController::class, 'index'])
    ->name('catalog');


// BUKU
Route::resource('books', BookController::class);


// SIRKULASI
Route::get('/circulation', [CirculationController::class, 'index'])
    ->name('circulation');

Route::post('/circulation', [CirculationController::class, 'store'])
    ->name('circulation.store');

Route::patch('/circulation/{borrowing}/return', [CirculationController::class, 'returnBook'])
    ->name('circulation.return');


// RESERVASI
Route::get('/reservations', [ReservationController::class, 'index'])
    ->name('reservations');

Route::post('/reservations', [ReservationController::class, 'store'])
    ->name('reservations.store');

Route::patch('/reservations/{reservation}/approve', [ReservationController::class, 'updateStatus'])
    ->name('reservations.approve');

Route::patch('/reservations/{reservation}/reject', [ReservationController::class, 'updateStatus'])
    ->name('reservations.reject');

Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])
    ->name('reservations.update-status');

Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])
    ->name('reservations.destroy');


// DENDA
Route::get('/fines', [FineController::class, 'index'])
    ->name('fines');


// ANGGOTA
Route::get('/members', [MemberController::class, 'index'])
    ->name('members');


// KALENDER
Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar');


// LAPORAN
Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports');


// PENGATURAN
Route::get('/settings', [SettingController::class, 'index'])
    ->name('settings');
