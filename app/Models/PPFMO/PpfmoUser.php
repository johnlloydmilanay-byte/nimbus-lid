<?php

namespace App\Models\PPFMO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpfmoUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppfmo_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'department_id',
        'designation_id',
        'is_active',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Designation Constants
    const DESIGNATION_HEAD = 1;
    const DESIGNATION_MGMT_STAFF = 2;
    const DESIGNATION_MAINTENANCE = 3;

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the human-readable designation name.
     */
    public function getDesignationNameAttribute()
    {
        switch ($this->designation_id) {
            case self::DESIGNATION_HEAD:
                return 'Department Head';
            case self::DESIGNATION_MGMT_STAFF:
                return 'Management Staff';
            case self::DESIGNATION_MAINTENANCE:
                return 'Maintenance Personnel';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get full display name with designation.
     */
    public function getFullNameWithDesignationAttribute()
    {
        return "{$this->name} ({$this->designation_name})";
    }
}