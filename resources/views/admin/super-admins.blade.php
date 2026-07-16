@extends('layouts.admin')

@section('title', 'لیست سوپر ادمین‌ها')

@section('content')
    <h2>لیست کاربران با نقش سوپر ادمین</h2>
    
    <div class="card">
        @forelse($users as $user)
            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong>{{ $user->fname }} {{ $user->lname }}</strong> ({{ $user->email }})
                <!-- کامنت‌گذاری در بلید (این متن در HTML خروجی دیده نمی‌شود) -->
                {{-- این کاربر دسترسی کامل به سیستم دارد --}}
            </div>
        @empty
            <p>سوپر ادمینی یافت نشد.</p>
        @endforelse
    </div>

    <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: #2980b9;">← بازگشت به داشبورد</a>
@endsection