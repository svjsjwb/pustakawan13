<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAwareNavigationController extends Controller
{
    public function dashboard(Request $request)
    {
        return Auth::user()->isAdmin() ? app(DashboardController::class)->index($request) : app(UserHomeController::class)->index();
    }

    public function catalog(Request $request)
    {
        return Auth::user()->isAdmin() ? app(CatalogController::class)->index($request) : app(UserCatalogController::class)->index($request);
    }

    public function reservations(Request $request)
    {
        return Auth::user()->isAdmin() ? app(ReservationController::class)->index($request) : app(UserReservationController::class)->index($request);
    }

    public function borrowings(Request $request)
    {
        return Auth::user()->isAdmin() ? app(BorrowingController::class)->index($request) : app(UserBorrowingsController::class)->index();
    }

    public function history(Request $request)
    {
        return app(UserHistoryController::class)->index($request);
    }
}
