<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'name' => 'User Test', 'username' => 'test', 'email' => 'test@example.com', 'password' => Hash::make('password123'), 'user_type' => 1, 'designation_id' => 1, 'active' => 1, 'created_by' => null, 'deleted_by' => null, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
