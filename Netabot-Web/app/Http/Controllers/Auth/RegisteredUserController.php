<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): RedirectResponse
{
    // 1️⃣ Validasi input
    $validated = $request->validate([
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed', // menggunakan field password_confirmation
        'userName' => 'required|string|max:50',
        'fullName' => 'required|string|max:100',
    ]);

    // 2️⃣ Buat user
    $user = User::create([
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // 3️⃣ Buat detail user
    UserDetail::create([
        'id_user' => $user->id,
        'username' => $validated['userName'],
        'fullname' => $validated['fullName'],
        'roles' => 'member',
    ]);

    // 4️⃣ Trigger event Registered
    event(new Registered($user));

    // 5️⃣ Login otomatis
    Auth::login($user);

    // 6️⃣ Redirect ke login page atau dashboard
    return redirect(route('login', absolute: false))
        ->with('success', 'Akun berhasil dibuat! Silahkan login.');
}
}
