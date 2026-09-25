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

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $mailException) {
            Log::warning('Email sambutan register gagal dikirim.', [
                'user_id' => $user->id,
                'recipient' => $user->email,
                'error' => $mailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Akun berhasil dibuat. Silakan login.'
            );
    }
}