<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeSubtest extends Model
{
    use HasFactory;

    protected $table = 'admission_college_subtests';

    protected $fillable = [
        'name',
        'slug',
        'ts',
        'type',
        'is_active',
    ];

    protected $casts = [
        'ts' => 'integer',
        'type' => 'integer',
        'is_active' => 'boolean',
    ];
}
