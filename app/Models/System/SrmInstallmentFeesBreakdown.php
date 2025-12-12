<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmInstallmentFeesBreakdown extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_installment_fees_breakdown';

    protected $fillable = [
        'installment_scheme_id',
        'payment_count',
        'fee_management_id',
        'fee_post_id',
        'rate',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function installmentScheme()
    {
        return $this->belongsTo(SrmInstallmentScheme::class, 'installment_scheme_id');
    }

    public function feeManagement()
    {
        return $this->belongsTo(SrmFeesManagement::class, 'fee_management_id');
    }

    public function feePost()
    {
        return $this->belongsTo(SrmFeesPost::class, 'fee_post_id');
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
