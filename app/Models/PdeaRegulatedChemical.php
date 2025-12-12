<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdeaRegulatedChemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'chemical_name',
        'is_solution',
        'has_concentration',
        'concentration_value',
        'volume_weight_needed',
        'working_instruction'
    ];

    protected $casts = [
        'is_solution' => 'boolean',
        'has_concentration' => 'boolean',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}