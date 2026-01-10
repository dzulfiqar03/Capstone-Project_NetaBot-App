<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
