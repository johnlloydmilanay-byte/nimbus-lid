<?php

namespace App\Models\Admission;

use App\Models\System\SrmPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PseAdmission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admission_pse';

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

        // remarks
        'interviewer_remarks', 'placement', 'remarks',

        // signatories
        'certifier_name', 'certifier_designation',
        'verifier_name', 'verifier_designation',

        'visibility', 'status', 'is_active',

        'total_rs', 'total_rating',

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
}
