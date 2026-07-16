<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Product;
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'product_count'
    ];

    // One To Many
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    // Many To One
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // One To Many
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
