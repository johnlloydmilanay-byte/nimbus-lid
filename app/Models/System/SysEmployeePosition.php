<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysEmployeePosition extends Model
{
    protected $table = 'sys_employee_position';

    protected $fillable = [
        'employee_position',
    ];
}
