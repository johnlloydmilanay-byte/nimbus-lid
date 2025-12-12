<?php
namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'reference_number',   // <-- added
        'borrower_name', 
        'borrower_type', 
        'purpose', 
        'room_no',
        'date_requested', 
        'term', 
        'start_date', 
        'end_date',
        'number_of_groups', 
        'time',
        'program', 
        'year_section', 
        'subject_code',
        'subject_description', 
        'activity_title', 
        'activity_no',
    ];

    public function chemicalRequests()
    {
        return $this->hasMany(ChemicalRequest::class);
    }

    public function pdeaRequests()
    {
        return $this->hasMany(PDEARequest::class);
    }
}