<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Comment;
use App\Model\CartItem;
use App\Model\Category;
class Product extends Model
{
     use HasFactory, SoftDeletes;
     protected $keyType = 'string';
     public $incrementing = false;


      protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock'
    ];


    // Mnay To One
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // One To Many
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    // One to Many
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
