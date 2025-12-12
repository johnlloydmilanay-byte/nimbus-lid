<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmissionShsSubtest extends Seeder
{
    public function run()
    {
        $shssubtest = [
            ['name' => 'English', 'totalscore' => 50, 'priority' => 1, 'subtest_group' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mathematics', 'totalscore' => 50, 'priority' => 1, 'subtest_group' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'totalscore' => 50, 'priority' => 1, 'subtest_group' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Arts and Design Rating', 'totalscore' => 100, 'priority' => 2, 'subtest_group' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('admission_shs_subtests')->insert($shssubtest);
    }
}
