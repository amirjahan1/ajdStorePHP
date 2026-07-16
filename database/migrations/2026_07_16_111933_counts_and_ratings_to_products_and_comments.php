<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->tinyInteger('rate')->unsigned()->nullable()->comment('1-5');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('subcategories_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['comments_count', 'ratings_count', 'average_rating']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('subcategories_count');
        });
    }
};