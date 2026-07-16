<?php

namespace App\Jobs;

use App\Models\CartItem;
use App\Mail\CartItemRemovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RemoveCartItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // فقط ID را نگه می‌داریم تا اگر مدل قبل از اجرای جاب حذف شد، خطا ندهد
    public function __construct(
        public string $cartItemId
    ) {}

    /**
     * منطق اصلی اجرای جاب
     */
    public function handle(): void
    {
        // 1. دریافت آیتم به همراه روابط (برای جلوگیری از کوئری N+1 در ایمیل)
        $cartItem = CartItem::with(['user', 'product'])->find($this->cartItemId);

        // 2. اگر آیتم قبلاً حذف شده بود، کاری انجام نده (ایمن‌سازی)
        if (!$cartItem) {
            return;
        }

        // 3. ارسال ایمیل به کاربر
        if ($cartItem->user && $cartItem->user->email) {
            Mail::to($cartItem->user->email)->send(new CartItemRemovedMail($cartItem));
        }

        // 4. حذف نهایی آیتم از دیتابیس
        $cartItem->delete();
    }
}