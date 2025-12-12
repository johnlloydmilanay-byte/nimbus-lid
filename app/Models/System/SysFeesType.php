<?php 

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysFeesType extends Model 
{
    use HasFactory;
    
	protected $table = 'sys_fees_types';

    protected $fillable = [
        'name',
        'fee_management_id',
        'is_active',
    ];
}
