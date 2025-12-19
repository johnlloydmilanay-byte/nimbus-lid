<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'reference_number',
        'faculty_reference_id',
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
        'status',
        'items_released',
        'items_released_at',
        'items_returned',
        'items_returned_at',
        'student_id',
        'email',
        'contact_number',
        'group_number',
    ];

    protected $casts = [
        'items_released' => 'boolean',
        'items_returned' => 'boolean',
        'items_released_at' => 'datetime',
        'items_returned_at' => 'datetime',
        'date_requested' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function chemicalRequests(): HasMany
    {
        return $this->hasMany(ChemicalRequest::class);
    }

    public function pdeaRequests(): HasMany
    {
        return $this->hasMany(PDEARequest::class);
    }

    public function facultyReservation()
    {
        return $this->belongsTo(Reservation::class, 'faculty_reference_id');
    }

    public function studentReservations()
    {
        return $this->hasMany(Reservation::class, 'faculty_reference_id');
    }

    public function glasswareRequests(): HasMany
    {
        return $this->hasMany(GlasswareRequest::class);
    }

    public function equipmentRequests(): HasMany
    {
        return $this->hasMany(EquipmentRequest::class);
    }
}