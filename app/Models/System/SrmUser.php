<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SrmUser extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'usertype',
        'department_id',
        'is_active',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
    ];

    public function creator()
    {
        return $this->belongsTo(SrmUser::class, 'created_by', 'user_id');
    }

    public function deleter()
    {
        return $this->belongsTo(SrmUser::class, 'deleted_by', 'user_id');
    }

    public function createdUsers()
    {
        return $this->hasMany(SrmUser::class, 'created_by', 'user_id');
    }

    public function deletedUsers()
    {
        return $this->hasMany(SrmUser::class, 'deleted_by', 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(SrmEmployee::class, 'employee_id', 'user_id');
    }
}
