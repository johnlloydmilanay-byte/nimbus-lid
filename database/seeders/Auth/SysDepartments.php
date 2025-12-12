<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SysDepartments extends Seeder
{
    public function run()
    {
        $sysdepartments = [
            ['academicgroup_id' => NULL, 'code' => 'CREED', 'name' => 'Center for Religious Education', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CCM', 'name' => 'Center for Campus Ministry', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CCI', 'name' => 'Center for Community Involvement', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 3, 'code' => 'PSE', 'name' => 'Preschool and Elementary', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 2, 'code' => 'JHS', 'name' => 'AQUI Junior Highschool Department', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 1, 'code' => 'CASE', 'name' => 'College of Arts, Sciences and Education', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 1, 'code' => 'CBMA', 'name' => 'College of Business Management and Accountancy', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 1, 'code' => 'CHS', 'name' => 'College of Health Sciences', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 1, 'code' => 'CEAFA', 'name' => 'College of Engineering, Architecture and Fine Arts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 5, 'code' => 'LAW', 'name' => 'College of Law', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 4, 'code' => 'GS', 'name' => 'Graduate School', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OUR', 'name' => 'Office of the University Registrar', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OSS', 'name' => 'Office of Student Services', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OGT', 'name' => 'Office of Guidance and Testing', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'ULIS', 'name' => 'University Library and Information Services', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'LID', 'name' => 'Laboratories and Instrumentation Department', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'HRMO', 'name' => 'Human Resource Management Office', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'PPFMO', 'name' => 'Physical Plant and Facilities Management Office', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'ETC', 'name' => 'Educational Technology Center', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CSWCA', 'name' => 'Center for Sports, Wellness, Culture and the Arts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OUC', 'name' => 'Office of the University Controller', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'FRMO', 'name' => 'Financial Resources Management Office', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CCA', 'name' => 'Center for Culture and Arts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CCE', 'name' => 'Center for Continuing Education', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OVPAA', 'name' => 'Office of the Vice President for Academic Affairs', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'MAPA', 'name' => 'Media, Alumni and Public Affairs Office', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OVPRA', 'name' => 'Office of the Vice President for Religious Affairs', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'ORP', 'name' => 'Office of the Rector and President', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OPD', 'name' => 'Office of Planning and Development', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OUTSOURCED', 'name' => 'Outsourced', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 6, 'code' => 'SHS', 'name' => 'AQUI Senior Highschool Department', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 7, 'code' => 'AUL Masbate', 'name' => 'AUL MASBATE', 'is_active' => 0, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'VISITOR', 'name' => 'VISITOR', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'CONCESSIONAIRE', 'name' => 'CONCESSIONAIRE', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 8, 'code' => 'GS-CPT', 'name' => 'GS-CPT', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OR', 'name' => 'Office of Research', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'OVPAF', 'name' => 'Office of the Vice President for Administration and Finance', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'BSC', 'name' => 'Bicol Studies Center', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'INSTITUTIONAL', 'name' => 'Institutional', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 9, 'code' => 'GS-MLAW', 'name' => 'GS-Master of Laws', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'FETCHER', 'name' => 'FETCHER', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'WAREHOUSE', 'name' => 'Warehouse', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => NULL, 'code' => 'Bookstore', 'name' => 'Bookstore', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['academicgroup_id' => 10, 'code' => 'GS-SSP', 'name' => 'Graduate School-SSP', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('sys_departments')->insert($sysdepartments);
    }
}
