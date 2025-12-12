<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmInstallmentScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_installment_scheme';

    protected $fillable = [
        'year',
        'term_id',
        'academicgroup_id',
        'scheme_name',
        'payment_count',
        'installment_fee',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    public function term()
    {
        return $this->belongsTo(SysTerms::class, 'term_id');
    }

    public function academicGroup()
    {
        return $this->belongsTo(SysAcademicGroups::class, 'academicgroup_id');
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
