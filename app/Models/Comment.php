<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'parent_id',
        'body',
        'is_approved',
        'rate',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rate' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    protected static function booted()
    {
        static::created(function ($comment) {
            $comment->updateProductCounts();
        });

        static::updated(function ($comment) {
            if ($comment->wasChanged('rate') || $comment->wasChanged('is_approved')) {
                $comment->updateProductCounts();
            }
        });

        static::deleted(function ($comment) {
            $comment->updateProductCounts();
        });
    }

    private function updateProductCounts()
    {
        $product = $this->product;
        if (!$product) {
            return;
        }

        $comments = $product->comments()->where('is_approved', true)->get();
        $totalComments = $comments->count();
        $totalRatings = $comments->whereNotNull('rate')->count();
        $sumRates = $comments->whereNotNull('rate')->sum('rate');

        $product->comments_count = $totalComments;
        $product->ratings_count = $totalRatings;
        $product->average_rating = $totalRatings > 0 ? round($sumRates / $totalRatings, 2) : 0;

        $product->saveQuietly();
    }
}