<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Jobs\RemoveCartItemJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ClearExpiredCartItems extends Command
{
    // نام دستوری که در ترمینال تایپ می‌شود
    protected $signature = 'cart:clear-expired';

    // توضیحات دستور
    protected $description = 'حذف خودکار آیتم‌های سبد خرید که بیش از ۳ ساعت از عمرشان گذشته است';

    public function handle()
    {
        // محاسبه زمان ۳ ساعت پیش
        $threeHoursAgo = Carbon::now()->subHours(3);

        // پیدا کردن آیتم‌های منقضی شده (استفاده از cursor برای مصرف رم پایین در حجم بالای داده)
        $expiredItems = CartItem::where('created_at', '<', $threeHoursAgo)->cursor();

        $count = 0;
        foreach ($expiredItems as $item) {
            // ارسال جاب به صف (بدون توقف اجرای حلقه)
            RemoveCartItemJob::dispatch($item->id);
            $count++;
        }

        $this->info("تعداد {$count} آیتم منقضی شده برای حذف به صف ارسال شدند.");
    }
}