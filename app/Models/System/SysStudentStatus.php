<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysStudentStatus extends Model
{
    use HasFactory;

    protected $table = 'sys_studentstatus';

    protected $fillable = [
        'code',
        'name',
    ];
}
