<?php

namespace App\Models;

use App\Models\System\SysDepartment;
use Illuminate\Database\Eloquent\Model;

class Elogs extends Model
{
    protected $table = 'elogs';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'department_id',
        'purpose',
        'is_used',
    ];

    public function department()
    {
        return $this->belongsTo(SysDepartment::class);
    }

    protected $casts = [
        'is_used' => 'boolean',
    ];
}
