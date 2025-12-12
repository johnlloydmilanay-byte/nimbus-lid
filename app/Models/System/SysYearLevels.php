<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysYearLevels extends Model
{
    protected $table = 'sys_year_levels';

    protected $fillable = [
        'academic_group_id',
        'name',
        'is_active',
    ];

    public function details()
    {
        return $this->hasMany(SysYearLevelsDetails::class, 'year_level_id');
    }
}
