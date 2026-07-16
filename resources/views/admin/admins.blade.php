@extends('layouts.admin')

@section('title', 'لیست ادمین‌ها')

@section('content')
    <h2>لیست کاربران با نقش ادمین</h2>

    {{-- نمایش پیام موفقیت --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        @forelse($users as $user)
            @if($loop->first)
                <table>
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>ایمیل</th>
                            <th>تاریخ عضویت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif
            
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->fname }} {{ $user->lname }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('Y/m/d') }}</td>
                <td>
                    <a 
                        href="{{ route('admin.admins.edit', $user) }}" 
                        style="padding: 6px 15px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;"
                    >
                        ✏️ ویرایش
                    </a>
                </td>
            </tr>

            @if($loop->last)
                    </tbody>
                </table>
            @endif
        @empty
            <p style="color: red;">هیچ ادمینی در سیستم ثبت نشده است.</p>
        @endforelse
    </div>

    <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: #2980b9;">← بازگشت به داشبورد</a>
@endsection