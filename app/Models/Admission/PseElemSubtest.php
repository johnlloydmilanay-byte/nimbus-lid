<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PseElemSubtest extends Model
{
    use HasFactory;

    protected $table = 'admission_pse_elem_subtest';

    protected $fillable = [
        'name',
        'maxscore',
    ];
}