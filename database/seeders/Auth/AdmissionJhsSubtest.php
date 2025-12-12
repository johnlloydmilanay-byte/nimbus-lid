<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionJhsSubtest extends Seeder
{
    public function run()
    {
        $shssubtest = [
            ['name' => 'ENGLISH', 'totalscore' => 50, 'priority' => 1, 'subtest_group' => 1, 'weight' => 0.20, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'MATHEMATICS', 'totalscore' => 50, 'priority' => 2, 'subtest_group' => 1, 'weight' => 0.20, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'SCIENCE', 'totalscore' => 50, 'priority' => 3, 'subtest_group' => 1, 'weight' => 0.20, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'IQ TEST', 'totalscore' => 80, 'priority' => 1, 'subtest_group' => 2, 'weight' => 0.10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'AUDITION', 'totalscore' => 100, 'priority' => 1, 'subtest_group' => 3, 'weight' => 0.20, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'INTERVIEW', 'totalscore' => 4, 'priority' => 1, 'subtest_group' => 4, 'weight' => 0.10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('admission_jhs_subtest')->insert($shssubtest);
    }
}
