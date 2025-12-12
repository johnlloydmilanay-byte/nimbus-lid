<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JhsAdmissionSubtestPercentage extends Model
{
    use HasFactory;

    protected $table = 'admission_jhs_subtest_percentage';

    protected $fillable = [
        'program_id',
        'subtest_id',
        'percentage',
    ];
}
