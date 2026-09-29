<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->dashboardFor($request->user()->role));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ponytail: path URL langsung (bukan route name) — routes TAHAP 5-9 belum ada,
    // path di 05_routes.txt stabil jadi tidak ada kopling ke file route masa depan
    private function dashboardFor(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'pelatih' => '/pelatih/dashboard',
            'scoring' => '/scoring/dashboard',
            default => '/atlet/dashboard',
        };
    }
}
