<?php

namespace App\Models\Admission;

use App\Models\System\SrmPayment;
use App\Models\System\SysDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JhsAdmission extends Model
{
    use HasFactory;

    protected $table = 'admission_jhs';

    protected $fillable = [
        'application_number',
        'year',
        'term',

        // student information
        'lastname', 'firstname', 'middlename', 'suffix',
        'gender', 'mobile_no', 'email',
        'dob', 'age', 'nationality', 'religion',
        'zip_code', 'address',
        'contact_person', 'contact_number',

        // last school attended
        'school_name', 'lrn', 'school_address', 'school_zip',

        // program preference
        'program',

        // applicant details
        'or_number', 'applicant_status',
        'exam_schedule_date', 'exam_schedule_time',

        'exam_taken',

        // signatories
        'certifier_name', 'certifier_designation',
        'verifier_name', 'verifier_designation',

        'visibility', 'status', 'is_active',

        'total_rating', 'remarks',

        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'exam_taken' => 'boolean',
        'is_active' => 'boolean',
        'visibility' => 'boolean',
        'dob' => 'date',
        'exam_schedule_date' => 'date',
        'exam_schedule_time' => 'datetime:H:i',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function collection()
    {
        return $this->hasOne(SrmPayment::class, 'application_number', 'application_number');
    }

    public function department()
    {
        return $this->belongsTo(SysDepartment::class, 'program', 'code');
    }
}
