<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmissionCollegeShsProgramsSeeder extends Seeder
{
    public function run()
    {
        $programs = [
            ['program' => 'ACADEMIC : Humanities and Social Sciences (HumSS)', 'is_active' => 1, 'shortname' => 'HUMSS'],
            ['program' => 'ACADEMIC : Accountancy, Business, and Management (ABM)', 'is_active' => 1, 'shortname' => 'ABM'],
            ['program' => 'ACADEMIC : Science, Technology, Engineering, and Mathematics (STEM)', 'is_active' => 1, 'shortname' => 'STEM'],
            ['program' => 'ACADEMIC : Science, Technology, Engineering, and Mathematics (STEM+)', 'is_active' => 1, 'shortname' => 'STEM'],
            ['program' => 'ARTS AND DESIGN', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'SPORTS', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Cookery', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Food and Beverage Services', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Baking and Pastry Production', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Plumbing', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Pharmacy Aid', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Accounting and Markering Services Management', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD : Technical Drafting and Design', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TECHNICAL-VOCATIONAL LIVELIHOOD', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'ACADEMIC : General Academic Strand (GAS)', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'N/A', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'OTHERS', 'is_active' => 1, 'shortname' => ''],
            ['program' => '', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Tourism Operation', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - CSS', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - HOME ECONOMICS', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - AUTOMATIVE', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL- ICT', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL- Cookery', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - FOOD PROCESSING', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL- AFA Organic Agriculture NC II', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Electronic Products Assembly & Servicing', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL- Hospitality And Restaurant Services', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL-TOURISM', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'IT in Mobile App and Web Development', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - EPAS', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Maritime Pre-Baccalaureate', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Computer Systems Servicing', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL ICT-CSS', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL - HE', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Sports Track', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Arts and Design', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'TVL- Technical Drafting', 'is_active' => 1, 'shortname' => ''],
            ['program' => 'Pre-Baccalaureate Maritime Specialization', 'is_active' => 1, 'shortname' => ''],
        ];
        
        foreach ($programs as &$program) {
            $program['created_at'] = now();
            $program['updated_at'] = now();
        }

        DB::table('admission_college_shs_programs')->insert($programs);
    }
}
