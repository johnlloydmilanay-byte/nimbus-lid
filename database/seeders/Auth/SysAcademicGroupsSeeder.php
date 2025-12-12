<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SysAcademicGroupsSeeder extends Seeder
{
    public function run()
    {
        $sysacademicgroups = [
            ['code' => 'Tertiary', 'name' => 'Tertiary', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'JHS', 'name' => 'Junior High School', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'PSE', 'name' => 'Preschool and Elementary', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'GS', 'name' => 'Graduate School', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'LAW', 'name' => 'College of Law', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'SHS', 'name' => 'Senior High School', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'AUL Masbate', 'name' => 'AUL Masbate', 'is_active' => 0, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'GS-CPT', 'name' => 'Graduate School-CPT', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'GS-MLAW', 'name' => 'Graduate School-MLAW', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['code' => 'GS-SSP', 'name' => 'Graduate School-SSP', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('sys_academicgroups')->insert($sysacademicgroups);
    }
}
