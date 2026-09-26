<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(email) = ?', [
                        strtolower(trim((string) $request->input('email'))),
                    ]);
                }),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make(
                $validated['password']
            ),
            'role' => 'user',
        ]);

        \App\Services\NotificationService::userRegistered($user);

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Akun berhasil dibuat. Silakan login.'
            );
    }
}