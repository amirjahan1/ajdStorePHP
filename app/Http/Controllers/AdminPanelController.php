<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; // ✅ این خط حیاتی بود که جا افتاده بود
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminPanelController extends Controller
{
    /**
     * نمایش داشبورد
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'super_admins' => User::where('role', 'superAdmin')->count(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    /**
     * نمایش لیست ادمین‌ها
     */
    public function admins()
    {
        $users = User::where('role', 'admin')->get();
        return view('admin.admins', compact('users'));
    }

    /**
     * نمایش لیست سوپر ادمین‌ها
     */
    public function superAdmins()
    {
        $users = User::where('role', 'superAdmin')->get();
        return view('admin.super-admins', compact('users'));
    }

    /**
     * نمایش فرم ویرایش اطلاعات ادمین
     */
    public function edit(User $user)
    {
        // اطمینان از اینکه فقط ادمین‌ها قابل ویرایش باشند
        if ($user->role !== 'admin') {
            return redirect()->route('admin.admins')->with('error', 'فقط می‌توانید اطلاعات ادمین‌ها را ویرایش کنید.');
        }

        return view('admin.admins.edit', compact('user'));
    }

    /**
     * ذخیره تغییرات ادمین
     */
    public function update(Request $request, User $user) // ✅ حالا Request به درستی شناخته می‌شود
    {
        // اطمینان از اینکه فقط ادمین‌ها قابل ویرایش باشند
        if ($user->role !== 'admin') {
            return redirect()->route('admin.admins')->with('error', 'فقط می‌توانید اطلاعات ادمین‌ها را ویرایش کنید.');
        }

        // اعتبارسنجی داده‌ها
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // به‌روزرسانی اطلاعات کاربر
        $user->fname = $validated['fname'];
        $user->lname = $validated['lname'];
        $user->email = $validated['email'];

        // اگر رمز عبور وارد شده بود، آن را هش و ذخیره کن
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.admins')->with('success', 'اطلاعات ادمین با موفقیت به‌روزرسانی شد.');
    }
}