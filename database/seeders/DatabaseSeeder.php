<?php

namespace Database\Seeders;

use Database\Seeders\Auth\AdmissionCollegeScoreTransmutation;
use Database\Seeders\Auth\AdmissionCollegeSubtest;
use Database\Seeders\Auth\AdmissionJhsScoreTransmutation;
use Database\Seeders\Auth\AdmissionJhsSubtest;
use Database\Seeders\Auth\AdmissionJhsSubtestPercentage;
use Database\Seeders\Auth\AdmissionPseElemG1Rating;
use Database\Seeders\Auth\AdmissionPseElemSubtest;
use Database\Seeders\Auth\AdmissionShsScoreTransmutation;
use Database\Seeders\Auth\AdmissionShsStrands;
use Database\Seeders\Auth\AdmissionShsSubtest;
use Database\Seeders\Auth\SrmPrograms;
use Database\Seeders\Auth\SysAcademicGroupsSeeder;
use Database\Seeders\Auth\SysDepartments;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Auth\AdmissionCollegeShsProgramsSeeder;
use Database\Seeders\Auth\SysAddressProvincesSeeder;
use Database\Seeders\Auth\SysAddressTownsSeeder;
use Database\Seeders\Auth\SrmUser;
use Database\Seeders\Auth\SysEmployeePosition;
use Database\Seeders\Auth\SysEmployeeRank;
use Database\Seeders\Auth\SysEmployeeType;
use Database\Seeders\Auth\SysEmployeeStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SysAcademicGroupsSeeder::class,
            SysDepartments::class,
            SrmPrograms::class,
            AdmissionCollegeSubtest::class,
            AdmissionCollegeScoreTransmutation::class,
            AdmissionShsStrands::class,
            AdmissionShsSubtest::class,
            AdmissionShsScoreTransmutation::class,
            AdmissionPseElemSubtest::class,
            AdmissionJhsSubtest::class,
            AdmissionJhsSubtestPercentage::class,
            AdmissionJhsScoreTransmutation::class,
            AdmissionPseElemG1Rating::class,
            AdmissionCollegeShsProgramsSeeder::class,
            SysAddressProvincesSeeder::class,
            SysAddressTownsSeeder::class,
            SrmUser::class,
            SysEmployeePosition::class,
            SysEmployeeRank::class,
            SysEmployeeType::class,
            SysEmployeeStatus::class,
        ]);
    }
}