<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class LIDGlassware extends Model
{
    protected $table = 'lid_glassware';

    protected $fillable = [
        'name',
        'type', // e.g., glassware, apparatus, material
        'description',
        'quantity',
        'unit',
        'available_quantity',
        'reserved_quantity',
    ];
}