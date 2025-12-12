<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JhsAdmissionScoreTransmutation extends Model
{
    use HasFactory;

    protected $table = 'admission_jhs_score_transmutation';

    protected $fillable = [
        'group_id',
        'subtest_id',
        'subtest_name',
        'rawscore',
        'equivalent',
        'diq',
        'description',
    ];
}
