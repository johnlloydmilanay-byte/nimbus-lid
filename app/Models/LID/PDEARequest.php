<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class PDEARequest extends Model
{
    protected $table = 'pdea_requests';
    
    protected $fillable = [
        'reservation_id',
        'name',
        'is_solution',
        'has_concentration',
        'concentration_value',
        'concentration_unit',
        'volume',
        'volume_unit',
        'quantity_per_group',
        'total_quantity',
        'unit',
        'instruction'
    ];
    
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}