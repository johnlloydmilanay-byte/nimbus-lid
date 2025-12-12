<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\SrmFeesTuition;

class Chartmaster extends Model
{
    protected $table = 'chartmaster';

    public $timestamps = false; 

    protected $primaryKey = 'accountcode'; 
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'accountcode',
        'accountname',
        'group',
        'contra'
    ];

    public function tuitionFees()
    {
        return $this->hasMany(SrmFeesTuition::class, 'account_id', 'accountcode');
    }
}
