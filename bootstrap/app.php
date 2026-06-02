<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahkan security headers ke semua response web (CSP, X-Frame-Options, dll).
        $middleware->appendToGroup('web', SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SyncCartSession::class);

        $middleware->alias([
            'role'         => CheckRole::class,
            'online.hours' => \App\Http\Middleware\CheckOnlineOrderHours::class,
        ]);
        // Kecualikan endpoint webhook Midtrans dari proteksi CSRF
        // karena Midtrans server tidak mengirim CSRF token saat POST notification
        $middleware->validateCsrfTokens(except: [
            'payment/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
