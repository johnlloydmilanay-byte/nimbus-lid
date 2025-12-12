<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysEmployeeRank extends Model
{
    protected $table = 'sys_employee_rank';

    protected $fillable = [
        'employee_rank',
    ];
}
