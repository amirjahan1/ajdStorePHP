<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'phoneNumber' => 'required|string',
            'role' => ['required', Rule::in(['user', 'admin', 'superAdmin'])],
            'profile' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'fname.required' => 'وارد کردن نام الزامی است.',
            'lname.required' => 'وارد کردن نام خانوادگی الزامی است.',
            'email.unique' => 'این آدرس ایمیل قبلاً ثبت شده است.',
            'role.in' => 'نقش انتخاب شده معتبر نمی‌باشد.',
            'profile.image' => 'فایل آپلود شده باید یک تصویر باشد.',
        ];
    }
}