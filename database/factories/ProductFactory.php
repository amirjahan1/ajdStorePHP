<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'id' => $this->faker->uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 99999),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(3, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'comments_count' => 0,
            'ratings_count' => 0,
            'average_rating' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}