<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeAdmissionShsPrograms extends Model
{
    use HasFactory;

    protected $table = 'admission_college_shs_programs';

    protected $fillable = [
        'program',
        'shortname',
    ];
}
