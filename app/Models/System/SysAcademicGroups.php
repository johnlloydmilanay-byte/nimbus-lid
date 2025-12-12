<?php 

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysAcademicGroups extends Model 
{
    use HasFactory;
    
	protected $table = 'sys_academicgroups';

    protected $fillable = [
        'code',
        'name',
    ];
}
