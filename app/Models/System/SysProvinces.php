<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysProvinces extends Model
{
    use HasFactory;

    protected $table = 'sys_address_provinces';

    protected $fillable = [
        'name',
    ];

    public function towns()
    {
        return $this->hasMany(SysTowns::class, 'province_id');
    }
}
