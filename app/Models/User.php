<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'uuid';
    
    public $incrementing = false;
    
   
    protected $keyType = 'string';

    protected $fillable = [
        'fname',
        'lname',
        'email',
        'phoneNumber',
        'password',
        'profile',
        'role',
        'uuid'
        
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array 
    {
        return [
            'uuid' => 'string',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    protected function scopeAdmin($query) 
    {
        return $query->where('role', 'admin')->orWhere('role', 'superAdmin');
    }
}