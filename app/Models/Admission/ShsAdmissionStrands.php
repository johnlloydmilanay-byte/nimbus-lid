<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShsAdmissionStrands extends Model
{
    use HasFactory;

    protected $table = 'admission_shs_strands';

    protected $fillable = [
        'track_id',
        'code',
        'name',
        'curriculum',
    ];
}
