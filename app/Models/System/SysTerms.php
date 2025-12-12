<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysTerms extends Model
{
    use HasFactory;

    protected $table = 'sys_terms';

    protected $fillable = [
        'name',
    ];
}
