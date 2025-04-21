<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Vincent Ndegwa',
            'email' => 'ndegwavincent7@gmail.com',
            'password' => '1234',
        ]);
    }
}
