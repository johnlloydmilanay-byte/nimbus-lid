<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionJhsSubtestPercentage extends Seeder
{
    public function run()
    {
        $percentage = [
            ['program_id' => 3, 'subtest_id' => 1, 'percentage' => 0.25],
            ['program_id' => 3, 'subtest_id' => 2, 'percentage' => 0.25],
            ['program_id' => 3, 'subtest_id' => 3, 'percentage' => 0.25],
            ['program_id' => 3, 'subtest_id' => 4, 'percentage' => 0.15],
            ['program_id' => 3, 'subtest_id' => 5, 'percentage' => 0.00],
            ['program_id' => 3, 'subtest_id' => 6, 'percentage' => 0.10],

            ['program_id' => 4, 'subtest_id' => 1, 'percentage' => 0.20],
            ['program_id' => 4, 'subtest_id' => 2, 'percentage' => 0.20],
            ['program_id' => 4, 'subtest_id' => 3, 'percentage' => 0.20],
            ['program_id' => 4, 'subtest_id' => 4, 'percentage' => 0.10],
            ['program_id' => 4, 'subtest_id' => 5, 'percentage' => 0.20],
            ['program_id' => 4, 'subtest_id' => 6, 'percentage' => 0.10],

            ['program_id' => 50, 'subtest_id' => 1, 'percentage' => 0.25],
            ['program_id' => 50, 'subtest_id' => 2, 'percentage' => 0.25],
            ['program_id' => 50, 'subtest_id' => 3, 'percentage' => 0.25],
            ['program_id' => 50, 'subtest_id' => 4, 'percentage' => 0.15],
            ['program_id' => 50, 'subtest_id' => 5, 'percentage' => 0.00],
            ['program_id' => 50, 'subtest_id' => 6, 'percentage' => 0.10],
        ];

        DB::table('admission_jhs_subtest_percentage')->insert($percentage);
    }
}
