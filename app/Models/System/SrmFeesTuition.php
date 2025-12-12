<?php

namespace App\Models\System;

use App\Models\System\SrmUser;
use App\Models\System\SrmProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmFeesTuition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_fees_tuition';

    protected $primaryKey = 'id';

    // Fields that can be mass-assigned
    protected $fillable = [
        'year',
        'year_level',
        'program_id',
        'rate_regular',
        'rate_major',
        'setup_type',
        'ar_account',
        'gl_account',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Casts
    protected $casts = [
        'rate_regular' => 'decimal:2',
        'rate_major' => 'decimal:2',
        'setup_type' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships

    public function program()
    {
        return $this->belongsTo(SrmProgram::class, 'program_id');
    }

    public function creator()
    {
        return $this->belongsTo(SrmUser::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(SrmUser::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(SrmUser::class, 'deleted_by');
    }

    public function account()
    {
        return $this->belongsTo(Chartmaster::class, 'account_id');
    }

    public function arAccount()
    {
        return $this->belongsTo(Chartmaster::class, 'ar_account', 'accountcode');
    }
    public function glAccount()
    {
        return $this->belongsTo(Chartmaster::class, 'gl_account', 'accountcode');
    }
}
