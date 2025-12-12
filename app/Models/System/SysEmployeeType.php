<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysEmployeeType extends Model
{
    protected $table = 'sys_employee_type';

    protected $fillable = [
        'employee_type',
    ];
}
