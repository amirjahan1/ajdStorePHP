<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException; // اضافه شد برای رفع خطای تایپی

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // اصلاح شد: اضافه کردن \ در ابتدا و ::class در انتها
            'role' => \App\Http\Middleware\CheckRole::class, 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
 
        $exceptions->render(
            function (AuthorizationException $e, Request $request) { // اصلاح شد: Exception به جای Exeption
                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'error_code' => 'FORBIDDEN'
                    ], 403);
                }
            }
        );
    })->create();