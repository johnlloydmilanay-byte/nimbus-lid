<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SrmProgram extends Model
{
    protected $table = 'srm_programs';

    protected $fillable = [
        'code',
        'name',
        'department_id',
        'is_active',
    ];

    public function department()
    {
        return $this->belongsTo(SysDepartment::class, 'department_id');
    }

    protected $appends = ['dcode'];

    public function getDcodeAttribute()
    {
        return $this->department?->code ?? '';
    }
}
