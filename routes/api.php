<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;





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


Route::prefix('v1')->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-paasword', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

 Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Route::apiResource('users', UserController::class);
    
    Route::middleware('auth:api')->group(function(){
     Route::post('/logout', [AuthController::class, 'logout']);
     Route::middleware('role:admin,superAdmin')->group(function(){
       
     Route::get('/admin/dashboard', function(){
            return response()->json(['message' => 'welcome to admin dahsboard']);
        });

         Route::get('/users', [UserController::class, 'index'])->name('users.index');
     });


     Route::middleware('role:superAdmin')->group(function(){
        Route::get('/super/admin/dashboard', function(){
            return response()->json(['message' => 'welcome to super admin dahsboard']);
        });
     });
    });

});