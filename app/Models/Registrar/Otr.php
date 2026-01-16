<?php

namespace App\Models\Registrar;

use App\Models\System\SrmProgram;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otr extends Model
{
    use HasFactory;

    protected $table = 'otrs';

    protected $fillable = [
        'Last_Name',
        'First_Name',
        'Middle_Name',
        'Student_ID',
        'Degree_Course',
        'Exemption_Note',
        'Accreditation_Level',
        'CHED_Memo_Order',
        'Date_of_Graduation',
        'NSTP_Serial_Number',
        'Admission_Credentials',
        'Category',
        'School_Last_Attended',
        'School_Year_Last_Attended',
        'School_Address',
        'Semester_Year_Admitted',
        'College',
        'Address',
        'Birth_Date',
        'Birth_Place',
        'Citizenship',
        'Religion',
        'Gender',
        'Prepared_By',
        'Checked_By',
        'Dean_Name',
        'Registrar_Name',
        'Date_Prepared',
        'Photo_Path',
    ];

    protected $casts = [
        'Date_of_Graduation' => 'date',
        'Birth_Date' => 'date',
        'Date_Prepared' => 'date',
    ];

    // Relationship to SrmProgram
    public function program()
    {
        return $this->belongsTo(SrmProgram::class, 'Degree_Course', 'id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->First_Name.' '.$this->Last_Name;
    }

    // Check if record is graduated
    public function getIsGraduatedAttribute()
    {
        return ! is_null($this->Date_of_Graduation);
    }

    public function grades()
    {
        return $this->hasMany(OtrGrade::class);
    }
}
