<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionCollegeSubtest extends Seeder
{
    public function run()
    {
        $collegesubtests = [
            ['name' => 'English', 'slug' => 'english', 'ts' => 50, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Math', 'slug' => 'math', 'ts' => 50, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Science', 'slug' => 'science', 'ts' => 50, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Vocabulary', 'slug' => 'vocabulary', 'ts' => 30, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Analogy', 'slug' => 'analogy', 'ts' => 30, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Numerical', 'slug' => 'numerical', 'ts' => 25, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Non-Verbal', 'slug' => 'nonverbal', 'ts' => 50, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Oral Communication', 'slug' => 'oralcomm', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Reading and Writing Skills', 'slug' => 'readwrite', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'General Math', 'slug' => 'genmath', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Statistics and Probability', 'slug' => 'statprob', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Earth Science', 'slug' => 'earthsci', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Physical Science', 'slug' => 'physci', 'ts' => null, 'type' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'LANGUAGES', 'slug' => 'language', 'ts' => 40, 'type' => 10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'MATH', 'slug' => 'math', 'ts' => 40, 'type' => 10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'SCIENCE', 'slug' => 'science', 'ts' => 40, 'type' => 10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'HUMANITIES', 'slug' => 'humanities', 'ts' => 35, 'type' => 10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'SOCIAL SCIENCES', 'slug' => 'socsci', 'ts' => 35, 'type' => 10, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'ENGLISH', 'slug' => 'language', 'ts' => 50, 'type' => 11, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'MATHEMATICS', 'slug' => 'math', 'ts' => 50, 'type' => 11, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'SCIENCE', 'slug' => 'science', 'ts' => 50, 'type' => 11, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('admission_college_subtests')->insert($collegesubtests);
    }
}