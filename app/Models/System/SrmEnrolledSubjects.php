<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SrmEnrolledSubjects extends Model
{
    use SoftDeletes;

    protected $table = 'srm_enrollment_details';

    protected $fillable = [
        'year',
        'term_id',
        'application_number',
        'student_number',
        'subject_id',
        'is_changeofreg',
        'is_dropped',
        'registrar_drop',
        'registrar_warning',
        'is_active',
        'prelim',
        'midterm',
        'prefinal',
        'final',
        'dean_by',
        'prelimdean_at',
        'midtermdean_at',
        'prefinaldean_at',
        'finaldean_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function subject()
    {
        return $this->belongsTo(SrmSubject::class, 'subject_id');
    }

}
