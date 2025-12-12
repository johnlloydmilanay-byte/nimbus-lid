<?php

namespace App\Models\Registrar;

use App\Models\System\SrmUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementsShs extends Model
{
    use HasFactory;

    protected $table = 'registar_requirement_checklist_shs';

    protected $fillable = [
        'application_number',

        'has_shs_result',
        'shs_result_signed_by',
        'shs_result_signed_at',
        'shs_result_updated_by',
        'shs_result_updated_at',

        'has_shs_report_card',
        'shs_report_card_signed_by',
        'shs_report_card_signed_at',
        'shs_report_card_updated_by',
        'shs_report_card_updated_at',

        'has_shs_good_moral',
        'shs_good_moral_signed_by',
        'shs_good_moral_signed_at',
        'shs_good_moral_updated_by',
        'shs_good_moral_updated_at',

        'has_shs_psa',
        'shs_psa_signed_by',
        'shs_psa_signed_at',
        'shs_psa_updated_by',
        'shs_psa_updated_at',

        'has_shs_completion_cert',
        'shs_completion_cert_signed_by',
        'shs_completion_cert_signed_at',
        'shs_completion_cert_updated_by',
        'shs_completion_cert_updated_at',

        'has_shs_pic',
        'shs_pic_signed_by',
        'shs_pic_signed_at',
        'shs_pic_updated_by',
        'shs_pic_updated_at',

        'has_shs_esc',
        'shs_esc_signed_by',
        'shs_esc_signed_at',
        'shs_esc_updated_by',
        'shs_esc_updated_at',

        'has_shs_envelope',
        'shs_envelope_signed_by',
        'shs_envelope_signed_at',
        'shs_envelope_updated_by',
        'shs_envelope_updated_at',

        'has_shs_folder',
        'shs_folder_signed_by',
        'shs_folder_signed_at',
        'shs_folder_updated_by',
        'shs_folder_updated_at',
    ];

    protected $casts = [
        'has_shs_result' => 'boolean',
        'has_shs_report_card' => 'boolean',
        'has_shs_good_moral' => 'boolean',
        'has_shs_psa' => 'boolean',
        'has_shs_completion_cert' => 'boolean',
        'has_shs_pic' => 'boolean',
        'has_shs_esc' => 'boolean',
        'has_shs_envelope' => 'boolean',
        'has_shs_folder' => 'boolean',

        'shs_result_signed_at' => 'datetime',
        'shs_report_card_signed_at' => 'datetime',
        'shs_good_moral_signed_at' => 'datetime',
        'shs_psa_signed_at' => 'datetime',
        'shs_completion_cert_signed_at' => 'datetime',
        'shs_pic_signed_at' => 'datetime',
        'shs_esc_signed_at' => 'datetime',
        'shs_envelope_signed_at' => 'datetime',
        'shs_folder_signed_at' => 'datetime',

        'shs_result_updated_at' => 'datetime',
        'shs_report_card_updated_at' => 'datetime',
        'shs_good_moral_updated_at' => 'datetime',
        'shs_psa_updated_at' => 'datetime',
        'shs_completion_cert_updated_at' => 'datetime',
        'shs_pic_updated_at' => 'datetime',
        'shs_esc_updated_at' => 'datetime',
        'shs_envelope_updated_at' => 'datetime',
        'shs_folder_updated_at' => 'datetime',
    ];

    /**
     * Relations to the srm_users table (for signatories)
     */
    public function shsResultSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_result_signed_by', 'user_id');
    }

    public function shsReportCardSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_report_card_signed_by', 'user_id');
    }

    public function shsGoodMoralSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_good_moral_signed_by', 'user_id');
    }

    public function shsPsaSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_psa_signed_by', 'user_id');
    }

    public function shsCompletionSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_completion_cert_signed_by', 'user_id');
    }

    public function shsPicSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_pic_signed_by', 'user_id');
    }

    public function shsEscSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_esc_signed_by', 'user_id');
    }

    public function shsEnvelopeSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_envelope_signed_by', 'user_id');
    }

    public function shsFolderSigner()
    {
        return $this->belongsTo(SrmUser::class, 'shs_folder_signed_by', 'user_id');
    }
}
