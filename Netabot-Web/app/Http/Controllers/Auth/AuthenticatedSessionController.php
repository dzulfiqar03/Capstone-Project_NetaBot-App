<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $title = 'Login';
        return view('auth.login', compact('title'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();


        $title = 'Launch';

        // Ambil role dari relasi user_detail
        $role = optional($user->user_detail)->roles ?? $user->roles ?? null;

        if ($role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))->with('title', 'Dashboard');
        }

        if ($role === 'member') {
            return redirect()->intended(route('dashboard'))->with('title', 'Home');
        }
        

        // fallback
        return redirect()->intended('/')->with('title', $title);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $title='Launch';
        return redirect('/')->with('title', $title);
    }
}
