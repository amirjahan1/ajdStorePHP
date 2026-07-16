<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'parent_id',
        'name',
        'slug',
        'product_count',
        'subcategories_count',
    ];

    protected $casts = [
        'product_count' => 'integer',
        'subcategories_count' => 'integer',
    ];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::created(function ($category) {
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                if ($parent) {
                    $parent->increment('subcategories_count');
                }
            }
        });

        static::updated(function ($category) {
            $originalParentId = $category->getOriginal('parent_id');
            $newParentId = $category->parent_id;

            if ($originalParentId !== $newParentId) {
                if ($originalParentId) {
                    $oldParent = static::find($originalParentId);
                    if ($oldParent) {
                        $oldParent->decrement('subcategories_count');
                    }
                }
                if ($newParentId) {
                    $newParent = static::find($newParentId);
                    if ($newParent) {
                        $newParent->increment('subcategories_count');
                    }
                }
            }
        });

        static::deleted(function ($category) {
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                if ($parent) {
                    $parent->decrement('subcategories_count');
                }
            }
        });
    }
}