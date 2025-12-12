<?php

namespace App\Models\Registrar;

use App\Models\System\SrmUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementsCollege extends Model
{
    use HasFactory;

    protected $table = 'registar_requirement_checklist_college';

    protected $fillable = [
        'application_number',

        'has_college_result',
        'college_result_signed_by',
        'college_result_signed_at',
        'college_result_updated_by',
        'college_result_updated_at',

        'has_college_report_card',
        'college_report_card_signed_by',
        'college_report_card_signed_at',
        'college_report_card_updated_by',
        'college_report_card_updated_at',

        'has_college_good_moral',
        'college_good_moral_signed_by',
        'college_good_moral_signed_at',
        'college_good_moral_updated_by',
        'college_good_moral_updated_at',

        'has_college_psa',
        'college_psa_signed_by',
        'college_psa_signed_at',
        'college_psa_updated_by',
        'college_psa_updated_at',

        'has_college_pic',
        'college_pic_signed_by',
        'college_pic_signed_at',
        'college_pic_updated_by',
        'college_pic_updated_at',

        'has_college_envelope',
        'college_envelope_signed_by',
        'college_envelope_signed_at',
        'college_envelope_updated_by',
        'college_envelope_updated_at',
    ];

    protected $casts = [
        'has_college_result' => 'boolean',
        'has_college_report_card' => 'boolean',
        'has_college_good_moral' => 'boolean',
        'has_college_psa' => 'boolean',
        'has_college_pic' => 'boolean',
        'has_college_envelope' => 'boolean',

        'college_result_signed_at' => 'datetime',
        'college_report_card_signed_at' => 'datetime',
        'college_good_moral_signed_at' => 'datetime',
        'college_psa_signed_at' => 'datetime',
        'college_pic_signed_at' => 'datetime',
        'college_envelope_signed_at' => 'datetime',

        'college_result_updated_at' => 'datetime',
        'college_report_card_updated_at' => 'datetime',
        'college_good_moral_updated_at' => 'datetime',
        'college_psa_updated_at' => 'datetime',
        'college_pic_updated_at' => 'datetime',
        'college_envelope_updated_at' => 'datetime',
    ];

    /**
     * Relations to the srm_users table (for signatories)
     */
    public function collegeResultSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_result_signed_by', 'user_id');
    }

    public function collegeReportCardSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_report_card_signed_by', 'user_id');
    }

    public function collegeGoodMoralSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_good_moral_signed_by', 'user_id');
    }

    public function collegePsaSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_psa_signed_by', 'user_id');
    }

    public function collegePicSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_pic_signed_by', 'user_id');
    }

    public function collegeEnvelopeSigner()
    {
        return $this->belongsTo(SrmUser::class, 'college_envelope_signed_by', 'user_id');
    }
}
