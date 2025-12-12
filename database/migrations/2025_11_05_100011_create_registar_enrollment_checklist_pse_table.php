<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registar_requirement_checklist_pse', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable()->index();

            // PSE Test Result
            $table->boolean('has_pse_result')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_result_signed_by')->nullable();
            $table->timestamp('pse_result_signed_at')->nullable();
            $table->string('pse_result_updated_by')->nullable();
            $table->timestamp('pse_result_updated_at')->nullable();

            // PSE Report Card
            $table->boolean('has_pse_report_card')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_report_card_signed_by')->nullable();
            $table->timestamp('pse_report_card_signed_at')->nullable();
            $table->string('pse_report_card_updated_by')->nullable();
            $table->timestamp('pse_report_card_updated_at')->nullable();

            // Certificate of Good Moral
            $table->boolean('has_pse_good_moral')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_good_moral_signed_by')->nullable();
            $table->timestamp('pse_good_moral_signed_at')->nullable();
            $table->string('pse_good_moral_updated_by')->nullable();
            $table->timestamp('pse_good_moral_updated_at')->nullable();

            // PSA
            $table->boolean('has_pse_psa')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_psa_signed_by')->nullable();
            $table->timestamp('pse_psa_signed_at')->nullable();
            $table->string('pse_psa_updated_by')->nullable();
            $table->timestamp('pse_psa_updated_at')->nullable();

            // 2x2
            $table->boolean('has_pse_pic')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_pic_signed_by')->nullable();
            $table->timestamp('pse_pic_signed_at')->nullable();
            $table->string('pse_pic_updated_by')->nullable();
            $table->timestamp('pse_pic_updated_at')->nullable();

            // 1x1
            $table->boolean('has_pse_pic1')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_pic1_signed_by')->nullable();
            $table->timestamp('pse_pic1_signed_at')->nullable();
            $table->string('pse_pic1_updated_by')->nullable();
            $table->timestamp('pse_pic1_updated_at')->nullable();

            // Medical Cert
            $table->boolean('has_pse_medcert')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_medcert_signed_by')->nullable();
            $table->timestamp('pse_medcert_signed_at')->nullable();
            $table->string('pse_medcert_updated_by')->nullable();
            $table->timestamp('pse_medcert_updated_at')->nullable();

            // Brown Envelope
            $table->boolean('has_pse_envelope')->default(0)->comment('1 = true, 0 = false');
            $table->string('pse_envelope_signed_by')->nullable();
            $table->timestamp('pse_envelope_signed_at')->nullable();
            $table->string('pse_envelope_updated_by')->nullable();
            $table->timestamp('pse_envelope_updated_at')->nullable();

            // timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pse_result_signed_by', 'fk_pse_test_result_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_report_card_signed_by', 'fk_pse_report_card_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_good_moral_signed_by', 'fk_pse_goodmoral_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_psa_signed_by', 'fk_pse_psa_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_pic_signed_by', 'fk_pse_2x2_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_pic1_signed_by', 'fk_pse_2x2_user1')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_medcert_signed_by', 'fk_pse_medcert_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_envelope_signed_by', 'fk_pse_envelope_user')->references('user_id')->on('srm_users')->onDelete('cascade');

            $table->foreign('pse_result_updated_by', 'fk_pse_test_result_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_report_card_updated_by', 'fk_pse_report_card_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_good_moral_updated_by', 'fk_pse_goodmoral_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_psa_updated_by', 'fk_pse_psa_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_pic_updated_by', 'fk_pse_2x2_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_pic1_updated_by', 'fk_pse_2x2_user1_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_medcert_updated_by', 'fk_pse_income_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('pse_envelope_updated_by', 'fk_pse_envelope_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registar_requirement_checklist_pse');
    }
};
