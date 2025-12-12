<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysPaymentTypes extends Model
{
    protected $table = 'sys_payment_types';

    protected $fillable = [
        'name',
    ];
}
