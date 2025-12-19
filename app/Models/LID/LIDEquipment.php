<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class LIDEquipment extends Model
{
    protected $table = 'lid_equipment';

    protected $fillable = [
        'name',
        'type', // e.g., equipment, consumable
        'description',
        'quantity',
        'unit',
        'available_quantity',
        'reserved_quantity',
    ];
}