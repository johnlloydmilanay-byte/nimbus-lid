<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysEmployeeStatus extends Model
{
    protected $table = 'sys_employee_status';

    protected $fillable = [
        'employee_status',
    ];
}
