<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PseElemSubtestResult extends Model
{
    use HasFactory;

    protected $table = 'admission_pse_elem_subtest_result';

    protected $fillable = [
        'application_number_id',
        'name',
        'subtest_id',
        'ts',
        'rs',
        'percentage',
    ];

    public function pseAdmission()
    {
        return $this->belongsTo(PseAdmission::class, 'application_number_id');
    }
}
