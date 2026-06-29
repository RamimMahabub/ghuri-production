<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'payment/sslcommerz/callback'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Vercel read-only filesystem override
// getenv() is more reliable than $_ENV/$_SERVER for system-injected vars in PHP serverless
if (getenv('VERCEL') || getenv('APP_ENV') === 'production' && !is_writable(dirname(__DIR__) . '/storage')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
