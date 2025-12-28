<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Jika belum login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil role dari user_detail jika ada, fallback ke user->roles
        $role = optional($user->user_detail)->roles ?? $user->roles ?? null;

        // Jika role tidak ada atau tidak sesuai dengan role yang diizinkan
        if (!$role || !in_array($role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
