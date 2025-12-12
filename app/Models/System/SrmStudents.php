<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmStudents extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_students';

    protected $fillable = [
        'application_number',

        'lastname', 'firstname', 'middlename',
        'department_id', 'program_id',
        'studentstatus_id', 'year_level_id', 'year_entry',
        'gender', 'mobile_no', 'email',
        'no_of_siblings', 'dob', 'birthplace',
        'religion', 'nationality',

        'province_id', 'city_id', 'barangay', 'staying_in',
        'current_province_id', 'current_city_id', 'current_barangay',

        'elem_school_name', 'elem_address', 'elem_school_year_attended',
        'jhs_name', 'jhs_address', 'jhs_year_attended',
        'awards', 'organization', 'position',

        'father_name', 'father_occupation', 'father_age', 'father_education', 'father_mobile_no', 'father_status', 'father_placework', 'father_ofw_status',
        'mother_name', 'mother_occupation', 'mother_age', 'mother_education', 'mother_mobile_no', 'mother_status', 'mother_placework', 'mother_ofw_status',
        'guardian_name', 'guardian_occupation', 'guardian_number',
        'parents_marital_status', 'monthly_family_income', 'family_living_arrangement', 'others_specify',

        'is_pwd', 'is_pwd_yes',
        'is_scholar', 'is_scholar_type', 'is_scholar_yes_others',

        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'is_pwd' => 'boolean',
        'is_scholar' => 'boolean',
        'dob' => 'date',
    ];

    // Relationships

    public function departmentRelation()
    {
        return $this->belongsTo(SysDepartment::class, 'department_id');
    }

    public function program()
    {
        return $this->belongsTo(SrmProgram::class, 'program_id');
    }

    public function year() {
        return $this->belongsTo(SysYearLevelsDetails::class, 'year_level_id');
    }

    public function provinceRelation()
    {
        return $this->belongsTo(SysAddressProvince::class, 'province');
    }

    public function cityRelation()
    {
        return $this->belongsTo(SysAddressTown::class, 'city');
    }

    public function creator()
    {
        return $this->belongsTo(SrmUser::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(SrmUser::class, 'updated_by', 'user_id');
    }

    public function deleter()
    {
        return $this->belongsTo(SrmUser::class, 'deleted_by', 'user_id');
    }
}
