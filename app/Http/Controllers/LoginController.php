<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }


    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt($credentials)) {

            return back()
                ->withErrors([
                    'email' => 'Email/username atau password salah.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('user.home');
    }
}