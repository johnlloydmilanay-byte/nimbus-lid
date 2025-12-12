<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysTowns extends Model
{
    use HasFactory;

    protected $table = 'sys_address_towns';

    protected $fillable = [
        'name',
        'province_id',
    ];

    public function province()
    {
        return $this->belongsTo(SysProvinces::class, 'province_id');
    }
}
