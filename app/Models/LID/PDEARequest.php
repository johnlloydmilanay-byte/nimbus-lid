<?php
namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class PDEARequest extends Model
{
    protected $table = 'pdea_requests';
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