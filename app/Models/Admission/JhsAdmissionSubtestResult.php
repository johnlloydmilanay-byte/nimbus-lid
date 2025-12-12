<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JhsAdmissionSubtestResult extends Model
{
    use HasFactory;

    protected $table = 'admission_jhs_subtest_result';

    protected $fillable = [
        'application_number_id',
        'subtest_name',
        'subtest_id',
        'ts',
        'rawscore',
        'transmutation',
        'hs_grade',
        'api',
        'percentage',
        'equivalent',
        'diq',
        'description',
        'rating',
    ];

    public function collegeAdmission()
    {
        return $this->belongsTo(CollegeAdmission::class, 'admission_jhs_subtest_result');
    }
}
