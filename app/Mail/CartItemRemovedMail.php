<?php

namespace App\Mail;

use App\Models\CartItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartItemRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    // دریافت آبجکت CartItem برای دسترسی به اطلاعات کاربر و محصول
    public function __construct(
        public CartItem $cartItem
    ) {}

    /**
     * تنظیمات پاکت ایمیل (موضوع و فرستنده)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'آیتم سبد خرید شما منقضی و حذف شد',
        );
    }

    /**
     * محتوای ایمیل (استفاده از Blade)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cart_item_removed', // مسیر فایل ویو که در مرحله بعد می‌سازیم
        );
    }
}