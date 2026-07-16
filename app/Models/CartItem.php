<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\User;
use App\Model\Product;

class CartItem extends Model
{
     use HasFactory;
     protected $keyType = 'string';
     public $incrementing = false;

      protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'quantity'
    ];


    // Many To One
    public function user()
    {
         return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    // Many To One
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
