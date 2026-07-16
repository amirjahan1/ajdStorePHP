<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->unique(['user_id', 'product_id']);
            $table->timestamps();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // تغییر: به جای 'id' به 'uuid' در جدول users ارجاع دهید
            $table->foreign('user_id')
                  ->references('uuid')   // <-- اصلاح این خط
                  ->on('users')
                  ->onDelete('cascade');

            // product_id به products.id ارجاع می‌دهد (که primary key است، مشکلی ندارد)
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};