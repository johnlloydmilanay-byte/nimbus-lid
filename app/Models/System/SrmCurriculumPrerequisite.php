<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmCurriculumPrerequisite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'srm_curriculum_prerequisite';

    protected $fillable = [
        'curriculum_subject_id',
        'prereq_subject_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function curriculumSubject()
    {
        return $this->belongsTo(SrmCurriculumSubject::class, 'curriculum_subject_id');
    }

    public function prereqSubject()
    {
        return $this->belongsTo(SrmSubject::class, 'prereq_subject_id');
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
