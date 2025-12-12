<?php 

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrmSubject extends Model 
{
    use HasFactory;
    
	protected $table = 'srm_subjects';

    protected $fillable = [
        'code',
        'name',
        'units',
        'clock_hours',
        'is_lab',
        'lab_type',
        'is_seminar',
        'has_conflicts',
        'has_energy',
        'is_evaluated',
        'is_graded',
        'is_major',
        'program_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function program()
    {
        return $this->belongsTo(SrmProgram::class, 'program_id');
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
