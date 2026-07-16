<!DOCTYPE html>
<html>
<head>
    <title>حذف آیتم سبد خرید</title>
</head>
<body>
    <h2>سلام {{ $cartItem->user->name ?? 'کاربر گرامی' }},</h2>
    <p>آیتم زیر به دلیل گذشت بیش از ۳ ساعت از زمان افزودن، به صورت خودکار از سبد خرید شما حذف شده است:</p>
    
    <ul>
        <li><strong>نام محصول:</strong> {{ $cartItem->product->name ?? 'محصول نامشخص' }}</li>
        <li><strong>تعداد:</strong> {{ $cartItem->quantity }}</li>
    </ul>

    <p>در صورت تمایل، می‌توانید مجدداً آن را به سبد خرید اضافه کنید.</p>
    <p>با تشکر,<br>تیم فروشگاه</p>
</body>
</html>