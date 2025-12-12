<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JhsAdmissionSubtest extends Model
{
    use HasFactory;

    protected $table = 'admission_jhs_subtest';

    protected $fillable = [
        'name',
        'totalscore',
        'priority',
        'subtest_group',
        'weight',
    ];
}
