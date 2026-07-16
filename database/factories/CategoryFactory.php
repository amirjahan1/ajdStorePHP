<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $name = $this->faker->unique()->word;
        return [
            'id' => $this->faker->uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 99999),
            'parent_id' => null, // در سیدر مقداردهی می‌شود
            'product_count' => 0,
            'subcategories_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}