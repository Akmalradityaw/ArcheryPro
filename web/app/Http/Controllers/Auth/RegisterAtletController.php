<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterAtletRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterAtletController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register-atlet');
    }

    public function store(RegisterAtletRequest $request): RedirectResponse
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'atlet',
        ]);
        $user->assignRole('atlet');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/atlet/dashboard');
    }
}
