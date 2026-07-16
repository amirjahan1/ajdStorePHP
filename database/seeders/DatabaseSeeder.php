<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Comment;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ۱. ایجاد ۲۰ کاربر
        User::factory(20)->create();

        // ۲. ایجاد دسته‌بندی‌ها
        // ۲۰ دسته ریشه
        Category::factory(20)->create(['parent_id' => null]);

        // ۱۰۰ زیردسته با انتخاب تصادفی والد از بین دسته‌های موجود
        for ($i = 0; $i < 100; $i++) {
            Category::factory()->create([
                'parent_id' => Category::inRandomOrder()->first()->id,
            ]);
        }

        // ۳. ایجاد ۱۵۰ محصول
        Product::factory(150)->create();

        // ۴. ایجاد کامنت‌ها
        // ۸۰ کامنت اصلی (بدون والد)
        Comment::factory(80)->create();

        // ۲۰ کامنت پاسخ (با والد تصادفی از کامنت‌های موجود)
        $comments = Comment::all();
        for ($i = 0; $i < 20; $i++) {
            Comment::factory()->create([
                'parent_id' => $comments->random()->id,
            ]);
        }

        // ۵. ایجاد آیتم‌های سبد خرید (۱۰۰ آیتم با ترکیب منحصربه‌فرد کاربر-محصول)
        $existingCombos = [];
        for ($i = 0; $i < 100; $i++) {
            $user = User::inRandomOrder()->first();
            $product = Product::inRandomOrder()->first();
            $key = $user->uuid . '|' . $product->id;

            // فقط در صورتی که این ترکیب قبلاً اضافه نشده باشد
            if (!in_array($key, $existingCombos)) {
                CartItem::factory()->create([
                    'user_id' => $user->uuid,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                ]);
                $existingCombos[] = $key;
            }
        }
    }
}