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

         // User::factory()
        // ->count(300) 
        // ->create()
        // ->each(function ($user) {
        //     UserDetail::factory()->create([
        //         'id_user' => $user->id
        //     ]);
        // });

        // User::doesntHave('user_detail')->each(function ($user) {
        //     UserDetail::factory()->create([
        //         'id_user' => $user->id
        //     ]);
        // });

        // User::where('email', 'like', '%@example%')
        //     ->each(function ($user) {
        //         $username = explode('@', $user->email)[0];
        //         $user->update([
        //             'email' => $username.'@gmail.com'
        //         ]);
        //     });
    }
}
