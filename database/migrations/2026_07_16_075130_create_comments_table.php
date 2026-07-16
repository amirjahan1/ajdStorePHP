<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // مرحله ۱: ایجاد جدول بدون کلید خارجی خودارجاع
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->uuid('parent_id')->nullable(); // فقط ستون، بدون محدودیت
            $table->text('body');
            $table->boolean('is_approved')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        // مرحله ۲: افزودن کلیدهای خارجی
        Schema::table('comments', function (Blueprint $table) {
            // کلید خارجی به users (ارجاع به uuid به جای id)
            $table->foreign('user_id')
                  ->references('uuid')   // <--- اصلاح اینجا
                  ->on('users')
                  ->onDelete('cascade');

            // کلید خارجی به products (products.id کلید اصلی است، مشکلی ندارد)
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            // کلید خارجی خودارجاع (parent_id -> comments.id)
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('comments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};