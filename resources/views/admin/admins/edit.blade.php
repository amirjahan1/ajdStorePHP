@extends('layouts.admin')

@section('title', 'ویرایش ادمین')

@section('content')
    <h2>ویرایش اطلاعات ادمین</h2>

    {{-- نمایش پیام‌های خطا --}}
    @if($errors->any())
        <div style="background: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('admin.admins.update', $user) }}">
            @csrf
            @method('PUT')

            {{-- نام --}}
            <div class="form-group">
                <label for="fname">نام</label>
                <input 
                    type="text" 
                    id="fname" 
                    name="fname" 
                    value="{{ old('fname', $user->fname) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                >
            </div>

            {{-- نام خانوادگی --}}
            <div class="form-group">
                <label for="lname">نام خانوادگی</label>
                <input 
                    type="text" 
                    id="lname" 
                    name="lname" 
                    value="{{ old('lname', $user->lname) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                >
            </div>

            {{-- ایمیل --}}
            <div class="form-group">
                <label for="email">ایمیل</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                >
            </div>

            {{-- رمز عبور (اختیاری) --}}
            <div class="form-group">
                <label for="password">رمز عبور جدید (اختیاری - در صورت تمایل به تغییر)</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="خالی بگذارید اگر نمی‌خواهید تغییر دهید"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                >
            </div>

            {{-- تکرار رمز عبور --}}
            <div class="form-group">
                <label for="password_confirmation">تکرار رمز عبور جدید</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                >
            </div>

            {{-- دکمه‌ها --}}
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button 
                    type="submit" 
                    style="padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;"
                >
                    ذخیره تغییرات
                </button>
                <a 
                    href="{{ route('admin.admins') }}" 
                    style="padding: 12px 30px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-size: 16px;"
                >
                    انصراف
                </a>
            </div>
        </form>
    </div>
@endsection