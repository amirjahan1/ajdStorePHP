<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'role' => $this->role,
            
            // ✅ فراخوانی مستقیم اکسسوری که در مدل User ساختید
            // این خط به طور خودکار temporaryUrl را تولید می‌کند
            'profile_url' => $this->profile_url, 
            
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}