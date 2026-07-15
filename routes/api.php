<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('users', UserController::class);



// // ۱. دریافت لیست کاربران
// Route::get('/users', [UserController::class, 'index'])->name('users.index');

// // ۲. ساخت کاربر جدید
// Route::post('/users', [UserController::class, 'store'])->name('users.store');

// // ۳. ویرایش کاربر (هم PUT و هم PATCH پشتیبانی می‌شود)
// Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
 
// // ۴. حذف کاربر
// Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// // (اختیاری) ۵. دریافت اطلاعات یک کاربر خاص
// // Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');