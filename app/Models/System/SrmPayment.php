<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmPayment extends Model
{
    use SoftDeletes;

    protected $table = 'srm_payments';

    protected $fillable = [
        'year',
        'term_id',
        'or_number',
        'application_number',
        'student_id',
        'payor_name',
        'payment_code_type_id',
        'payment_for_id',
        'amount_due',
        'amount_to_pay',
        'amount_tendered',
        'change',
        'payment_type_id',
        'remarks',
        'payment_date',
        'cashier_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount_due' => 'decimal:2',
        'amount_to_pay' => 'decimal:2',
        'amount_tendered' => 'decimal:2',
        'change' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function term()
    {
        return $this->belongsTo(SysTerms::class, 'term_id');
    }

    public function paymentCodeType()
    {
        return $this->belongsTo(SysPaymentCodeType::class, 'payment_code_type_id');
    }

    public function paymentFor()
    {
        return $this->belongsTo(SrmPaymentcodeManagement::class, 'payment_for_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(SysPaymentTypes::class, 'payment_type_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(SrmUsers::class, 'created_by', 'user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(SrmUsers::class, 'updated_by', 'user_id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(SrmUsers::class, 'deleted_by', 'user_id');
    }
}
