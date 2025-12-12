<?php

namespace App\Models\Admission;

use App\Models\System\SrmPayment;
use App\Models\System\SysDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeAdmission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admission_college';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'strand_id', 'school_name', 'school_address', 'school_zip',

        // program preference
        'choice_first', 'choice_second', 'choice_third',

        // applicant details
        'or_number', 'year_level', 'applicant_status',
        'exam_schedule_date', 'exam_schedule_time',

        'exam_taken',

        // signatories
        'certifier_name', 'certifier_designation',
        'verifier_name', 'verifier_designation',

        'visibility', 'status', 'is_active',

        'total_rs', 'total_ave_api', 'remarks',

        'created_by', 'updated_by', 'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'exam_taken' => 'boolean',
        'is_active' => 'boolean',
        'visibility' => 'boolean',
        'dob' => 'date',
        'exam_schedule_date' => 'date',
        'exam_schedule_time' => 'datetime:H:i',
    ];

    /**
     * Relationships with User model.
     */
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

    public function strand()
    {
        return $this->belongsTo(CollegeAdmissionShsPrograms::class, 'strand_id');
    }

    public function subtests()
    {
        return $this->hasMany(CollegeSubtestResult::class, 'application_number_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(SysDepartment::class, 'choice_first', 'code');
    }
}
