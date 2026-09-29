<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterPelatihRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterPelatihController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register-pelatih');
    }

    public function store(RegisterPelatihRequest $request): RedirectResponse
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'pelatih',
        ]);
        $user->assignRole('pelatih');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/pelatih/dashboard');
    }
}
