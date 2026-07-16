<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::inRandomOrder()->first()?->uuid ?? User::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'parent_id' => null,
            'body' => $this->faker->paragraph,
            'is_approved' => $this->faker->boolean(80),
            'rate' => $this->faker->optional(0.5)->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}