<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() 
    {
        $users = User::latest()->paginate(10);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'phoneNumber' => 'required',
            'role' => ['required', Rule::in(['user','admin','superadmin'])],
            'profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $validated['uuid'] = Str::uuid7()->toString();
        $validated['password'] = Hash::make($validated['password']);

        if($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')->store('profiles', 'public');
        }

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user) 
    {
 
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phoneNumber' => 'required',
            'role' => ['required', Rule::in(['user','admin','superadmin'])],
            'profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|max:255'
        ],
        // [
        //     // پیام‌های خطای سفارشی به زبان فارسی
        //     'fname.required' => 'وارد کردن نام الزامی است.',
        //     'fname.string' => 'نام وارد شده باید متنی باشد.',
        //     'fname.max' => 'نام نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            
        //     'lname.required' => 'وارد کردن نام خانوادگی الزامی است.',
        //     'lname.string' => 'نام خانوادگی وارد شده باید متنی باشد.',
        //     'lname.max' => 'نام خانوادگی نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            
        //     'email.required' => 'وارد کردن ایمیل الزامی است.',
        //     'email.email' => 'فرمت ایمیل وارد شده صحیح نمی‌باشد.',
        //     'email.unique' => 'این آدرس ایمیل قبلاً ثبت شده است.',
            
        //     'phoneNumber.required' => 'وارد کردن شماره تماس الزامی است.',
            
        //     'role.required' => 'انتخاب نقش کاربری الزامی است.',
        //     'role.in' => 'نقش انتخاب شده معتبر نمی‌باشد.',
            
        //     'profile.image' => 'فایل آپلود شده باید یک تصویر باشد.',
        //     'profile.mimes' => 'فرمت تصویر باید یکی از موارد jpeg، png یا jpg باشد.',
        //     'profile.max' => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد.',
            
        //     'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        //     'password.max' => 'رمز عبور نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
        // ]
        ); 
    

        if($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')->store('profiles', 'public');
        }

        if(!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
  
        $user->update($validated);
 
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}