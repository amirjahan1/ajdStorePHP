<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login user and generate JWT token.
     *
     * @tag Authentication
     * @summary ورود کاربر (Login)
     * @description دریافت ایمیل و رمز عبور و بازگرداندن توکن احراز هویت (JWT).
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    /**
     * Send password reset link to user's email.
     *
     * @tag Authentication
     * @summary فراموشی رمز عبور
     * @description ارسال لینک بازیابی رمز عبور به آدرس ایمیل ثبت‌شده.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request) 
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent successfully.'])
            : response()->json(['error' => 'Unable to send reset link.'], 500);
    }

    /**
     * Reset user password using the provided token.
     *
     * @tag Authentication
     * @summary بازنشانی رمز عبور
     * @description تغییر رمز عبور با استفاده از توکن ارسالی به ایمیل.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // اضافه شدن confirmed برای تطابق با password_confirmation
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'), // اصلاح غلط املایی
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully.'])
            : response()->json(['error' => 'Invalid token or email.'], 400);
    }

    /**
     * Logout user and invalidate the JWT token.
     *
     * @tag Authentication
     * @summary خروج کاربر (Logout)
     * @description ابطال توکن JWT جاری و خروج از حساب کاربری. (نیازمند احراز هویت)
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        
        return response()->json(['message' => 'Successfully logged out.']);
    }
}