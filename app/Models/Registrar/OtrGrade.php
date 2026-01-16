<?php

namespace App\Models\Registrar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtrGrade extends Model
{
    use HasFactory;

    protected $table = 'otr_grades';
    
    protected $fillable = [
        'otr_id',
        'school_year',
        'semester',
        'subject_code',
        'subject_title',
        'type',
        'final_rating',
        'units_earned',
    ];

    protected $casts = [
        'final_rating' => 'decimal:2',
        'units_earned' => 'decimal:2',
    ];

    public function otr()
    {
        return $this->belongsTo(Otr::class);
    }
}