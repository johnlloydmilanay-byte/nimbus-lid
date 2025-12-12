<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SrmPaymentCodeManagement extends Model
{
    protected $table = 'srm_paymentcode_management';

    protected $fillable = [
        'name',
        'paymentcode_id',
    ];

    public function paymentcodeType()
    {
        return $this->belongsTo(SysPaymentCodeTypes::class, 'paymentcode_id');
    }
}
