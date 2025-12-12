<?php 

namespace App\Models\System;

use App\Models\System\SrmClassSchedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrmClassSchedule extends Model 
{
    use HasFactory;
    
	protected $table = 'srm_class_schedules';

    protected $fillable = [
        'year',
        'term',
        'subject_id',
        'section',
    ];

    public function subject()
    {
        return $this->belongsTo(SrmSubject::class, 'subject_id');
    }

}
