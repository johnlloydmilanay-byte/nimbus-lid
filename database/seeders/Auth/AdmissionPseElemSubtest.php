<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmissionPseElemSubtest extends Seeder
{
    public function run()
    {
        $pseelemsubtest = [
            // type 1 (incoming grade 1)
            ['name' => 'Vocabulary', 'maxscore' => 16, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Identifying Letters', 'maxscore' => 18, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Visual Discrimination', 'maxscore' => 15, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Phonemic Awareness', 'maxscore' => 16, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Comprehensive and Interpretation', 'maxscore' => 12, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mathematical Knowledge', 'maxscore' => 24, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Development Spelling Ability', 'maxscore' => 25, 'type' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],

            // type 2 (transferee)
            ['name' => 'English', 'maxscore' => 20, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mathematics', 'maxscore' => 20, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'maxscore' => 20, 'type' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('admission_pse_elem_subtest')->insert($pseelemsubtest);
    }
}
