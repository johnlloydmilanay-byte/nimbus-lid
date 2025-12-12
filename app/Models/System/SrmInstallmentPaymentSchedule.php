<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmInstallmentPaymentSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_installment_payment_schedule';

    protected $fillable = [
        'order',
        'installment_scheme_id',
        'description',
        'date_from',
        'date_to',
        'period_start',
        'period_end',
        'exam',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    public function installmentScheme()
    {
        return $this->belongsTo(SrmInstallmentScheme::class, 'installment_scheme_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(SrmUsers::class, 'created_by', 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(SrmUsers::class, 'updated_by', 'user_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(SrmUsers::class, 'deleted_by', 'user_id');
    }
}
