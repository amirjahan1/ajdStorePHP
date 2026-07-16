@extends('layouts.admin')

@section('title', 'داشبورد مدیریت')

@section('content')
    <h2>داشبورد مدیریت</h2>
    
    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- کارت آمار کل کاربران -->
        <div class="card" style="flex: 1; text-align: center; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3 style="margin: 0; color: #2c3e50;">{{ $stats['total_users'] ?? 0 }}</h3>
            <p style="color: #7f8c8d; margin-top: 10px;">کل کاربران</p>
        </div>

        <!-- کارت آمار ادمین‌ها -->
        <div class="card" style="flex: 1; text-align: center; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3 style="margin: 0; color: #2980b9;">{{ $stats['admins'] ?? 0 }}</h3>
            <p style="color: #7f8c8d; margin-top: 10px;">تعداد ادمین‌ها</p>
        </div>

        <!-- کارت آمار سوپر ادمین‌ها -->
        <div class="card" style="flex: 1; text-align: center; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3 style="margin: 0; color: #e74c3c;">{{ $stats['super_admins'] ?? 0 }}</h3>
            <p style="color: #7f8c8d; margin-top: 10px;">تعداد سوپر ادمین‌ها</p>
        </div>
    </div>

    <div class="card" style="margin-top: 30px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">خوش آمدید!</h3>
        <p>شما با موفقیت وارد پنل مدیریت شده‌اید.</p>
        <p>نقش فعلی شما: <strong>{{ auth()->user()->role }}</strong></p>
        <p>ایمیل: <strong>{{ auth()->user()->email }}</strong></p>
    </div>
@endsection