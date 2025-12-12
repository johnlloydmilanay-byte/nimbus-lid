<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysEmployeePosition extends Seeder
{
    public function run()
    {
        $employeePosition = [
            ['employee_position' => 'FACULTY'],
            ['employee_position' => 'NON-TEACHING PERSONNEL'],
            ['employee_position' => 'DEPARTMENT HEAD'],
            ['employee_position' => 'FETCHER'],
            ['employee_position' => 'OUTSOURCED'],
            ['employee_position' => 'VISITOR'],
            ['employee_position' => 'CONCESSIONAIRE'],
        ];

        foreach ($employeePosition as $position) {
            DB::table('sys_employee_position')->insert([
                'employee_position' => $position['employee_position'],
                'is_active' => 1,
                'created_by' => '123456X',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
