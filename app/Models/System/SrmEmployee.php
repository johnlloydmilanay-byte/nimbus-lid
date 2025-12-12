<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrmEmployee extends Model
{
    use HasFactory;

    protected $table = 'srm_employees';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'tk_id',

        'lastname',
        'firstname',
        'middlename',
        'suffix',
        'prefix',
        'extension',

        'department_id',
        'designation',

        'position_id',
        'employment_date',
        'rank_faculty_id',

        'employee_type_id',
        'employment_status_id',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the related user record.
     */
    public function user()
    {
        return $this->belongsTo(SrmUser::class, 'employee_id', 'user_id');
    }

    /**
     * Get full name accessor (optional).
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname} {$this->suffix}");
    }
}
