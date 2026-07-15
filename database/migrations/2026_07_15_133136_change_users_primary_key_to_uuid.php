<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ۱. حذف کلید اصلی (Primary Key) فعلی که روی فیلد id قرار دارد
            $table->dropPrimary();

            // ۲. حذف ایندکس یونیک از فیلد uuid (چون می‌خواهیم آن را Primary کنیم، یونیک بودن خودکار اعمال می‌شود)
            $table->dropUnique(['uuid']);
        });

        // ۳. تبدیل فیلد id از حالت Auto-increment (bigserial) به یک عدد معمولی (bigint) در پستگرس
        // این کار باعث می‌شود id دیگر ایندکس یا کلید نباشد و صرفاً یک ستون عددی برای ذخیره‌سازی باشد
        DB::statement('ALTER TABLE users ALTER COLUMN id DROP DEFAULT');
        DB::statement('DROP SEQUENCE IF EXISTS users_id_seq');

        Schema::table('users', function (Blueprint $table) {
            // ۴. تعریف فیلد uuid به عنوان کلید اصلی (Primary Key)
            $table->primary('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // بازگردانی تغییرات در صورت نیاز به Rollback
            $table->dropPrimary(['uuid']);
        });

        // بازگردانی id به حالت auto-increment
        DB::statement('CREATE SEQUENCE IF NOT EXISTS users_id_seq');
        DB::statement('ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval(\'users_id_seq\')');
        DB::statement('ALTER SEQUENCE users_id_seq OWNED BY users.id');

        Schema::table('users', function (Blueprint $table) {
            $table->primary('id');
            $table->unique('uuid');
        });
    }
};