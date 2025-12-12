<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysEmployeeStatus extends Seeder
{
    public function run()
    {
        $employeeStatus = [
            ['employee_status' => 'CLINICAL INSTRUCTOR'],
            ['employee_status' => 'CONTRACTUAL'],
            ['employee_status' => 'FIXED TERM'],
            ['employee_status' => 'FULLTIME'],
            ['employee_status' => 'GUEST LECTURER'],
            ['employee_status' => 'PARTTIME'],
            ['employee_status' => 'PROBATIONARY'],
            ['employee_status' => 'PROJECT BASED'],
            ['employee_status' => 'REGULAR'],
            ['employee_status' => 'RETAINER'],
            ['employee_status' => 'SPECIAL'],
            ['employee_status' => 'SPECIAL LECTURER'],
            ['employee_status' => 'VISITOR'],
        ];

        foreach ($employeeStatus as $status) {
            DB::table('sys_employee_status')->insert([
                'employee_status' => $status['employee_status'],
                'is_active' => 1,
                'created_by' => '123456X',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
