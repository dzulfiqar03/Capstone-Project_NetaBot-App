<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
                // ✅ Daftarkan middleware route custom di sini
        $middleware->alias([
            'roles' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // --- TAMBAHKAN BAGIAN INI ---
        $middleware->validateCsrfTokens(except: [
            'api/*',       // Matikan CSRF untuk semua route yang diawali /api/
            'scrape/*',    // Matikan untuk route scrape (jika ada)
            'products',    // Tambahkan juga endpoint spesifik jika perlu
        ]);
        // ----------------------------
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
