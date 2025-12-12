<?php

namespace App\Models\Registrar;

use App\Models\System\SrmUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementsJhs extends Model
{
    use HasFactory;

    protected $table = 'registar_requirement_checklist_jhs';

    protected $fillable = [
        'application_number',

        'has_jhs_result',
        'jhs_result_signed_by',
        'jhs_result_signed_at',
        'jhs_result_updated_by',
        'jhs_result_updated_at',

        'has_jhs_report_card',
        'jhs_report_card_signed_by',
        'jhs_report_card_signed_at',
        'jhs_report_card_updated_by',
        'jhs_report_card_updated_at',

        'has_jhs_good_moral',
        'jhs_good_moral_signed_by',
        'jhs_good_moral_signed_at',
        'jhs_good_moral_updated_by',
        'jhs_good_moral_updated_at',

        'has_jhs_psa',
        'jhs_psa_signed_by',
        'jhs_psa_signed_at',
        'jhs_psa_updated_by',
        'jhs_psa_updated_at',

        'has_jhs_pic',
        'jhs_pic_signed_by',
        'jhs_pic_signed_at',
        'jhs_pic_updated_by',
        'jhs_pic_updated_at',

        'has_jhs_income',
        'jhs_income_signed_by',
        'jhs_income_signed_at',
        'jhs_income_updated_by',
        'jhs_income_updated_at',

        'has_jhs_envelope',
        'jhs_envelope_signed_by',
        'jhs_envelope_signed_at',
        'jhs_envelope_updated_by',
        'jhs_envelope_updated_at',

        'has_jhs_folder',
        'jhs_folder_signed_by',
        'jhs_folder_signed_at',
        'jhs_folder_updated_by',
        'jhs_folder_updated_at',
    ];

    protected $casts = [
        'has_jhs_result' => 'boolean',
        'has_jhs_report_card' => 'boolean',
        'has_jhs_good_moral' => 'boolean',
        'has_jhs_psa' => 'boolean',
        'has_jhs_pic' => 'boolean',
        'has_jhs_income' => 'boolean',
        'has_jhs_envelope' => 'boolean',
        'has_jhs_folder' => 'boolean',

        'jhs_result_signed_at' => 'datetime',
        'jhs_report_card_signed_at' => 'datetime',
        'jhs_good_moral_signed_at' => 'datetime',
        'jhs_psa_signed_at' => 'datetime',
        'jhs_pic_signed_at' => 'datetime',
        'jhs_income_signed_at' => 'datetime',
        'jhs_envelope_signed_at' => 'datetime',
        'jhs_folder_signed_at' => 'datetime',

        'jhs_result_updated_at' => 'datetime',
        'jhs_report_card_updated_at' => 'datetime',
        'jhs_good_moral_updated_at' => 'datetime',
        'jhs_psa_updated_at' => 'datetime',
        'jhs_pic_updated_at' => 'datetime',
        'jhs_income_updated_at' => 'datetime',
        'jhs_envelope_updated_at' => 'datetime',
        'jhs_folder_updated_at' => 'datetime',
    ];

    /**
     * Relations to the srm_users table (for signatories)
     */
    public function jhsResultSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_result_signed_by', 'user_id');
    }

    public function jhsReportCardSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_report_card_signed_by', 'user_id');
    }

    public function jhsGoodMoralSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_good_moral_signed_by', 'user_id');
    }

    public function jhsPsaSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_psa_signed_by', 'user_id');
    }

    public function jhsPicSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_pic_signed_by', 'user_id');
    }

    public function jhsIncomeSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_income_signed_by', 'user_id');
    }

    public function jhsEnvelopeSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_envelope_signed_by', 'user_id');
    }

    public function jhsFolderSigner()
    {
        return $this->belongsTo(SrmUser::class, 'jhs_folder_signed_by', 'user_id');
    }
}
