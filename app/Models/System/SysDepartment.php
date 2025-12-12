<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysDepartment extends Model
{
    protected $table = 'sys_departments';

    protected $fillable = [
        'academicgroup_id',
        'code',
        'name',
        'emp_id',
        'active',
    ];

    public function programs()
    {
        return $this->hasMany(SrmProgram::class, 'department_id');
    }
}
