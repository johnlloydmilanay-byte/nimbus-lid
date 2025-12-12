<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeScoreTransmutation extends Model
{
    use HasFactory;

    protected $table = 'admission_college_score_transmutation';

    protected $fillable = [
        'totalscore',
        'rawscore',
        'transmutation',
        'is_active',
    ];

    protected $casts = [
        'totalscore' => 'integer',
        'rawscore' => 'integer',
        'transmutation' => 'float',
        'is_active' => 'boolean',
    ];
}
