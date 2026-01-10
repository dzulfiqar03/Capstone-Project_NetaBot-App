<?php

namespace Database\Factories;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDetailFactory extends Factory
{
    protected $model = UserDetail::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'fullname' => $this->faker->name(),
            'roles' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin()
    {
        return $this->state(fn () => [
            'roles' => 'admin'
        ]);
    }

    public function security()
    {
        return $this->state(fn () => [
            'roles' => 'security'
        ]);
    }
}
