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
        Schema::create('registar_requirement_checklist_shs', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable()->index();

            // CAT
            $table->boolean('has_shs_result')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_result_signed_by')->nullable();
            $table->timestamp('shs_result_signed_at')->nullable();
            $table->string('shs_result_updated_by')->nullable();
            $table->timestamp('shs_result_updated_at')->nullable();

            // SHS Report Card
            $table->boolean('has_shs_report_card')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_report_card_signed_by')->nullable();
            $table->timestamp('shs_report_card_signed_at')->nullable();
            $table->string('shs_report_card_updated_by')->nullable();
            $table->timestamp('shs_report_card_updated_at')->nullable();

            // Certificate of Good Moral
            $table->boolean('has_shs_good_moral')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_good_moral_signed_by')->nullable();
            $table->timestamp('shs_good_moral_signed_at')->nullable();
            $table->string('shs_good_moral_updated_by')->nullable();
            $table->timestamp('shs_good_moral_updated_at')->nullable();

            // PSA
            $table->boolean('has_shs_psa')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_psa_signed_by')->nullable();
            $table->timestamp('shs_psa_signed_at')->nullable();
            $table->string('shs_psa_updated_by')->nullable();
            $table->timestamp('shs_psa_updated_at')->nullable();

            // Completion Cert
            $table->boolean('has_shs_completion_cert')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_completion_cert_signed_by')->nullable();
            $table->timestamp('shs_completion_cert_signed_at')->nullable();
            $table->string('shs_completion_cert_updated_by')->nullable();
            $table->timestamp('shs_completion_cert_updated_at')->nullable();

            // 2x2
            $table->boolean('has_shs_pic')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_pic_signed_by')->nullable();
            $table->timestamp('shs_pic_signed_at')->nullable();
            $table->string('shs_pic_updated_by')->nullable();
            $table->timestamp('shs_pic_updated_at')->nullable();

            // ESC Certificate
            $table->boolean('has_shs_esc')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_esc_signed_by')->nullable();
            $table->timestamp('shs_esc_signed_at')->nullable();
            $table->string('shs_esc_updated_by')->nullable();
            $table->timestamp('shs_esc_updated_at')->nullable();

            // Brown Envelope
            $table->boolean('has_shs_envelope')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_envelope_signed_by')->nullable();
            $table->timestamp('shs_envelope_signed_at')->nullable();
            $table->string('shs_envelope_updated_by')->nullable();
            $table->timestamp('shs_envelope_updated_at')->nullable();

            // White Folder
            $table->boolean('has_shs_folder')->default(0)->comment('1 = true, 0 = false');
            $table->string('shs_folder_signed_by')->nullable();
            $table->timestamp('shs_folder_signed_at')->nullable();
            $table->string('shs_folder_updated_by')->nullable();
            $table->timestamp('shs_folder_updated_at')->nullable();

            // timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('shs_result_signed_by', 'fk_shs_test_result_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_report_card_signed_by', 'fk_shs_report_card_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_good_moral_signed_by', 'fk_shs_goodmoral_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_psa_signed_by', 'fk_shs_psa_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_completion_cert_signed_by', 'fk_shs_completion_cert_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_pic_signed_by', 'fk_shs_2x2_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_esc_signed_by', 'fk_shs_esc_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_envelope_signed_by', 'fk_shs_envelope_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_folder_signed_by', 'fk_shs_folder_user')->references('user_id')->on('srm_users')->onDelete('cascade');

            $table->foreign('shs_result_updated_by', 'fk_shs_test_result_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_report_card_updated_by', 'fk_shs_report_card_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_good_moral_updated_by', 'fk_shs_goodmoral_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_psa_updated_by', 'fk_shs_psa_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_completion_cert_updated_by', 'fk_shs_completion_cert_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_pic_updated_by', 'fk_shs_2x2_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_esc_updated_by', 'fk_shs_esc_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_envelope_updated_by', 'fk_shs_envelope_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('shs_folder_updated_by', 'fk_shs_folder_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registar_requirement_checklist_shs');
    }
};
