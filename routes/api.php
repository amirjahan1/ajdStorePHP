<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CommentController;

Route::prefix('v1')->group(function () {
    // مسیرهای عمومی (بدون احراز هویت)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // ثبت کاربر (عمومی)
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // محصولات - عمومی (مشاهده)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // کامنت‌ها - عمومی (مشاهده)
    Route::get('/comments', [CommentController::class, 'index']);

    // مسیرهای نیازمند احراز هویت
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // سبد خرید (کاربر معمولی)
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{cartItem}', [CartController::class, 'update']);
        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);

        // کامنت‌گذاری (کاربر معمولی)
        Route::post('/comments', [CommentController::class, 'store']);

        // حذف کامنت خود (کاربر معمولی) و ادمین می‌تواند هر کامنتی را حذف کند
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

        // مسیرهای ادمین و سوپرادمین
        Route::middleware('role:admin,superAdmin')->group(function () {
            // مدیریت محصولات
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{product}', [ProductController::class, 'update']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']);

            // تایید/رد کامنت
            Route::put('/comments/{comment}', [CommentController::class, 'update']);
        });

        // مسیرهای فقط سوپرادمین
        Route::middleware('role:superAdmin')->group(function () {
            // مدیریت دسته‌بندی‌ها
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
            // index و show را عمومی می‌کنیم (برای همه قابل مشاهده)
        });
    });

    // دسته‌بندی‌ها (عمومی برای مشاهده)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/categories/tr/tree', [CategoryController::class, 'tree']);

    // مدیریت کاربران (فقط ادمین/سوپرادمین) - قبلاً در UserController تعریف شده
    Route::middleware('auth:api')->group(function () {
        Route::middleware('role:admin,superAdmin')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});