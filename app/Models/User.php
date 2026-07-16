<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage; // ✅ اضافه شد برای کار با MinIO
use Tymon\JWTAuth\Contracts\JWTSubject; // ✅ اضافه شد برای JWT
use Illuminate\Notifications\Notifiable;
use App\Model\CartItem;
use App\Model\Comment;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'uuid';
    
    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'fname',
        'lname',
        'email',
        'phoneNumber',
        'password',
        'profile',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // ✅ نام کلید باید دقیقاً با نام متد Accessor هماهنگ باشد
    protected $appends = [
        'profile_url',
    ];

    /**
     * ✅ اصلاح شد: نام متد باید getProfileUrlAttribute باشد تا با $appends هماهنگ شود
     */
    public function getProfileUrlAttribute(): ?string
    {
        if (!$this->profile) {
            return null;
        }

        // لینک موقت با اعتبار ۶۰ دقیقه
        return Storage::disk('minio')->temporaryUrl(
            $this->profile,
            now()->addMinutes(60)
        );
    }

    protected function casts(): array 
    {
        return [
            'uuid' => 'string',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * اسکوپ برای بررسی دسترسی ادمین
     * ✅ اصلاح شد: superAdmin با حروف کوچک تا با قوانین Validation هماهنگ باشد
     */
    public function scopeAdmin($query) 
    {
        return $query->where('role', 'admin')->orWhere('role', 'superAdmin');
    }

    // ==========================================
    // ✅ متدهای الزامی برای JWTSubject (رفع خطای اصلی)
    // ==========================================

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        // چون primaryKey روی 'uuid' تنظیم شده، این متد به طور خودکار uuid را برمی‌گرداند
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        // می‌توانید داده‌های اضافی را داخل توکن JWT قرار دهید (اختیاری)
        return [
            'role' => $this->role,
        ];
    }

    public function cartItems()
    {
        return $this->hasMany(CartItems::class);
    }


    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }



   
}

