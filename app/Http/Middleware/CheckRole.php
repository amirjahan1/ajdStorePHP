<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException; // ✅ اصلاح شد: Access (دو تا c دارد)
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // ۱. بررسی احراز هویت کاربر
        if (!$user) {
            throw new AuthorizationException('شما احراز هویت نشده‌اید. لطفاً وارد شوید.');
        }

        // ۲. بررسی نقش کاربر
        if (!in_array($user->role, $roles)) {
            throw new AuthorizationException('شما مجوز دسترسی به این بخش را ندارید.');
        }

        return $next($request);
    }
}