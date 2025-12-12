<?php 

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrmFeesManagement extends Model 
{
    use HasFactory;
    
	protected $table = 'srm_fees_management';

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
    ];
    
    public function creator()
    {
        return $this->belongsTo(SrmUser::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(SrmUser::class, 'updated_by', 'user_id');
    }

    public function feeTypes()
    {
        return $this->hasMany(SysFeesType::class, 'fee_management_id', 'id');
    }
}
