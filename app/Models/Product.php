<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'comments_count',
        'ratings_count',
        'average_rating',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'stock' => 'integer',
        'comments_count' => 'integer',
        'ratings_count' => 'integer',
        'average_rating' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    protected static function booted()
    {
        static::created(function ($product) {
            if ($product->category_id) {
                $category = Category::find($product->category_id);
                if ($category) {
                    $category->increment('product_count');
                }
            }
        });

        static::updated(function ($product) {
            $originalCategoryId = $product->getOriginal('category_id');
            $newCategoryId = $product->category_id;

            if ($originalCategoryId !== $newCategoryId) {
                if ($originalCategoryId) {
                    $oldCategory = Category::find($originalCategoryId);
                    if ($oldCategory) {
                        $oldCategory->decrement('product_count');
                    }
                }
                if ($newCategoryId) {
                    $newCategory = Category::find($newCategoryId);
                    if ($newCategory) {
                        $newCategory->increment('product_count');
                    }
                }
            }
        });

        static::deleted(function ($product) {
            if ($product->category_id) {
                $category = Category::find($product->category_id);
                if ($category) {
                    $category->decrement('product_count');
                }
            }
        });
    }
}