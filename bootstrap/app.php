<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ✅ ادغام تمام تنظیمات میدل‌ور در یک بلاک واحد
    ->withMiddleware(function (Middleware $middleware): void {

        // 1. تنظیم مسیر ریدایرکت برای کاربران مهمان (Guest)
        $middleware->redirectGuestsTo(fn ($request) => $request->is('admin*') ? route('admin.login') : route('login'));

        // 2. ثبت میدل‌ورهای سفارشی (Alias)
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // تشخیص درخواست‌های API برای بازگرداندن پاسخ JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // مدیریت خطای عدم دسترسی (403 Forbidden) به صورت JSON برای API
        $exceptions->render(
            function (AuthorizationException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'error_code' => 'FORBIDDEN',
                    ], 403);
                }
            }
        );
    })->create();
