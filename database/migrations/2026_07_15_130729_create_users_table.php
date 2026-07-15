<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('fname',50)->nullable();
                $table->string('lname',50)->nullable();
                $table->string('email')->unique();
                $table->string('phoneNumber')->unique()->nullable();
                $table->string('password');
                $table->string('profile')->nullable();
                $table->enum('role', ['user','admin','superAdmin'])->default('user');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
