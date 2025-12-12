<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PseElemSubtestG1Rating extends Model
{
    use HasFactory;

    protected $table = 'admission_pse_subtest_g1_rating';

    protected $fillable = [
        'subtest_id',
        'score',
        'rating',
    ];
}
