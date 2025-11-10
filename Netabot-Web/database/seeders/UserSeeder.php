<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user utama tanpa set id (biar auto increment)
        $user = User::create([
            'email' => 'netabotadmin@gmail.com',
            'password' => Hash::make('netabot2025'),
        ]);

        // Hubungkan user_detail ke user.id yang baru
        UserDetail::create([
            'id_user' => $user->id,
            'username' => 'netabotadmin',
            'fullname' => 'Netabot Admin',
            'roles' => 'admin',
        ]);
    }
}
