<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class EquipmentRequest extends Model
{
    protected $table = 'equipment_requests';

    protected $fillable = [
        'reservation_id',
        'name',
        'type',
        'quantity_per_group',
        'total_quantity',
        'unit',
        'instruction',
    ];
}