<?php 

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysOtherFees extends Model 
{
    use HasFactory;
    
	protected $table = 'sys_otherfeetypes';

    protected $fillable = [
        'name',
    ];
}
