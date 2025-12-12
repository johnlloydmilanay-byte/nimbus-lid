<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShsAdmissionSubtestResult extends Model
{
    use HasFactory;

    protected $table = 'admission_shs_subtests_result';

    protected $fillable = [
        'application_number_id',
        'subtest_name',
        'subtest_id',
        'ts',
        'rawscore',
        'transmutation',
        'hs_grade',
        'api',
    ];

    public function shsAdmission()
    {
        return $this->belongsTo(ShsAdmission::class, 'application_number_id');
    }
}
