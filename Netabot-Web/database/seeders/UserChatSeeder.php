<?php

namespace Database\Seeders;

use App\Models\UserChat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserChat::factory()->count(1000)->create();
    }
}
