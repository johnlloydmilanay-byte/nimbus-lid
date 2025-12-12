<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SrmUser extends Seeder
{
    public function run()
    {
        $user = [
            [
                'user_id' => '123456X',
                'username' => '123456X',
                'password' => Hash::make('123456X'), // hashed password
                'usertype' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('srm_users')->insert($user);
    }
}
