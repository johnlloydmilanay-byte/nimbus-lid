<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineAdmissionForm extends Model
{
    use HasFactory;

    protected $table = 'online_admission_forms';

    protected $fillable = [
        'application_number',

        'lastname', 'firstname', 'middlename', 'suffix',
        'program_name', 'applicant_status', 'gender',
        'mobile_no', 'email', 'no_of_siblings', 'dob', 'birthplace',
        'religion', 'nationality', 'province', 'city', 'barangay',
        'staying_in', 'same_as_permanent', 'current_province', 'current_city', 'current_barangay',

        'elem_school_name', 'elem_address', 'elem_school_year_attended',
        'jhs_name', 'jhs_address', 'jhs_year_attended',
        'shs_name', 'shs_address', 'shs_year_attended',
        'awards', 'organizations', 'position',

        'father_name', 'father_occupation', 'father_age', 'father_education', 'father_mobile_no',
        'father_status', 'father_placework', 'father_ofw_status',

        'mother_name', 'mother_occupation', 'mother_age', 'mother_education', 'mother_mobile_no',
        'mother_status', 'mother_placework', 'mother_ofw_status',

        'guardian_name', 'guardian_occupation', 'guardian_number',
        'parents_marital_status', 'monthly_family_income', 'family_living_arrangement', 'others_specify',

        'is_pwd', 'is_pwd_yes', 'is_scholar', 'is_scholar_type',
        'is_scholar_yes_others', 'declaration',
    ];

    protected $casts = [
        'same_as_permanent' => 'boolean',
        'is_pwd' => 'boolean',
        'is_scholar' => 'boolean',
        'declaration' => 'boolean',
        'dob' => 'date',
    ];
}
