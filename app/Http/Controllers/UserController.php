<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @tag Users
     * @summary لیست کاربران
     * @description دریافت لیست صفحه‌بندی شده کاربران سیستم.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() 
    {
        $users = UserResource::collection(User::latest()->paginate(10));
        return response()->json($users);
    }

    /**
     * Store a newly created user.
     *
     * @tag Users
     * @summary ایجاد کاربر جدید
     * @description ثبت اطلاعات کاربر جدید همراه با آپلود تصویر پروفایل اختیاری.
     * 
     * @bodyParam fname string required نام کاربر. Example: Amir
     * @bodyParam lname string required نام خانوادگی کاربر. Example: Hosseini
     * @bodyParam email string required ایمیل کاربر (باید یکتا باشد). Example: amir@example.com
     * @bodyParam password string required رمز عبور (حداقل ۸ کاراکتر). Example: password123
     * @bodyParam phoneNumber string required شماره تماس. Example: +989123456789
     * @bodyParam role string required نقش کاربر. Allowed values: user, admin, superAdmin. Example: user
     * @bodyParam profile string format:binary nullable تصویر پروفایل کاربر (حداکثر ۲ مگابایت).
     * 
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $validated['uuid'] = Str::uuid()->toString();
        $validated['password'] = Hash::make($validated['password']);
        
        if ($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')->store('profiles', 'minio');
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @tag Users
     * @summary نمایش جزئیات کاربر
     * @param \App\Models\User $user
     * @return \App\Http\Resources\UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified user.
     *
     * @tag Users
     * @summary ویرایش اطلاعات کاربر
     * @description به‌روزرسانی اطلاعات کاربر. رمز عبور اختیاری است.
     * 
     * @bodyParam fname string required نام کاربر. Example: Amir
     * @bodyParam lname string required نام خانوادگی کاربر. Example: Hosseini
     * @bodyParam email string required ایمیل کاربر (باید یکتا باشد). Example: amir@example.com
     * @bodyParam password string nullable رمز عبور جدید (حداقل ۸ کاراکتر). Example: newpassword123
     * @bodyParam phoneNumber string required شماره تماس. Example: +989123456789
     * @bodyParam role string required نقش کاربر. Allowed values: user, admin, superAdmin. Example: user
     * @bodyParam profile string format:binary nullable تصویر پروفایل کاربر (حداکثر  مگابایت).
     * 
     * @param \App\Http\Requests\UpdateUserRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user) 
    {
        $validated = $request->validated();

        if ($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')->store('profiles', 'minio');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
  
        $user->update($validated);
 
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified user.
     *
     * @tag Users
     * @summary حذف کاربر
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}