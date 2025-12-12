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
        Schema::create('registar_requirement_checklist_jhs', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable()->index();

            // JHS Test Result
            $table->boolean('has_jhs_result')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_result_signed_by')->nullable();
            $table->timestamp('jhs_result_signed_at')->nullable();
            $table->string('jhs_result_updated_by')->nullable();
            $table->timestamp('jhs_result_updated_at')->nullable();

            // JHS Report Card
            $table->boolean('has_jhs_report_card')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_report_card_signed_by')->nullable();
            $table->timestamp('jhs_report_card_signed_at')->nullable();
            $table->string('jhs_report_card_updated_by')->nullable();
            $table->timestamp('jhs_report_card_updated_at')->nullable();

            // Certificate of Good Moral
            $table->boolean('has_jhs_good_moral')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_good_moral_signed_by')->nullable();
            $table->timestamp('jhs_good_moral_signed_at')->nullable();
            $table->string('jhs_good_moral_updated_by')->nullable();
            $table->timestamp('jhs_good_moral_updated_at')->nullable();

            // PSA
            $table->boolean('has_jhs_psa')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_psa_signed_by')->nullable();
            $table->timestamp('jhs_psa_signed_at')->nullable();
            $table->string('jhs_psa_updated_by')->nullable();
            $table->timestamp('jhs_psa_updated_at')->nullable();

            // 2x2
            $table->boolean('has_jhs_pic')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_pic_signed_by')->nullable();
            $table->timestamp('jhs_pic_signed_at')->nullable();
            $table->string('jhs_pic_updated_by')->nullable();
            $table->timestamp('jhs_pic_updated_at')->nullable();

            // Proof of Income
            $table->boolean('has_jhs_income')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_income_signed_by')->nullable();
            $table->timestamp('jhs_income_signed_at')->nullable();
            $table->string('jhs_income_updated_by')->nullable();
            $table->timestamp('jhs_income_updated_at')->nullable();

            // Brown Envelope
            $table->boolean('has_jhs_envelope')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_envelope_signed_by')->nullable();
            $table->timestamp('jhs_envelope_signed_at')->nullable();
            $table->string('jhs_envelope_updated_by')->nullable();
            $table->timestamp('jhs_envelope_updated_at')->nullable();

            // White Folder
            $table->boolean('has_jhs_folder')->default(0)->comment('1 = true, 0 = false');
            $table->string('jhs_folder_signed_by')->nullable();
            $table->timestamp('jhs_folder_signed_at')->nullable();
            $table->string('jhs_folder_updated_by')->nullable();
            $table->timestamp('jhs_folder_updated_at')->nullable();

            // timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('jhs_result_signed_by', 'fk_jhs_test_result_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_report_card_signed_by', 'fk_jhs_report_card_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_good_moral_signed_by', 'fk_jhs_goodmoral_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_psa_signed_by', 'fk_jhs_psa_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_pic_signed_by', 'fk_jhs_2x2_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_income_signed_by', 'fk_jhs_income_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_envelope_signed_by', 'fk_jhs_envelope_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_folder_signed_by', 'fk_jhs_folder_user')->references('user_id')->on('srm_users')->onDelete('cascade');

            $table->foreign('jhs_result_updated_by', 'fk_jhs_test_result_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_report_card_updated_by', 'fk_jhs_report_card_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_good_moral_updated_by', 'fk_jhs_goodmoral_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_psa_updated_by', 'fk_jhs_psa_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_pic_updated_by', 'fk_jhs_2x2_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_income_updated_by', 'fk_jhs_income_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_envelope_updated_by', 'fk_jhs_envelope_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('jhs_folder_updated_by', 'fk_jhs_folder_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registar_requirement_checklist_jhs');
    }
};
