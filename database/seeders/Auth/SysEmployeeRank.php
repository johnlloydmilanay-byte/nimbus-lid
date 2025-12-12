<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysEmployeeRank extends Seeder
{
    public function run()
    {
        $employeeRank = [
            ['employee_rank' => 'Instructor A/1', 'is_active' => 0],
            ['employee_rank' => 'Instructor', 'is_active' => 0],
            ['employee_rank' => 'Instructor B/2', 'is_active' => 0],
            ['employee_rank' => 'Assistant Professor A/1', 'is_active' => 0],
            ['employee_rank' => 'Assistant Professor B/2', 'is_active' => 0],
            ['employee_rank' => 'Assistant Professor C/3', 'is_active' => 0],
            ['employee_rank' => 'Associate Professor A/1', 'is_active' => 0],
            ['employee_rank' => 'Associate Professor B/2', 'is_active' => 0],
            ['employee_rank' => 'Associate Professor C/3', 'is_active' => 0],
            ['employee_rank' => 'Professor A/1', 'is_active' => 0],
            ['employee_rank' => 'Professor B/2', 'is_active' => 0],
            ['employee_rank' => 'Professor C/3', 'is_active' => 0],
            ['employee_rank' => 'Professor Emeritus', 'is_active' => 0],
            ['employee_rank' => 'Lecturer 1', 'is_active' => 1],
            ['employee_rank' => 'Lecturer 2', 'is_active' => 1],
            ['employee_rank' => 'Lecturer 3', 'is_active' => 1],
            ['employee_rank' => 'Instructor 1', 'is_active' => 1],
            ['employee_rank' => 'Instructor 2', 'is_active' => 1],
            ['employee_rank' => 'Master Teacher 1', 'is_active' => 1],
            ['employee_rank' => 'Master Teacher 2', 'is_active' => 1],
            ['employee_rank' => 'Master Teacher 3', 'is_active' => 1],
            ['employee_rank' => 'Assistant Professor 1', 'is_active' => 1],
            ['employee_rank' => 'Assistant Professor 2', 'is_active' => 1],
            ['employee_rank' => 'Assistant Professor 3', 'is_active' => 1],
            ['employee_rank' => 'Associate Professor 1', 'is_active' => 1],
            ['employee_rank' => 'Associate Professor 2', 'is_active' => 1],
            ['employee_rank' => 'Associate Professor 3', 'is_active' => 1],
            ['employee_rank' => 'Professor 1', 'is_active' => 1],
            ['employee_rank' => 'Professor 2', 'is_active' => 1],
            ['employee_rank' => 'Professor 3', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 1', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 2', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 3', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 4', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 5', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 6', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 7', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 8', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 9', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 10', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 11', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 12', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 13', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 14', 'is_active' => 1],
            ['employee_rank' => 'Clinical Instructor 15', 'is_active' => 1],
        ];

        foreach ($employeeRank as $rank) {
            DB::table('sys_employee_rank')->insert([
                'employee_rank' => $rank['employee_rank'],
                'created_by' => '123456X',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
