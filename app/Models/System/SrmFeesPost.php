<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\System\Chartmaster;
use App\Models\System\SysOtherFees;
use App\Models\System\SysAcademicGroups;
use App\Models\System\SysStudentStatus;
use App\Models\System\SysDepartment;
use App\Models\System\SrmClassSched;
use App\Models\System\SrmProgram;
use App\Models\System\SrmUser;

class SrmFeesPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_fees_post';

    protected $primaryKey = 'id';

    protected $fillable = [
        'year',
        'term',
        'fee_name',
        'fee_types_id',
        'rate',
        'deposit',
        'ar_account',
        'gl_account',
        'academicgroup_id',
        'department_id',
        'class_schedule_id',
        'program_id',
        'studentstatus_id',
        'year_level',
        'year_entry',
        
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'deposit' => 'decimal:2',
        'year' => 'integer',
        'term' => 'integer',
        'year_level' => 'integer',
        'year_entry' => 'integer',
    ];

    public function feeType()
    {
        return $this->belongsTo(SysFeesType::class, 'fee_types_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(Chartmaster::class, 'gl_account', 'accountcode');
    }

    public function arAccount()
    {
        return $this->belongsTo(Chartmaster::class, 'ar_account', 'accountcode');
    }

    public function academicGroup()
    {
        return $this->belongsTo(SysAcademicGroups::class, 'academicgroup_id');
    }

    public function department()
    {
        return $this->belongsTo(SysDepartment::class, 'department_id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(SrmClassSchedule::class, 'class_schedule_id');
    }

    public function program()
    {
        return $this->belongsTo(SrmProgram::class, 'program_id');
    }

    public function studentStatus()
    {
        return $this->belongsTo(SysStudentStatus::class, 'studentstatus_id');
    }

    public function creator()
    {
        return $this->belongsTo(SrmUser::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(SrmUser::class, 'updated_by', 'user_id');
    }

    public function deleter()
    {
        return $this->belongsTo(SrmUser::class, 'deleted_by', 'user_id');
    }
}
