<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'fname' => $this->faker->firstName,
            'lname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phoneNumber' => $this->faker->unique()->phoneNumber,
            'password' => Hash::make('password'), // رمز پیش‌فرض
            'profile' => null,
            'role' => $this->faker->randomElement(['user', 'admin', 'superAdmin']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}