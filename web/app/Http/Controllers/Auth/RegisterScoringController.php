<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterScoringRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterScoringController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register-scoring');
    }

    public function store(RegisterScoringRequest $request): RedirectResponse
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'scoring',
        ]);
        $user->assignRole('scoring');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/scoring/dashboard');
    }
}
