<?php

namespace App\Models\LID;

use Illuminate\Database\Eloquent\Model;

class GlasswareRequest extends Model
{
    protected $table = 'glassware_requests';

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