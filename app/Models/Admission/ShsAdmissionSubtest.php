<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShsAdmissionSubtest extends Model
{
    use HasFactory;

    protected $table = 'admission_shs_subtests';

    protected $fillable = [
        'name',
        'totalscore',
        'priority',
        'subtest_group',
    ];
}
