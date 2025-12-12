<?php

namespace App\Models\Registrar;

use App\Models\System\SrmUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementsPse extends Model
{
    use HasFactory;

    protected $table = 'registar_requirement_checklist_pse';

    protected $fillable = [
        'application_number',

        'has_pse_result',
        'pse_result_signed_by',
        'pse_result_signed_at',
        'pse_result_updated_by',
        'pse_result_updated_at',

        'has_pse_report_card',
        'pse_report_card_signed_by',
        'pse_report_card_signed_at',
        'pse_report_card_updated_by',
        'pse_report_card_updated_at',

        'has_pse_good_moral',
        'pse_good_moral_signed_by',
        'pse_good_moral_signed_at',
        'pse_good_moral_updated_by',
        'pse_good_moral_updated_at',

        'has_pse_psa',
        'pse_psa_signed_by',
        'pse_psa_signed_at',
        'pse_psa_updated_by',
        'pse_psa_updated_at',

        'has_pse_pic',
        'pse_pic_signed_by',
        'pse_pic_signed_at',
        'pse_pic_updated_by',
        'pse_pic_updated_at',

        'has_pse_pic1',
        'pse_pic1_signed_by',
        'pse_pic1_signed_at',
        'pse_pic1_updated_by',
        'pse_pic1_updated_at',

        'has_pse_medcert',
        'pse_medcert_signed_by',
        'pse_medcert_signed_at',
        'pse_medcert_updated_by',
        'pse_medcert_updated_at',

        'has_pse_envelope',
        'pse_envelope_signed_by',
        'pse_envelope_signed_at',
        'pse_envelope_updated_by',
        'pse_envelope_updated_at',
    ];

    protected $casts = [
        'has_pse_result' => 'boolean',
        'has_pse_report_card' => 'boolean',
        'has_pse_good_moral' => 'boolean',
        'has_pse_psa' => 'boolean',
        'has_pse_pic' => 'boolean',
        'has_pse_pic1' => 'boolean',
        'has_pse_medcert' => 'boolean',
        'has_pse_envelope' => 'boolean',

        'pse_result_signed_at' => 'datetime',
        'pse_report_card_signed_at' => 'datetime',
        'pse_good_moral_signed_at' => 'datetime',
        'pse_psa_signed_at' => 'datetime',
        'pse_pic_signed_at' => 'datetime',
        'pse_pic1_signed_at' => 'datetime',
        'pse_medcert_signed_at' => 'datetime',
        'pse_envelope_signed_at' => 'datetime',

        'pse_result_updated_at' => 'datetime',
        'pse_report_card_updated_at' => 'datetime',
        'pse_good_moral_updated_at' => 'datetime',
        'pse_psa_updated_at' => 'datetime',
        'pse_pic_updated_at' => 'datetime',
        'pse_pic1_updated_at' => 'datetime',
        'pse_medcert_updated_at' => 'datetime',
        'pse_envelope_updated_at' => 'datetime',
    ];

    /**
     * Relations to the srm_users table (for signatories)
     */
    public function pseResultSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_result_signed_by', 'user_id');
    }

    public function pseReportCardSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_report_card_signed_by', 'user_id');
    }

    public function pseGoodMoralSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_good_moral_signed_by', 'user_id');
    }

    public function psePsaSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_psa_signed_by', 'user_id');
    }

    public function psePicSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_pic_signed_by', 'user_id');
    }

    public function psePic1Signer()
    {
        return $this->belongsTo(SrmUser::class, 'pse_pic1_signed_by', 'user_id');
    }

    public function pseMedCertSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_medcert_signed_by', 'user_id');
    }

    public function pseEnvelopeSigner()
    {
        return $this->belongsTo(SrmUser::class, 'pse_envelope_signed_by', 'user_id');
    }
}
