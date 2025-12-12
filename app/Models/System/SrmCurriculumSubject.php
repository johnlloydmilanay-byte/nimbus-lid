<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmCurriculumSubject extends Model
{
    use SoftDeletes;

    protected $table = 'srm_curriculum_subject';

    protected $fillable = [
        'curriculum_year_id',
        'subject_id',
        'term_id',
        'year_level_details_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function curriculumYear()
    {
        return $this->belongsTo(SrmCurriculumYear::class, 'curriculum_year_id');
    }

    public function subject()
    {
        return $this->belongsTo(SrmSubject::class, 'subject_id');
    }

    public function prerequisites()
    {
        return $this->hasMany(SrmCurriculumPrerequisite::class, 'curriculum_subject_id')
                    ->with('prereqSubject'); 
    }

    public function term()
    {
        return $this->belongsTo(SysTerms::class, 'term_id');
    }

    public function yearLevelDetail()
    {
        return $this->belongsTo(SysYearLevelsDetails::class, 'year_level_details_id');
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
