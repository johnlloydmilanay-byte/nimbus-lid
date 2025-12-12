<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SHSAdmissionScoreTransmutation extends Model
{
    use HasFactory;

    protected $table = 'admission_shs_score_transmutation';

    protected $fillable = [
        'subtest_id',
        'subtest_group_id',
        'subtest_name',
        'rawscore',
        'equivalent',
        'transmutation',
    ];

    public function subtest()
    {
        return $this->belongsTo(ShsSubtest::class, 'subtest_id');
    }
}
