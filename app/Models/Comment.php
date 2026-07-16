<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
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

    /**
     * به‌روزرسانی آمار محصول (تعداد کامنت، تعداد امتیاز، میانگین امتیاز)
     * با استفاده از تراکنش و قفل برای جلوگیری از Race Condition
     */
    private function updateProductCounts()
    {
        $product = $this->product;
        if (!$product) {
            return;
        }

        // استفاده از تراکنش و قفل برای به‌روزرسانی اتمیک
        DB::transaction(function () use ($product) {
            // قفل کردن رکورد محصول برای به‌روزرسانی
            $product = Product::where('id', $product->id)->lockForUpdate()->first();
            if (!$product) {
                return;
            }

            // محاسبه آمار از کامنت‌های تایید شده
            $comments = $product->comments()->where('is_approved', true)->get();
            $totalComments = $comments->count();
            $totalRatings = $comments->whereNotNull('rate')->count();
            $sumRates = $comments->whereNotNull('rate')->sum('rate');

            $product->comments_count = $totalComments;
            $product->ratings_count = $totalRatings;
            $product->average_rating = $totalRatings > 0 ? round($sumRates / $totalRatings, 2) : 0;

            // ذخیره بدون فراخوانی event ها (برای جلوگیری از حلقه)
            $product->saveQuietly();
        });
    }
}