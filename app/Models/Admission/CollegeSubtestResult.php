<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeSubtestResult extends Model
{
    use HasFactory;

    protected $table = 'admission_college_subtests_result';

    protected $fillable = [
        'application_number_id',
        'name',
        'subtest',
        'subtest_id',
        'ts',
        'rawscore',
        'transmutation',
        'hs_grade',
        'api',
    ];

    public function collegeAdmission()
    {
        return $this->belongsTo(CollegeAdmission::class, 'application_number_id');
    }
}
