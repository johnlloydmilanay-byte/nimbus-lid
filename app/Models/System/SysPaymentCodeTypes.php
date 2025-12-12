<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SysPaymentCodeTypes extends Model
{
    protected $table = 'sys_paymentcode_types';

    protected $fillable = [
        'name',
    ];

    public function paymentcodes()
    {
        return $this->hasMany(SrmPaymentCodeManagement::class, 'paymentcode_id');
    }
}
