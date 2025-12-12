<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class LIDChemical extends Model
{
    protected $table = 'lid_chemicals';

    protected $fillable = [
        'name',
        'solution',
        'concentration',
        'concentration_value',
        'volume',
        'quantity',
    ];
}
