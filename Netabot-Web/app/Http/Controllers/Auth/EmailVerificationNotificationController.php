<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
{
    $title = 'Verifikasi Email'; // Definisikan title

    if ($request->user()->hasVerifiedEmail()) {
        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('title', 'Dashboard'); // Jika sudah verifikasi, pindah ke Dashboard
    }

    $request->user()->sendEmailVerificationNotification();

    return back()
        ->with('status', 'verification-link-sent')
        ->with('title', $title); // Tetap di halaman verifikasi dengan status baru
}
}
