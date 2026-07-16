<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت')</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f4f6f9; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #2c3e50; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px; margin: 5px 0; border-radius: 4px; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; }
        .content { flex: 1; padding: 30px; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-logout { background: none; border: none; color: #e74c3c; cursor: pointer; width: 100%; text-align: right; padding: 10px; font-family: inherit; font-size: 14px; margin-top: 20px; }
        .btn-logout:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <!-- سایدبار -->
    <div class="sidebar">
        <h3 style="text-align: center; margin-bottom: 30px;">پنل مدیریت</h3>
    
        <p style="text-align: center; margin-bottom: 5px;">
            👤 {{ auth()->user()->fname ?? auth()->user()->name }}
        </p>
        <p style="text-align: center; color: #bdc3c7; font-size: 13px; margin-top: 0;">
            نقش: {{ auth()->user()->role }}
        </p>
        
        <hr style="border-color: #34495e; margin: 20px 0;">

        <a href="{{ route('admin.dashboard') }}">📊 داشبورد</a>
        
        @if(auth()->user()->role === 'superAdmin')
            <a href="{{ route('admin.admins') }}">👥 لیست ادمین‌ها</a>
            <a href="{{ route('admin.super-admins') }}">👑 لیست سوپر ادمین‌ها</a>
        @endif

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">🚪 خروج از سیستم</button>
        </form>
    </div>

    <!-- محتوای اصلی -->
    <div class="content">
        @if(session('error'))
            <div class="alert alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        {{-- اینجا جایی است که محتوای صفحات فرزند (مثل dashboard) تزریق می‌شود --}}
        @yield('content')
    </div>

</body>
</html>