<?php
namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class ChemicalRequest extends Model
{
    protected $fillable = [
        'reservation_id',
        'name',
        'solution',
        'concentration',
        'concentration_value',
        'volume',
        'instruction',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}