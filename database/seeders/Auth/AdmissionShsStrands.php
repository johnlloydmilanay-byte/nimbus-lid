<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionShsStrands extends Seeder
{
    public function run()
    {
        $shsstrands = [
            ['track_id' => 1, 'code' => '', 'name' => 'Science, Technology, Engineering and Mathematics Plus (STEM+)', 'curriculum' => 'Science', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 1, 'code' => '', 'name' => 'Science, Technology, Engineering and Mathematics (STEM)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 1, 'code' => '', 'name' => 'Accountancy, Business and Management (ABM)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 1, 'code' => '', 'name' => 'General Academic Strand (GAS)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 1, 'code' => '', 'name' => 'Humanities and Social Sciences (HUMS)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Pharmacy Aid', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Accounting and Marketing Services Management', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Technical Drafting and Design', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Plumbing', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Food and Beverage Services', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 4, 'code' => '', 'name' => 'Sports', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Baking and Pastry Production', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 2, 'code' => '', 'name' => 'Cookery/Commercial Cooking', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'GAS', 'name' => 'Academic: General Academic Strand (GAS)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'HUMSS', 'name' => 'Academic: Humanities and Social Sciences ( HUMSS)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'ABM', 'name' => 'Academic: Accountancy, Business, and Management (ABM)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'STEM', 'name' => 'Academic: Science, Technology, Engineering, and Mathematics (STEM)', 'curriculum' => 'Science', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'ARTS AND DESIGN', 'name' => 'Arts and Design', 'curriculum' => 'Arts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'HE', 'name' => 'Technical-Vocational Livelihood: Home Economics', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'IA', 'name' => 'Technical-Vocational Livelihood: Industrial Arts', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'ICT', 'name' => 'Technical-Vocational Livelihood: Information and Communications Technology (ICT)', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['track_id' => 0, 'code' => 'SPORTS', 'name' => 'Sports', 'curriculum' => 'General', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('admission_shs_strands')->insert($shsstrands);
    }
}