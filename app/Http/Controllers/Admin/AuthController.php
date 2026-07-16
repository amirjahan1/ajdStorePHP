<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

       public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
       
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'ایمیل یا رمز عبور اشتباه است.')->withInput();
        }

        // 👇👇👇 این بخش را اضافه کنید تا ببینیم لاراول چه فکری می‌کند 👇👇👇
        $authIdentifier = $user->getAuthIdentifier();
        
        

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}