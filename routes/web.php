<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\AdminPanelController;
use Illuminate\Support\Facades\Route;

// مسیرهای عمومی (مثلاً صفحه لاگین اگر نیاز باشد)
Route::get('/login', function () {
    return 'لطفاً ابتدا وارد شوید (Login)';
})->name('login');

// گروه مسیرهای پنل ادمین (فقط برای ادمین و سوپر ادمین)
Route::middleware(['auth', 'role:admin,superAdmin'])->prefix('admin-panel')->name('admin.')->group(function () {

    // داشبورد اصلی (هم ادمین و هم سوپر ادمین دسترسی دارند)
    Route::get('/dashboard', [AdminPanelController::class, 'dashboard'])->name('dashboard');

    // لیست ادمین‌ها (فقط سوپر ادمین دسترسی دارد - کنترل در میدل‌ور یا کنترلر)
    Route::get('/admins', [AdminPanelController::class, 'admins'])->middleware('role:superAdmin')->name('admins');

    // لیست سوپر ادمین‌ها (فقط سوپر ادمین)
    Route::get('/super-admins', [AdminPanelController::class, 'superAdmins'])->middleware('role:superAdmin')->name('super-admins');
});

 // نمایش فرم لاگین
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');

// پردازش فرم لاگین
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

// مسیر خروج (نیاز به لاگین دارد)
Route::post('/admin/logout', [AuthController::class, 'logout'])->middleware('auth')->name('admin.logout');

// دیباگ: بررسی وضعیت لاگین
Route::get('/debug-auth', function () {
    dd(
        'Auth::check(): ' . (auth()->check() ? 'true' : 'false'),
        'User: ' . (auth()->check() ? auth()->user()->email . ' (role: ' . auth()->user()->role . ')' : 'null'),
    );
});


Route::middleware(['auth', 'role:superAdmin'])->prefix('admin-panel')->name('admin.')->group(function () {

Route::get('/admins/{user}/edit', [AdminPanelController::class, 'edit'])->name('admins.edit');

// ذخیره تغییرات ادمین
Route::put('/admins/{user}', [AdminPanelController::class, 'update'])->name('admins.update');

});
 