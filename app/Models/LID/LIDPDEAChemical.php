<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class LIDPDEAChemical extends Model
{
    protected $table = 'lid_pdea_chemicals';

    protected $fillable = [
        'name',
        'solution',
        'concentration',
        'concentration_value',
        'volume',
        'quantity',
    ];
}
