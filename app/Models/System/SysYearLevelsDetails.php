<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysYearLevelsDetails extends Model
{
    protected $table = 'sys_year_levels_details';

    protected $fillable = [
        'year_level_id',
        'code',
        'name',
        'order',
    ];

    public function yearLevel()
    {
        return $this->belongsTo(SysYearLevels::class, 'year_level_id');
    }

    public function curricula()
    {
        return $this->hasMany(SrmCurriculum::class, 'year_level_details_id');
    }
}
