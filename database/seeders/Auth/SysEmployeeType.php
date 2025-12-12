<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysEmployeeType extends Seeder
{
    public function run()
    {
        $employeeType = [
            ['employee_type' => 'ADMINISTRATOR'],
            ['employee_type' => 'CONCESSIONAIRE'],
            ['employee_type' => 'FETCHER'],
            ['employee_type' => 'OJT'],
            ['employee_type' => 'OUTSOURCED'],
            ['employee_type' => 'RANK AND FILE'],
            ['employee_type' => 'SUPERVISOR'],
            ['employee_type' => 'VISITOR'],
        ];

        foreach ($employeeType as $type) {
            DB::table('sys_employee_type')->insert([
                'employee_type' => $type['employee_type'],
                'is_active' => 1,
                'created_by' => '123456X',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
