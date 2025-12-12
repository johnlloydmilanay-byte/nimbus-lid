<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonRegulatedChemical extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This corresponds to the columns in our database table.
     */
    protected $fillable = [
        'reservation_id',
        'chemical_name',
        'is_solution',
        'has_concentration',
        'concentration_value',
        'volume_weight_needed',
        'working_instruction'
    ];

    /**
     * The attributes that should be cast.
     * This is crucial for converting database values to common PHP types.
     */
    protected $casts = [
        'is_solution' => 'boolean', // Converts 0/1 to false/true
        'has_concentration' => 'boolean',
    ];

    /**
     * Get the reservation that owns the chemical.
     * This defines the relationship: a chemical belongs to one reservation.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}